<?php

namespace App\Filament\Admin\Resources\InterUnitTransferResource\Pages;

use App\Filament\Admin\Resources\InterUnitTransferResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInterUnitTransfers extends ListRecords
{
    protected static string $resource = InterUnitTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [Actions\CreateAction::make()];
    }
}
