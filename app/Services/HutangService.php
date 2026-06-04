<?php

namespace App\Services;

use App\Models\Hutang;
use App\Models\HutangPayment;
use App\Models\Transaction;
use App\Models\JournalEntry;
use Illuminate\Support\Facades\DB;

/**
 * Hutang Service — double-entry journal integration
 *
 * Saat Hutang Dibuat:
 *   DR: Beban/Asset (5xxx/1xxx) — sesuai kategori
 *   CR: Hutang Usaha (2101) — kewajiban bertambah
 *
 * Saat Bayar (Cicilan/Lunas):
 *   DR: Hutang Usaha (2101) — kewajiban berkurang
 *   CR: Kas/Bank (1101/1102) — kas keluar
 */
class HutangService
{
    /**
     * Create hutang with auto journal
     */
    public static function create(array $data): Hutang
    {
        return DB::transaction(function () use ($data) {
            // Create transaction (new system)
            $transaction = Transaction::create([
                'transaction_number' => Transaction::generateNumber('HTG'),
                'transaction_date' => $data['hutang_date'],
                'type' => 'expense',
                'category_id' => $data['category_id'],
                'unit_id' => $data['unit_id'],
                'amount' => $data['amount'],
                'description' => 'Hutang: ' . $data['supplier_name'] . ' - ' . ($data['description'] ?? ''),
                'fiscal_year' => \Carbon\Carbon::parse($data['hutang_date'])->year,
                'created_by' => auth()->id() ?? 1,
            ]);

            // Auto journal — DR: Beban, CR: Hutang Usaha
            $category = \App\Models\Category::find($data['category_id']);
            $accountCode = $category ? $category->getAccountCodeForUnit($data['unit_id']) : '5101';

            JournalEntry::create([
                'transaction_id' => $transaction->id,
                'entry_date' => $data['hutang_date'],
                'entry_type' => 'normal',
                'account_code' => $accountCode,
                'debit' => $data['amount'],
                'credit' => 0,
                'unit_id' => $data['unit_id'],
                'description' => 'Hutang: ' . $data['supplier_name'],
                'fiscal_year' => $transaction->fiscal_year,
            ]);

            JournalEntry::create([
                'transaction_id' => $transaction->id,
                'entry_date' => $data['hutang_date'],
                'entry_type' => 'normal',
                'account_code' => '2101', // Hutang Usaha
                'debit' => 0,
                'credit' => $data['amount'],
                'unit_id' => $data['unit_id'],
                'description' => 'Hutang: ' . $data['supplier_name'],
                'fiscal_year' => $transaction->fiscal_year,
            ]);

            // Create hutang record
            $hutang = Hutang::create([
                'hutang_date' => $data['hutang_date'],
                'supplier_name' => $data['supplier_name'],
                'supplier_phone' => $data['supplier_phone'] ?? null,
                'supplier_address' => $data['supplier_address'] ?? null,
                'supplier_npwp' => $data['supplier_npwp'] ?? null,
                'category_id' => $data['category_id'],
                'unit_id' => $data['unit_id'],
                'amount' => $data['amount'],
                'remaining' => $data['amount'],
                'due_date' => $data['due_date'] ?? null,
                'description' => $data['description'] ?? null,
                'reference_number' => $data['reference_number'] ?? null,
                'attachment_path' => $data['attachment_path'] ?? null,
                'agreement_letter_path' => $data['agreement_letter_path'] ?? null,
                'transaction_id' => $transaction->id,
                'created_by' => auth()->id() ?? 1,
                'notes' => $data['notes'] ?? null,
            ]);

            return $hutang;
        });
    }

    /**
     * Record payment with auto journal
     */
    public static function recordPayment(Hutang $hutang, array $data): HutangPayment
    {
        return DB::transaction(function () use ($hutang, $data) {
            // Determine kas/bank account
            $kasAccount = match($data['payment_method'] ?? 'tunai') {
                'transfer' => '1102',
                default => '1101',
            };

            // Create transaction
            $transaction = Transaction::create([
                'transaction_number' => Transaction::generateNumber('BYR'),
                'transaction_date' => $data['payment_date'],
                'type' => 'expense',
                'category_id' => $hutang->category_id,
                'unit_id' => $hutang->unit_id,
                'amount' => $data['amount'],
                'description' => 'Bayar hutang: ' . $hutang->supplier_name,
                'fiscal_year' => \Carbon\Carbon::parse($data['payment_date'])->year,
                'created_by' => auth()->id() ?? 1,
            ]);

            // Auto journal — DR: Hutang Usaha, CR: Kas/Bank
            JournalEntry::create([
                'transaction_id' => $transaction->id,
                'entry_date' => $data['payment_date'],
                'entry_type' => 'normal',
                'account_code' => '2101', // Hutang Usaha
                'debit' => $data['amount'],
                'credit' => 0,
                'unit_id' => $hutang->unit_id,
                'description' => 'Bayar hutang: ' . $hutang->supplier_name,
                'fiscal_year' => $transaction->fiscal_year,
            ]);

            JournalEntry::create([
                'transaction_id' => $transaction->id,
                'entry_date' => $data['payment_date'],
                'entry_type' => 'normal',
                'account_code' => $kasAccount,
                'debit' => 0,
                'credit' => $data['amount'],
                'unit_id' => $hutang->unit_id,
                'description' => 'Bayar hutang: ' . $hutang->supplier_name,
                'fiscal_year' => $transaction->fiscal_year,
            ]);

            // Create payment record
            $payment = HutangPayment::create([
                'hutang_id' => $hutang->id,
                'payment_date' => $data['payment_date'],
                'amount' => $data['amount'],
                'payment_method' => $data['payment_method'] ?? 'tunai',
                'reference_number' => $data['reference_number'] ?? null,
                'notes' => $data['notes'] ?? null,
                'transaction_id' => $transaction->id,
                'created_by' => auth()->id() ?? 1,
            ]);

            // Update hutang
            $hutang->paid_amount += $data['amount'];
            $hutang->updateStatus();

            return $payment;
        });
    }

    /**
     * Void hutang — reverse journal entries
     */
    public static function voidHutang(Hutang $hutang, string $reason = ''): void
    {
        DB::transaction(function () use ($hutang, $reason) {
            if ($hutang->transaction) {
                AutoJournalService::recordVoid($hutang->transaction);
                $hutang->transaction->update([
                    'is_void' => true,
                    'voided_by' => auth()->id(),
                    'voided_at' => now(),
                ]);
            }
            $hutang->update(['status' => 'macet']);
        });
    }

    /**
     * Get hutang summary (single query)
     */
    public static function getSummary(?int $unitId = null): array
    {
        $query = Hutang::query();
        if ($unitId) {
            $query->where('unit_id', $unitId);
        }

        $result = $query->selectRaw("
            COALESCE(SUM(amount), 0) as total,
            COALESCE(SUM(paid_amount), 0) as dibayar,
            COALESCE(SUM(CASE WHEN status IN ('belum_lunas', 'sebagian') THEN remaining ELSE 0 END), 0) as sisa,
            COUNT(CASE WHEN status = 'lunas' THEN 1 END) as lunas,
            COUNT(CASE WHEN status = 'belum_lunas' THEN 1 END) as belum_lunas,
            COUNT(CASE WHEN status = 'sebagian' THEN 1 END) as sebagian,
            COUNT(CASE WHEN status = 'macet' THEN 1 END) as macet,
            COALESCE(SUM(CASE WHEN status IN ('belum_lunas', 'sebagian') AND due_date < NOW() THEN remaining ELSE 0 END), 0) as jatuh_tempo
        ")->first();

        return [
            'total' => (float) $result->total,
            'dibayar' => (float) $result->dibayar,
            'sisa' => (float) $result->sisa,
            'lunas' => (int) $result->lunas,
            'belum_lunas' => (int) $result->belum_lunas,
            'sebagian' => (int) $result->sebagian,
            'macet' => (int) $result->macet,
            'jatuh_tempo' => (float) $result->jatuh_tempo,
        ];
    }
}
