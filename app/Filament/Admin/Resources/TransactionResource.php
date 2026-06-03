<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\TransactionResource\Pages;
use App\Models\Transaction;
use App\Models\Category;
use App\Models\FiscalYear;
use App\Services\AutoJournalService;
use App\Services\AuditLogService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    protected static ?string $navigationGroup = '💰 Keuangan';
    protected static ?string $navigationLabel = 'Transaksi';
    protected static ?string $pluralModelLabel = 'Daftar Transaksi';
    protected static ?string $modelLabel = 'Transaksi';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Form Transaksi')
                    ->description('Isi data transaksi. Sistem akan otomatis membuat jurnal double-entry.')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\DatePicker::make('transaction_date')
                                ->label('📅 Tanggal')
                                ->required()
                                ->default(now()),
                            Forms\Components\Select::make('type')
                                ->label('💰 Jenis')
                                ->options([
                                    'income' => '✅ Pemasukan (Uang Masuk)',
                                    'expense' => '❌ Pengeluaran (Uang Keluar)',
                                ])
                                ->required()
                                ->live(),
                        ])->columns(2),

                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Select::make('unit_id')
                                ->label('🏢 Unit')
                                ->options(fn () => \App\Models\BusinessUnit::pluck('name', 'id'))
                                ->required(),
                            Forms\Components\Select::make('category_id')
                                ->label('📋 Kategori')
                                ->options(fn (Forms\Get $get) => Category::active()
                                    ->where('type', $get('type') ?? 'income')
                                    ->pluck('name', 'id'))
                                ->searchable()
                                ->required()
                                ->live(),
                        ])->columns(2),

                        Forms\Components\TextInput::make('amount')
                            ->label('💵 Jumlah (Rp)')
                            ->numeric()
                            ->prefix('Rp')
                            ->required()
                            ->minValue(1),

                        Forms\Components\TextInput::make('description')
                            ->label('📝 Keterangan')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('reference_number')
                            ->label('📄 No. Referensi')
                            ->placeholder('Faktur / Bon / Kwitansi')
                            ->maxLength(50),

                        Forms\Components\FileUpload::make('attachment_path')
                            ->label('📎 Lampiran (Foto Nota/Bon)')
                            ->directory('attachments')
                            ->image()
                            ->maxSize(5120),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'income' => 'success',
                        'expense' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),
                Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(30),
                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Unit')
                    ->badge(),
                Tables\Columns\IconColumn::make('is_void')
                    ->label('Void')
                    ->boolean(),
            ])
            ->defaultSort('transaction_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->label('Jenis')
                    ->options([
                        'income' => 'Pemasukan',
                        'expense' => 'Pengeluaran',
                    ]),
                Tables\Filters\SelectFilter::make('unit_id')
                    ->label('Unit')
                    ->options(fn () => \App\Models\BusinessUnit::pluck('name', 'id')),
                Tables\Filters\Filter::make('bulan')
                    ->form([
                        Forms\Components\Select::make('month')
                            ->label('Bulan')
                            ->options([
                                1 => 'Januari', 2 => 'Februari', 3 => 'Maret',
                                4 => 'April', 5 => 'Mei', 6 => 'Juni',
                                7 => 'Juli', 8 => 'Agustus', 9 => 'September',
                                10 => 'Oktober', 11 => 'November', 12 => 'Desember',
                            ])
                            ->default(now()->month),
                        Forms\Components\Select::make('year')
                            ->label('Tahun')
                            ->options(collect(array_reverse(range(now()->year - 3, now()->year + 1)))->mapWithKeys(fn ($y) => [$y => $y]))
                            ->default(now()->year),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->whereMonth('transaction_date', $data['month'] ?? now()->month)
                            ->whereYear('transaction_date', $data['year'] ?? now()->year);
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
                    ->visible(fn (Transaction $record) => $record->canBeEdited()),
                Tables\Actions\Action::make('void')
                    ->label('Void')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->form([
                        Forms\Components\Textarea::make('reason')
                            ->label('Alasan Void')
                            ->required(),
                    ])
                    ->action(function (Transaction $record, array $data): void {
                        DB::transaction(function () use ($record, $data) {
                            // Create reversing journal entries
                            AutoJournalService::recordVoid($record);
                            
                            // Mark as void
                            $record->update([
                                'is_void' => true,
                                'void_reason' => $data['reason'],
                                'voided_by' => auth()->id(),
                                'voided_at' => now(),
                            ]);

                            // Log audit
                            AuditLogService::logVoid('transactions', $record->id, $record->toArray(), $data['reason']);
                        });

                        Notification::make()
                            ->title('✅ Transaksi di-Void')
                            ->body('Jurnal reversal telah dibuat.')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Void Transaksi')
                    ->modalDescription('Membuat jurnal reversal. Tindakan ini tidak dapat dibatalkan.'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
            'create' => Pages\CreateTransaction::route('/create'),
            'edit' => Pages\EditTransaction::route('/{record}/edit'),
            'view' => Pages\ViewTransaction::route('/{record}'),
        ];
    }
}
