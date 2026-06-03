<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\NewsResource\Pages;
use App\Models\News;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class NewsResource extends Resource
{
    protected static ?string $model = News::class;

    protected static ?string $navigationIcon = 'heroicon-o-newspaper';
    
    protected static ?string $navigationLabel = 'Berita';
    
    protected static ?string $modelLabel = 'Berita';
    
    protected static ?string $navigationGroup = '📰 Konten';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Berita')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, $set) => $set('slug', Str::slug($state))),
                        
                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\Select::make('category')
                            ->label('Kategori')
                            ->options([
                                'umum' => 'Umum',
                                'pembangunan' => 'Pembangunan',
                                'ekonomi' => 'Ekonomi',
                                'sosial' => 'Sosial',
                                'budaya' => 'Budaya',
                            ])
                            ->default('umum')
                            ->required(),
                        
                        Forms\Components\TextInput::make('author')
                            ->label('Penulis')
                            ->maxLength(255),
                    ])->columns(2),
                
                Forms\Components\Section::make('Konten')
                    ->schema([
                        Forms\Components\Textarea::make('excerpt')
                            ->label('Ringkasan')
                            ->rows(3)
                            ->maxLength(500),
                        
                        Forms\Components\RichEditor::make('content')
                            ->label('Konten')
                            ->required()
                            ->columnSpanFull(),
                    ]),
                
                Forms\Components\Section::make('Gambar')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->label('Gambar Utama')
                            ->image()
                            ->directory('news')
                            ->maxSize(5120),
                    ]),
                
                Forms\Components\Section::make('Pengaturan')
                    ->schema([
                        Forms\Components\Toggle::make('is_featured')
                            ->label('Berita Utama')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_published')
                            ->label('Terbitkan')
                            ->default(true),
                        
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Tanggal Terbit')
                            ->default(now()),
                    ])->columns(3),
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
                    ->limit(50),
                
                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'umum' => 'gray',
                        'pembangunan' => 'blue',
                        'ekonomi' => 'green',
                        'sosial' => 'yellow',
                        'budaya' => 'purple',
                    }),
                
                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Utama')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Terbit')
                    ->boolean(),
                
                Tables\Columns\TextColumn::make('views')
                    ->label('Views')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options([
                        'umum' => 'Umum',
                        'pembangunan' => 'Pembangunan',
                        'ekonomi' => 'Ekonomi',
                        'sosial' => 'Sosial',
                        'budaya' => 'Budaya',
                    ]),
                Tables\Filters\TernaryFilter::make('is_published')
                    ->label('Terbitkan'),
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
            'index' => Pages\ListNews::route('/'),
            'create' => Pages\CreateNews::route('/create'),
            'edit' => Pages\EditNews::route('/{record}/edit'),
        ];
    }
}
