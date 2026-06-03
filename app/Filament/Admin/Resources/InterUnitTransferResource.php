<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\InterUnitTransferResource\Pages;
use App\Models\InterUnitTransfer;
use App\Models\BusinessUnit;
use App\Models\JournalEntry;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;

class InterUnitTransferResource extends Resource
{
    protected static ?string $model = InterUnitTransfer::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrow-right-left';
    protected static ?string $navigationGroup = '💰 Keuangan';
    protected static ?string $navigationLabel = 'Transfer Antar Unit';
    protected static ?string $pluralModelLabel = 'Transfer Antar Unit';
    protected static ?string $modelLabel = 'Transfer';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transfer Antar Unit (RAK)')
                    ->description('Dana dipindahkan antar unit. Sistem otomatis membuat jurnal RAK.')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\DatePicker::make('created_at')
                                ->label('Tanggal')
                                ->default(now())
                                ->required(),
                            Forms\Components\TextInput::make('amount')
                                ->label('Jumlah (Rp)')
                                ->numeric()
                                ->prefix('Rp')
                                ->required()
                                ->minValue(1),
                        ])->columns(2),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\Select::make('from_unit_id')
                                ->label('Dari Unit')
                                ->options(fn () => BusinessUnit::pluck('name', 'id'))
                                ->required(),
                            Forms\Components\Select::make('to_unit_id')
                                ->label('Ke Unit')
                                ->options(fn () => BusinessUnit::pluck('name', 'id'))
                                ->required(),
                        ])->columns(2),
                        Forms\Components\TextInput::make('description')
                            ->label('Keterangan')
                            ->required()
                            ->maxLength(255),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fromUnit.name')
                    ->label('Dari'),
                Tables\Columns\TextColumn::make('toUnit.name')
                    ->label('Ke'),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR'),
                Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(30),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'confirmed' => 'success',
                    }),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('confirm')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (InterUnitTransfer $record) => $record->isPending())
                    ->action(function (InterUnitTransfer $record): void {
                        DB::transaction(function () use ($record) {
                            // Create journal entries for RAK
                            $date = $record->created_at->format('Y-m-d');
                            $year = $record->created_at->year;
                            $desc = "Transfer dari {$record->fromUnit->name} ke {$record->toUnit->name}";

                            // Entry 1: Sender (DEBIT RAK, CREDIT Kas)
                            $senderEntry = JournalEntry::create([
                                'transaction_id' => null,
                                'entry_date' => $date,
                                'entry_type' => 'normal',
                                'account_code' => '1101', // Kas
                                'debit' => 0,
                                'credit' => $record->amount,
                                'unit_id' => $record->from_unit_id,
                                'description' => $desc,
                                'fiscal_year' => $year,
                            ]);

                            // Entry 2: Receiver (DEBIT Kas, CREDIT RAK)
                            $receiverEntry = JournalEntry::create([
                                'transaction_id' => null,
                                'entry_date' => $date,
                                'entry_type' => 'normal',
                                'account_code' => '1101', // Kas
                                'debit' => $record->amount,
                                'credit' => 0,
                                'unit_id' => $record->to_unit_id,
                                'description' => $desc,
                                'fiscal_year' => $year,
                            ]);

                            $record->update([
                                'status' => 'confirmed',
                                'confirmed_by' => auth()->id(),
                                'confirmed_at' => now(),
                                'journal_entry_sender_id' => $senderEntry->id,
                                'journal_entry_receiver_id' => $receiverEntry->id,
                            ]);
                        });

                        Notification::make()
                            ->title('✅ Transfer Dikonfirmasi!')
                            ->body('Jurnal RAK telah dibuat.')
                            ->success()
                            ->send();
                    })
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Transfer')
                    ->modalDescription('Membuat jurnal RAK untuk transfer ini.'),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInterUnitTransfers::route('/'),
            'create' => Pages\CreateInterUnitTransfer::route('/create'),
            'view' => Pages\ViewInterUnitTransfer::route('/{record}'),
        ];
    }
}
