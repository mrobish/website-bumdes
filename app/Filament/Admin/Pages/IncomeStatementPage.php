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

class IncomeStatementPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.income-statement';
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = '📊 Laporan';
    protected static ?string $navigationLabel = 'Laba/Rugi';
    protected static ?string $title = 'Laporan Laba/Rugi';
    protected static ?int $navigationSort = 3;

    public ?array $data = [];
    public array $revenues = [];
    public array $expenses = [];
    public float $totalRevenue = 0;
    public float $totalExpense = 0;
    public float $netIncome = 0;

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

        // Get revenue accounts
        $revenueAccounts = Account::where('type', 'revenue')->active()->orderBy('code')->get();
        $this->revenues = [];
        foreach ($revenueAccounts as $account) {
            $balance = AutoJournalService::getAccountBalance($account->code, $unitId, $year);
            if (abs($balance) > 0.01) {
                $this->revenues[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'amount' => abs($balance),
                ];
            }
        }

        // Get expense accounts
        $expenseAccounts = Account::where('type', 'expense')->active()->orderBy('code')->get();
        $this->expenses = [];
        foreach ($expenseAccounts as $account) {
            $balance = AutoJournalService::getAccountBalance($account->code, $unitId, $year);
            if (abs($balance) > 0.01) {
                $this->expenses[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'amount' => abs($balance),
                ];
            }
        }

        $this->totalRevenue = collect($this->revenues)->sum('amount');
        $this->totalExpense = collect($this->expenses)->sum('amount');
        $this->netIncome = $this->totalRevenue - $this->totalExpense;
    }
}
