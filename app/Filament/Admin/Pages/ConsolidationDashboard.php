<?php

namespace App\Filament\Admin\Pages;

use App\Models\BusinessUnit;
use App\Models\FinancialTransaction;
use App\Models\InterAccountTransfer;
use Filament\Pages\Page;

class ConsolidationDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationGroup = '💰 Keuangan';
    protected static ?string $navigationLabel = 'Dashboard Konsolidasi';
    protected static ?string $title = 'Dashboard Konsolidasi BUMDes';
    protected static ?int $navigationSort = 4;
    protected static string $view = 'filament.admin.pages.consolidation-dashboard';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public $units = [];
    public $totalRevenue = 0;
    public $totalExpenses = 0;
    public $netProfit = 0;
    public $pendingTransfers = 0;
    public $completedTransfers = 0;
    public $totalTransferAmount = 0;

    public function mount(): void
    {
        $this->loadData();
    }

    public function loadData(): void
    {
        $units = BusinessUnit::active()->get();
        
        foreach ($units as $unit) {
            $transactions = FinancialTransaction::where('business_unit_id', $unit->id)
                ->whereIn('status', ['published', 'approved'])
                ->whereYear('transaction_date', now()->year)
                ->get();

            $revenue = $transactions->where('type', 'pemasukan')->sum('amount');
            $expenses = $transactions->where('type', 'pengeluaran')->sum('amount');
            
            $this->units[] = [
                'id' => $unit->id,
                'code' => $unit->code,
                'name' => $unit->name,
                'type' => $unit->type,
                'revenue' => $revenue,
                'expenses' => $expenses,
                'net_profit' => $revenue - $expenses,
                'transaction_count' => $transactions->count(),
            ];

            $this->totalRevenue += $revenue;
            $this->totalExpenses += $expenses;
        }

        $this->netProfit = $this->totalRevenue - $this->totalExpenses;

        // Transfer stats
        $this->pendingTransfers = InterAccountTransfer::pending()->count();
        $this->completedTransfers = InterAccountTransfer::completed()->count();
        $this->totalTransferAmount = InterAccountTransfer::completed()
            ->whereYear('transfer_date', now()->year)
            ->sum('amount');
    }
}
