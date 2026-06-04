<?php

namespace App\Filament\Admin\Widgets;

use App\Models\Budget;
use App\Models\Transaction;
use App\Models\Piutang;
use App\Models\Hutang;
use Filament\Widgets\Widget;

class BudgetAlertWidget extends Widget
{
    protected static string $view = 'filament.admin.widgets.budget-alert';
    protected static ?int $sort = 2;

    public array $alerts = [];
    public array $piutangOverdue = [];
    public array $hutangOverdue = [];

    public function mount(): void
    {
        $this->loadAlerts();
    }

    protected function loadAlerts(): void
    {
        $year = now()->year;
        $month = now()->month;

        // Budget alerts
        $budgets = Budget::where('year', $year)
            ->where('month', $month)
            ->with('category')
            ->get();

        foreach ($budgets as $budget) {
            $actualSpent = Transaction::where('category_id', $budget->category_id)
                ->where('unit_id', $budget->unit_id)
                ->whereMonth('transaction_date', $month)
                ->whereYear('transaction_date', $year)
                ->where('type', 'expense')
                ->where('is_void', false)
                ->sum('amount');

            $utilization = $budget->amount > 0 ? ($actualSpent / $budget->amount) * 100 : 0;

            if ($utilization >= 80) {
                $this->alerts[] = [
                    'type' => $utilization >= 100 ? 'danger' : 'warning',
                    'icon' => $utilization >= 100 ? '🔴' : '🟡',
                    'message' => "Anggaran \"{$budget->category->name}\" terpakai " . number_format($utilization, 1) . "% (Rp " . number_format($actualSpent, 0, ',', '.') . " / Rp " . number_format($budget->amount, 0, ',', '.') . ")",
                    'unit' => $budget->unit?->name ?? 'Pusat',
                ];
            }
        }

        // Piutang overdue
        $this->piutangOverdue = Piutang::whereIn('status', ['belum_lunas', 'sebagian'])
            ->where('due_date', '<', now())
            ->with('unit')
            ->get()
            ->map(fn ($p) => [
                'name' => $p->customer_name,
                'amount' => $p->remaining,
                'due_date' => $p->due_date->format('d/m/Y'),
                'unit' => $p->unit?->name ?? '-',
            ])
            ->toArray();

        // Hutang overdue
        $this->hutangOverdue = Hutang::whereIn('status', ['belum_lunas', 'sebagian'])
            ->where('due_date', '<', now())
            ->with('unit')
            ->get()
            ->map(fn ($h) => [
                'name' => $h->supplier_name,
                'amount' => $h->remaining,
                'due_date' => $h->due_date->format('d/m/Y'),
                'unit' => $h->unit?->name ?? '-',
            ])
            ->toArray();
    }

    public function hasAlerts(): bool
    {
        return !empty($this->alerts) || !empty($this->piutangOverdue) || !empty($this->hutangOverdue);
    }
}
