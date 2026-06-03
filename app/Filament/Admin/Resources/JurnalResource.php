<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\JurnalResource\Pages;
use App\Models\ChartOfAccount;
use App\Models\FinancialTransaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JurnalResource extends Resource
{
    protected static ?string $model = FinancialTransaction::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = '💰 Keuangan';
    protected static ?string $navigationLabel = 'Jurnal Umum';
    protected static ?string $pluralModelLabel = 'Transaksi Jurnal';
    protected static ?string $modelLabel = 'Transaksi';
    protected static ?int $navigationSort = 2;

    public static function shouldRegisterNavigation(): bool
    {
        return false; // Hide from sidebar — use Input Jurnal page instead
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Data Transaksi')
                    ->icon('heroicon-m-document-text')
                    ->columns(3)
                    ->schema([
                        Forms\Components\DatePicker::make('transaction_date')
                            ->label('Tanggal (*)')
                            ->required()
                            ->default(now())
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('transaction_number')
                            ->label('No. Ref')
                            ->placeholder('Otomatis jika kosong')
                            ->maxLength(50)
                            ->columnSpan(1),

                        Forms\Components\Select::make('account_id')
                            ->label('Kode Akun (*)')
                            ->options(fn () => ChartOfAccount::where('is_group', false)->orderBy('code')->pluck('name', 'id')->map(fn($name, $id) => ChartOfAccount::find($id)->code . ' - ' . $name))
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live()
                            ->afterStateUpdated(fn ($state, $set) => $set('account_name', ChartOfAccount::find($state)?->name ?? ''))
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('account_name')
                            ->label('Nama Akun')
                            ->readOnly()
                            ->dehydrated(false)
                            ->columnSpan(1),

                        Forms\Components\Select::make('type')
                            ->label('Tipe (*)')
                            ->options([
                                'pemasukan' => '💰 Pemasukan (Debet)',
                                'pengeluaran' => '💸 Pengeluaran (Kredit)',
                            ])
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah (Rp) (*)')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('description')
                            ->label('Keterangan (*)')
                            ->rows(2)
                            ->required()
                            ->maxLength(500)
                            ->columnSpan(3),
                    ]),

                Forms\Components\Section::make('Status')
                    ->icon('heroicon-m-check-circle')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'draft' => '📝 Draft (Belum selesai)',
                                'published' => '✅ Published (Sudah final)',
                            ])
                            ->default('draft')
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Placeholder::make('info')
                            ->label('Info')
                            ->content(fn ($record) => $record?->created_at?->format('d M Y H:i') ?? 'Transaksi baru')
                            ->columnSpan(1),
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

                Tables\Columns\TextColumn::make('transaction_number')
                    ->label('No. Ref')
                    ->sortable(),

                Tables\Columns\TextColumn::make('account.code')
                    ->label('Kode')
                    ->sortable(),

                Tables\Columns\TextColumn::make('account.name')
                    ->label('Akun')
                    ->searchable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pemasukan' => 'success',
                        'pengeluaran' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->alignRight(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->description),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'warning',
                        'published' => 'success',
                        'approved' => 'success',
                        default => 'gray',
                    }),
            ])
            ->defaultSort('transaction_date', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ]),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Tipe')
                    ->options([
                        'pemasukan' => 'Pemasukan',
                        'pengeluaran' => 'Pengeluaran',
                    ]),

                Tables\Filters\Filter::make('bulan')
                    ->label('Bulan')
                    ->form([
                        Forms\Components\Select::make('month')
                            ->label('Bulan')
                            ->options(collect(range(1, 12))->mapWithKeys(fn ($m) => [$m => \Carbon\Carbon::create()->month($m)->translatedFormat('F')]))
                            ->default(now()->month),
                        Forms\Components\Select::make('year')
                            ->label('Tahun')
                            ->options(collect(array_reverse(range(now()->year - 2, now()->year + 1)))
                                ->mapWithKeys(fn ($y) => [$y => $y]))
                            ->default(now()->year),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->whereMonth('transaction_date', $data['month'] ?? now()->month)
                            ->whereYear('transaction_date', $data['year'] ?? now()->year);
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('publish')
                    ->label('Publish')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Publish Transaksi')
                    ->modalDescription('Transaksi ini akan dipublikasikan dan masuk ke laporan keuangan.')
                    ->visible(fn ($record) => $record->status === 'draft')
                    ->action(function ($record) {
                        $record->update(['status' => 'published']);
                        Notification::make()
                            ->title('Transaksi dipublikasikan')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),

                    Tables\Actions\BulkAction::make('publishSelected')
                        ->label('Publish Terpilih')
                        ->icon('heroicon-o-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                if ($record->status === 'draft') {
                                    $record->update(['status' => 'published']);
                                    $count++;
                                }
                            }
                            Notification::make()
                                ->title("{$count} transaksi dipublikasikan")
                                ->success()
                                ->send();
                        })
                        ->deselectRecordsAfterCompletion(),
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
            'index' => Pages\ListJurnals::route('/'),
            'create' => Pages\CreateJurnal::route('/create'),
            'edit' => Pages\EditJurnal::route('/{record}/edit'),
        ];
    }
}
