<?php

namespace App\Filament\Admin\Resources\TransactionResource\Pages;

use App\Filament\Admin\Resources\TransactionResource;
use App\Models\Transaction;
use App\Models\FiscalYear;
use App\Services\AutoJournalService;
use App\Services\AuditLogService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateTransaction extends CreateRecord
{
    protected static string $resource = TransactionResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set fiscal year
        $data['fiscal_year'] = \Carbon\Carbon::parse($data['transaction_date'])->year;
        
        // Set created_by
        $data['created_by'] = auth()->id() ?? 1;

        // Generate transaction number
        $date = \Carbon\Carbon::parse($data['transaction_date']);
        $prefix = strtoupper(substr($data['type'], 0, 3)) . '-' . $date->format('Ymd');
        $count = Transaction::where('transaction_number', 'like', $prefix . '%')->count();
        $data['transaction_number'] = $prefix . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);

        return $data;
    }

    protected function afterCreate(): void
    {
        $transaction = $this->record;

        DB::transaction(function () use ($transaction) {
            // Create journal entries based on type
            match ($transaction->type) {
                'income' => AutoJournalService::recordIncome($transaction),
                'expense' => AutoJournalService::recordExpense($transaction),
                default => null,
            };

            // Verify balance
            if (!AutoJournalService::verifyBalance($transaction)) {
                throw new \Exception('Jurnal tidak seimbang!');
            }

            // Log audit
            AuditLogService::logCreate('transactions', $transaction->id, $transaction->toArray());
        });

        Notification::make()
            ->title('✅ Transaksi Tersimpan!')
            ->body('Jurnal double-entry telah dibuat otomatis.')
            ->success()
            ->send();
    }
}
