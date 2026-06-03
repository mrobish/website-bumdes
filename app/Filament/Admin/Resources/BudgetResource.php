<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BudgetResource\Pages;
use App\Models\Budget;
use App\Models\Category;
use App\Models\BusinessUnit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BudgetResource extends Resource
{
    protected static ?string $model = Budget::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'Anggaran';

    protected static ?string $modelLabel = 'Anggaran';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Anggaran')
                    ->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Kategori')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->name} ({$record->type})"),

                        Forms\Components\Select::make('unit_id')
                            ->label('Unit Usaha')
                            ->relationship('unit', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable(),

                        Forms\Components\TextInput::make('year')
                            ->label('Tahun')
                            ->required()
                            ->numeric()
                            ->default(now()->year)
                            ->minValue(2020)
                            ->maxValue(2030),

                        Forms\Components\Select::make('month')
                            ->label('Bulan')
                            ->required()
                            ->options([
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                                4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                                10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                            ])
                            ->default(now()->month),
                    ])->columns(2),

                Forms\Components\Section::make('Nominal Anggaran')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah Anggaran')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->default(0)
                            ->minValue(0),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('category.type')
                    ->label('Jenis')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => $state === 'income' ? 'Pendapatan' : 'Pengeluaran')
                    ->color(fn (string $state) => $state === 'income' ? 'success' : 'danger'),

                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Unit')
                    ->searchable()
                    ->default('-'),

                Tables\Columns\TextColumn::make('year')
                    ->label('Tahun')
                    ->sortable(),

                Tables\Columns\TextColumn::make('month')
                    ->label('Bulan')
                    ->formatStateUsing(function ($state) {
                        $months = [
                            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                            5 => 'Mei', 6 => 'Jun', 7 => 'Jul', 8 => 'Agu',
                            9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des',
                        ];
                        return $months[$state] ?? $state;
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Anggaran')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('actual_spent')
                    ->label('Realisasi')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),

                Tables\Columns\TextColumn::make('utilization')
                    ->label('Realisasi %')
                    ->formatStateUsing(fn ($state) => number_format($state, 1) . '%')
                    ->color(fn ($state) => $state > 100 ? 'danger' : ($state >= 80 ? 'warning' : 'success')),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'ok' => '🟢 Baik',
                        'warning' => '🟡 Hati-hati',
                        'over' => '🔴 Melebihi',
                        default => '-',
                    })
                    ->color(fn ($state) => match ($state) {
                        'ok' => 'success',
                        'warning' => 'warning',
                        'over' => 'danger',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->label('Tahun')
                    ->options(range(now()->year - 3, now()->year + 2))
                    ->default(now()->year),

                Tables\Filters\SelectFilter::make('month')
                    ->label('Bulan')
                    ->options([
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                        4 => 'April', 5 => 'Mei', 6 => 'Juni',
                        7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                        10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                    ]),

                Tables\Filters\SelectFilter::make('category.type')
                    ->label('Jenis')
                    ->options([
                        'income' => 'Pendapatan',
                        'expense' => 'Pengeluaran',
                    ]),

                Tables\Filters\SelectFilter::make('unit_id')
                    ->label('Unit')
                    ->options(fn () => BusinessUnit::where('is_active', true)->pluck('name', 'id')),
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
