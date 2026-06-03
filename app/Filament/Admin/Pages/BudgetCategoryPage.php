<?php

namespace App\Filament\Admin\Pages;

use App\Models\Budget;
use App\Models\Category;
use App\Models\BusinessUnit;
use App\Models\FiscalYear;
use App\Models\Transaction;
use Filament\Pages\Page;

class BudgetCategoryPage extends Page
{
    protected static string $view = 'filament.pages.budget-category';

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Keuangan';

    protected static ?string $navigationLabel = 'Anggaran';

    protected static ?string $title = 'Anggaran per Kategori';

    protected static ?int $navigationSort = 5;

    public int $year = 0;

    public ?int $unitId = null;

    public ?string $typeFilter = null;

    public bool $loaded = false;

    public array $budgetData = [];

    public array $monthNames = [
        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember',
    ];

    public array $summaryByCategory = [];

    public array $grandTotal = [
        'budget' => 0,
        'actual' => 0,
        'diff' => 0,
        'pct' => 0,
    ];

    public function mount(): void
    {
        $this->year = (int) now()->year;
    }

    public function loadData(): void
    {
        $year = $this->year;
        $unitId = $this->unitId;
        $typeFilter = $this->typeFilter;

        // Fetch all budgets for the selected year (and optionally unit)
        $budgetQuery = Budget::where('year', $year);

        if ($unitId) {
            $budgetQuery->where('unit_id', $unitId);
        }

        $budgets = $budgetQuery->get();

        // Get category IDs from budgets
        $categoryIds = $budgets->pluck('category_id')->unique()->values()->all();

        // Fetch categories
        $categoryQuery = Category::whereIn('id', $categoryIds);
        if ($typeFilter) {
            $categoryQuery->where('type', $typeFilter);
        }
        $categories = $categoryQuery->orderBy('type')->orderBy('name')->get();

        // Build budget data per category per month
        $this->budgetData = [];
        $this->summaryByCategory = [];
        $totalBudget = 0;
        $totalActual = 0;

        foreach ($categories as $category) {
            $monthlyData = [];
            $catBudgetTotal = 0;
            $catActualTotal = 0;

            for ($month = 1; $month <= 12; $month++) {
                $budget = $budgets->first(function ($b) use ($category, $month, $unitId) {
                    return $b->category_id === $category->id
                        && $b->month === $month
                        && ($unitId ? $b->unit_id == $unitId : true);
                });

                $budgetAmount = $budget ? (float) $budget->amount : 0;

                // Calculate actual from transactions
                $actualAmount = Transaction::where('category_id', $category->id)
                    ->whereYear('transaction_date', $year)
                    ->whereMonth('transaction_date', $month)
                    ->where('is_void', false)
                    ->where('type', $category->type === 'income' ? 'income' : 'expense');

                if ($unitId) {
                    $actualAmount->where('unit_id', $unitId);
                }

                $actualAmount = (float) $actualAmount->sum('amount');

                $selisih = $actualAmount - $budgetAmount;
                $persentase = $budgetAmount > 0 ? ($actualAmount / $budgetAmount) * 100 : 0;

                if ($persentase < 80) {
                    $status = 'ok';
                    $statusIcon = '🟢';
                } elseif ($persentase <= 100) {
                    $status = 'warning';
                    $statusIcon = '🟡';
                } else {
                    $status = 'over';
                    $statusIcon = '🔴';
                }

                $monthlyData[$month] = [
                    'budget' => $budgetAmount,
                    'actual' => $actualAmount,
                    'selisih' => $selisih,
                    'persentase' => $persentase,
                    'status' => $status,
                    'status_icon' => $statusIcon,
                    'has_budget' => $budgetAmount > 0,
                ];

                $catBudgetTotal += $budgetAmount;
                $catActualTotal += $actualAmount;
            }

            $catSelisih = $catActualTotal - $catBudgetTotal;
            $catPersentase = $catBudgetTotal > 0 ? ($catActualTotal / $catBudgetTotal) * 100 : 0;

            $this->budgetData[] = [
                'category' => $category,
                'monthly' => $monthlyData,
                'total_budget' => $catBudgetTotal,
                'total_actual' => $catActualTotal,
                'total_selisih' => $catSelisih,
                'total_persentase' => $catPersentase,
            ];

            $totalBudget += $catBudgetTotal;
            $totalActual += $catActualTotal;
        }

        $this->grandTotal = [
            'budget' => $totalBudget,
            'actual' => $totalActual,
            'diff' => $totalActual - $totalBudget,
            'pct' => $totalBudget > 0 ? ($totalActual / $totalBudget) * 100 : 0,
        ];

        $this->loaded = true;
    }

    public function getStatusClass(float $pct): string
    {
        if ($pct < 80) return 'text-green-600';
        if ($pct <= 100) return 'text-yellow-600';
        return 'text-red-600';
    }

    public function getStatusIcon(float $pct): string
    {
        if ($pct < 80) return '🟢';
        if ($pct <= 100) return '🟡';
        return '🔴';
    }

    public function getStatusBadgeClass(float $pct): string
    {
        if ($pct < 80) return 'bg-green-100 text-green-800';
        if ($pct <= 100) return 'bg-yellow-100 text-yellow-800';
        return 'bg-red-100 text-red-800';
    }

    public function formatCurrency(float $amount): string
    {
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }
}
