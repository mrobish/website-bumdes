<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\CapitalContributionResource\Pages;
use App\Models\CapitalContribution;
use App\Models\BusinessUnit;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CapitalContributionResource extends Resource
{
    protected static ?string $model = CapitalContribution::class;
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = '💰 Keuangan';
    protected static ?string $navigationLabel = 'Penyertaan Modal';
    protected static ?string $modelLabel = 'Penyertaan Modal';
    protected static ?string $pluralModelLabel = 'Penyertaan Modal';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Sumber Penyertaan')
                ->schema([
                    Forms\Components\TextInput::make('contribution_number')
                        ->label('No. Registrasi')->disabled()->dehydrated(false),
                    Forms\Components\Select::make('source_type')
                        ->label('Sumber Dari')
                        ->options([
                            'desa' => 'Desa (Penyertaan Modal)',
                            'pemda_kab_kot' => 'Pemda Kabupaten/Kota',
                            'pemprov' => 'Pemerintah Provinsi',
                            'kementerian' => 'Kementerian (Kemendes, dll)',
                            'lembaga_negara' => 'Lembaga Negara',
                            'lembaga_swasta' => 'Lembaga Swasta / CSR',
                            'perorangan' => 'Perorangan',
                            'bantuan_luar_negeri' => 'Bantuan Luar Negeri',
                            'lainnya' => 'Lainnya',
                        ])->required()->live(),
                    Forms\Components\TextInput::make('source_name')
                        ->label('Nama Sumber')
                        ->placeholder('Contoh: Kemendes PDTT, PT Telkom, dll')
                        ->required(),
                    Forms\Components\TextInput::make('source_contact')
                        ->label('Contact Person'),
                    Forms\Components\TextInput::make('source_phone')
                        ->label('Telepon')->tel(),
                    Forms\Components\TextInput::make('source_email')
                        ->label('Email')->email(),
                    Forms\Components\Textarea::make('source_address')
                        ->label('Alamat Sumber')->rows(2),
                ])->columns(2),

            Forms\Components\Section::make('Detail Penyertaan Modal')
                ->schema([
                    Forms\Components\Select::make('contribution_type')
                        ->label('Jenis Penyertaan')
                        ->options([
                            'hibah' => 'Hibah (Grant) - Tidak perlu dikembalikan',
                            'pinjaman' => 'Pinjaman (Loan) - Perlu dikembalikan',
                            'pinjaman_bunga' => 'Pinjaman Berbunga',
                            'penyertaan_saham' => 'Penyertaan Saham/Kekayaan',
                        ])->required()->live(),
                    Forms\Components\Select::make('form')
                        ->label('Bentuk Penyertaan')
                        ->options([
                            'uang' => 'Uang Tunai',
                            'barang' => 'Barang Berupa',
                            'uang_dan_barang' => 'Uang dan Barang',
                            'jasa' => 'Jasa/Layanan',
                        ])->required()->live(),
                    Forms\Components\TextInput::make('amount')
                        ->label('Nilai Uang (Rp)')
                        ->numeric()->prefix('Rp')->default(0)
                        ->visible(fn ($get) => in_array($get('form'), ['uang', 'uang_dan_barang'])),
                    Forms\Components\Textarea::make('goods_description')
                        ->label('Deskripsi Barang')
                        ->placeholder('Contoh: 1 unit traktor, 50 karung pupuk, dll')
                        ->rows(2)
                        ->visible(fn ($get) => in_array($get('form'), ['barang', 'uang_dan_barang'])),
                    Forms\Components\TextInput::make('goods_value')
                        ->label('Nilai Barang (Rp)')
                        ->numeric()->prefix('Rp')->default(0)
                        ->visible(fn ($get) => in_array($get('form'), ['barang', 'uang_dan_barang'])),
                    Forms\Components\TextInput::make('total_value')
                        ->label('Total Nilai')
                        ->numeric()->prefix('Rp')->disabled()->dehydrated(false),
                ])->columns(2),

            Forms\Components\Section::make('Waktu & Periode')
                ->schema([
                    Forms\Components\Select::make('contribution_year')
                        ->label('Tahun Penyertaan')
                        ->options(collect(range(date('Y') - 5, date('Y') + 1))->mapWithKeys(fn ($y) => [$y => (string) $y]))
                        ->required(),
                    Forms\Components\DatePicker::make('disbursement_date')
                        ->label('Tanggal Pencairan'),
                    Forms\Components\DatePicker::make('received_date')
                        ->label('Tanggal Diterima'),
                ])->columns(3),

            Forms\Components\Section::make('Detail Pinjaman')
                ->schema([
                    Forms\Components\TextInput::make('interest_rate')
                        ->label('Suku Bunga (% per tahun)')
                        ->numeric()->suffix('%'),
                    Forms\Components\TextInput::make('loan_term_months')
                        ->label('Jangka Waktu (Bulan)')
                        ->numeric()->suffix('bulan'),
                    Forms\Components\DatePicker::make('first_payment_date')
                        ->label('Tanggal Pembayaran Pertama'),
                    Forms\Components\TextInput::make('monthly_payment')
                        ->label('Cicilan per Bulan (Rp)')
                        ->numeric()->prefix('Rp'),
                    Forms\Components\Textarea::make('repayment_terms')
                        ->label('Syarat Pengembalian')->rows(2),
                ])->columns(2)
                ->visible(fn ($get) => in_array($get('contribution_type'), ['pinjaman', 'pinjaman_bunga'])),

            Forms\Components\Section::make('Penggunaan Dana')
                ->schema([
                    Forms\Components\TextInput::make('purpose')
                        ->label('Tujuan Penyertaan')
                        ->placeholder('Contoh: Modal kerja unit pertanian'),
                    Forms\Components\Textarea::make('restrictions')
                        ->label('Pembatasan Penggunaan')
                        ->placeholder('Contoh: Hanya untuk pengadaan alat pertanian')
                        ->rows(2),
                    Forms\Components\Toggle::make('is_restricted')
                        ->label('Ada Pembatasan Penggunaan Dana')
                        ->default(false),
                    Forms\Components\Select::make('business_unit_id')
                        ->label('Unit Penerima')
                        ->options(BusinessUnit::active()->pluck('name', 'id')),
                ])->columns(2),

            Forms\Components\Section::make('Dokumen & Status')
                ->schema([
                    Forms\Components\TextInput::make('agreement_number')
                        ->label('No. Perjanjian'),
                    Forms\Components\DatePicker::make('agreement_date')
                        ->label('Tanggal Perjanjian'),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'pending' => 'Menunggu',
                            'approved' => 'Disetujui',
                            'disbursed' => 'Sudah Dicaairkan',
                            'received' => 'Sudah Diterima',
                            'active' => 'Aktif',
                            'completed' => 'Selesai',
                            'cancelled' => 'Dibatalkan',
                            'rejected' => 'Ditolak',
                        ])->default('pending'),
                    Forms\Components\FileUpload::make('attachments')
                        ->label('Dokumen (SK, Perjanjian, Bukti Transfer)')
                        ->multiple()->maxFiles(10),
                    Forms\Components\Textarea::make('notes')
                        ->label('Catatan')->rows(2),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            Tables\Columns\TextColumn::make('contribution_number')
                ->label('No.')->searchable(),
            Tables\Columns\TextColumn::make('source_type')
                ->label('Sumber')->badge()
                ->formatStateUsing(fn ($s) => match($s) {
                    'desa' => 'Desa', 'pemda_kab_kot' => 'Pemda Kab/Kota', 'pemprov' => 'Pemprov',
                    'kementerian' => 'Kementerian', 'lembaga_negara' => 'Lembaga Negara',
                    'lembaga_swasta' => 'CSR/Swasta', 'perorangan' => 'Perorangan',
                    default => ucfirst(str_replace('_', ' ', $s)),
                })
                ->color(fn ($s) => match($s) {
                    'desa' => 'warning', 'pemda_kab_kot' => 'info', 'pemprov' => 'success',
                    'kementerian' => 'danger', default => 'gray',
                }),
            Tables\Columns\TextColumn::make('source_name')
                ->label('Nama Sumber')->limit(25)->searchable(),
            Tables\Columns\TextColumn::make('contribution_type')
                ->label('Jenis')->badge()
                ->formatStateUsing(fn ($s) => match($s) {
                    'hibah' => 'Hibah', 'pinjaman' => 'Pinjaman', 'pinjaman_bunga' => 'Pinjaman+Bunga', default => $s,
                })
                ->color(fn ($s) => match($s) { 'hibah' => 'success', default => 'warning' }),
            Tables\Columns\TextColumn::make('total_value')
                ->label('Nilai')->money('IDR')->sortable(),
            Tables\Columns\TextColumn::make('contribution_year')
                ->label('Tahun')->sortable(),
            Tables\Columns\TextColumn::make('status')
                ->label('Status')->badge()
                ->color(fn ($s) => match($s) {
                    'pending' => 'gray', 'approved' => 'info', 'disbursed' => 'success',
                    'received' => 'success', 'active' => 'success', 'completed' => 'warning',
                    default => 'danger',
                }),
        ])->filters([
            Tables\Filters\SelectFilter::make('source_type')
                ->options([
                    'desa' => 'Desa', 'pemda_kab_kot' => 'Pemda Kab/Kota', 'pemprov' => 'Pemprov',
                    'kementerian' => 'Kementerian', 'lembaga_swasta' => 'CSR/Swasta', 'perorangan' => 'Perorangan',
                ]),
            Tables\Filters\SelectFilter::make('contribution_type')
                ->options(['hibah' => 'Hibah', 'pinjaman' => 'Pinjaman', 'pinjaman_bunga' => 'Pinjaman Berbunga']),
            Tables\Filters\SelectFilter::make('contribution_year')
                ->options(collect(range(date('Y') - 5, date('Y')))->mapWithKeys(fn ($y) => [(string) $y => (string) $y])),
            Tables\Filters\SelectFilter::make('status')
                ->options([
                    'pending' => 'Menunggu', 'approved' => 'Disetujui', 'disbursed' => 'Dicaairkan',
                    'received' => 'Diterima', 'active' => 'Aktif', 'completed' => 'Selesai',
                ]),
        ])->actions([
            Tables\Actions\EditAction::make(),
            Tables\Actions\DeleteAction::make(),
        ])->bulkActions([
            Tables\Actions\BulkActionGroup::make([Tables\Actions\DeleteBulkAction::make()]),
        ]);
    }

    public static function getRelations(): array { return []; }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCapitalContributions::route('/'),
            'create' => Pages\CreateCapitalContribution::route('/create'),
            'edit' => Pages\EditCapitalContribution::route('/{record}/edit'),
        ];
    }
}
