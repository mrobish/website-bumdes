<?php

namespace App\Filament\Admin\Pages;

use App\Models\ChartOfAccount;
use App\Models\FinancialTransaction;
use App\Models\Budget;
use Filament\Pages\Page;
use Illuminate\Support\Facades\DB;

class FinanceReport extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    
    protected static ?string $navigationLabel = 'Laporan Keuangan';
    
    protected static ?string $title = 'Laporan Keuangan';
    
    protected static ?string $navigationGroup = '💰 Keuangan';
    
    protected static ?int $navigationSort = 4;
    
    protected static string $view = 'filament.admin.pages.finance-report';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public ?int $year = null;
    
    public ?string $report_type = 'neraca';

    public function mount(): void
    {
        $this->year = $this->year ?? now()->year;
    }

    public function getReportData(): array
    {
        $year = $this->year;
        
        // Total assets
        $totalAssets = FinancialTransaction::approved()
            ->whereYear('transaction_date', $year)
            ->whereHas('account', fn ($q) => $q->where('type', 'aset'))
            ->where('type', 'pemasukan')
            ->sum('amount')
            - FinancialTransaction::approved()
            ->whereYear('transaction_date', $year)
            ->whereHas('account', fn ($q) => $q->where('type', 'aset'))
            ->where('type', 'pengeluaran')
            ->sum('amount');

        // Total liabilities
        $totalLiabilities = FinancialTransaction::approved()
            ->whereYear('transaction_date', $year)
            ->whereHas('account', fn ($q) => $q->where('type', 'kewajiban'))
            ->where('type', 'pengeluaran')
            ->sum('amount')
            - FinancialTransaction::approved()
            ->whereYear('transaction_date', $year)
            ->whereHas('account', fn ($q) => $q->where('type', 'kewajiban'))
            ->where('type', 'pemasukan')
            ->sum('amount');

        // Total equity
        $totalEquity = FinancialTransaction::approved()
            ->whereYear('transaction_date', $year)
            ->whereHas('account', fn ($q) => $q->where('type', 'modal'))
            ->where('type', 'pemasukan')
            ->sum('amount')
            - FinancialTransaction::approved()
            ->whereYear('transaction_date', $year)
            ->whereHas('account', fn ($q) => $q->where('type', 'modal'))
            ->where('type', 'pengeluaran')
            ->sum('amount');

        // Total revenue
        $totalRevenue = FinancialTransaction::approved()
            ->whereYear('transaction_date', $year)
            ->whereHas('account', fn ($q) => $q->where('type', 'pendapatan'))
            ->where('type', 'pemasukan')
            ->sum('amount');

        // Total expense
        $totalExpense = FinancialTransaction::approved()
            ->whereYear('transaction_date', $year)
            ->whereHas('account', fn ($q) => $q->where('type', 'beban'))
            ->where('type', 'pengeluaran')
            ->sum('amount');

        // Net income
        $netIncome = $totalRevenue - $totalExpense;

        // Monthly transactions
        $monthlyTransactions = FinancialTransaction::approved()
            ->whereYear('transaction_date', $year)
            ->select(
                DB::raw('MONTH(transaction_date) as month'),
                DB::raw('SUM(CASE WHEN type = "pemasukan" THEN amount ELSE 0 END) as income'),
                DB::raw('SUM(CASE WHEN type = "pengeluaran" THEN amount ELSE 0 END) as expense')
            )
            ->groupBy(DB::raw('MONTH(transaction_date)'))
            ->get();

        return [
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
            'total_revenue' => $totalRevenue,
            'total_expense' => $totalExpense,
            'net_income' => $netIncome,
            'monthly_transactions' => $monthlyTransactions,
        ];
    }
}
