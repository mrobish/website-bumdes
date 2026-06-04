<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\HutangResource\Pages;
use App\Models\Hutang;
use App\Models\Category;
use App\Models\BusinessUnit;
use App\Services\HutangService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class HutangResource extends Resource
{
    protected static ?string $model = Hutang::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = '💰 Keuangan';
    protected static ?string $navigationLabel = 'Hutang';
    protected static ?string $title = 'Hutang Supplier';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Data Hutang')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\DatePicker::make('hutang_date')
                            ->label('Tanggal')
                            ->required()
                            ->default(now()),
                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah Hutang (Rp)')
                            ->required()
                            ->numeric()
                            ->prefix('Rp'),
                    ]),
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\Select::make('category_id')
                            ->label('Kategori')
                            ->options(Category::where('type', 'expense')->pluck('name', 'id'))
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
                            ->label('No. Invoice/Faktur'),
                        Forms\Components\DatePicker::make('due_date')
                            ->label('Jatuh Tempo'),
                    ]),
                ])->columns(1),

            Forms\Components\Section::make('Data Supplier')
                ->schema([
                    Forms\Components\Grid::make(2)->schema([
                        Forms\Components\TextInput::make('supplier_name')
                            ->label('Nama Supplier')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('supplier_phone')
                            ->label('No. HP')
                            ->tel()
                            ->maxLength(20),
                    ]),
                    Forms\Components\TextInput::make('supplier_npwp')
                        ->label('NPWP (opsional)')
                        ->maxLength(20),
                    Forms\Components\Textarea::make('supplier_address')
                        ->label('Alamat')
                        ->rows(2),
                ])->columns(1),

            Forms\Components\Section::make('Lampiran & Kelengkapan')
                ->schema([
                    Forms\Components\FileUpload::make('attachment_path')
                        ->label('Foto Invoice/Faktur')
                        ->image()
                        ->directory('bumdes/hutang')
                        ->maxSize(2048),
                    Forms\Components\FileUpload::make('agreement_letter_path')
                        ->label('Surat Perjanjian (opsional)')
                        ->directory('bumdes/hutang/dokumen')
                        ->maxSize(5120)
                        ->acceptedFileTypes(['application/pdf', 'image/*']),
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
                Tables\Columns\TextColumn::make('hutang_number')
                    ->label('No. Hutang')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('hutang_date')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('supplier_name')
                    ->label('Supplier')
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
                            if ($data['amount'] > $record->remaining) {
                                Notification::make()
                                    ->title('Jumlah bayar melebih sisa hutang!')
                                    ->danger()
                                    ->send();
                                return;
                            }

                            HutangService::recordPayment($record, $data);

                            Notification::make()
                                ->title('Pembayaran berhasil dicatat!')
                                ->body('Sisa hutang: Rp ' . number_format($record->fresh()->remaining, 0, ',', '.'))
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
                            HutangService::voidHutang($record);
                            Notification::make()
                                ->title('Hutang di-void & jurnal dibalik')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('hutang_date', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHutangs::route('/'),
            'create' => Pages\CreateHutang::route('/create'),
            'edit' => Pages\EditHutang::route('/{record}/edit'),
        ];
    }
}
