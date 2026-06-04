<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BumdesSettingResource\Pages;
use App\Models\BumdesSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Forms\Components\Tabs;

class BumdesSettingResource extends Resource
{
    protected static ?string $model = BumdesSetting::class;
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationGroup = '⚙️ Pengaturan';
    protected static ?string $navigationLabel = 'Identitas BUMDes';
    protected static ?string $modelLabel = 'Identitas BUMDes';
    protected static ?string $pluralModelLabel = 'Pengaturan BUMDes';
    protected static ?int $navigationSort = 1;
    protected static ?string $slug = 'identitas-bumdes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('tabs')
                    ->schema([
                        // ===== TAB 1: Identitas BUMDes =====
                        Tabs\Tab::make('Identitas BUMDes')
                            ->icon('heroicon-o-building-office-2')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Section::make('Data BUMDes')
                                            ->schema([
                                                Forms\Components\TextInput::make('bumdes_name')
                                                    ->label('Nama BUMDes')
                                                    ->required()
                                                    ->maxLength(255),

                                                Forms\Components\TextInput::make('village_name')
                                                    ->label('Nama Desa')
                                                    ->required()
                                                    ->maxLength(255),

                                                Forms\Components\TextInput::make('village_code')
                                                    ->label('Kode Desa')
                                                    ->maxLength(255),

                                                Forms\Components\TextInput::make('motto')
                                                    ->label('Motto/Slogan')
                                                    ->maxLength(255)
                                                    ->placeholder('Motto atau slogan BUMDes'),

                                                Forms\Components\ColorPicker::make('motto_color')
                                                    ->label('Warna Motto')
                                                    ->default('#1e293b'),

                                                Forms\Components\DatePicker::make('established_date')
                                                    ->label('Tanggal Berdiri'),
                                            ]),

                                        Forms\Components\Section::make('Legalitas')
                                            ->schema([
                                                Forms\Components\TextInput::make('bumdes_nib')
                                                    ->label('NIB')
                                                    ->maxLength(255),

                                                Forms\Components\TextInput::make('nomor_ahu')
                                                    ->label('Nomor AHU')
                                                    ->maxLength(255),

                                                Forms\Components\TextInput::make('npwp')
                                                    ->label('NPWP')
                                                    ->maxLength(255)
                                                    ->placeholder('XX.XXX.XXX.X-XXX.XXX'),

                                                Forms\Components\TextInput::make('nomor_perdes')
                                                    ->label('Nomor Perdes')
                                                    ->maxLength(255),

                                                Forms\Components\DatePicker::make('tanggal_perdes')
                                                    ->label('Tanggal Perdes'),

                                                Forms\Components\FileUpload::make('file_perdes_path')
                                                    ->label('File Perdes (PDF)')
                                                    ->acceptedFileTypes(['application/pdf'])
                                                    ->maxSize(5120)
                                                    ->directory('bumdes/dokumen'),

                                                Forms\Components\FileUpload::make('file_adart_path')
                                                    ->label('File AD/ART (PDF)')
                                                    ->acceptedFileTypes(['application/pdf'])
                                                    ->maxSize(5120)
                                                    ->directory('bumdes/dokumen'),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('Visi & Misi')
                                    ->schema([
                                        Forms\Components\Textarea::make('vision')
                                            ->label('Visi')
                                            ->rows(3),

                                        Forms\Components\Textarea::make('mission')
                                            ->label('Misi')
                                            ->rows(5),
                                    ]),

                                Forms\Components\Section::make('Tentang BUMDes')
                                    ->schema([
                                        Forms\Components\RichEditor::make('about')
                                            ->label('Tentang BUMDes'),
                                    ]),
                            ]),

                        // ===== TAB 2: Alamat & Kontak =====
                        Tabs\Tab::make('Alamat & Kontak')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Section::make('Alamat')
                                            ->schema([
                                                Forms\Components\Textarea::make('bumdes_address')
                                                    ->label('Alamat Lengkap')
                                                    ->required()
                                                    ->rows(2),

                                                Forms\Components\TextInput::make('bumdes_village')
                                                    ->label('Desa/Kelurahan')
                                                    ->maxLength(255),

                                                Forms\Components\TextInput::make('bumdes_district')
                                                    ->label('Kecamatan')
                                                    ->required()
                                                    ->maxLength(255),

                                                Forms\Components\TextInput::make('bumdes_regency')
                                                    ->label('Kabupaten/Kota')
                                                    ->required()
                                                    ->maxLength(255),

                                                Forms\Components\TextInput::make('bumdes_province')
                                                    ->label('Provinsi')
                                                    ->required()
                                                    ->maxLength(255),

                                                Forms\Components\TextInput::make('bumdes_postal_code')
                                                    ->label('Kode Pos')
                                                    ->maxLength(10),
                                            ]),

                                        Forms\Components\Section::make('Kontak')
                                            ->schema([
                                                Forms\Components\TextInput::make('phone')
                                                    ->label('Telepon/HP')
                                                    ->tel()
                                                    ->required(),

                                                Forms\Components\TextInput::make('whatsapp')
                                                    ->label('WhatsApp')
                                                    ->tel(),

                                                Forms\Components\TextInput::make('email')
                                                    ->label('Email')
                                                    ->email(),

                                                Forms\Components\TextInput::make('website')
                                                    ->label('Website')
                                                    ->url(),
                                            ]),
                                    ]),
                            ]),

                        // ===== TAB 3: Logo & Foto =====
                        Tabs\Tab::make('Logo & Foto')
                            ->icon('heroicon-o-photo')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Section::make('Logo')
                                            ->schema([
                                                Forms\Components\FileUpload::make('logo_path')
                                                    ->label('Logo Warna')
                                                    ->image()
                                                    ->directory('bumdes/logo')
                                                    ->maxSize(2048),

                                                Forms\Components\FileUpload::make('logo_white_path')
                                                    ->label('Logo Putih')
                                                    ->image()
                                                    ->directory('bumdes/logo')
                                                    ->maxSize(2048),
                                            ]),

                                        Forms\Components\Section::make('Foto')
                                            ->schema([
                                                Forms\Components\FileUpload::make('kantor_photo_path')
                                                    ->label('Foto Kantor')
                                                    ->image()
                                                    ->directory('bumdes/foto')
                                                    ->maxSize(5120),

                                                Forms\Components\FileUpload::make('kegiatan_photo_path')
                                                    ->label('Foto Kegiatan')
                                                    ->image()
                                                    ->directory('bumdes/foto')
                                                    ->maxSize(5120),
                                            ]),
                                    ]),

                                Forms\Components\Section::make('Tanda Tangan')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\FileUpload::make('signature_photo_path')
                                                    ->label('Foto Tanda Tangan')
                                                    ->image()
                                                    ->directory('bumdes/ttd')
                                                    ->maxSize(1024),

                                                Forms\Components\FileUpload::make('pdf_stamp_path')
                                                    ->label('Stempel')
                                                    ->image()
                                                    ->directory('bumdes/stempel')
                                                    ->maxSize(1024),
                                            ]),
                                    ]),
                            ]),

                        // ===== TAB 4: Pejabat BUMDes =====
                        Tabs\Tab::make('Pejabat BUMDes')
                            ->icon('heroicon-o-user-group')
                            ->schema([
                                // Foto Pejabat
                                Forms\Components\Section::make('📷 Foto Pejabat')
                                    ->schema([
                                        Forms\Components\FileUpload::make('pejabat_foto_path')
                                            ->label('Foto Bersama Pejabat')
                                            ->image()
                                            ->directory('bumdes/pejabat')
                                            ->maxSize(2048),
                                    ])->collapsible()->collapsed(),

                                // Penasihat
                                Forms\Components\Section::make('👤 Penasihat')
                                    ->description('Kepala Desa (ex officio)')
                                    ->schema([
                                        Forms\Components\FileUpload::make('kepala_desa_photo')
                                            ->label('Foto Penasihat')
                                            ->image()
                                            ->directory('bumdes/pejabat')
                                            ->maxSize(2048)
                                            ->columnSpanFull(),
                                        Forms\Components\Grid::make(2)->schema([
                                            Forms\Components\TextInput::make('kepala_desa_name')
                                                ->label('Nama')
                                                ->placeholder('H. Dadang Suhendar, S.Pd.I')
                                                ->maxLength(255),
                                            Forms\Components\TextInput::make('kepala_desa_phone')
                                                ->label('No. HP')
                                                ->placeholder('0812xxxx')
                                                ->maxLength(255),
                                        ]),
                                    ])->collapsible(),

                                // Pelaksana Operasional
                                Forms\Components\Section::make('🏢 Pelaksana Operasional')
                                    ->schema([
                                        // Direktur
                                        Forms\Components\Group::make([
                                            Forms\Components\Section::make('Direktur')
                                                ->schema([
                                                    Forms\Components\FileUpload::make('direktur_photo')
                                                        ->label('Foto')
                                                        ->image()
                                                        ->directory('bumdes/pejabat')
                                                        ->maxSize(2048),
                                                    Forms\Components\Grid::make(2)->schema([
                                                        Forms\Components\TextInput::make('direktur_name')
                                                            ->label('Nama')
                                                            ->maxLength(255),
                                                        Forms\Components\TextInput::make('direktur_phone')
                                                            ->label('No. HP')
                                                            ->maxLength(255),
                                                    ]),
                                                ])->collapsible(),
                                        ])->columnSpanFull(),
                                        // Sekretaris
                                        Forms\Components\Group::make([
                                            Forms\Components\Section::make('Sekretaris')
                                                ->schema([
                                                    Forms\Components\FileUpload::make('sekretaris_photo')
                                                        ->label('Foto')
                                                        ->image()
                                                        ->directory('bumdes/pejabat')
                                                        ->maxSize(2048),
                                                    Forms\Components\Grid::make(2)->schema([
                                                        Forms\Components\TextInput::make('sekretaris_name')
                                                            ->label('Nama')
                                                            ->maxLength(255),
                                                        Forms\Components\TextInput::make('sekretaris_phone')
                                                            ->label('No. HP')
                                                            ->maxLength(255),
                                                    ]),
                                                ])->collapsible(),
                                        ])->columnSpanFull(),
                                        // Bendahara
                                        Forms\Components\Group::make([
                                            Forms\Components\Section::make('Bendahara')
                                                ->schema([
                                                    Forms\Components\FileUpload::make('bendahara_photo')
                                                        ->label('Foto')
                                                        ->image()
                                                        ->directory('bumdes/pejabat')
                                                        ->maxSize(2048),
                                                    Forms\Components\Grid::make(2)->schema([
                                                        Forms\Components\TextInput::make('bendahara_umum_name')
                                                            ->label('Nama')
                                                            ->maxLength(255),
                                                        Forms\Components\TextInput::make('bendahara_umum_phone')
                                                            ->label('No. HP')
                                                            ->maxLength(255),
                                                    ]),
                                                ])->collapsible(),
                                        ])->columnSpanFull(),
                                        Forms\Components\Placeholder::make('info_unit')
                                            ->label('')
                                            ->content('👆 Kepala Unit Usaha dikelola di menu Master Data → Unit Usaha'),
                                    ])->collapsible(),

                                // Pengawas
                                Forms\Components\Section::make('👥 Pengawas')
                                    ->description('Dari BPD atau Tokoh Masyarakat')
                                    ->schema([
                                        // Pengawas 1
                                        Forms\Components\Group::make([
                                            Forms\Components\Section::make('Pengawas 1')
                                                ->schema([
                                                    Forms\Components\FileUpload::make('pengawas1_photo')
                                                        ->label('Foto')
                                                        ->image()
                                                        ->directory('bumdes/pejabat')
                                                        ->maxSize(2048),
                                                    Forms\Components\Grid::make(2)->schema([
                                                        Forms\Components\TextInput::make('pengawas1_name')
                                                            ->label('Nama')
                                                            ->maxLength(255),
                                                        Forms\Components\TextInput::make('pengawas1_phone')
                                                            ->label('No. HP')
                                                            ->maxLength(255),
                                                    ]),
                                                ])->collapsible(),
                                        ])->columnSpanFull(),
                                        // Pengawas 2
                                        Forms\Components\Group::make([
                                            Forms\Components\Section::make('Pengawas 2')
                                                ->schema([
                                                    Forms\Components\FileUpload::make('pengawas2_photo')
                                                        ->label('Foto')
                                                        ->image()
                                                        ->directory('bumdes/pejabat')
                                                        ->maxSize(2048),
                                                    Forms\Components\Grid::make(2)->schema([
                                                        Forms\Components\TextInput::make('pengawas2_name')
                                                            ->label('Nama')
                                                            ->maxLength(255),
                                                        Forms\Components\TextInput::make('pengawas2_phone')
                                                            ->label('No. HP')
                                                            ->maxLength(255),
                                                    ]),
                                                ])->collapsible(),
                                        ])->columnSpanFull(),
                                    ])->collapsible(),
                            ]),

                        // ===== TAB 5: Rekening Bank =====
                        Tabs\Tab::make('Rekening Bank')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Forms\Components\Section::make('Informasi Rekening')
                                    ->schema([
                                        Forms\Components\Grid::make(3)
                                            ->schema([
                                                Forms\Components\TextInput::make('bank_name')
                                                    ->label('Nama Bank')
                                                    ->maxLength(255)
                                                    ->placeholder('Bank Jateng, BRI, BNI, dll.'),

                                                Forms\Components\TextInput::make('bank_account_number')
                                                    ->label('Nomor Rekening')
                                                    ->maxLength(255),

                                                Forms\Components\TextInput::make('bank_account_name')
                                                    ->label('Nama Pemegang Rekening')
                                                    ->maxLength(255),
                                            ]),
                                    ]),
                            ]),

                        // ===== TAB 6: Kop Surat & Branding =====
                        Tabs\Tab::make('Kop Surat & Branding')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Section::make('Header Surat')
                                            ->description('Untuk kop surat laporan keuangan')
                                            ->schema([
                                                Forms\Components\TextInput::make('pdf_header_line1')
                                                    ->label('Baris 1')
                                                    ->placeholder('PEMERINTAH KABUPATEN...'),

                                                Forms\Components\TextInput::make('pdf_header_line2')
                                                    ->label('Baris 2')
                                                    ->placeholder('KECAMATAN...'),

                                                Forms\Components\TextInput::make('pdf_header_line3')
                                                    ->label('Baris 3')
                                                    ->placeholder('BUMDes...'),

                                                Forms\Components\TextInput::make('pdf_footer_text')
                                                    ->label('Footer'),
                                            ]),

                                        Forms\Components\Section::make('Warna Branding')
                                            ->schema([
                                                Forms\Components\ColorPicker::make('primary_color')
                                                    ->label('Warna Utama')
                                                    ->default('#1e40af'),

                                                Forms\Components\ColorPicker::make('secondary_color')
                                                    ->label('Warna Sekunder')
                                                    ->default('#059669'),
                                            ]),
                                    ]),
                            ]),

                        // ===== TAB 7: Media & SEO =====
                        Tabs\Tab::make('Media & SEO')
                            ->icon('heroicon-o-globe-alt')
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\Section::make('Media Sosial')
                                            ->schema([
                                                Forms\Components\TextInput::make('facebook')
                                                    ->label('Facebook')
                                                    ->url()
                                                    ->placeholder('https://facebook.com/...'),

                                                Forms\Components\TextInput::make('instagram')
                                                    ->label('Instagram')
                                                    ->url()
                                                    ->placeholder('https://instagram.com/...'),

                                                Forms\Components\TextInput::make('youtube')
                                                    ->label('YouTube')
                                                    ->url()
                                                    ->placeholder('https://youtube.com/...'),

                                                Forms\Components\TextInput::make('tiktok')
                                                    ->label('TikTok')
                                                    ->url()
                                                    ->placeholder('https://tiktok.com/...'),

                                                Forms\Components\TextInput::make('twitter')
                                                    ->label('Twitter/X')
                                                    ->url()
                                                    ->placeholder('https://x.com/...'),
                                            ]),

                                        Forms\Components\Section::make('SEO')
                                            ->schema([
                                                Forms\Components\TextInput::make('meta_title')
                                                    ->label('Meta Title')
                                                    ->maxLength(255),

                                                Forms\Components\Textarea::make('meta_description')
                                                    ->label('Meta Description')
                                                    ->rows(2),

                                                Forms\Components\Textarea::make('meta_keywords')
                                                    ->label('Meta Keywords')
                                                    ->rows(2),
                                            ]),
                                    ]),


                                Forms\Components\Section::make('Pengaturan')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->default(true),
                                    ]),

                                Forms\Components\Section::make('Custom CSS')
                                    ->description('Tambahkan CSS kustom untuk panel admin (opsional)')
                                    ->schema([
                                        Forms\Components\Textarea::make('custom_css')
                                            ->label('Custom CSS')
                                            ->rows(8)
                                            ->placeholder('.fi-sidebar { background: #1E3A5F; }')
                                            ->helperText('Contoh: .fi-sidebar { background: #your-color; }'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBumdesSettings::route('/'),
            'edit' => Pages\EditBumdesSetting::route('/{record}/edit'),
        ];
    }

    public static function getNavigationUrl(): string
    {
        $settings = BumdesSetting::getSettings();
        return static::getUrl('edit', ['record' => $settings]);
    }
}
