<?php

namespace App\Filament\Admin\Resources\HutangResource\Pages;

use App\Filament\Admin\Resources\HutangResource;
use Filament\Resources\Pages\EditRecord;
use Filament\Actions;

class EditHutang extends EditRecord
{
    protected static string $resource = HutangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
