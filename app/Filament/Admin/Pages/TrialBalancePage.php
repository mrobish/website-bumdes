<?php

namespace App\Filament\Admin\Pages;

use App\Models\Account;
use App\Services\AutoJournalService;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class TrialBalancePage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.trial-balance';
    protected static ?string $navigationIcon = 'heroicon-o-balance';
    protected static ?string $navigationGroup = '📊 Laporan';
    protected static ?string $navigationLabel = 'Neraca Saldo';
    protected static ?string $title = 'Neraca Saldo';
    protected static ?int $navigationSort = 2;

    public ?array $data = [];
    public array $accounts = [];
    public float $totalDebit = 0;
    public float $totalCredit = 0;
    public bool $isBalanced = false;

    public function mount(): void
    {
        $this->form->fill([
            'year' => now()->year,
            'unit_id' => '',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    Select::make('year')
                        ->label('Tahun')
                        ->options(collect(array_reverse(range(now()->year - 3, now()->year + 1)))->mapWithKeys(fn ($y) => [$y => $y]))
                        ->default(now()->year)
                        ->required(),
                    Select::make('unit_id')
                        ->label('Unit')
                        ->options(fn () => ['' => 'Semua Unit'] + \App\Models\BusinessUnit::pluck('name', 'id')->toArray())
                        ->default(''),
                ])->columns(2),
            ])
            ->statePath('data');
    }

    public function loadData(): void
    {
        $data = $this->form->getState();
        $year = $data['year'];
        $unitId = !empty($data['unit_id']) ? (int) $data['unit_id'] : null;

        $accounts = Account::active()->orderBy('code')->get();
        $result = [];

        foreach ($accounts as $account) {
            $balance = AutoJournalService::getAccountBalance($account->code, $unitId, $year);
            
            if (abs($balance) > 0.01) {
                $result[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type,
                    'debit' => $account->normal_balance === 'debit' ? max($balance, 0) : 0,
                    'credit' => $account->normal_balance === 'credit' ? max($balance, 0) : 0,
                    'balance' => $balance,
                ];
            }
        }

        $this->accounts = $result;
        $this->totalDebit = collect($result)->sum('debit');
        $this->totalCredit = collect($result)->sum('credit');
        $this->isBalanced = abs($this->totalDebit - $this->totalCredit) < 0.01;
    }
}
