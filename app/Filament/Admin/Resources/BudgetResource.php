<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BudgetResource\Pages;
use App\Models\Budget;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static ?string $navigationIcon = 'heroicon-o-calculator';
    
    protected static ?string $navigationLabel = 'Anggaran';
    
    protected static ?string $modelLabel = 'Anggaran';
    
    protected static ?string $navigationGroup = 'Keuangan';
    
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Anggaran')
                    ->schema([
                        Forms\Components\Select::make('account_id')
                            ->label('Akun')
                            ->relationship('account', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->code} - {$record->name}"),
                        
                        Forms\Components\TextInput::make('year')
                            ->label('Tahun')
                            ->required()
                            ->numeric()
                            ->default(now()->year)
                            ->minValue(2020)
                            ->maxValue(2030),
                    ])->columns(2),
                
                Forms\Components\Section::make('Anggaran')
                    ->schema([
                        Forms\Components\TextInput::make('planned_amount')
                            ->label('Rencana Anggaran')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                        
                        Forms\Components\TextInput::make('actual_amount')
                            ->label('Realisasi')
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0),
                        
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
                Tables\Columns\TextColumn::make('account.code')
                    ->label('Kode')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('account.name')
                    ->label('Akun')
                    ->searchable()
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('year')
                    ->label('Tahun')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('planned_amount')
                    ->label('Rencana')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('actual_amount')
                    ->label('Realisasi')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('realization_percent')
                    ->label('Realisasi %')
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->color(fn ($state) => $state > 100 ? 'danger' : ($state > 80 ? 'success' : 'warning')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->options(range(now()->year - 2, now()->year + 2))
                    ->default(now()->year),
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
            'index' => Pages\ListBudgets::route('/'),
            'create' => Pages\CreateBudget::route('/create'),
            'edit' => Pages\EditBudget::route('/{record}/edit'),
        ];
    }
}
