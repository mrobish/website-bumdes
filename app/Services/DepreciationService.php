<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\DepreciationLog;
use App\Models\JournalEntry;
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
            $depreciableAmount = $asset->purchase_price - $asset->salvage_value;
            $accumulated = $asset->accumulated_depreciation ?? 0;
            
            if ($accumulated >= $depreciableAmount) {
                $asset->update(['status' => 'fully_depreciated']);
                continue;
            }

            $monthlyDepreciation = $depreciableAmount / $asset->useful_life_months;

            // Pro-rate if purchase month
            $purchaseDate = \Carbon\Carbon::parse($asset->purchase_date);
            if ($purchaseDate->year == $year && $purchaseDate->month == $month && $purchaseDate->day > 15) {
                $daysInMonth = $purchaseDate->daysInMonth;
                $daysRemaining = $daysInMonth - $purchaseDate->day + 1;
                $monthlyDepreciation = $monthlyDepreciation * ($daysRemaining / $daysInMonth);
            }

            $remainingDepreciable = $depreciableAmount - $accumulated;
            $monthlyDepreciation = min($monthlyDepreciation, $remainingDepreciable);

            if ($monthlyDepreciation <= 0) continue;

            DB::transaction(function () use ($asset, $monthlyDepreciation, $year, $month) {
                $journalEntry = JournalEntry::create([
                    'transaction_id' => null,
                    'entry_date' => "$year-$month-28",
                    'entry_type' => 'depreciation',
                    'account_code' => '6104',
                    'debit' => $monthlyDepreciation,
                    'credit' => 0,
                    'unit_id' => $asset->business_unit_id,
                    'description' => "Depresiasi: {$asset->name}",
                    'fiscal_year' => $year,
                ]);

                $contraCode = match($asset->category) {
                    'kendaraan' => '1292',
                    'peralatan' => '1293',
                    default => '1291',
                };

                JournalEntry::create([
                    'transaction_id' => null,
                    'entry_date' => "$year-$month-28",
                    'entry_type' => 'depreciation',
                    'account_code' => $contraCode,
                    'debit' => 0,
                    'credit' => $monthlyDepreciation,
                    'unit_id' => $asset->business_unit_id,
                    'description' => "Akum. Depresiasi: {$asset->name}",
                    'fiscal_year' => $year,
                ]);

                $newAccumulated = $accumulated + $monthlyDepreciation;
                $asset->update([
                    'accumulated_depreciation' => $newAccumulated,
                    'current_book_value' => $asset->purchase_price - $newAccumulated,
                ]);

                DepreciationLog::create([
                    'asset_id' => $asset->id,
                    'run_date' => "$year-$month-28",
                    'depreciation_amount' => $monthlyDepreciation,
                    'journal_entry_id' => $journalEntry->id,
                    'fiscal_year' => $year,
                ]);
            });

            $totalDepreciation += $monthlyDepreciation;
            $processed++;
        }

        return ['processed' => $processed, 'total_depreciation' => $totalDepreciation];
    }
}
