<?php

namespace App\Filament\Admin\Resources\BumdesStructureResource\Pages;

use App\Filament\Admin\Resources\BumdesStructureResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBumdesStructure extends EditRecord
{
    protected static string $resource = BumdesStructureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
