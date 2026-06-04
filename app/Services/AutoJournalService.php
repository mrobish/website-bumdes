<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\JournalEntry;
use App\Models\Account;
use App\Models\Category;
use App\Models\FiscalYear;
use Illuminate\Support\Facades\DB;

class AutoJournalService
{
    /**
     * Record income transaction with auto double-entry
     * DEBIT: Kas/Bank (1101/1102), CREDIT: Pendapatan (4xxx)
     */
    public static function recordIncome(Transaction $transaction): void
    {
        $category = $transaction->category;
        $accountCode = $category->getAccountCodeForUnit($transaction->unit_id);

        // DEBIT: Kas/Bank
        JournalEntry::create([
            'transaction_id' => $transaction->id,
            'entry_date' => $transaction->transaction_date,
            'entry_type' => 'normal',
            'account_code' => '1101', // Kas
            'debit' => $transaction->amount,
            'credit' => 0,
            'unit_id' => $transaction->unit_id,
            'description' => $transaction->description,
            'fiscal_year' => $transaction->fiscal_year,
        ]);

        // CREDIT: Pendapatan
        JournalEntry::create([
            'transaction_id' => $transaction->id,
            'entry_date' => $transaction->transaction_date,
            'entry_type' => 'normal',
            'account_code' => $accountCode,
            'debit' => 0,
            'credit' => $transaction->amount,
            'unit_id' => $transaction->unit_id,
            'description' => $transaction->description,
            'fiscal_year' => $transaction->fiscal_year,
        ]);
    }

    /**
     * Record expense transaction with auto double-entry
     * DEBIT: Beban (5xxx/6xxx), CREDIT: Kas/Bank (1101/1102)
     */
    public static function recordExpense(Transaction $transaction): void
    {
        $category = $transaction->category;
        $accountCode = $category->getAccountCodeForUnit($transaction->unit_id);

        // DEBIT: Beban
        JournalEntry::create([
            'transaction_id' => $transaction->id,
            'entry_date' => $transaction->transaction_date,
            'entry_type' => 'normal',
            'account_code' => $accountCode,
            'debit' => $transaction->amount,
            'credit' => 0,
            'unit_id' => $transaction->unit_id,
            'description' => $transaction->description,
            'fiscal_year' => $transaction->fiscal_year,
        ]);

        // CREDIT: Kas/Bank
        JournalEntry::create([
            'transaction_id' => $transaction->id,
            'entry_date' => $transaction->transaction_date,
            'entry_type' => 'normal',
            'account_code' => '1101', // Kas
            'debit' => 0,
            'credit' => $transaction->amount,
            'unit_id' => $transaction->unit_id,
            'description' => $transaction->description,
            'fiscal_year' => $transaction->fiscal_year,
        ]);
    }

    /**
     * Record asset purchase with auto double-entry
     * DEBIT: Aset Tetap (12xx), CREDIT: Kas/Bank (1101/1102)
     */
    public static function recordAssetPurchase(Transaction $transaction, string $assetAccountCode): void
    {
        // DEBIT: Aset Tetap
        JournalEntry::create([
            'transaction_id' => $transaction->id,
            'entry_date' => $transaction->transaction_date,
            'entry_type' => 'normal',
            'account_code' => $assetAccountCode,
            'debit' => $transaction->amount,
            'credit' => 0,
            'unit_id' => $transaction->unit_id,
            'description' => $transaction->description,
            'fiscal_year' => $transaction->fiscal_year,
        ]);

        // CREDIT: Kas/Bank
        JournalEntry::create([
            'transaction_id' => $transaction->id,
            'entry_date' => $transaction->transaction_date,
            'entry_type' => 'normal',
            'account_code' => '1101', // Kas
            'debit' => 0,
            'credit' => $transaction->amount,
            'unit_id' => $transaction->unit_id,
            'description' => $transaction->description,
            'fiscal_year' => $transaction->fiscal_year,
        ]);
    }

    /**
     * Record void transaction (reversing entry)
     * Creates opposite entries to zero out the original
     */
    public static function recordVoid(Transaction $transaction): void
    {
        $originalEntries = $transaction->journalEntries;

        foreach ($originalEntries as $entry) {
            JournalEntry::create([
                'transaction_id' => $transaction->id,
                'entry_date' => $transaction->transaction_date,
                'entry_type' => 'reversal',
                'account_code' => $entry->account_code,
                'debit' => $entry->credit, // Swap debit/credit
                'credit' => $entry->debit,
                'unit_id' => $entry->unit_id,
                'description' => 'VOID: ' . $entry->description,
                'fiscal_year' => $entry->fiscal_year,
            ]);
        }
    }

    /**
     * Record adjustment transaction
     * Similar to income/expense but for corrections/adjustments
     */
    public static function recordAdjustment(Transaction $transaction): void
    {
        $category = $transaction->category;
        if (!$category) return;

        $accountCode = $category->getAccountCodeForUnit($transaction->unit_id);

        if ($category->type === 'income') {
            // Adjustment income: DR Kas, CR Pendapatan
            JournalEntry::create([
                'transaction_id' => $transaction->id,
                'entry_date' => $transaction->transaction_date,
                'entry_type' => 'adjustment',
                'account_code' => '1101',
                'debit' => $transaction->amount,
                'credit' => 0,
                'unit_id' => $transaction->unit_id,
                'description' => 'Penyesuaian: ' . $transaction->description,
                'fiscal_year' => $transaction->fiscal_year,
            ]);
            JournalEntry::create([
                'transaction_id' => $transaction->id,
                'entry_date' => $transaction->transaction_date,
                'entry_type' => 'adjustment',
                'account_code' => $accountCode,
                'debit' => 0,
                'credit' => $transaction->amount,
                'unit_id' => $transaction->unit_id,
                'description' => 'Penyesuaian: ' . $transaction->description,
                'fiscal_year' => $transaction->fiscal_year,
            ]);
        } else {
            // Adjustment expense: DR Beban, CR Kas
            JournalEntry::create([
                'transaction_id' => $transaction->id,
                'entry_date' => $transaction->transaction_date,
                'entry_type' => 'adjustment',
                'account_code' => $accountCode,
                'debit' => $transaction->amount,
                'credit' => 0,
                'unit_id' => $transaction->unit_id,
                'description' => 'Penyesuaian: ' . $transaction->description,
                'fiscal_year' => $transaction->fiscal_year,
            ]);
            JournalEntry::create([
                'transaction_id' => $transaction->id,
                'entry_date' => $transaction->transaction_date,
                'entry_type' => 'adjustment',
                'account_code' => '1101',
                'debit' => 0,
                'credit' => $transaction->amount,
                'unit_id' => $transaction->unit_id,
                'description' => 'Penyesuaian: ' . $transaction->description,
                'fiscal_year' => $transaction->fiscal_year,
            ]);
        }
    }

    /**
     * Record opening balance with contra entry
     * DEBIT or CREDIT based on normal balance + contra to equity (3200)
     */
    public static function recordOpeningBalance(Transaction $transaction, string $accountCode, float $amount): void
    {
        $account = Account::where('code', $accountCode)->first();
        if (!$account) return;

        if ($account->normal_balance === 'debit') {
            // Asset/Expense: DR Account, CR Saldo Awal (3200)
            JournalEntry::create([
                'transaction_id' => $transaction->id,
                'entry_date' => $transaction->transaction_date,
                'entry_type' => 'opening_balance',
                'account_code' => $accountCode,
                'debit' => $amount,
                'credit' => 0,
                'unit_id' => $transaction->unit_id,
                'description' => 'Saldo Awal: ' . $account->name,
                'fiscal_year' => $transaction->fiscal_year,
            ]);
            JournalEntry::create([
                'transaction_id' => $transaction->id,
                'entry_date' => $transaction->transaction_date,
                'entry_type' => 'opening_balance',
                'account_code' => '3200', // Saldo Awal / Ekuitas
                'debit' => 0,
                'credit' => $amount,
                'unit_id' => $transaction->unit_id,
                'description' => 'Saldo Awal: ' . $account->name,
                'fiscal_year' => $transaction->fiscal_year,
            ]);
        } else {
            // Liability/Equity/Revenue: CR Account, DR Saldo Awal (3200)
            JournalEntry::create([
                'transaction_id' => $transaction->id,
                'entry_date' => $transaction->transaction_date,
                'entry_type' => 'opening_balance',
                'account_code' => '3200', // Saldo Awal / Ekuitas
                'debit' => $amount,
                'credit' => 0,
                'unit_id' => $transaction->unit_id,
                'description' => 'Saldo Awal: ' . $account->name,
                'fiscal_year' => $transaction->fiscal_year,
            ]);
            JournalEntry::create([
                'transaction_id' => $transaction->id,
                'entry_date' => $transaction->transaction_date,
                'entry_type' => 'opening_balance',
                'account_code' => $accountCode,
                'debit' => 0,
                'credit' => $amount,
                'unit_id' => $transaction->unit_id,
                'description' => 'Saldo Awal: ' . $account->name,
                'fiscal_year' => $transaction->fiscal_year,
            ]);
        }
    }

    /**
     * Verify journal balance for a transaction
     * Returns true if debits == credits
     */
    public static function verifyBalance(Transaction $transaction): bool
    {
        $totalDebit = $transaction->journalEntries->sum('debit');
        $totalCredit = $transaction->journalEntries->sum('credit');
        
        return abs($totalDebit - $totalCredit) < 0.01;
    }

    /**
     * Get account balance for a specific period
     */
    public static function getAccountBalance(string $accountCode, ?int $unitId = null, ?int $year = null, ?int $month = null): float
    {
        $query = JournalEntry::where('account_code', $accountCode);
        
        if ($unitId) {
            $query->where('unit_id', $unitId);
        }
        if ($year) {
            $query->where('fiscal_year', $year);
        }
        if ($month) {
            $start = "$year-" . str_pad($month, 2, '0', STR_PAD_LEFT) . "-01";
            $end = date('Y-m-t', strtotime($start));
            $query->whereBetween('entry_date', [$start, $end]);
        }

        $account = Account::where('code', $accountCode)->first();
        $debit = $query->sum('debit');
        $credit = $query->sum('credit');

        if ($account && $account->normal_balance === 'debit') {
            return $debit - $credit;
        }
        return $credit - $debit;
    }

    /**
     * Get total balance for account type (for balance sheet)
     */
    public static function getTotalByType(string $type, ?int $unitId = null, ?int $year = null, ?int $month = null): float
    {
        $accounts = Account::where('type', $type)->pluck('code');
        $total = 0;

        foreach ($accounts as $code) {
            $total += self::getAccountBalance($code, $unitId, $year, $month);
        }

        return $total;
    }
}
