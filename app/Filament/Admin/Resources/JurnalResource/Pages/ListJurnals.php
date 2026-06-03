<?php

namespace App\Filament\Admin\Resources\JurnalResource\Pages;

use App\Filament\Admin\Resources\JurnalResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJurnals extends ListRecords
{
    protected static string $resource = JurnalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Transaksi'),
        ];
    }
}
