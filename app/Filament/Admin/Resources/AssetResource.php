<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AssetResource\Pages;
use App\Models\Asset;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = '💰 Keuangan';
    protected static ?string $navigationLabel = 'Aset Tetap';
    protected static ?string $pluralModelLabel = 'Daftar Aset';
    protected static ?string $modelLabel = 'Aset';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Aset')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('asset_code')
                                ->label('Kode Aset')
                                ->required()
                                ->unique(ignoreRecord: true),
                            Forms\Components\TextInput::make('name')
                                ->label('Nama Aset')
                                ->required(),
                        ])->columns(2),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Select::make('category')
                                ->label('Kategori')
                                ->options([
                                    'tanah' => 'Tanah',
                                    'bangunan' => 'Bangunan',
                                    'peralatan' => 'Peralatan',
                                    'kendaraan' => 'Kendaraan',
                                    'inventaris' => 'Inventaris',
                                    'lainnya' => 'Lainnya',
                                ])
                                ->required(),
                            Forms\Components\Select::make('business_unit_id')
                                ->label('Unit')
                                ->options(fn () => \App\Models\BusinessUnit::pluck('name', 'id'))
                                ->required(),
                        ])->columns(2),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\DatePicker::make('purchase_date')
                                ->label('Tanggal Beli')
                                ->required(),
                            Forms\Components\TextInput::make('purchase_price')
                                ->label('Harga Beli (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->required(),
                        ])->columns(2),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('useful_life_months')
                                ->label('Umur Ekonomis (bulan)')
                                ->numeric()
                                ->required()
                                ->default(60),
                            Forms\Components\TextInput::make('salvage_value')
                                ->label('Nilai Sisa (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->default(0),
                        ])->columns(2),
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'disposed' => 'Dijual/Dibuang',
                                'fully_depreciated' => 'Fully Depreciated',
                            ])
                            ->default('active'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('asset_code')
                    ->label('Kode')
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge(),
                Tables\Columns\TextColumn::make('purchase_price')
                    ->label('Harga Beli')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('accumulated_depreciation')
                    ->label('Akum. Penyusutan')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('current_book_value')
                    ->label('Nilai Buku')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'disposed' => 'danger',
                        'fully_depreciated' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}
