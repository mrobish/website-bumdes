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

        foreach ($units as $unit) {
            // Get transactions for this unit in the period
            $transactions = FinancialTransaction::where('business_unit_id', $unit->id)
                ->where('status', 'posted')
                ->where('transaction_date', '>=', $period . '-01')
                ->where('transaction_date', '<=', date('Y-m-t', strtotime($period . '-01')))
                ->get();

            $unitRevenue = $transactions->where('type', 'income')->sum('amount');
            $unitExpenses = $transactions->where('type', 'expense')->sum('amount');
            $unitNetProfit = $unitRevenue - $unitExpenses;

            $unitBreakdown[$unit->code] = [
                'name' => $unit->name,
                'type' => $unit->type,
                'revenue' => $unitRevenue,
                'expenses' => $unitExpenses,
                'net_profit' => $unitNetProfit,
                'transaction_count' => $transactions->count(),
            ];

            $totalRevenue += $unitRevenue;
            $totalExpenses += $unitExpenses;
        }

        $netProfit = $totalRevenue - $totalExpenses;

        // Generate balance sheet structure
        $balanceSheet = [
            'assets' => [
                'current' => ['total' => $totalAssets * 0.6], // Simplified
                'fixed' => ['total' => $totalAssets * 0.4],
                'total' => $totalAssets,
            ],
            'liabilities' => [
                'current' => ['total' => $totalLiabilities * 0.7],
                'long_term' => ['total' => $totalLiabilities * 0.3],
                'total' => $totalLiabilities,
            ],
            'equity' => [
                'total' => $totalEquity + $netProfit,
            ],
        ];

        // Generate income statement structure
        $incomeStatement = [
            'revenue' => ['total' => $totalRevenue],
            'cost_of_goods' => ['total' => $totalExpenses * 0.6],
            'gross_profit' => ['total' => $totalRevenue - ($totalExpenses * 0.6)],
            'operating_expenses' => ['total' => $totalExpenses * 0.4],
            'net_profit' => ['total' => $netProfit],
        ];

        // Generate cash flow structure
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
