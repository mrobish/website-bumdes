<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\ConsolidatedReportResource\Pages;
use App\Models\ConsolidatedReport;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ConsolidatedReportResource extends Resource
{
    protected static ?string $model = ConsolidatedReport::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Laporan Konsolidasi';
    protected static ?string $modelLabel = 'Laporan Konsolidasi';
    protected static ?string $pluralModelLabel = 'Laporan Konsolidasi';
    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Laporan')
                    ->schema([
                        Forms\Components\TextInput::make('report_number')
                            ->label('No. Laporan')
                            ->disabled()
                            ->dehydrated(false),
                        
                        Forms\Components\Select::make('period')
                            ->label('Periode')
                            ->options(fn () => collect(range(1, 12))
                                ->mapWithKeys(fn ($m) => [
                                    now()->year . '-' . str_pad($m, 2, '0', STR_PAD_LEFT) => now()->year . '-' . str_pad($m, 2, '0', STR_PAD_LEFT),
                                ]))
                            ->required(),
                        
                        Forms\Components\Select::make('period_type')
                            ->label('Tipe Periode')
                            ->options([
                                'monthly' => 'Bulanan',
                                'quarterly' => 'Triwulan',
                                'yearly' => 'Tahunan',
                            ])
                            ->required()
                            ->default('monthly'),
                        
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'final' => 'Final',
                                'archived' => 'Arsip',
                            ])
                            ->default('draft')
                            ->disabled(),
                    ])->columns(2),
                
                Forms\Components\Section::make('Ringkasan Keuangan')
                    ->schema([
                        Forms\Components\TextInput::make('total_assets')
                            ->label('Total Aset')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('total_liabilities')
                            ->label('Total Kewajiban')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('total_equity')
                            ->label('Total Ekuitas')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('total_revenue')
                            ->label('Total Pendapatan')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('total_expenses')
                            ->label('Total Beban')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('net_profit')
                            ->label('Laba Bersih')
                            ->numeric()
                            ->prefix('Rp')
                            ->disabled(),
                    ])->columns(3),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('report_number')
                    ->label('No. Laporan')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('period')
                    ->label('Periode')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('period_type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'monthly' => 'Bulanan',
                        'quarterly' => 'Triwulan',
                        'yearly' => 'Tahunan',
                    }),
                
                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('Pendapatan')
                    ->money('IDR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('total_expenses')
                    ->label('Beban')
                    ->money('IDR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('net_profit')
                    ->label('Laba Bersih')
                    ->money('IDR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'draft' => 'Draft',
                        'final' => 'Final',
                        'archived' => 'Arsip',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'warning',
                        'final' => 'success',
                        'archived' => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->date('d M Y')
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'final' => 'Final',
                        'archived' => 'Arsip',
                    ]),
                Tables\Filters\SelectFilter::make('period_type')
                    ->options([
                        'monthly' => 'Bulanan',
                        'quarterly' => 'Triwulan',
                        'yearly' => 'Tahunan',
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
            'index' => Pages\ListConsolidatedReports::route('/'),
            'create' => Pages\CreateConsolidatedReport::route('/create'),
            'edit' => Pages\EditConsolidatedReport::route('/{record}/edit'),
        ];
    }
}
