<?php

namespace App\Filament\Admin\Resources\PiutangResource\Pages;

use App\Filament\Admin\Resources\PiutangResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPiutangs extends ListRecords
{
    protected static string $resource = PiutangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
