<?php

namespace App\Services;

use App\Models\Piutang;
use App\Models\PiutangPayment;
use App\Models\Transaction;
use App\Models\JournalEntry;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PiutangService
{
    /**
     * Create piutang + auto journal
     * DR: Piutang Usaha (1103)
     * CR: Pendapatan (4xxx) atau Kas (1101) jika reversal
     */
    public static function createPiutang(array $data): Piutang
    {
        return DB::transaction(function () use ($data) {
            // 1. Create Transaction
            $transaction = Transaction::create([
                'transaction_number' => Transaction::generateNumber('PIU'),
                'transaction_date' => $data['piutang_date'],
                'type' => 'piutang',
                'category_id' => $data['category_id'] ?? null,
                'unit_id' => $data['unit_id'] ?? null,
                'amount' => $data['amount'],
                'description' => 'Piutang: ' . $data['customer_name'] . ' - ' . ($data['description'] ?? ''),
                'reference_number' => $data['reference_number'] ?? null,
                'fiscal_year' => date('Y', strtotime($data['piutang_date'])),
                'created_by' => Auth::id(),
            ]);

            // 2. Create Piutang record
            $piutang = Piutang::create([
                'piutang_date' => $data['piutang_date'],
                'customer_name' => $data['customer_name'],
                'customer_phone' => $data['customer_phone'] ?? null,
                'customer_address' => $data['customer_address'] ?? null,
                'customer_nik' => $data['customer_nik'] ?? null,
                'category_id' => $data['category_id'] ?? null,
                'unit_id' => $data['unit_id'] ?? null,
                'amount' => $data['amount'],
                'remaining' => $data['amount'],
                'due_date' => $data['due_date'] ?? null,
                'description' => $data['description'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'transaction_id' => $transaction->id,
                'created_by' => Auth::id(),
                'notes' => $data['notes'] ?? null,
            ]);

            // 3. Auto Journal: DR Piutang, CR Pendapatan
            // DR: Piutang Usaha (1103)
            JournalEntry::create([
                'transaction_id' => $transaction->id,
                'entry_date' => $data['piutang_date'],
                'entry_type' => 'normal',
                'account_code' => '1103', // Piutang Usaha
                'debit' => $data['amount'],
                'credit' => 0,
                'unit_id' => $data['unit_id'] ?? null,
                'description' => 'Piutang: ' . $data['customer_name'],
                'fiscal_year' => date('Y', strtotime($data['piutang_date'])),
            ]);

            // CR: Pendapatan (sesuai kategori) atau default 4101
            $creditAccount = '4101'; // default Pendapatan Jasa
            if (!empty($data['category_id'])) {
                $category = \App\Models\Category::find($data['category_id']);
                if ($category && $category->default_account_code) {
                    $creditAccount = $category->default_account_code;
                }
            }

            JournalEntry::create([
                'transaction_id' => $transaction->id,
                'entry_date' => $data['piutang_date'],
                'entry_type' => 'normal',
                'account_code' => $creditAccount,
                'debit' => 0,
                'credit' => $data['amount'],
                'unit_id' => $data['unit_id'] ?? null,
                'description' => 'Pendapatan dari piutang: ' . $data['customer_name'],
                'fiscal_year' => date('Y', strtotime($data['piutang_date'])),
            ]);

            return $piutang;
        });
    }

    /**
     * Record payment + auto journal
     * DR: Kas (1101)
     * CR: Piutang Usaha (1103)
     */
    public static function recordPayment(Piutang $piutang, array $data): PiutangPayment
    {
        return DB::transaction(function () use ($piutang, $data) {
            // 1. Create Transaction for payment
            $transaction = Transaction::create([
                'transaction_number' => Transaction::generateNumber('BYR'),
                'transaction_date' => $data['payment_date'],
                'type' => 'income',
                'category_id' => $piutang->category_id,
                'unit_id' => $piutang->unit_id,
                'amount' => $data['amount'],
                'description' => 'Pembayaran piutang: ' . $piutang->customer_name . ' (' . $piutang->piutang_number . ')',
                'reference_number' => $data['reference_number'] ?? null,
                'fiscal_year' => date('Y', strtotime($data['payment_date'])),
                'created_by' => Auth::id(),
            ]);

            // 2. Create payment record
            $payment = PiutangPayment::create([
                'piutang_id' => $piutang->id,
                'payment_date' => $data['payment_date'],
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'] ?? 'tunai',
                'reference_number' => $data['reference_number'] ?? null,
                'notes' => $data['notes'] ?? null,
                'transaction_id' => $transaction->id,
                'created_by' => Auth::id(),
            ]);

            // 3. Update piutang totals
            $piutang->paid_amount += $data['amount'];
            $piutang->updateStatus();

            // 4. Auto Journal: DR Kas, CR Piutang
            // DR: Kas/Bank (1101 or 1102)
            $kasAccount = ($data['payment_method'] ?? 'tunai') === 'transfer' ? '1102' : '1101';
            
            JournalEntry::create([
                'transaction_id' => $transaction->id,
                'entry_date' => $data['payment_date'],
                'entry_type' => 'normal',
                'account_code' => $kasAccount,
                'debit' => $data['amount'],
                'credit' => 0,
                'unit_id' => $piutang->unit_id,
                'description' => 'Bayar piutang: ' . $piutang->customer_name,
                'fiscal_year' => date('Y', strtotime($data['payment_date'])),
            ]);

            // CR: Piutang Usaha (1103)
            JournalEntry::create([
                'transaction_id' => $transaction->id,
                'entry_date' => $data['payment_date'],
                'entry_type' => 'normal',
                'account_code' => '1103',
                'debit' => 0,
                'credit' => $data['amount'],
                'unit_id' => $piutang->unit_id,
                'description' => 'Pelunasan piutang: ' . $piutang->customer_name,
                'fiscal_year' => date('Y', strtotime($data['payment_date'])),
            ]);

            return $payment;
        });
    }

    /**
     * Get total piutang outstanding (for Neraca)
     */
    public static function getOutstandingTotal(?int $unitId = null, ?int $year = null, ?int $month = null): float
    {
        $query = Piutang::whereIn('status', ['belum_lunas', 'sebagian']);
        
        if ($unitId) {
            $query->where('unit_id', $unitId);
        }
        if ($year) {
            $query->whereYear('piutang_date', '<=', $year);
        }
        if ($month && $year) {
            $query->where(function ($q) use ($year, $month) {
                $q->where(function ($q2) use ($year, $month) {
                    $q2->whereYear('piutang_date', '<', $year);
                })->orWhere(function ($q2) use ($year, $month) {
                    $q2->whereYear('piutang_date', $year)
                        ->whereMonth('piutang_date', '<=', $month);
                });
            });
        }

        return $query->sum('remaining');
    }

    /**
     * Get piutang summary by status
     */
    public static function getSummary(?int $unitId = null): array
    {
        $query = Piutang::query();
        if ($unitId) {
            $query->where('unit_id', $unitId);
        }

        return [
            'total' => $query->sum('amount'),
            'dibayar' => $query->sum('paid_amount'),
            'sisa' => $query->whereIn('status', ['belum_lunas', 'sebagian'])->sum('remaining'),
            'lunas' => $query->where('status', 'lunas')->count(),
            'belum_lunas' => $query->where('status', 'belum_lunas')->count(),
            'sebagian' => $query->where('status', 'sebagian')->count(),
            'macet' => $query->where('status', 'macet')->count(),
            'jatuh_tempo' => $query->whereIn('status', ['belum_lunas', 'sebagian'])
                ->where('due_date', '<', now())->sum('remaining'),
        ];
    }
}
