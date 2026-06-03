<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\VillageInfoResource\Pages;
use App\Filament\Admin\Resources\VillageInfoResource\RelationManagers;
use App\Models\VillageInfo;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class VillageInfoResource extends Resource
{
    protected static ?string $model = VillageInfo::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    
    protected static ?string $navigationLabel = 'Info Desa';
    
    // Sembunyikan dari sidebar - sudah digabung ke Identitas BUMDes
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
    
    protected static ?string $modelLabel = 'Informasi Desa';
    
    protected static ?string $navigationGroup = '📂 Pengaturan';
    
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Info Desa')
                    ->columnSpanFull()
                    ->tabs([
                        // Tab 1: Data Umum
                        Tab::make('Data Umum')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Section::make('Identitas Desa')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextInput::make('village_name')
                                                ->label('Nama Desa')
                                                ->required()
                                                ->maxLength(100),
                                            TextInput::make('village_code')
                                                ->label('Kode Desa (BPS)')
                                                ->maxLength(10),
                                            TextInput::make('postal_code')
                                                ->label('Kode Pos')
                                                ->maxLength(10),
                                        ]),
                                        Grid::make(3)->schema([
                                            TextInput::make('district_name')
                                                ->label('Nama Kecamatan')
                                                ->required()
                                                ->maxLength(100),
                                            TextInput::make('district_code')
                                                ->label('Kode Kecamatan')
                                                ->maxLength(10),
                                        ]),
                                        Grid::make(3)->schema([
                                            TextInput::make('regency_name')
                                                ->label('Nama Kabupaten')
                                                ->required()
                                                ->maxLength(100),
                                            TextInput::make('regency_code')
                                                ->label('Kode Kabupaten')
                                                ->maxLength(10),
                                        ]),
                                        Grid::make(3)->schema([
                                            TextInput::make('province_name')
                                                ->label('Nama Provinsi')
                                                ->required()
                                                ->maxLength(100),
                                            TextInput::make('province_code')
                                                ->label('Kode Provinsi')
                                                ->maxLength(10),
                                        ]),
                                        Textarea::make('address')
                                            ->label('Alamat Lengkap Kantor Desa')
                                            ->required()
                                            ->rows(2),
                                    ]),
                                
                                Section::make('Kontak')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            TextInput::make('phone')
                                                ->label('Telepon')
                                                ->tel(),
                                            TextInput::make('email')
                                                ->label('Email')
                                                ->email(),
                                            TextInput::make('website')
                                                ->label('Website')
                                                ->url(),
                                        ]),
                                    ]),
                            ]),
                        
                        // Tab 2: Geografis
                        Tab::make('Geografis')
                            ->icon('heroicon-o-map')
                            ->schema([
                                Section::make('Luas Wilayah')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            Forms\Components\TextInput::make('area_total')
                                                ->label('Luas Total (Ha)')
                                                ->required()
                                                ->numeric()
                                                ->suffix('Ha'),
                                            Forms\Components\TextInput::make('area_land')
                                                ->label('Luas Daratan (Ha)')
                                                ->numeric()
                                                ->suffix('Ha'),
                                            Forms\Components\TextInput::make('area_water')
                                                ->label('Luas Perairan (Ha)')
                                                ->numeric()
                                                ->suffix('Ha'),
                                        ]),
                                        Grid::make(3)->schema([
                                            Forms\Components\TextInput::make('area_farming')
                                                ->label('Lahan Pertanian (Ha)')
                                                ->numeric()
                                                ->suffix('Ha'),
                                            Forms\Components\TextInput::make('area_settlement')
                                                ->label('Pemukiman (Ha)')
                                                ->numeric()
                                                ->suffix('Ha'),
                                            Forms\Components\TextInput::make('area_forest')
                                                ->label('Hutan (Ha)')
                                                ->numeric()
                                                ->suffix('Ha'),
                                        ]),
                                    ]),
                                
                                Section::make('Kondisi Alam')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            Forms\Components\TextInput::make('elevation')
                                                ->label('Ketinggian (mdpl)')
                                                ->numeric()
                                                ->suffix('mdpl'),
                                            Forms\Components\TextInput::make('rainfall')
                                                ->label('Curah Hujan (mm/tahun)')
                                                ->numeric()
                                                ->suffix('mm'),
                                            TextInput::make('climate_type')
                                                ->label('Jenis Iklim')
                                                ->placeholder('Af, Am, dll'),
                                        ]),
                                    ]),
                                
                                Section::make('Batas Wilayah')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Textarea::make('border_north')
                                                ->label('Batas Utara')
                                                ->rows(2),
                                            Textarea::make('border_south')
                                                ->label('Batas Selatan')
                                                ->rows(2),
                                            Textarea::make('border_east')
                                                ->label('Batas Timur')
                                                ->rows(2),
                                            Textarea::make('border_west')
                                                ->label('Batas Barat')
                                                ->rows(2),
                                        ]),
                                        Textarea::make('topography')
                                            ->label('Deskripsi Topografi')
                                            ->rows(3),
                                    ]),
                            ]),
                        
                        // Tab 3: Demografis
                        Tab::make('Demografis')
                            ->icon('heroicon-o-users')
                            ->schema([
                                Section::make('Jumlah Penduduk')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            Forms\Components\TextInput::make('total_population')
                                                ->label('Total Penduduk')
                                                ->required()
                                                ->numeric(),
                                            Forms\Components\TextInput::make('male_population')
                                                ->label('Laki-laki')
                                                ->numeric(),
                                            Forms\Components\TextInput::make('female_population')
                                                ->label('Perempuan')
                                                ->numeric(),
                                        ]),
                                        Grid::make(3)->schema([
                                            Forms\Components\TextInput::make('total_family')
                                                ->label('Jumlah KK')
                                                ->required()
                                                ->numeric(),
                                            Forms\Components\TextInput::make('total_rt')
                                                ->label('Jumlah RT')
                                                ->numeric(),
                                            Forms\Components\TextInput::make('total_rw')
                                                ->label('Jumlah RW')
                                                ->numeric(),
                                        ]),
                                        Grid::make(3)->schema([
                                            Forms\Components\TextInput::make('total_dusun')
                                                ->label('Jumlah Dusun')
                                                ->numeric(),
                                            Forms\Components\TextInput::make('population_density')
                                                ->label('Kepadatan (/Ha)')
                                                ->numeric(),
                                            Forms\Components\TextInput::make('growth_rate')
                                                ->label('Laju Pertumbuhan (%)')
                                                ->numeric()
                                                ->suffix('%'),
                                        ]),
                                    ]),
                                
                                Section::make('Angka Kependudukan')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('birth_rate')
                                                ->label('Angka Kelahiran (/1000)')
                                                ->numeric(),
                                            Forms\Components\TextInput::make('death_rate')
                                                ->label('Angka Kematian (/1000)')
                                                ->numeric(),
                                        ]),
                                        Textarea::make('population_notes')
                                            ->label('Catatan Demografis')
                                            ->rows(3),
                                    ]),
                            ]),
                        
                        // Tab 4: Pemerintahan
                        Tab::make('Pemerintahan')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                Section::make('Kepala Desa')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('head_village_name')
                                                ->label('Nama Kepala Desa')
                                                ->required()
                                                ->maxLength(100),
                                            TextInput::make('head_village_nip')
                                                ->label('NIP/NIK')
                                                ->maxLength(30),
                                        ]),
                                        Grid::make(2)->schema([
                                            Forms\Components\DatePicker::make('head_village_start')
                                                ->label('Masa Jabatan Mulai'),
                                            Forms\Components\DatePicker::make('head_village_end')
                                                ->label('Masa Jabatan Berakhir'),
                                        ]),
                                    ]),
                                
                                Section::make('Perangkat Desa')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('village_secretary_name')
                                                ->label('Sekretaris Desa')
                                                ->maxLength(100),
                                            Forms\Components\TextInput::make('total_staff')
                                                ->label('Jumlah Perangkat Desa')
                                                ->numeric(),
                                            Forms\Components\TextInput::make('total_bpd_members')
                                                ->label('Jumlah Anggota BPD')
                                                ->numeric(),
                                        ]),
                                        Textarea::make('village_regulation')
                                            ->label('Dasar Hukum Pendirian BUMDes')
                                            ->rows(3),
                                    ]),
                            ]),
                        
                        // Tab 5: Ekonomi
                        Tab::make('Ekonomi')
                            ->icon('heroicon-o-currency-dollar')
                            ->schema([
                                Section::make('Data Ekonomi')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            Forms\Components\TextInput::make('total_umskm')
                                                ->label('Jumlah UMKM')
                                                ->numeric(),
                                            Forms\Components\TextInput::make('total_market')
                                                ->label('Jumlah Pasar')
                                                ->numeric(),
                                            Forms\Components\TextInput::make('avg_income')
                                                ->label('Penghasilan Rata-rata')
                                                ->numeric()
                                                ->prefix('Rp'),
                                        ]),
                                        Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('poverty_rate')
                                                ->label('Tingkat Kemiskinan (%)')
                                                ->numeric()
                                                ->suffix('%'),
                                            Forms\Components\TextInput::make('unemployment_rate')
                                                ->label('Tingkat Pengangguran (%)')
                                                ->numeric()
                                                ->suffix('%'),
                                        ]),
                                    ]),
                                
                                Section::make('Potensi Ekonomi')
                                    ->schema([
                                        Textarea::make('main_commodities')
                                            ->label('Komoditas Utama')
                                            ->rows(3),
                                        Textarea::make('economic_activities')
                                            ->label('Kegiatan Ekonomi Utama')
                                            ->rows(3),
                                        Textarea::make('economic_potential')
                                            ->label('Potensi Ekonomi')
                                            ->rows(3),
                                        Textarea::make('economic_challenges')
                                            ->label('Tantangan Ekonomi')
                                            ->rows(3),
                                    ]),
                            ]),
                        
                        // Tab 6: Sosial & Kesehatan
                        Tab::make('Sosial')
                            ->icon('heroicon-o-heart')
                            ->schema([
                                Section::make('Fasilitas')
                                    ->schema([
                                        Grid::make(4)->schema([
                                            Forms\Components\TextInput::make('total_schools')
                                                ->label('Jumlah Sekolah')
                                                ->numeric(),
                                            Forms\Components\TextInput::make('total_health_facilities')
                                                ->label('Fasilitas Kesehatan')
                                                ->numeric(),
                                            Forms\Components\TextInput::make('total_mosques')
                                                ->label('Masjid/Musholla')
                                                ->numeric(),
                                            Forms\Components\TextInput::make('total_churches')
                                                ->label('Gereja')
                                                ->numeric(),
                                        ]),
                                    ]),
                                
                                Section::make('Indikator Sosial')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('literacy_rate')
                                                ->label('Tingkat Melek Huruf (%)')
                                                ->numeric()
                                                ->suffix('%'),
                                            Forms\Components\TextInput::make('school_participation_rate')
                                                ->label('Partisipasi Sekolah (%)')
                                                ->numeric()
                                                ->suffix('%'),
                                        ]),
                                        Textarea::make('education_facilities')
                                            ->label('Fasilitas Pendidikan')
                                            ->rows(3),
                                        Textarea::make('health_facilities')
                                            ->label('Fasilitas Kesehatan')
                                            ->rows(3),
                                        Textarea::make('social_notes')
                                            ->label('Catatan Sosial')
                                            ->rows(3),
                                    ]),
                            ]),
                        
                        // Tab 7: Infrastruktur
                        Tab::make('Infrastruktur')
                            ->icon('heroicon-o-wrench-screwdriver')
                            ->schema([
                                Section::make('Jaringan Jalan')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            Forms\Components\TextInput::make('total_road_length')
                                                ->label('Panjang Jalan (Km)')
                                                ->numeric()
                                                ->suffix('Km'),
                                            Forms\Components\TextInput::make('road_paved')
                                                ->label('Jalan Aspal/Beton (Km)')
                                                ->numeric()
                                                ->suffix('Km'),
                                            Forms\Components\TextInput::make('road_not_paved')
                                                ->label('Jalan Tanah (Km)')
                                                ->numeric()
                                                ->suffix('Km'),
                                        ]),
                                    ]),
                                
                                Section::make('Cakupan Layanan')
                                    ->schema([
                                        Grid::make(3)->schema([
                                            Forms\Components\TextInput::make('electricity_coverage')
                                                ->label('Cakupan Listrik (%)')
                                                ->numeric()
                                                ->suffix('%'),
                                            Forms\Components\TextInput::make('clean_water_coverage')
                                                ->label('Cakupan Air Bersih (%)')
                                                ->numeric()
                                                ->suffix('%'),
                                            Forms\Components\TextInput::make('internet_coverage')
                                                ->label('Cakupan Internet (%)')
                                                ->numeric()
                                                ->suffix('%'),
                                        ]),
                                        Textarea::make('infrastructure_notes')
                                            ->label('Catatan Infrastruktur')
                                            ->rows(3),
                                    ]),
                            ]),
                        
                        // Tab 8: Keuangan
                        Tab::make('Keuangan')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Section::make('Pendapatan Desa')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('village_fund')
                                                ->label('Dana Desa (DD)')
                                                ->numeric()
                                                ->prefix('Rp'),
                                            Forms\Components\TextInput::make('add_fund')
                                                ->label('ADD')
                                                ->numeric()
                                                ->prefix('Rp'),
                                        ]),
                                        Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('bdg_fund')
                                                ->label('Bagi Hasil Pajak')
                                                ->numeric()
                                                ->prefix('Rp'),
                                            Forms\Components\TextInput::make('own_revenue')
                                                ->label('PADes')
                                                ->numeric()
                                                ->prefix('Rp'),
                                        ]),
                                    ]),
                                
                                Section::make('Anggaran')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('total_budget')
                                                ->label('Total Anggaran')
                                                ->numeric()
                                                ->prefix('Rp'),
                                            Forms\Components\TextInput::make('total_expenditure')
                                                ->label('Total Belanja')
                                                ->numeric()
                                                ->prefix('Rp'),
                                            TextInput::make('budget_year')
                                                ->label('Tahun Anggaran')
                                                ->maxLength(4),
                                        ]),
                                        Textarea::make('budget_notes')
                                            ->label('Catatan Keuangan')
                                            ->rows(3),
                                    ]),
                            ]),
                        
                        // Tab 9: BUMDes
                        Tab::make('BUMDes')
                            ->icon('heroicon-o-building-storefront')
                            ->schema([
                                Section::make('Data BUMDes')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('bumdes_name')
                                                ->label('Nama BUMDes')
                                                ->required()
                                                ->maxLength(100),
                                            TextInput::make('bumdes_legal_number')
                                                ->label('Nomor SK/LEGAL')
                                                ->maxLength(50),
                                        ]),
                                        Forms\Components\DatePicker::make('bumdes_established')
                                            ->label('Tanggal Pendirian'),
                                    ]),
                                
                                Section::make('Keuangan BUMDes')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('bumdes_initial_capital')
                                                ->label('Modal Awal')
                                                ->numeric()
                                                ->prefix('Rp'),
                                            Forms\Components\TextInput::make('bumdes_current_capital')
                                                ->label('Modal Saat Ini')
                                                ->numeric()
                                                ->prefix('Rp'),
                                        ]),
                                        Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('bumdes_total_assets')
                                                ->label('Total Aset')
                                                ->numeric()
                                                ->prefix('Rp'),
                                            Forms\Components\TextInput::make('bumdes_annual_revenue')
                                                ->label('Omset Tahunan')
                                                ->numeric()
                                                ->prefix('Rp'),
                                        ]),
                                        Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('bumdes_profit')
                                                ->label('Laba/Rugi')
                                                ->numeric()
                                                ->prefix('Rp'),
                                            Forms\Components\TextInput::make('bumdes_employees')
                                                ->label('Jumlah Karyawan')
                                                ->numeric(),
                                            Forms\Components\TextInput::make('bumdes_partners')
                                                ->label('Jumlah Mitra')
                                                ->numeric(),
                                        ]),
                                    ]),
                                
                                Section::make('Profil BUMDes')
                                    ->schema([
                                        Textarea::make('bumdes_vision')
                                            ->label('Visi BUMDes')
                                            ->rows(3),
                                        Textarea::make('bumdes_mission')
                                            ->label('Misi BUMDes')
                                            ->rows(3),
                                        Textarea::make('bumdes_services')
                                            ->label('Layanan BUMDes')
                                            ->rows(3),
                                        Textarea::make('bumdes_achievements')
                                            ->label('Pencapaian BUMDes')
                                            ->rows(3),
                                    ]),
                            ]),
                        
                        // Tab 10: Potensi
                        Tab::make('Potensi')
                            ->icon('heroicon-o-sparkles')
                            ->schema([
                                Section::make('Potensi Unggulan')
                                    ->schema([
                                        Textarea::make('tourism_potential')
                                            ->label('Potensi Wisata')
                                            ->rows(3),
                                        Textarea::make('agriculture_potential')
                                            ->label('Potensi Pertanian')
                                            ->rows(3),
                                        Textarea::make('livestock_potential')
                                            ->label('Potensi Peternakan')
                                            ->rows(3),
                                        Textarea::make('fishery_potential')
                                            ->label('Potensi Perikanan')
                                            ->rows(3),
                                    ]),
                                
                                Section::make('Potensi Lainnya')
                                    ->schema([
                                        Textarea::make('craft_potential')
                                            ->label('Potensi Kerajinan')
                                            ->rows(3),
                                        Textarea::make('cultural_potential')
                                            ->label('Potensi Budaya')
                                            ->rows(3),
                                        Textarea::make('natural_potential')
                                            ->label('Potensi SDA')
                                            ->rows(3),
                                        Textarea::make('human_resource_potential')
                                            ->label('Potensi SDM')
                                            ->rows(3),
                                    ]),
                            ]),
                        
                        // Tab 11: Profil & SEO
                        Tab::make('Profil & SEO')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Section::make('Tentang Desa')
                                    ->schema([
                                        Textarea::make('about_village')
                                            ->label('Tentang Desa')
                                            ->rows(5),
                                        Textarea::make('village_history')
                                            ->label('Sejarah Desa')
                                            ->rows(5),
                                        TextInput::make('village_motto')
                                            ->label('Moto/Slogan')
                                            ->maxLength(255),
                                    ]),
                                
                                Section::make('Media Sosial & SEO')
                                    ->schema([
                                        Grid::make(2)->schema([
                                            TextInput::make('facebook')
                                                ->label('Facebook')
                                                ->url(),
                                            TextInput::make('instagram')
                                                ->label('Instagram')
                                                ->url(),
                                            TextInput::make('youtube')
                                                ->label('YouTube')
                                                ->url(),
                                            TextInput::make('tiktok')
                                                ->label('TikTok')
                                                ->url(),
                                        ]),
                                        TextInput::make('meta_title')
                                            ->label('SEO Title')
                                            ->maxLength(255),
                                        Textarea::make('meta_description')
                                            ->label('SEO Description')
                                            ->rows(2),
                                        Toggle::make('is_published')
                                            ->label('Publikasikan')
                                            ->default(false),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('village_name')
                    ->label('Nama Desa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('district_name')
                    ->label('Kecamatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('regency_name')
                    ->label('Kabupaten')
                    ->searchable(),
                Tables\Columns\TextColumn::make('total_population')
                    ->label('Penduduk')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('bumdes_name')
                    ->label('BUMDes')
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Status')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                //
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
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListVillageInfos::route('/'),
            'create' => Pages\CreateVillageInfo::route('/create'),
            'edit' => Pages\EditVillageInfo::route('/{record}/edit'),
        ];
    }
}
