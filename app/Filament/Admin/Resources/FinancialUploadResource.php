<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FinancialUploadResource\Pages;
use App\Models\FinancialTemplate;
use App\Models\FinancialUpload;
use App\Services\FinancialTemplateService;
use App\Services\FinancialImportService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FinancialUploadResource extends Resource
{
    protected static ?string $model = FinancialUpload::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';

    protected static ?string $navigationGroup = '⚙️ Pengaturan';

    protected static ?string $navigationLabel = 'Upload Laporan';

    protected static ?string $pluralModelLabel = 'Upload Laporan';

    protected static ?string $modelLabel = 'Upload';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Upload Laporan')
                    ->icon('heroicon-m-cloud-arrow-up')
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('template_id')
                            ->label('Template')
                            ->relationship('template', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('period')
                            ->label('Periode')
                            ->placeholder('Contoh: 2026-01, 2026-Q1, 2026')
                            ->required()
                            ->maxLength(20)
                            ->columnSpan(1),

                        Forms\Components\FileUpload::make('file_path')
                            ->label('File Laporan')
                            ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'text/csv'])
                            ->maxSize(5120) // 5MB
                            ->directory('financial-uploads')
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(2)
                            ->columnSpan(2),
                    ]),

                Forms\Components\Section::make('Status')
                    ->icon('heroicon-m-check-circle')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Menunggu Validasi',
                                'validated' => 'Sah',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                            ])
                            ->default('pending')
                            ->required(),
                    ]),

                Forms\Components\Section::make('Hasil Import')
                    ->icon('heroicon-m-information-circle')
                    ->visible(fn ($record) => $record !== null)
                    ->schema([
                        Forms\Components\Placeholder::make('import_status')
                            ->label('Status Import')
                            ->content(fn ($record) => match($record->import_status ?? 'pending') {
                                'imported' => '✅ Berhasil diimport',
                                'error' => '❌ Gagal import',
                                default => '⏳ Menunggu import',
                            }),
                        Forms\Components\Placeholder::make('imported_count')
                            ->label('Jumlah Data')
                            ->content(fn ($record) => ($record->imported_count ?? 0) . ' baris'),
                        Forms\Components\Textarea::make('import_error')
                            ->label('Error')
                            ->rows(3)
                            ->readOnly()
                            ->visible(fn ($record) => !empty($record->import_error)),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('template.name')
                    ->label('Template')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('period')
                    ->label('Periode')
                    ->sortable(),

                Tables\Columns\TextColumn::make('file_name')
                    ->label('File')
                    ->limit(30),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'validated' => 'info',
                        'approved' => 'success',
                        'rejected' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('import_status')
                    ->label('Import')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'imported' => 'success',
                        'error' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('imported_count')
                    ->label('Data')
                    ->sortable(),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Diupload oleh')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime('d M Y H:i')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->url(fn ($record) => Storage::url($record->file_path))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('import')
                    ->label('Import')
                    ->icon('heroicon-o-arrow-path')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Import Data')
                    ->modalDescription('Data dari Excel akan diimport ke database. Untuk Jurnal Umum, data masuk ke transaksi keuangan. Untuk laporan lain, data disimpan sebagai referensi.')
                    ->visible(fn ($record) => $record->import_status !== 'imported')
                    ->action(function ($record) {
                        $importService = new FinancialImportService();
                        $result = $importService->import($record);

                        if ($result['success']) {
                            Notification::make()
                                ->title('Import Berhasil')
                                ->body($result['message'])
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('Import Gagal')
                                ->body($result['error'] ?? 'Terjadi kesalahan')
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('validate')
                    ->label('Validasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Validasi Laporan')
                    ->modalDescription('Yakin ingin memvalidasi laporan ini?')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'validated',
                            'validated_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Laporan berhasil divalidasi')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('approve')
                    ->label('Setujui')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Setujui Laporan')
                    ->modalDescription('Yakin ingin menyetujui laporan ini?')
                    ->visible(fn ($record) => $record->status === 'validated')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'approved',
                            'approved_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Laporan berhasil disetujui')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('reject')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Tolak Laporan')
                    ->modalDescription('Yakin ingin menolak laporan ini?')
                    ->visible(fn ($record) => in_array($record->status, ['pending', 'validated']))
                    ->action(function ($record) {
                        $record->update(['status' => 'rejected']);

                        Notification::make()
                            ->title('Laporan ditolak')
                            ->warning()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFinancialUploads::route('/'),
            'create' => Pages\CreateFinancialUpload::route('/create'),
            'edit' => Pages\EditFinancialUpload::route('/{record}/edit'),
        ];
    }
}
