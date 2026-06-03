<?php

namespace App\Filament\Admin\Resources\VillageInfoResource\Pages;

use App\Filament\Admin\Resources\VillageInfoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditVillageInfo extends EditRecord
{
    protected static string $resource = VillageInfoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
