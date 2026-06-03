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

class BalanceSheetPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.balance-sheet';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = '📊 Laporan';
    protected static ?string $navigationLabel = 'Neraca';
    protected static ?string $title = 'Laporan Neraca';
    protected static ?int $navigationSort = 4;

    public ?array $data = [];
    public array $assets = [];
    public array $liabilities = [];
    public array $equity = [];
    public float $totalAsset = 0;
    public float $totalLiability = 0;
    public float $totalEquity = 0;

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

        // Assets
        $assetAccounts = Account::where('type', 'asset')->active()->orderBy('code')->get();
        $this->assets = [];
        foreach ($assetAccounts as $account) {
            $balance = AutoJournalService::getAccountBalance($account->code, $unitId, $year);
            if (abs($balance) > 0.01) {
                $this->assets[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'amount' => abs($balance),
                    'is_negative' => $account->normal_balance === 'credit', // contra asset
                ];
            }
        }

        // Liabilities
        $liabilityAccounts = Account::where('type', 'liability')->active()->orderBy('code')->get();
        $this->liabilities = [];
        foreach ($liabilityAccounts as $account) {
            $balance = AutoJournalService::getAccountBalance($account->code, $unitId, $year);
            if (abs($balance) > 0.01) {
                $this->liabilities[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'amount' => abs($balance),
                ];
            }
        }

        // Equity
        $equityAccounts = Account::where('type', 'equity')->active()->orderBy('code')->get();
        $this->equity = [];
        foreach ($equityAccounts as $account) {
            $balance = AutoJournalService::getAccountBalance($account->code, $unitId, $year);
            if (abs($balance) > 0.01) {
                $this->equity[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'amount' => abs($balance),
                ];
            }
        }

        $this->totalAsset = collect($this->assets)->sum('amount');
        $this->totalLiability = collect($this->liabilities)->sum('amount');
        $this->totalEquity = collect($this->equity)->sum('amount');
    }
}
