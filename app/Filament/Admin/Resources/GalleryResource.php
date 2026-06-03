<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\GalleryResource\Pages;
use App\Models\Gallery;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class GalleryResource extends Resource
{
    protected static ?string $model = Gallery::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';
    
    protected static ?string $navigationLabel = 'Galeri';
    
    protected static ?string $modelLabel = 'Galeri';
    
    protected static ?string $navigationGroup = '📰 Konten';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Galeri')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3),
                        
                        Forms\Components\Select::make('type')
                            ->label('Tipe')
                            ->options([
                                'foto' => 'Foto',
                                'video' => 'Video',
                            ])
                            ->default('foto')
                            ->required(),
                        
                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->options([
                                'umum' => 'Umum',
                                'kegiatan' => 'Kegiatan',
                                'pembangunan' => 'Pembangunan',
                                'budaya' => 'Budaya',
                                'alam' => 'Alam',
                            ])
                            ->default('umum')
                            ->required(),
                    ])->columns(2),
                
                Forms\Components\Section::make('File')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Gambar/Video')
                            ->image()
                            ->directory('galleries')
                            ->maxSize(10240)
                            ->required(),
                    ]),
                
                Forms\Components\Section::make('Pengaturan')
                    ->schema([
                        Forms\Components\Toggle::make('is_published')
                            ->label('Terbitkan')
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
                Tables\Columns\ImageColumn::make('image')
                    ->label('Gambar')
                    ->disk('public')
                    ->circular(),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(40),
                
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'foto' => 'green',
                        'video' => 'blue',
                    }),
                
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'umum' => 'gray',
                        'kegiatan' => 'blue',
                        'pembangunan' => 'green',
                        'budaya' => 'purple',
                        'alam' => 'yellow',
                    }),
                
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Terbit')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('views')
                    ->label('Views')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'foto' => 'Foto',
                        'video' => 'Video',
                    ]),
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'umum' => 'Umum',
                        'kegiatan' => 'Kegiatan',
                        'pembangunan' => 'Pembangunan',
                        'budaya' => 'Budaya',
                        'alam' => 'Alam',
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

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGalleries::route('/'),
            'create' => Pages\CreateGallery::route('/create'),
            'edit' => Pages\EditGallery::route('/{record}/edit'),
        ];
    }
}
