<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BumdesStructureResource\Pages;
use App\Models\BumdesStructure;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BumdesStructureResource extends Resource
{
    protected static ?string $model = BumdesStructure::class;
    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = '⚙️ Pengaturan';
    protected static ?string $navigationLabel = 'Struktur Kepengurusan';
    protected static ?string $modelLabel = 'Struktur Kepengurusan';
    protected static ?string $pluralModelLabel = 'Struktur Kepengurusan';
    protected static ?int $navigationSort = 2;
    protected static ?string $slug = 'struktur-kepengurusan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Jabatan')
                    ->schema([
                        Forms\Components\TextInput::make('position')
                            ->label('Nama Jabatan')
                            ->required()
                            ->maxLength(255)
                            ->placeholder('Contoh: Direktur, Sekretaris, Bendahara'),
                        
                        Forms\Components\Select::make('position_group')
                            ->label('Kelompok Jabatan')
                            ->options([
                                'pengurus' => 'Pengurus BUMDes',
                                'unit' => 'Pengelola Unit Usaha',
                                'pengawas' => 'Pengawas',
                                'lainnya' => 'Lainnya',
                            ])
                            ->required()
                            ->default('pengurus'),
                        
                        Forms\Components\Select::make('business_unit_id')
                            ->label('Unit Usaha (Jika Terkait)')
                            ->relationship('businessUnit', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->helperText('Pilih unit usaha jika jabatan ini terkait dengan unit tertentu'),
                        
                        Forms\Components\TextInput::make('sort_order')
                            ->label('Urutan Tampil')
                            ->numeric()
                            ->default(0),
                    ])->columns(2),
                
                Forms\Components\Section::make('Data Pejabat')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('nip')
                            ->label('NIP (Jika Ada)')
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('phone')
                            ->label('Nomor HP')
                            ->tel(),
                        
                        Forms\Components\TextInput::make('email')
                            ->label('Email')
                            ->email(),
                        
                        Forms\Components\FileUpload::make('photo_path')
                            ->label('Foto')
                            ->image()
                            ->directory('bumdes/struktur')
                            ->maxSize(2048)
                            ->helperText('Format: JPG/PNG, Ukuran: max 2MB'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Periode & Status')
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->label('Mulai Menjabat'),
                        
                        Forms\Components\DatePicker::make('end_date')
                            ->label('Akhir Menjabat'),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        
                        Forms\Components\Toggle::make('is_village_head')
                            ->label('Kepala Desa (Otomatis Penasehat)')
                            ->default(false)
                            ->helperText('Aktifkan jika pejabat ini adalah Kepala Desa'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Catatan')
                    ->schema([
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(3),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('position')
                    ->label('Jabatan')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('position_group')
                    ->label('Kelompok')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pengurus' => 'Pengurus',
                        'unit' => 'Unit Usaha',
                        'pengawas' => 'Pengawas',
                        'lainnya' => 'Lainnya',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pengurus' => 'primary',
                        'unit' => 'success',
                        'pengawas' => 'warning',
                        'lainnya' => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('businessUnit.name')
                    ->label('Unit Usaha')
                    ->searchable(),
                
                Tables\Columns\IconColumn::make('is_village_head')
                    ->label('Kades')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('position_group')
                    ->options([
                        'pengurus' => 'Pengurus',
                        'unit' => 'Unit Usaha',
                        'pengawas' => 'Pengawas',
                        'lainnya' => 'Lainnya',
                    ]),
                Tables\Filters\SelectFilter::make('is_active')
                    ->options([
                        true => 'Aktif',
                        false => 'Non-Aktif',
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
            ])
            ->defaultSort('sort_order');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBumdesStructures::route('/'),
            'create' => Pages\CreateBumdesStructure::route('/create'),
            'edit' => Pages\EditBumdesStructure::route('/{record}/edit'),
        ];
    }
}
