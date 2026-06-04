<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AccountResource\Pages;
use App\Models\Account;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AccountResource extends Resource
{
    protected static ?string $model = Account::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationGroup = '⚙️ Master Data';
    protected static ?string $navigationLabel = 'Akun (COA)';
    protected static ?string $pluralModelLabel = 'Daftar Akun';
    protected static ?string $modelLabel = 'Akun';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Akun')
                    ->schema([
                        Forms\Components\TextInput::make('code')
                            ->label('Kode Akun')
                            ->required()
                            ->maxLength(10)
                            ->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Akun')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('type')
                            ->label('Jenis')
                            ->options([
                                'asset' => 'Aset',
                                'liability' => 'Kewajiban',
                                'equity' => 'Ekuitas',
                                'revenue' => 'Pendapatan',
                                'expense' => 'Beban',
                            ])
                            ->required(),
                        Forms\Components\Select::make('normal_balance')
                            ->label('Normal Balance')
                            ->options([
                                'debit' => 'Debet',
                                'credit' => 'Kredit',
                            ])
                            ->required(),
                        Forms\Components\Toggle::make('is_system')
                            ->label('Akun Sistem')
                            ->disabled()
                            ->default(false),
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query
                ->withSum('journalEntries', 'debit')
                ->withSum('journalEntries', 'credit')
            )
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'asset' => 'success',
                        'liability' => 'danger',
                        'equity' => 'info',
                        'revenue' => 'warning',
                        'expense' => 'gray',
                    }),
                Tables\Columns\TextColumn::make('normal_balance')
                    ->label('Normal'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->defaultSort('code')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Jenis')
                    ->options([
                        'asset' => 'Aset',
                        'liability' => 'Kewajiban',
                        'equity' => 'Ekuitas',
                        'revenue' => 'Pendapatan',
                        'expense' => 'Beban',
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAccounts::route('/'),
            'create' => Pages\CreateAccount::route('/create'),
            'edit' => Pages\EditAccount::route('/{record}/edit'),
        ];
    }
}
