<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\FinancialReportTemplateResource\Pages;
use App\Models\FinancialReportTemplate;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class FinancialReportTemplateResource extends Resource
{
    protected static ?string $model = FinancialReportTemplate::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Template Laporan';
    protected static ?string $modelLabel = 'Template Laporan';
    protected static ?string $pluralModelLabel = 'Template Laporan';
    protected static ?int $navigationSort = 8;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Template')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('Nama Template')
                            ->required()
                            ->maxLength(255),
                        
                        Forms\Components\TextInput::make('code')
                            ->label('Kode')
                            ->required()
                            ->maxLength(20)
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\Select::make('type')
                            ->label('Tipe Laporan')
                            ->options([
                                'balance_sheet' => 'Neraca (Laporan Posisi Keuangan)',
                                'income_statement' => 'Laporan Laba/Rugi',
                                'cash_flow' => 'Laporan Arus Kas',
                                'notes' => 'Catatan Atas Laporan Keuangan (CALK)',
                            ])
                            ->required(),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->rows(3),
                        
                        Forms\Components\Toggle::make('is_default')
                            ->label('Template Default')
                            ->default(false),
                        
                        Forms\Components\Toggle::make('is_active')
                            ->label('Aktif')
                            ->default(true),
                    ])->columns(2),
                
                Forms\Components\Section::make('Struktur Template (JSON)')
                    ->schema([
                        Forms\Components\Textarea::make('structure')
                            ->label('Struktur')
                            ->rows(15)
                            ->helperText('Format JSON: {"rows": [...], "columns": [...]}'),
                        
                        Forms\Components\Textarea::make('settings')
                            ->label('Pengaturan Tambahan')
                            ->rows(5)
                            ->helperText('Format JSON (opsional)'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label('Kode')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama Template')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'balance_sheet' => 'Neraca',
                        'income_statement' => 'Laba/Rugi',
                        'cash_flow' => 'Arus Kas',
                        'notes' => 'CALK',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'balance_sheet' => 'info',
                        'income_statement' => 'success',
                        'cash_flow' => 'warning',
                        'notes' => 'gray',
                    }),
                
                Tables\Columns\IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),
                
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'balance_sheet' => 'Neraca',
                        'income_statement' => 'Laba/Rugi',
                        'cash_flow' => 'Arus Kas',
                        'notes' => 'CALK',
                    ]),
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

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListFinancialReportTemplates::route('/'),
            'create' => Pages\CreateFinancialReportTemplate::route('/create'),
            'edit' => Pages\EditFinancialReportTemplate::route('/{record}/edit'),
        ];
    }
}
