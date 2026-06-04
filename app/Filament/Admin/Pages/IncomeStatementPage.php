<?php

namespace App\Filament\Admin\Pages;

use App\Models\Account;
use App\Services\AutoJournalService;
use Filament\Pages\Page;

class IncomeStatementPage extends Page
{
    protected static string $view = 'filament.pages.income-statement';
    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    protected static ?string $navigationGroup = '📊 Laporan';
    protected static ?string $navigationLabel = 'Laba/Rugi';
    protected static ?string $title = 'Laporan Laba/Rugi';
    protected static ?int $navigationSort = 3;

    public int $year = 0;
    public int $month = 0;
    public ?int $unitId = null;
    public bool $loaded = false;

    public array $revenues = [];
    public array $expenses = [];
    public float $totalRevenue = 0;
    public float $totalExpense = 0;
    public float $netIncome = 0;

    public function mount(): void
    {
        $this->year = (int) now()->year;
        $this->month = (int) now()->month;
    }

    public function loadData(): void
    {
        $year = $this->year;
        $month = $this->month > 0 ? $this->month : null;
        $unitId = $this->unitId;

        $revenueAccounts = Account::where('type', 'revenue')->active()->orderBy('code')->get();
        $this->revenues = [];
        foreach ($revenueAccounts as $account) {
            $balance = AutoJournalService::getAccountBalance($account->code, $unitId, $year, $month);
            if (abs($balance) > 0.01) {
                $this->revenues[] = ['code' => $account->code, 'name' => $account->name, 'amount' => abs($balance)];
            }
        }

        $expenseAccounts = Account::where('type', 'expense')->active()->orderBy('code')->get();
        $this->expenses = [];
        foreach ($expenseAccounts as $account) {
            $balance = AutoJournalService::getAccountBalance($account->code, $unitId, $year, $month);
            if (abs($balance) > 0.01) {
                $this->expenses[] = ['code' => $account->code, 'name' => $account->name, 'amount' => abs($balance)];
            }
        }

        $this->totalRevenue = collect($this->revenues)->sum('amount');
        $this->totalExpense = collect($this->expenses)->sum('amount');
        $this->netIncome = $this->totalRevenue - $this->totalExpense;
        $this->loaded = true;
    }
}
