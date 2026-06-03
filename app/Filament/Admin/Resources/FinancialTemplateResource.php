<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FinancialTemplateResource\Pages;
use App\Models\FinancialTemplate;
use App\Services\FinancialTemplateService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Storage;

class FinancialTemplateResource extends Resource
{
    protected static ?string $model = FinancialTemplate::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-arrow-up';

    protected static ?string $navigationGroup = '📊 Laporan';

    protected static ?string $navigationLabel = 'Template Laporan';

    protected static ?string $pluralModelLabel = 'Template Laporan';

    protected static ?string $modelLabel = 'Template';

    protected static ?int $navigationSort = 7;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Template')
                    ->icon('heroicon-m-information-circle')
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Template')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Forms\Components\TextInput::make('slug')
                            ->label('Slug')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->columnSpan(1),

                        Forms\Components\Select::make('type')
                            ->label('Jenis Laporan')
                            ->options([
                                'jurnal' => 'Jurnal Umum',
                                'buku_besar' => 'Buku Besar',
                                'laba_rugi' => 'Laba/Rugi',
                                'neraca' => 'Neraca',
                                'arus_kas' => 'Arus Kas',
                                'modal' => 'Perubahan Modal',
                                'realisasi_anggaran' => 'Realisasi Anggaran',
                                'cat' => 'CAT',
                            ])
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('frequency')
                            ->label('Frekuensi')
                            ->options([
                                'harian' => 'Harian',
                                'bulanan' => 'Bulanan',
                                'tahunan' => 'Tahunan',
                                'sekali' => 'Sekali',
                            ])
                            ->required()
                            ->columnSpan(1),

                        Forms\Components\Select::make('sort_order')
                            ->label('Urutan')
                            ->options(range(1, 20))
                            ->default(1)
                            ->columnSpan(1),

                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(2)
                            ->columnSpan(2),
                    ]),

                Forms\Components\Section::make('Status')
                    ->icon('heroicon-m-check-circle')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Template')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Jenis')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'jurnal' => 'info',
                        'buku_besar' => 'info',
                        'laba_rugi' => 'warning',
                        'neraca' => 'success',
                        'arus_kas' => 'primary',
                        'modal' => 'gray',
                        'realisasi_anggaran' => 'danger',
                        'cat' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('frequency')
                    ->label('Frekuensi')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'harian' => 'danger',
                        'bulanan' => 'warning',
                        'tahunan' => 'success',
                        'sekali' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                Tables\Columns\TextColumn::make('sort_order')
                    ->label('Urutan')
                    ->sortable()
                    ->alignCenter(),
            ])
            ->actions([
                Tables\Actions\Action::make('download')
                    ->label('Download')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(function ($record) {
                        $service = app(FinancialTemplateService::class);
                        $filePath = $service->generateTemplate($record);

                        return response()->download(
                            storage_path('app/' . $filePath),
                            $record->slug . '-template.xlsx'
                        );
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
            'index' => Pages\ListFinancialTemplates::route('/'),
            'create' => Pages\CreateFinancialTemplate::route('/create'),
            'edit' => Pages\EditFinancialTemplate::route('/{record}/edit'),
        ];
    }
}
