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
    protected static ?string $navigationGroup = '🏢 Manajemen';
    protected static ?string $navigationLabel = 'Unit Usaha';
    protected static ?string $modelLabel = 'Unit Usaha';
    protected static ?string $pluralModelLabel = 'Unit Usaha';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Dasar')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Unit')
                            ->required()
                            ->maxLength(10)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Contoh: INDUK, PANG, WIS, SAM, NET'),
                        
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug URL')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Contoh: bumdes-induk, unit-pangan'),
                        
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Unit')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: BUMDes Induk, Unit Ketahanan Pangan'),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi Singkat')
                            ->rows(2),
                        
                        Forms\Components\Select::make('type')
                            ->label('Tipe')
                            ->options([
                                'induk' => 'Induk (Pusat)',
                                'unit_usaha' => 'Unit Usaha (Profit Center)',
                            ])
                            ->required()
                            ->default('unit_usaha'),
                        
                        Forms\Components\TextInput::make('color')
                            ->label('Warna Tema')
                            ->placeholder('Contoh: indigo, green, purple, teal, blue'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Urutan')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
                
                Forms\Components\Section::make('Informasi Kontak')
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->label('Nomor Telepon')
                            ->tel()
                            ->placeholder('Contoh: 628123456789'),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email()
                            ->placeholder('Contoh: info@bumdes.id'),
                        
                        Forms\Components\TextInput::make('manager')
                            ->label('Kepala Unit / Manager')
                            ->placeholder('Nama kepala unit'),
                        
                        Forms\Components\TextInput::make('manager_nip')
                            ->label('NIP Kepala Unit')
                            ->placeholder('NIP / No. Identitas'),
                        
                        Forms\Components\TextInput::make('manager_phone')
                            ->label('HP Kepala Unit')
                            ->placeholder('No. HP'),
                        
                        Forms\Components\TextInput::make('operating_hours')
                            ->label('Jam Operasional')
                            ->placeholder('Contoh: Senin-Jumat 08:00-16:00'),
                        
                        Forms\Components\Textarea::make('address')
                            ->label('Alamat')
                            ->rows(2),
                    ])->columns(2),
                
                Forms\Components\Section::make('Konten')
                    ->schema([
                        Forms\Components\RichEditor::make('about')
                            ->label('Tentang Unit')
                            ->columnSpanFull()
                            ->toolbarButtons([
                                'bold', 'italic', 'underline', 'strike',
                                'link', 'bulletList', 'orderedList',
                                'h2', 'h3', 'blockquote',
                            ]),
                    ]),
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
                
                Tables\Columns\TextColumn::make('manager')
                    ->label('Pengelola')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('phone')
                    ->label('Telepon'),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
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
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        true => 'Aktif',
                        false => 'Non-Aktif',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
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
