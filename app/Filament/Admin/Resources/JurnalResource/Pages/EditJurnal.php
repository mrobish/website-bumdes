<?php

namespace App\Filament\Admin\Resources\JurnalResource\Pages;

use App\Filament\Admin\Resources\JurnalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJurnal extends EditRecord
{
    protected static string $resource = JurnalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
