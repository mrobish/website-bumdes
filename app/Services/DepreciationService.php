<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetDepreciation;
use App\Models\JournalEntry;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;

class DepreciationService
{
    public static function runMonthlyDepreciation(?int $year = null, ?int $month = null): array
    {
        $year = $year ?? (int) date('Y');
        $month = $month ?? (int) date('m');
        
        $assets = Asset::where('status', 'active')
            ->where('useful_life_months', '>', 0)
            ->get();

        $totalDepreciation = 0;
        $processed = 0;

        foreach ($assets as $asset) {
            $depreciableAmount = $asset->purchase_price - ($asset->salvage_value ?? 0);
            $accumulated = $asset->accumulated_depreciation ?? 0;
            
            if ($accumulated >= $depreciableAmount) {
                $asset->update(['status' => 'fully_depreciated']);
                continue;
            }

            $monthlyDepreciation = $depreciableAmount / $asset->useful_life_months;

            // Pro-rate if purchase day > 15
            $purchaseDate = \Carbon\Carbon::parse($asset->purchase_date);
            if ($purchaseDate->year == $year && $purchaseDate->month == $month && $purchaseDate->day > 15) {
                $daysInMonth = $purchaseDate->daysInMonth;
                $daysRemaining = $daysInMonth - $purchaseDate->day + 1;
                $monthlyDepreciation = $monthlyDepreciation * ($daysRemaining / $daysInMonth);
            }

            $remainingDepreciable = $depreciableAmount - $accumulated;
            $monthlyDepreciation = min($monthlyDepreciation, $remainingDepreciable);
            $monthlyDepreciation = round($monthlyDepreciation, 2);

            if ($monthlyDepreciation <= 0) continue;

            $beginningBookValue = $asset->current_book_value ?? ($asset->purchase_price - $accumulated);
            $newAccumulated = $accumulated + $monthlyDepreciation;
            $endingBookValue = $asset->purchase_price - $newAccumulated;
            $period = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT);

            DB::transaction(function () use ($asset, $monthlyDepreciation, $year, $month, $accumulated, $beginningBookValue, $newAccumulated, $endingBookValue, $period) {
                // 1. Create transaction record first
                $transaction = Transaction::create([
                    'transaction_number' => "DEP-$period-" . substr($asset->asset_code, -3),
                    'transaction_date' => "$year-$month-28",
                    'type' => 'adjustment',
                    'amount' => $monthlyDepreciation,
                    'description' => "Depresiasi Aset: {$asset->name}",
                    'reference_number' => "DEP-$period-" . $asset->asset_code,
                    'unit_id' => $asset->business_unit_id,
                    'category_id' => null,
                    'created_by' => 1,
                    'fiscal_year' => $year,
                ]);

                // 2. Journal Entry: DR Beban Depresiasi
                JournalEntry::create([
                    'transaction_id' => $transaction->id,
                    'entry_date' => "$year-$month-28",
                    'entry_type' => 'depreciation',
                    'account_code' => '6104',
                    'debit' => $monthlyDepreciation,
                    'credit' => 0,
                    'unit_id' => $asset->business_unit_id,
                    'description' => "Depresiasi: {$asset->name}",
                    'fiscal_year' => $year,
                ]);

                // 3. Journal Entry: CR Akumulasi Depresiasi
                $contraCode = match($asset->category) {
                    'kendaraan' => '1292',
                    'peralatan' => '1293',
                    default => '1291',
                };

                JournalEntry::create([
                    'transaction_id' => $transaction->id,
                    'entry_date' => "$year-$month-28",
                    'entry_type' => 'depreciation',
                    'account_code' => $contraCode,
                    'debit' => 0,
                    'credit' => $monthlyDepreciation,
                    'unit_id' => $asset->business_unit_id,
                    'description' => "Akum. Depresiasi: {$asset->name}",
                    'fiscal_year' => $year,
                ]);

                // 4. Update asset
                $asset->update([
                    'accumulated_depreciation' => $newAccumulated,
                    'current_book_value' => $endingBookValue,
                ]);

                // 5. Log depreciation
                AssetDepreciation::create([
                    'asset_id' => $asset->id,
                    'depreciation_date' => "$year-$month-28",
                    'period' => $period,
                    'beginning_book_value' => $beginningBookValue,
                    'depreciation_amount' => $monthlyDepreciation,
                    'accumulated_depreciation' => $newAccumulated,
                    'ending_book_value' => $endingBookValue,
                    'journal_entry_id' => $transaction->id,
                ]);
            });

            $totalDepreciation += $monthlyDepreciation;
            $processed++;
        }

        return ['processed' => $processed, 'total_depreciation' => $totalDepreciation];
    }
}
