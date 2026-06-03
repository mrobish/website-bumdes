<?php

namespace App\Services;

use App\Models\Account;
use App\Models\FiscalYear;
use App\Models\JournalEntry;
use App\Models\Transaction;
use Illuminate\Support\Facades\DB;
use Exception;

class YearEndClosingService
{
    /**
     * Check if the fiscal year is open
     */
    public static function isYearOpen(int $fiscalYearId): bool
    {
        $fy = FiscalYear::find($fiscalYearId);
        return $fy && $fy->status === 'open';
    }

    /**
     * Check if the year can be closed (all transactions posted, no drafts)
     */
    public static function canCloseYear(int $fiscalYearId): array
    {
        $fy = FiscalYear::find($fiscalYearId);
        if (!$fy) {
            return ['can' => false, 'reason' => 'Tahun fiskal tidak ditemukan.'];
        }

        if ($fy->status === 'closed') {
            return ['can' => false, 'reason' => 'Tahun fiskal ini sudah ditutup sebelumnya.'];
        }

        if ($fy->status !== 'open') {
            return ['can' => false, 'reason' => 'Status tahun fiskal harus "open" untuk ditutup.'];
        }

        // Check for unapproved transactions (drafts)
        $draftCount = Transaction::where('fiscal_year', $fy->year)
            ->whereNull('approved_by')
            ->where('is_void', false)
            ->count();

        if ($draftCount > 0) {
            return [
                'can' => false,
                'reason' => "Masih ada {$draftCount} transaksi yang belum disetujui (draft).",
            ];
        }

        // Check for voided transactions that need reversal entries
        $voidedCount = Transaction::where('fiscal_year', $fy->year)
            ->where('is_void', true)
            ->count();

        if ($voidedCount > 0) {
            // Voided transactions are OK as long as reversal entries exist
            $voidsWithoutReversal = Transaction::where('fiscal_year', $fy->year)
                ->where('is_void', true)
                ->whereDoesntHave('journalEntries', function ($q) {
                    $q->where('entry_type', 'reversal');
                })
                ->count();

            if ($voidsWithoutReversal > 0) {
                return [
                    'can' => false,
                    'reason' => "Ada {$voidsWithoutReversal} transaksi void tanpa jurnal reversal.",
                ];
            }
        }

        return ['can' => true, 'reason' => 'Siap untuk ditutup.'];
    }

    /**
     * Get trial balance data for the fiscal year
     */
    public static function getTrialBalance(int $fiscalYearId): array
    {
        $fy = FiscalYear::find($fiscalYearId);
        if (!$fy) {
            return ['accounts' => [], 'totalDebit' => 0, 'totalCredit' => 0, 'balanced' => false];
        }

        $accounts = Account::active()->orderBy('code')->get();
        $result = [];
        $totalDebit = 0;
        $totalCredit = 0;

        foreach ($accounts as $account) {
            $debit = JournalEntry::where('account_code', $account->code)
                ->where('fiscal_year', $fy->year)
                ->sum('debit');
            $credit = JournalEntry::where('account_code', $account->code)
                ->where('fiscal_year', $fy->year)
                ->sum('credit');

            if (abs($debit - $credit) > 0.01) {
                $balance = $account->normal_balance === 'debit'
                    ? $debit - $credit
                    : $credit - $debit;

                $result[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'type' => $account->type,
                    'debit' => $debit,
                    'credit' => $credit,
                    'balance' => $balance,
                    'normal_balance' => $account->normal_balance,
                ];

                $totalDebit += $debit;
                $totalCredit += $credit;
            }
        }

        return [
            'accounts' => $result,
            'totalDebit' => $totalDebit,
            'totalCredit' => $totalCredit,
            'balanced' => abs($totalDebit - $totalCredit) < 0.01,
        ];
    }

    /**
     * Get income statement data for the fiscal year
     */
    public static function getIncomeStatement(int $fiscalYearId): array
    {
        $fy = FiscalYear::find($fiscalYearId);
        if (!$fy) {
            return ['revenues' => [], 'expenses' => [], 'totalRevenue' => 0, 'totalExpense' => 0, 'netIncome' => 0];
        }

        // Revenue accounts
        $revenueAccounts = Account::where('type', 'revenue')->active()->orderBy('code')->get();
        $revenues = [];
        $totalRevenue = 0;

        foreach ($revenueAccounts as $account) {
            $credit = JournalEntry::where('account_code', $account->code)
                ->where('fiscal_year', $fy->year)
                ->sum('credit');
            $debit = JournalEntry::where('account_code', $account->code)
                ->where('fiscal_year', $fy->year)
                ->sum('debit');
            $balance = $credit - $debit;

            if (abs($balance) > 0.01) {
                $revenues[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'amount' => abs($balance),
                ];
                $totalRevenue += abs($balance);
            }
        }

        // Expense accounts
        $expenseAccounts = Account::where('type', 'expense')->active()->orderBy('code')->get();
        $expenses = [];
        $totalExpense = 0;

        foreach ($expenseAccounts as $account) {
            $debit = JournalEntry::where('account_code', $account->code)
                ->where('fiscal_year', $fy->year)
                ->sum('debit');
            $credit = JournalEntry::where('account_code', $account->code)
                ->where('fiscal_year', $fy->year)
                ->sum('credit');
            $balance = $debit - $credit;

            if (abs($balance) > 0.01) {
                $expenses[] = [
                    'code' => $account->code,
                    'name' => $account->name,
                    'amount' => abs($balance),
                ];
                $totalExpense += abs($balance);
            }
        }

        return [
            'revenues' => $revenues,
            'expenses' => $expenses,
            'totalRevenue' => $totalRevenue,
            'totalExpense' => $totalExpense,
            'netIncome' => $totalRevenue - $totalExpense,
        ];
    }

    /**
     * Close the fiscal year: create closing entries, update status, create next year
     */
    public static function closeYear(int $fiscalYearId): array
    {
        // Pre-check
        $check = self::canCloseYear($fiscalYearId);
        if (!$check['can']) {
            throw new Exception('Tidak dapat menutup tahun: ' . $check['reason']);
        }

        $fy = FiscalYear::findOrFail($fiscalYearId);
        $year = $fy->year;

        return DB::transaction(function () use ($fy, $year) {
            // 1. Calculate net income
            $incomeData = self::getIncomeStatement($fy->id);
            $netIncome = $incomeData['netIncome'];
            $totalRevenue = $incomeData['totalRevenue'];
            $totalExpense = $incomeData['totalExpense'];

            // 2. Create closing journal entries
            $closingDate = \Carbon\Carbon::parse("{$year}-12-31");

            // 2a. Close revenue accounts (debit revenue, credit Income Summary)
            $revenueAccounts = Account::where('type', 'revenue')->active()->get();
            foreach ($revenueAccounts as $account) {
                $balance = JournalEntry::where('account_code', $account->code)
                    ->where('fiscal_year', $year)
                    ->sum('credit')
                    - JournalEntry::where('account_code', $account->code)
                    ->where('fiscal_year', $year)
                    ->sum('debit');

                if (abs($balance) > 0.01) {
                    JournalEntry::create([
                        'transaction_id' => null,
                        'entry_date' => $closingDate,
                        'entry_type' => 'closing',
                        'account_code' => $account->code,
                        'debit' => abs($balance),
                        'credit' => 0,
                        'unit_id' => null,
                        'description' => "Tutup Buku: {$account->name}",
                        'fiscal_year' => $year,
                        'is_closing_entry' => true,
                    ]);
                }
            }

            // 2b. Close expense accounts (debit Income Summary, credit expense)
            $expenseAccounts = Account::where('type', 'expense')->active()->get();
            foreach ($expenseAccounts as $account) {
                $balance = JournalEntry::where('account_code', $account->code)
                    ->where('fiscal_year', $year)
                    ->sum('debit')
                    - JournalEntry::where('account_code', $account->code)
                    ->where('fiscal_year', $year)
                    ->sum('credit');

                if (abs($balance) > 0.01) {
                    JournalEntry::create([
                        'transaction_id' => null,
                        'entry_date' => $closingDate,
                        'entry_type' => 'closing',
                        'account_code' => $account->code,
                        'debit' => 0,
                        'credit' => abs($balance),
                        'unit_id' => null,
                        'description' => "Tutup Buku: {$account->name}",
                        'fiscal_year' => $year,
                        'is_closing_entry' => true,
                    ]);
                }
            }

            // 2c. Transfer net income to retained earnings (Laba Ditahan)
            // If net income > 0: DR Income Summary, CR Laba Ditahan (3201)
            // If net income < 0: DR Laba Ditahan, CR Income Summary
            if (abs($netIncome) > 0.01) {
                if ($netIncome > 0) {
                    // Net profit: Debit Income Summary, Credit Retained Earnings
                    JournalEntry::create([
                        'transaction_id' => null,
                        'entry_date' => $closingDate,
                        'entry_type' => 'closing',
                        'account_code' => '3201', // Laba Ditahan / Retained Earnings
                        'debit' => 0,
                        'credit' => $netIncome,
                        'unit_id' => null,
                        'description' => "Tutup Buku: Laba bersih tahun {$year}",
                        'fiscal_year' => $year,
                        'is_closing_entry' => true,
                    ]);
                } else {
                    // Net loss: Debit Retained Earnings, Credit Income Summary
                    JournalEntry::create([
                        'transaction_id' => null,
                        'entry_date' => $closingDate,
                        'entry_type' => 'closing',
                        'account_code' => '3201', // Laba Ditahan / Retained Earnings
                        'debit' => abs($netIncome),
                        'credit' => 0,
                        'unit_id' => null,
                        'description' => "Tutup Buku: Rugi bersih tahun {$year}",
                        'fiscal_year' => $year,
                        'is_closing_entry' => true,
                    ]);
                }
            }

            // 3. Update fiscal year status
            $fy->update([
                'status' => 'closed',
                'closed_at' => now(),
                'closed_by' => auth()->id(),
            ]);

            // 4. Create next fiscal year if it doesn't exist
            $nextYear = $year + 1;
            $nextFy = FiscalYear::where('year', $nextYear)->first();
            if (!$nextFy) {
                FiscalYear::create([
                    'year' => $nextYear,
                    'status' => 'open',
                ]);
            }

            // 5. Log to audit log
            AuditLogService::logCreate(
                'fiscal_years',
                $fy->id,
                [
                    'action' => 'year_end_closing',
                    'year' => $year,
                    'net_income' => $netIncome,
                    'total_revenue' => $totalRevenue,
                    'total_expense' => $totalExpense,
                    'closed_by' => auth()->user()?->name ?? 'System',
                ]
            );

            return [
                'success' => true,
                'year' => $year,
                'netIncome' => $netIncome,
                'totalRevenue' => $totalRevenue,
                'totalExpense' => $totalExpense,
                'nextYear' => $nextYear,
                'message' => "Tahun fiskal {$year} berhasil ditutup. Tahun {$nextYear} telah dibuat.",
            ];
        });
    }
}
