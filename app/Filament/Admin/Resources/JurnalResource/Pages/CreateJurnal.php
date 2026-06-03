<?php

namespace App\Filament\Admin\Resources\JurnalResource\Pages;

use App\Filament\Admin\Resources\JurnalResource;
use Filament\Resources\Pages\CreateRecord;

class CreateJurnal extends CreateRecord
{
    protected static string $resource = JurnalResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Auto-generate transaction number if empty
        if (empty($data['transaction_number'])) {
            $date = $data['transaction_date'] instanceof \Carbon\Carbon
                ? $data['transaction_date']
                : \Carbon\Carbon::parse($data['transaction_date']);
            $prefix = 'J-' . $date->format('Ym');
            $lastNum = \App\Models\FinancialTransaction::where('transaction_number', 'like', $prefix . '%')->count();
            $data['transaction_number'] = $prefix . '-' . str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
        }

        // Set created_by
        $data['created_by'] = Auth::id() ?? 1;

        return $data;
    }
}
