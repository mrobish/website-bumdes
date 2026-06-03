<?php

namespace App\Filament\Admin\Pages;

use App\Models\Transaction;
use Filament\Pages\Page;

class CashFlowPage extends Page
{
    protected static string $view = 'filament.pages.cash-flow';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-trending-up';
    protected static ?string $navigationGroup = '📊 Laporan';
    protected static ?string $navigationLabel = 'Arus Kas';
    protected static ?string $title = 'Laporan Arus Kas';
    protected static ?int $navigationSort = 5;

    public int $year = 0;
    public int $month = 0;
    public ?int $unitId = null;
    public bool $loaded = false;

    public array $incomeByCategory = [];
    public array $expenseByCategory = [];
    public float $totalIncome = 0;
    public float $totalExpense = 0;
    public float $netCashFlow = 0;

    public function mount(): void
    {
        $this->year = (int) now()->year;
        $this->month = (int) now()->month;
    }

    public function loadData(): void
    {
        $year = $this->year;
        $month = $this->month;
        $unitId = $this->unitId;

        $incomeQuery = Transaction::where('type', 'income')
            ->where('is_void', false)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month);

        if ($unitId) {
            $incomeQuery->where('unit_id', $unitId);
        }

        $this->incomeByCategory = $incomeQuery->with('category')
            ->get()
            ->groupBy(fn ($t) => $t->category->name ?? 'Lainnya')
            ->map(fn ($group) => $group->sum('amount'))
            ->sortDesc()
            ->toArray();

        $expenseQuery = Transaction::where('type', 'expense')
            ->where('is_void', false)
            ->whereYear('transaction_date', $year)
            ->whereMonth('transaction_date', $month);

        if ($unitId) {
            $expenseQuery->where('unit_id', $unitId);
        }

        $this->expenseByCategory = $expenseQuery->with('category')
            ->get()
            ->groupBy(fn ($t) => $t->category->name ?? 'Lainnya')
            ->map(fn ($group) => $group->sum('amount'))
            ->sortDesc()
            ->toArray();

        $this->totalIncome = array_sum($this->incomeByCategory);
        $this->totalExpense = array_sum($this->expenseByCategory);
        $this->netCashFlow = $this->totalIncome - $this->totalExpense;
        $this->loaded = true;
    }
}
