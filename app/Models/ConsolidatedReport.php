<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class ConsolidatedReport extends Model
{
    protected $fillable = [
        'report_number',
        'period',
        'period_type',
        'balance_sheet',
        'income_statement',
        'cash_flow',
        'notes',
        'unit_breakdown',
        'total_assets',
        'total_liabilities',
        'total_equity',
        'total_revenue',
        'total_expenses',
        'net_profit',
        'status',
        'prepared_by',
        'approved_by',
        'approved_at',
        'pdf_path',
    ];

    protected $casts = [
        'balance_sheet' => 'array',
        'income_statement' => 'array',
        'cash_flow' => 'array',
        'notes' => 'array',
        'unit_breakdown' => 'array',
        'total_assets' => 'decimal:2',
        'total_liabilities' => 'decimal:2',
        'total_equity' => 'decimal:2',
        'total_revenue' => 'decimal:2',
        'total_expenses' => 'decimal:2',
        'net_profit' => 'decimal:2',
        'approved_at' => 'datetime',
    ];

    // Relationships
    public function preparer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'prepared_by');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    public function scopeFinal($query)
    {
        return $query->where('status', 'final');
    }

    public function scopeForPeriod($query, string $period)
    {
        return $query->where('period', $period);
    }

    // Auto-generate report number
    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->report_number)) {
                $year = date('Y');
                $last = self::whereYear('created_at', $year)->count() + 1;
                $model->report_number = 'KONSOL-' . $year . '-' . str_pad($last, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    // Generate consolidated report from all units
    public static function generate(string $period, string $periodType, int $userId): self
    {
        $units = BusinessUnit::active()->get();
        $unitBreakdown = [];

        $totalAssets = 0;
        $totalLiabilities = 0;
        $totalEquity = 0;
        $totalRevenue = 0;
        $totalExpenses = 0;

        $year = (int) substr($period, 0, 4);
        $month = (int) substr($period, 5, 2);

        foreach ($units as $unit) {
            // Get actual balances from journal entries (new system)
            $unitAssets = \App\Services\AutoJournalService::getTotalByType('asset', $unit->id, $year, $month);
            $unitLiabilities = \App\Services\AutoJournalService::getTotalByType('liability', $unit->id, $year, $month);
            $unitEquity = \App\Services\AutoJournalService::getTotalByType('equity', $unit->id, $year, $month);
            $unitRevenue = \App\Services\AutoJournalService::getTotalByType('revenue', $unit->id, $year, $month);
            $unitExpenses = \App\Services\AutoJournalService::getTotalByType('expense', $unit->id, $year, $month);
            $unitNetProfit = $unitRevenue - $unitExpenses;

            $unitBreakdown[$unit->code] = [
                'name' => $unit->name,
                'type' => $unit->type,
                'revenue' => $unitRevenue,
                'expenses' => $unitExpenses,
                'net_profit' => $unitNetProfit,
                'assets' => $unitAssets,
                'liabilities' => $unitLiabilities,
                'equity' => $unitEquity,
            ];

            $totalAssets += $unitAssets;
            $totalLiabilities += $unitLiabilities;
            $totalEquity += $unitEquity;
            $totalRevenue += $unitRevenue;
            $totalExpenses += $unitExpenses;
        }

        $netProfit = $totalRevenue - $totalExpenses;

        // Generate balance sheet from actual data
        $balanceSheet = [
            'assets' => [
                'current' => ['total' => $totalAssets * 0.6], // Simplified split
                'fixed' => ['total' => $totalAssets * 0.4],
                'total' => $totalAssets,
            ],
            'liabilities' => [
                'current' => ['total' => $totalLiabilities],
                'total' => $totalLiabilities,
            ],
            'equity' => [
                'total' => $totalEquity + $netProfit,
            ],
        ];

        // Generate income statement from actual data
        $incomeStatement = [
            'revenue' => ['total' => $totalRevenue],
            'expenses' => ['total' => $totalExpenses],
            'net_profit' => ['total' => $netProfit],
        ];

        // Generate cash flow from actual data
        $cashFlow = [
            'operating' => ['total' => $netProfit],
            'investing' => ['total' => 0],
            'financing' => ['total' => 0],
            'net_change' => ['total' => $netProfit],
        ];

        return self::create([
            'period' => $period,
            'period_type' => $periodType,
            'balance_sheet' => $balanceSheet,
            'income_statement' => $incomeStatement,
            'cash_flow' => $cashFlow,
            'unit_breakdown' => $unitBreakdown,
            'total_assets' => $totalAssets,
            'total_liabilities' => $totalLiabilities,
            'total_equity' => $totalEquity,
            'total_revenue' => $totalRevenue,
            'total_expenses' => $totalExpenses,
            'net_profit' => $netProfit,
            'status' => 'draft',
            'prepared_by' => $userId,
        ]);
    }

    // Helpers
    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function finalize(int $userId): bool
    {
        return $this->update([
            'status' => 'final',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);
    }

    public function getFormattedTotalAssetsAttribute(): string
    {
        return 'Rp ' . number_format($this->total_assets, 0, ',', '.');
    }

    public function getFormattedNetProfitAttribute(): string
    {
        return 'Rp ' . number_format($this->net_profit, 0, ',', '.');
    }
}
