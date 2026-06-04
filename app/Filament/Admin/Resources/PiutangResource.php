<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\PiutangResource\Pages;
use App\Models\Piutang;
use App\Models\Category;
use App\Models\BusinessUnit;
use App\Services\PiutangService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class PiutangResource extends Resource
{
    protected static ?string $model = Piutang::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-currency-dollar';
    protected static ?string $navigationGroup = '💰 Keuangan';
    protected static ?string $navigationLabel = 'Piutang';
    protected static ?string $title = 'Piutang Masyarakat';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data Piutang')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\DatePicker::make('piutang_date')
                            ->label('Tanggal')
                            ->required()
                            ->default(now()),
                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah Piutang (Rp)')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'),
                    ]),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Kategori')
                            ->options(Category::where('type', 'income')->pluck('name', 'id'))
                            ->searchable(),
                        Forms\Components\Select::make('unit_id')
                            ->label('Unit Usaha')
                            ->options(BusinessUnit::pluck('name', 'id'))
                            ->searchable(),
                    ]),
                    Forms\Components\Textarea::make('description')
                        ->label('Keterangan')
                        ->rows(2),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('reference_number')
                            ->label('No. Nota/Kwitansi'),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('Jatuh Tempo'),
                    ]),
                ])->columns(1),

            Forms\Components\Section::make('Data Debitur')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('customer_name')
                            ->label('Nama Lengkap')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('customer_phone')
                            ->label('No. HP')
                            ->tel()
                            ->maxLength(20),
                    ]),
                    Forms\Components\TextInput::make('customer_nik')
                        ->label('NIK (opsional)')
                        ->maxLength(20),
                    Forms\Components\Textarea::make('customer_address')
                        ->label('Alamat')
                        ->rows(2),
                ])->columns(1),

            Forms\Components\Section::make('Lampiran & Kelengkapan Administrasi')
                ->schema([
                    Forms\Components\FileUpload::make('attachment_path')
                        ->label('Foto Nota/Kwitansi')
                        ->image()
                        ->directory('bumdes/piutang')
                        ->maxSize(2048),
                    Forms\Components\FileUpload::make('agreement_letter_path')
                        ->label('Surat Perjanjian (opsional)')
                        ->directory('bumdes/piutang/dokumen')
                        ->maxSize(5120)
                        ->acceptedFileTypes(['application/pdf', 'image/*'])
                        ->helperText('PDF atau foto surat perjanjian'),
                    Forms\Components\FileUpload::make('ktp_photo_path')
                        ->label('Foto KTP Debitur (opsional)')
                        ->image()
                        ->directory('bumdes/piutang/ktp')
                        ->maxSize(2048)
                        ->helperText('Foto KTP untuk verifikasi identitas'),
                    Forms\Components\Textarea::make('notes')
                        ->label('Catatan Internal')
                        ->rows(2),
                ])->collapsible()->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('piutang_number')
                    ->label('No. Piutang')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('piutang_date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('customer_name')
                    ->label('Nama Debitur')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('paid_amount')
                    ->label('Dibayar')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->color('success'),
                Tables\Columns\TextColumn::make('remaining')
                    ->label('Sisa')
                    ->formatStateUsing(fn ($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'belum_lunas' => '🔴 Belum Lunas',
                        'sebagian' => '🟡 Sebagian',
                        'lunas' => '🟢 Lunas',
                        'macet' => '⚫ Macet',
                        default => '-',
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'belum_lunas' => 'danger',
                        'sebagian' => 'warning',
                        'lunas' => 'success',
                        'macet' => 'gray',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('due_date')
                    ->label('Jatuh Tempo')
                    ->date('d/m/Y')
                    ->color(fn ($record) => $record->is_overdue ? 'danger' : 'gray'),
                Tables\Columns\TextColumn::make('unit.name')
                    ->label('Unit')
                    ->sortable(),
                Tables\Columns\IconColumn::make('agreement_letter_path')
                    ->label('Surat Perjanjian')
                    ->boolean()
                    ->trueIcon('heroicon-o-document-check')
                    ->falseIcon('heroicon-o-document-minus')
                    ->trueColor('success')
                    ->falseColor('gray'),
                Tables\Columns\IconColumn::make('ktp_photo_path')
                    ->label('KTP')
                    ->boolean()
                    ->trueIcon('heroicon-o-identification')
                    ->falseIcon('heroicon-o-user')
                    ->trueColor('success')
                    ->falseColor('gray'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'belum_lunas' => 'Belum Lunas',
                        'sebagian' => 'Sebagian',
                        'lunas' => 'Lunas',
                        'macet' => 'Macet',
                    ]),
                Tables\Filters\SelectFilter::make('unit_id')
                    ->label('Unit')
                    ->options(BusinessUnit::pluck('name', 'id')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    // Bayar action
                    Tables\Actions\Action::make('bayar')
                        ->label('💰 Bayar')
                        ->icon('heroicon-o-banknotes')
                        ->color('success')
                        ->visible(fn ($record) => $record->status !== 'lunas')
                        ->form([
                            Forms\Components\DatePicker::make('payment_date')
                                ->label('Tanggal Bayar')
                                ->required()
                                ->default(now()),
                            Forms\Components\TextInput::make('amount')
                                ->label('Jumlah Bayar (Rp)')
                                ->required()
                                ->numeric()
                                ->prefix('Rp')
                                ->default(fn ($record) => $record->remaining),
                            Forms\Components\Select::make('payment_method')
                                ->label('Metode Bayar')
                                ->options([
                                    'tunai' => '💵 Tunai',
                                    'transfer' => '🏦 Transfer',
                                    'qris' => '📱 QRIS',
                                ])
                                ->default('tunai')
                                ->required(),
                            Forms\Components\TextInput::make('reference_number')
                                ->label('No. Referensi/Bukti'),
                            Forms\Components\Textarea::make('notes')
                                ->label('Catatan')
                                ->rows(2),
                        ])
                        ->action(function ($record, array $data) {
                            // Validate amount
                            if ($data['amount'] > $record->remaining) {
                                Notification::make()
                                    ->title('Jumlah bayar melebih sisa piutang!')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            PiutangService::recordPayment($record, $data);

                            Notification::make()
                                ->title('Pembayaran berhasil dicatat!')
                                ->body('Sisa piutang: Rp ' . number_format($record->fresh()->remaining, 0, ',', '.'))
                                ->success()
                                ->send();
                        }),
                    // Void action
                    Tables\Actions\Action::make('void')
                        ->label('🚫 Void')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->visible(fn ($record) => $record->paid_amount == 0)
                        ->action(function ($record) {
                            // Reverse journal entries
                            if ($record->transaction) {
                                \App\Services\AutoJournalService::recordVoid($record->transaction);
                                $record->transaction->update(['is_void' => true, 'voided_by' => auth()->id(), 'voided_at' => now()]);
                            }
                            $record->update(['status' => 'macet']);
                            Notification::make()
                                ->title('Piutang di-void & jurnal dibalik')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('piutang_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPiutangs::route('/'),
            'create' => Pages\CreatePiutang::route('/create'),
            'edit' => Pages\EditPiutang::route('/{record}/edit'),
        ];
    }
}
