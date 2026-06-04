<?php

namespace App\Filament\Admin\Resources\PiutangResource\Pages;

use App\Filament\Admin\Resources\PiutangResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePiutang extends CreateRecord
{
    protected static string $resource = PiutangResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        unset($data['attachment_path']);
        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        
        try {
            $transaction = \App\Models\Transaction::create([
                'transaction_number' => \App\Models\Transaction::generateNumber('PIU'),
                'transaction_date' => $record->piutang_date,
                'type' => 'income',
                'category_id' => $record->category_id,
                'unit_id' => $record->unit_id,
                'amount' => $record->amount,
                'description' => 'Piutang: ' . $record->customer_name . ' - ' . ($record->description ?? ''),
                'reference_number' => $record->reference_number,
                'fiscal_year' => $record->piutang_date->format('Y'),
                'created_by' => auth()->id(),
            ]);

            $record->update(['transaction_id' => $transaction->id]);

            // DR: Piutang Usaha (1103)
            \App\Models\JournalEntry::create([
                'transaction_id' => $transaction->id,
                'entry_date' => $record->piutang_date,
                'entry_type' => 'debit',
                'account_code' => '1103',
                'debit' => $record->amount,
                'credit' => 0,
                'unit_id' => $record->unit_id,
                'description' => 'Piutang: ' . $record->customer_name,
                'fiscal_year' => $record->piutang_date->format('Y'),
            ]);

            // CR: Pendapatan
            $creditAccount = '4101';
            if ($record->category_id) {
                $category = \App\Models\Category::find($record->category_id);
                if ($category && $category->default_account_code) {
                    $creditAccount = $category->default_account_code;
                }
            }

            \App\Models\JournalEntry::create([
                'transaction_id' => $transaction->id,
                'entry_date' => $record->piutang_date,
                'entry_type' => 'credit',
                'account_code' => $creditAccount,
                'debit' => 0,
                'credit' => $record->amount,
                'unit_id' => $record->unit_id,
                'description' => 'Pendapatan dari piutang: ' . $record->customer_name,
                'fiscal_year' => $record->piutang_date->format('Y'),
            ]);

            Notification::make()
                ->title('Piutang berhasil dicatat!')
                ->body('No: ' . $record->piutang_number . ' | Jurnal otomatis: DR 1103, CR ' . $creditAccount)
                ->success()
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error membuat jurnal')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
