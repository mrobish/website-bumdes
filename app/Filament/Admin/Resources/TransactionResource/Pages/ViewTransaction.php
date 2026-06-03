<?php

namespace App\Filament\Admin\Resources\TransactionResource\Pages;

use App\Filament\Admin\Resources\TransactionResource;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewTransaction extends ViewRecord
{
    protected static string $resource = TransactionResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Detail Transaksi')
                    ->schema([
                        Infolists\Components\TextEntry::make('transaction_number')
                            ->label('No. Transaksi'),
                        Infolists\Components\TextEntry::make('transaction_date')
                            ->label('Tanggal')
                            ->date('d/m/Y'),
                        Infolists\Components\TextEntry::make('type')
                            ->label('Jenis')
                            ->badge(),
                        Infolists\Components\TextEntry::make('category.name')
                            ->label('Kategori'),
                        Infolists\Components\TextEntry::make('amount')
                            ->label('Jumlah')
                            ->money('IDR'),
                        Infolists\Components\TextEntry::make('description')
                            ->label('Keterangan'),
                        Infolists\Components\TextEntry::make('unit.name')
                            ->label('Unit'),
                        Infolists\Components\TextEntry::make('creator.name')
                            ->label('Dibuat oleh'),
                    ])->columns(2),

                Infolists\Components\Section::make('Jurnal')
                    ->schema([
                        \App\Filament\Infolists\Components\JournalEntryTable::make($this->record),
                    ]),
            ]);
    }
}
