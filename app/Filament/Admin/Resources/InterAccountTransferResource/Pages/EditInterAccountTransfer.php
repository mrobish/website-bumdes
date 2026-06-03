<?php

namespace App\Filament\Admin\Resources\InterAccountTransferResource\Pages;

use App\Filament\Admin\Resources\InterAccountTransferResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInterAccountTransfer extends EditRecord
{
    protected static string $resource = InterAccountTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
