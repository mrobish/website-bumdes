<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\AssetResource\Pages;
use App\Models\Asset;
use App\Models\BusinessUnit;
use App\Models\ChartOfAccount;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $navigationLabel = 'Aset & Penyusutan';
    protected static ?string $modelLabel = 'Aset Tetap';
    protected static ?string $pluralModelLabel = 'Aset Tetap';
    protected static ?int $navigationSort = 9;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Aset')
                ->schema([
                    Forms\Components\TextInput::make('asset_code')
                        ->label('Kode Aset')->disabled()->dehydrated(false),
                    Forms\Components\TextInput::make('name')
                        ->label('Nama Aset')->required(),
                    Forms\Components\Textarea::make('description')
                        ->label('Deskripsi')->rows(2),
                    Forms\Components\Select::make('category')
                        ->label('Kategori')
                        ->options([
                            'tanah' => 'Tanah', 'bangunan' => 'Bangunan',
                            'peralatan' => 'Peralatan', 'kendaraan' => 'Kendaraan',
                            'inventaris' => 'Inventaris', 'lainnya' => 'Lainnya',
                        ])->required(),
                    Forms\Components\Select::make('business_unit_id')
                        ->label('Unit Usaha')
                        ->options(BusinessUnit::active()->pluck('name', 'id'))->required(),
                    Forms\Components\Select::make('account_id')
                        ->label('Akun Aset')
                        ->options(ChartOfAccount::where('code', 'like', '12%')->get()->mapWithKeys(fn($a) => [$a->id => $a->code . ' - ' . $a->name]))
                        ->required(),
                ])->columns(2),
            Forms\Components\Section::make('Nilai & Penyusutan')
                ->schema([
                    Forms\Components\TextInput::make('purchase_price')
                        ->label('Harga Beli')->required()->numeric()->prefix('Rp'),
                    Forms\Components\TextInput::make('salvage_value')
                        ->label('Nilai Sisa (Residual)')->numeric()->prefix('Rp')->default(0),
                    Forms\Components\DatePicker::make('purchase_date')
                        ->label('Tanggal Beli')->required(),
                    Forms\Components\TextInput::make('useful_life_months')
                        ->label('Umur Ekonomis (Bulan)')->required()->numeric(),
                    Forms\Components\Select::make('depreciation_method')
                        ->label('Metode Penyusutan')
                        ->options([
                            'straight_line' => 'Garis Lurus (Straight Line)',
                            'declining_balance' => 'Saldo Menurun',
                        ])->default('straight_line')->required(),
                ])->columns(2),
            Forms\Components\Section::make('Lokasi & Status')
                ->schema([
                    Forms\Components\TextInput::make('location')
                        ->label('Lokasi Aset'),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'active' => 'Aktif', 'fully_depreciated' => 'Fully Depreciated',
                            'disposed' => 'Dibuang', 'sold' => 'Dijual',
                        ])->default('active'),
                    Forms\Components\FileUpload::make('attachments')
                        ->label('Foto Aset')->multiple()->maxFiles(5)->image(),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('asset_code')->label('Kode')->searchable(),
            Tables\Columns\TextColumn::make('name')->label('Nama Aset')->searchable(),
            Tables\Columns\TextColumn::make('category')->label('Kategori')->badge()
                ->color(fn (string $s) => match($s) { 'tanah' => 'success', 'bangunan' => 'info', default => 'gray' }),
            Tables\Columns\TextColumn::make('businessUnit.name')->label('Unit'),
            Tables\Columns\TextColumn::make('purchase_price')->label('Harga Beli')->money('IDR')->sortable(),
            Tables\Columns\TextColumn::make('useful_life_months')->label('Umur (bln)')->sortable(),
            Tables\Columns\TextColumn::make('status')->label('Status')->badge()
                ->color(fn (string $s) => match($s) { 'active' => 'success', 'fully_depreciated' => 'warning', default => 'gray' }),
        ])->filters([
            Tables\Filters\SelectFilter::make('status')->options(['active' => 'Aktif', 'fully_depreciated' => 'Selesai', 'disposed' => 'Dibuang', 'sold' => 'Dijual']),
            Tables\Filters\SelectFilter::make('category')->options(['tanah' => 'Tanah', 'bangunan' => 'Bangunan', 'peralatan' => 'Peralatan', 'kendaraan' => 'Kendaraan', 'inventaris' => 'Inventaris']),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAssets::route('/'),
            'create' => Pages\CreateAsset::route('/create'),
            'edit' => Pages\EditAsset::route('/{record}/edit'),
        ];
    }
}
