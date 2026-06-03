<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BusinessUnitResource\Pages;
use App\Models\BusinessUnit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BusinessUnitResource extends Resource
{
    protected static ?string $model = BusinessUnit::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Unit Usaha';
    protected static ?string $modelLabel = 'Unit Usaha';
    protected static ?string $pluralModelLabel = 'Unit Usaha';
    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Unit Usaha')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Unit')
                            ->required()
                            ->maxLength(10)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Contoh: INDUK, PANG, WIS, SAM, NET'),
                        
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Unit')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: BUMDes Induk, Unit Ketahanan Pangan'),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3),
                        
                        Forms\Components\Select::make('type')
                            ->label('Tipe')
                            ->options([
                                'induk' => 'Induk (Pusat)',
                                'unit_usaha' => 'Unit Usaha (Profit Center)',
                            ])
                            ->required()
                            ->default('unit_usaha'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Unit')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'induk' => 'Induk (Pusat)',
                        'unit_usaha' => 'Unit Usaha',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'induk' => 'warning',
                        'unit_usaha' => 'success',
                    }),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('transactions_count')
                    ->counts('transactions')
                    ->label('Transaksi')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'induk' => 'Induk',
                        'unit_usaha' => 'Unit Usaha',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBusinessUnits::route('/'),
            'create' => Pages\CreateBusinessUnit::route('/create'),
            'edit' => Pages\EditBusinessUnit::route('/{record}/edit'),
        ];
    }
}
