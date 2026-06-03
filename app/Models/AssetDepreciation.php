<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetDepreciation extends Model
{
    protected $fillable = [
        'asset_id', 'depreciation_date', 'period', 'beginning_book_value',
        'depreciation_amount', 'accumulated_depreciation', 'ending_book_value',
        'status', 'journal_entry_id',
    ];

    protected $casts = [
        'depreciation_date' => 'date',
        'beginning_book_value' => 'decimal:2',
        'depreciation_amount' => 'decimal:2',
        'accumulated_depreciation' => 'decimal:2',
        'ending_book_value' => 'decimal:2',
    ];

    public function asset(): BelongsTo { return $this->belongsTo(Asset::class); }

    public function scopePosted($query) { return $query->where('status', 'posted'); }
    public function scopeForPeriod($query, string $period) { return $query->where('period', $period); }

    // Auto-generate penyusutan untuk semua aset aktif
    public static function generateForPeriod(string $period): array
    {
        $results = [];
        $assets = Asset::active()->get();

        foreach ($assets as $asset) {
            // Cek apakah sudah ada penyusutan untuk periode ini
            $exists = self::where('asset_id', $asset->id)->where('period', $period)->exists();
            if ($exists) continue;

            // Cek apakah masih ada umur ekonomis
            $totalDepreciated = $asset->depreciations()->posted()->count();
            if ($totalDepreciated >= $asset->useful_life_months) continue;

            // Hitung nilai buku awal
            $accumulatedBefore = $asset->depreciations()->posted()->sum('depreciation_amount');
            $beginningBookValue = $asset->purchase_price - $accumulatedBefore;

            // Hitung penyusutan
            $depreciationAmount = $asset->getMonthlyDepreciation();
            
            // Cek jika bulan terakhir, sesuaikan agar tidak melebihi nilai sisa
            if ($totalDepreciated + 1 >= $asset->useful_life_months) {
                $depreciationAmount = $beginningBookValue - $asset->salvage_value;
            }
            if ($depreciationAmount < 0) $depreciationAmount = 0;

            $accumulatedAfter = $accumulatedBefore + $depreciationAmount;
            $endingBookValue = $asset->purchase_price - $accumulatedAfter;

            $depreciation = self::create([
                'asset_id' => $asset->id,
                'depreciation_date' => \Carbon\Carbon::parse($period . '-01')->endOfMonth(),
                'period' => $period,
                'beginning_book_value' => $beginningBookValue,
                'depreciation_amount' => $depreciationAmount,
                'accumulated_depreciation' => $accumulatedAfter,
                'ending_book_value' => $endingBookValue,
                'status' => 'draft',
            ]);

            $results[] = [
                'asset' => $asset->name,
                'depreciation' => $depreciationAmount,
            ];
        }

        return $results;
    }

    public function getFormattedAmountAttribute(): string { return 'Rp ' . number_format($this->depreciation_amount, 0, ',', '.'); }
}
