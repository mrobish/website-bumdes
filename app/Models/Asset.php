<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asset extends Model
{
    protected $fillable = [
        'asset_code', 'name', 'description', 'category', 'business_unit_id',
        'account_id', 'purchase_price', 'salvage_value', 'purchase_date',
        'useful_life_months', 'depreciation_method', 'depreciation_rate',
        'status', 'disposal_date', 'disposal_price', 'location', 'attachments',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'salvage_value' => 'decimal:2',
        'purchase_date' => 'date',
        'depreciation_rate' => 'decimal:2',
        'disposal_date' => 'date',
        'disposal_price' => 'decimal:2',
        'attachments' => 'array',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->asset_code)) {
                $year = date('Y');
                $last = self::whereYear('created_at', $year)->count() + 1;
                $model->asset_code = 'AST-' . $year . '-' . str_pad($last, 3, '0', STR_PAD_LEFT);
            }
        });
    }

    public function businessUnit(): BelongsTo { return $this->belongsTo(BusinessUnit::class); }
    public function account(): BelongsTo { return $this->belongsTo(ChartOfAccount::class, 'account_id'); }
    public function depreciations(): HasMany { return $this->hasMany(AssetDepreciation::class); }

    public function scopeActive($query) { return $query->where('status', 'active'); }

    // Hitung penyusutan per bulan (straight line)
    public function getMonthlyDepreciation(): float
    {
        if ($this->useful_life_months <= 0) return 0;
        $depreciable = $this->purchase_price - $this->salvage_value;
        return $depreciable / $this->useful_life_months;
    }

    // Nilai buku saat ini
    public function getCurrentBookValue(): float
    {
        $accumulated = $this->depreciations()->where('status', 'posted')->sum('depreciation_amount');
        return $this->purchase_price - $accumulated;
    }

    // Total akumulasi penyusutan
    public function getAccumulatedDepreciation(): float
    {
        return $this->depreciations()->where('status', 'posted')->sum('depreciation_amount');
    }

    public function getFormattedPurchasePriceAttribute(): string { return 'Rp ' . number_format($this->purchase_price, 0, ',', '.'); }
    public function getFormattedBookValueAttribute(): string { return 'Rp ' . number_format($this->getCurrentBookValue(), 0, ',', '.'); }
}
