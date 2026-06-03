<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FinancialTransactionResource\Pages;
use App\Models\FinancialTransaction;
use App\Models\BusinessUnit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class FinancialTransactionResource extends Resource
{
    protected static ?string $model = FinancialTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-path-rounded-square';
    
    protected static ?string $navigationLabel = 'Transaksi';
    
    protected static ?string $modelLabel = 'Transaksi Keuangan';
    
    protected static ?string $navigationGroup = 'Keuangan';
    
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Transaksi')
                    ->schema([
                        Forms\Components\DatePicker::make('transaction_date')
                            ->label('Tanggal')
                            ->required()
                            ->default(now()),
                        
                        Forms\Components\Select::make('type')
                            ->label('Tipe')
                            ->options([
                                'pemasukan' => 'Pemasukan',
                                'pengeluaran' => 'Pengeluaran',
                            ])
                            ->required()
                            ->live(),
                        
                        Forms\Components\TextInput::make('transaction_number')
                            ->label('No. Transaksi')
                            ->disabled()
                            ->dehydrated(false),
                    ])->columns(3),
                
                Forms\Components\Section::make('Unit Usaha')
                    ->schema([
                        Forms\Components\Select::make('business_unit_id')
                            ->label('Unit Usaha')
                            ->options(BusinessUnit::active()->pluck('name', 'id'))
                            ->required()
                            ->default(1) // Default ke Induk
                            ->searchable()
                            ->preload(),
                    ]),
                
                Forms\Components\Section::make('Akun')
                    ->schema([
                        Forms\Components\Select::make('account_id')
                            ->label('Akun')
                            ->relationship('account', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->code} - {$record->name}"),
                        
                        Forms\Components\Select::make('counter_account_id')
                            ->label('Akun Lawan')
                            ->relationship('counterAccount', 'name')
                            ->searchable()
                            ->preload()
                            ->nullable()
                            ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->code} - {$record->name}"),
                    ])->columns(2),
                
                Forms\Components\Section::make('Detail')
                    ->schema([
                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(1),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Keterangan')
                            ->required()
                            ->rows(3),
                        
                        Forms\Components\TextInput::make('reference')
                            ->label('Referensi')
                            ->maxLength(100),
                    ]),
                
                Forms\Components\Section::make('Pengaturan')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => 'Draft',
                                'pending' => 'Pending',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                            ])
                            ->default('draft')
                            ->required(),
                        
                        Forms\Components\FileUpload::make('attachment')
                            ->label('Lampiran')
                            ->directory('transactions')
                            ->maxSize(5120),
                        
                        Forms\Components\FileUpload::make('attachments')
                            ->label('Bukti Nota/Struk')
                            ->multiple()
                            ->maxFiles(5)
                            ->image()
                            ->directory('transactions/attachments'),
                        
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(2),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_number')
                    ->label('No.')
                    ->searchable()
                    ->limit(15),
                
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('businessUnit.name')
                    ->label('Unit')
                    ->badge()
                    ->color(fn (?string $state): string => match ($state) {
                        'BUMDes Induk (Pusat)' => 'warning',
                        default => 'success',
                    }),
                
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pemasukan' => 'success',
                        'pengeluaran' => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('account.name')
                    ->label('Akun')
                    ->limit(25),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'pending' => 'warning',
                        'approved' => 'success',
                        'rejected' => 'danger',
                    }),
                
                Tables\Columns\TextColumn::make('creator.name')
                    ->label('Oleh'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('business_unit_id')
                    ->label('Unit Usaha')
                    ->options(BusinessUnit::pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'pemasukan' => 'Pemasukan',
                        'pengeluaran' => 'Pengeluaran',
                    ]),
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'pending' => 'Pending',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                    ]),
                Tables\Filters\Filter::make('transaction_date')
                    ->form([
                        Forms\Components\DatePicker::make('date_from')->label('Dari'),
                        Forms\Components\DatePicker::make('date_until')->label('Sampai'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['date_from'], fn ($query, $date) => $query->where('transaction_date', '>=', $date))
                            ->when($data['date_until'], fn ($query, $date) => $query->where('transaction_date', '<=', $date));
                    }),
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
            'index' => Pages\ListFinancialTransactions::route('/'),
            'create' => Pages\CreateFinancialTransaction::route('/create'),
            'edit' => Pages\EditFinancialTransaction::route('/{record}/edit'),
        ];
    }
}
