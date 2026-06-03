<?php

namespace App\Filament\Admin\Resources\InterAccountTransferResource\Pages;

use App\Filament\Admin\Resources\InterAccountTransferResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInterAccountTransfers extends ListRecords
{
    protected static string $resource = InterAccountTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
