<?php

namespace App\Filament\Admin\Resources\InterUnitTransferResource\Pages;

use App\Filament\Admin\Resources\InterUnitTransferResource;
use Filament\Resources\Pages\CreateRecord;

class CreateInterUnitTransfer extends CreateRecord
{
    protected static string $resource = InterUnitTransferResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id() ?? 1;
        $data['status'] = 'pending';
        return $data;
    }
}
