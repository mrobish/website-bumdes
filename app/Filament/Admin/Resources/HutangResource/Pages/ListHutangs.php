<?php

namespace App\Filament\Admin\Resources\HutangResource\Pages;

use App\Filament\Admin\Resources\HutangResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListHutangs extends ListRecords
{
    protected static string $resource = HutangResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
