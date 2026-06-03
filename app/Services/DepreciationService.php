<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\DepreciationLog;
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

            DB::transaction(function () use ($asset, $monthlyDepreciation, $year, $month) {
                // 1. Create transaction record first
                $transaction = Transaction::create([
                    'transaction_date' => "$year-$month-28",
                    'type' => 'expense',
                    'amount' => $monthlyDepreciation,
                    'description' => "Depresiasi Aset: {$asset->name}",
                    'reference_number' => "DEP-$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-" . $asset->asset_code,
                    'unit_id' => $asset->business_unit_id,
                    'account_id' => 1, // Kas (simplified)
                    'category_id' => null,
                    'status' => 'posted',
                    'created_by' => 1,
                ]);

                // 2. Create journal entries with transaction_id
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

                // 3. Update asset accumulated depreciation
                $newAccumulated = $accumulated + $monthlyDepreciation;
                $asset->update([
                    'accumulated_depreciation' => $newAccumulated,
                    'current_book_value' => $asset->purchase_price - $newAccumulated,
                ]);

                // 4. Log depreciation
                DepreciationLog::create([
                    'asset_id' => $asset->id,
                    'run_date' => "$year-$month-28",
                    'depreciation_amount' => $monthlyDepreciation,
                    'journal_entry_id' => $transaction->id,
                    'fiscal_year' => $year,
                ]);
            });

            $totalDepreciation += $monthlyDepreciation;
            $processed++;
        }

        return ['processed' => $processed, 'total_depreciation' => $totalDepreciation];
    }
}
