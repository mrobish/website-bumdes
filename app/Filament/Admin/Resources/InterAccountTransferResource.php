<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\InterAccountTransferResource\Pages;
use App\Models\InterAccountTransfer;
use App\Models\BusinessUnit;
use App\Models\ChartOfAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class InterAccountTransferResource extends Resource
{
    protected static ?string $model = InterAccountTransfer::class;
    protected static ?string $navigationIcon = 'heroicon-o-arrows-right-left';
    protected static ?string $navigationGroup = '💰 Keuangan';
    protected static ?string $navigationLabel = 'Transfer Antar Unit (RAK)';
    protected static ?string $modelLabel = 'Transfer Antar Unit';
    protected static ?string $pluralModelLabel = 'Transfer Antar Unit';
    protected static ?int $navigationSort = 6;

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Transfer')
                    ->schema([
                        Forms\Components\DatePicker::make('transfer_date')
                            ->label('Tanggal Transfer')
                            ->required()
                            ->default(now()),
                        
                        Forms\Components\Select::make('from_business_unit_id')
                            ->label('Dari Unit')
                            ->options(BusinessUnit::active()->pluck('name', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($set) => $set('from_account_id', null)),
                        
                        Forms\Components\Select::make('from_account_id')
                            ->label('Dari Rekening')
                            ->options(fn ($get) => ChartOfAccount::where('is_group', false)
                                ->where('code', 'like', '11%') // Aset Lancar
                                ->get()->mapWithKeys(fn($a) => [$a->id => $a->code . ' - ' . $a->name]))
                            ->required(),
                        
                        Forms\Components\Select::make('to_business_unit_id')
                            ->label('Ke Unit')
                            ->options(BusinessUnit::active()->pluck('name', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn ($set) => $set('to_account_id', null)),
                        
                        Forms\Components\Select::make('to_account_id')
                            ->label('Ke Rekening')
                            ->options(fn ($get) => ChartOfAccount::where('is_group', false)
                                ->where('code', 'like', '11%') // Aset Lancar
                                ->get()->mapWithKeys(fn($a) => [$a->id => $a->code . ' - ' . $a->name]))
                            ->required(),
                        
                        Forms\Components\TextInput::make('amount')
                            ->label('Jumlah Transfer')
                            ->required()
                            ->numeric()
                            ->prefix('Rp')
                            ->minValue(0),
                        
                        Forms\Components\Textarea::make('description')
                            ->label('Deskripsi')
                            ->required()
                            ->rows(3),
                        
                        Forms\Components\Textarea::make('notes')
                            ->label('Catatan')
                            ->rows(2),
                    ])->columns(2),
                
                Forms\Components\Section::make('Bukti Transfer')
                    ->schema([
                        Forms\Components\FileUpload::make('attachments')
                            ->label('Foto Nota/Struk')
                            ->multiple()
                            ->maxFiles(5)
                            ->image()
                            ->directory('transfers'),
                    ]),
                
                Forms\Components\Section::make('Status')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->label('Status')
                            ->options([
                                'pending' => 'Menunggu Persetujuan',
                                'approved' => 'Disetujui',
                                'rejected' => 'Ditolak',
                                'completed' => 'Selesai',
                            ])
                            ->default('pending')
                            ->disabled(), // Status diubah via action
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('transfer_number')
                    ->label('No. Transfer')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('transfer_date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('fromBusinessUnit.name')
                    ->label('Dari Unit')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('toBusinessUnit.name')
                    ->label('Ke Unit')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->limit(30),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'completed' => 'Selesai',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'approved' => 'info',
                        'rejected' => 'danger',
                        'completed' => 'success',
                    }),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'pending' => 'Menunggu',
                        'approved' => 'Disetujui',
                        'rejected' => 'Ditolak',
                        'completed' => 'Selesai',
                    ]),
                Tables\Filters\SelectFilter::make('from_business_unit_id')
                    ->label('Dari Unit')
                    ->options(BusinessUnit::pluck('name', 'id')),
                Tables\Filters\SelectFilter::make('to_business_unit_id')
                    ->label('Ke Unit')
                    ->options(BusinessUnit::pluck('name', 'id')),
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
            'index' => Pages\ListInterAccountTransfers::route('/'),
            'create' => Pages\CreateInterAccountTransfer::route('/create'),
            'edit' => Pages\EditInterAccountTransfer::route('/{record}/edit'),
        ];
    }
}
