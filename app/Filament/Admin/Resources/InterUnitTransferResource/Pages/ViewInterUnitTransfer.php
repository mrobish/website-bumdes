<?php

namespace App\Filament\Admin\Resources\InterUnitTransferResource\Pages;

use App\Filament\Admin\Resources\InterUnitTransferResource;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewInterUnitTransfer extends ViewRecord
{
    protected static string $resource = InterUnitTransferResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Detail Transfer')
                    ->schema([
                        Infolists\Components\TextEntry::make('created_at')->label('Tanggal')->date('d/m/Y'),
                        Infolists\Components\TextEntry::make('fromUnit.name')->label('Dari Unit'),
                        Infolists\Components\TextEntry::make('toUnit.name')->label('Ke Unit'),
                        Infolists\Components\TextEntry::make('amount')->label('Jumlah')->money('IDR'),
                        Infolists\Components\TextEntry::make('description')->label('Keterangan'),
                        Infolists\Components\TextEntry::make('status')->label('Status')->badge(),
                    ])->columns(2),
            ]);
    }
}
