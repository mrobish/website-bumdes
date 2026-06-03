<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ChartOfAccountResource\Pages;
use App\Models\ChartOfAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ChartOfAccountResource extends Resource
{
    protected static ?string $model = ChartOfAccount::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    
    protected static ?string $navigationLabel = 'COA';
    
    protected static ?string $modelLabel = 'Chart of Accounts';
    
    protected static ?string $navigationGroup = 'Keuangan';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Akun')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Akun')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Akun')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\Select::make('type')
                            ->label('Tipe')
                            ->options([
                                'aset' => 'Aset',
                                'kewajiban' => 'Kewajiban',
                                'modal' => 'Modal',
                                'pendapatan' => 'Pendapatan',
                                'beban' => 'Beban',
                            ])
                            ->required(),
                        
                        Forms\Components\Select::make('parent_id')
                            ->label('Akun Induk')
                            ->relationship('parent', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Detail')
                    ->schema([
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3),
                        
                        Forms\Components\Toggle::make('is_group')
                            ->label('Akun Kelompok')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])->columns(3),
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
                    ->label('Nama')
                    ->searchable()
                    ->limit(40),
                
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aset' => 'blue',
                        'kewajiban' => 'red',
                        'modal' => 'green',
                        'pendapatan' => 'emerald',
                        'beban' => 'orange',
                    }),
                
                Tables\Columns\TextColumn::make('parent.name')
                    ->label('Induk')
                    ->limit(20),
                
                Tables\Columns\IconColumn::make('is_group')
                    ->label('Kelompok')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'aset' => 'Aset',
                        'kewajiban' => 'Kewajiban',
                        'modal' => 'Modal',
                        'pendapatan' => 'Pendapatan',
                        'beban' => 'Beban',
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
            'index' => Pages\ListChartOfAccounts::route('/'),
            'create' => Pages\CreateChartOfAccount::route('/create'),
            'edit' => Pages\EditChartOfAccount::route('/{record}/edit'),
        ];
    }
}
