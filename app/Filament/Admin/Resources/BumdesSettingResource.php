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
    protected static ?string $navigationGroup = 'Pengaturan';
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
                            ->schema([
                                Forms\Components\Section::make('Data BUMDes')
                                    ->schema([
                                        Forms\Components\TextInput::make('bumdes_name')
                                            ->label('Nama BUMDes')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Contoh: BUMDes Keude Bakongan'),
                                        
                                        Forms\Components\TextInput::make('village_name')
                                            ->label('Nama Desa')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Contoh: Keude Bakongan'),
                                        
                                        Forms\Components\TextInput::make('village_code')
                                            ->label('Kode Desa')
                                            ->maxLength(255)
                                            ->placeholder('Contoh: 11.03.01.2001'),
                                    ])->columns(3),
                                
                                Forms\Components\Section::make('Legalitas BUMDes')
                                    ->schema([
                                        Forms\Components\TextInput::make('bumdes_nib')
                                            ->label('NIB (Nomor Induk Berusaha)')
                                            ->maxLength(255)
                                            ->helperText('Jika sudah terbit NIB'),
                                        
                                        Forms\Components\TextInput::make('nomor_ahu')
                                            ->label('Nomor AHU')
                                            ->maxLength(255)
                                            ->helperText('Jika sudah terbit SK Kemenkumham'),
                                        
                                        Forms\Components\DatePicker::make('established_date')
                                            ->label('Tanggal Berdiri BUMDes'),
                                    ])->columns(3),
                                
                                Forms\Components\Section::make('Legalitas Pendirian (Perdes)')
                                    ->schema([
                                        Forms\Components\TextInput::make('nomor_perdes')
                                            ->label('Nomor Perdes Pendirian')
                                            ->maxLength(255)
                                            ->placeholder('Contoh: 03/PERDES/2020'),
                                        
                                        Forms\Components\DatePicker::make('tanggal_perdes')
                                            ->label('Tanggal Perdes Pendirian'),
                                        
                                        Forms\Components\FileUpload::make('file_perdes_path')
                                            ->label('File Perdes Pendirian')
                                            ->acceptedFileTypes(['application/pdf'])
                                            ->maxSize(5120)
                                            ->directory('bumdes/dokumen')
                                            ->helperText('Upload PDF Perdes (max 5MB)'),
                                    ])->columns(3),
                                
                                Forms\Components\Section::make('AD/ART BUMDes')
                                    ->schema([
                                        Forms\Components\FileUpload::make('file_adart_path')
                                            ->label('File AD/ART BUMDes')
                                            ->acceptedFileTypes(['application/pdf'])
                                            ->maxSize(5120)
                                            ->directory('bumdes/dokumen')
                                            ->helperText('Upload PDF Anggaran Dasar / Anggaran Rumah Tangga (max 5MB)'),
                                    ]),
                                
                                Forms\Components\Section::make('Visi & Misi')
                                    ->schema([
                                        Forms\Components\Textarea::make('vision')
                                            ->label('Visi')
                                            ->rows(3)
                                            ->placeholder('Visi BUMDes...'),
                                        
                                        Forms\Components\Textarea::make('mission')
                                            ->label('Misi')
                                            ->rows(5)
                                            ->placeholder('Misi BUMDes...'),
                                    ]),
                                
                                Forms\Components\Section::make('Tentang BUMDes')
                                    ->schema([
                                        Forms\Components\RichEditor::make('about')
                                            ->label('Tentang BUMDes')
                                            ->columnSpanFull()
                                            ->toolbarButtons([
                                                'bold', 'italic', 'underline', 'strike',
                                                'link', 'bulletList', 'orderedList',
                                                'h2', 'h3', 'blockquote',
                                            ]),
                                    ]),
                            ]),
                        
                        // ===== TAB 2: Alamat & Kontak =====
                        Tabs\Tab::make('Alamat & Kontak')
                            ->schema([
                                Forms\Components\Section::make('Alamat Lengkap')
                                    ->schema([
                                        Forms\Components\Textarea::make('bumdes_address')
                                            ->label('Alamat Lengkap BUMDes')
                                            ->required()
                                            ->rows(2)
                                            ->placeholder('Jl. Nasional No. 1, Keude Bakongan'),
                                        
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
                                    ])->columns(2),
                                
                                Forms\Components\Section::make('Kontak')
                                    ->schema([
                                        Forms\Components\TextInput::make('phone')
                                            ->label('Nomor Telepon/HP')
                                            ->tel()
                                            ->required()
                                            ->placeholder('628123456789'),
                                        
                                        Forms\Components\TextInput::make('whatsapp')
                                            ->label('Nomor WhatsApp')
                                            ->tel()
                                            ->placeholder('628123456789'),
                                        
                                        Forms\Components\TextInput::make('email')
                                            ->label('Email')
                                            ->email(),
                                        
                                        Forms\Components\TextInput::make('website')
                                            ->label('Website')
                                            ->url()
                                            ->placeholder('https://bumdes.id'),
                                    ])->columns(2),
                            ]),
                        
                        // ===== TAB 3: Logo & Foto =====
                        Tabs\Tab::make('Logo & Foto')
                            ->schema([
                                Forms\Components\Section::make('Logo BUMDes')
                                    ->schema([
                                        Forms\Components\FileUpload::make('logo_path')
                                            ->label('Logo BUMDes (Warna)')
                                            ->image()
                                            ->directory('bumdes/logo')
                                            ->maxSize(2048)
                                            ->helperText('Format: PNG/JPG, max 2MB'),
                                        
                                        Forms\Components\FileUpload::make('logo_white_path')
                                            ->label('Logo BUMDes (Putih)')
                                            ->image()
                                            ->directory('bumdes/logo')
                                            ->maxSize(2048)
                                            ->helperText('Untuk background gelap'),
                                    ])->columns(2),
                                
                                Forms\Components\Section::make('Foto Latar')
                                    ->schema([
                                        Forms\Components\FileUpload::make('kantor_photo_path')
                                            ->label('Foto Kantor')
                                            ->image()
                                            ->directory('bumdes/foto')
                                            ->maxSize(5120)
                                            ->helperText('Untuk background website, max 5MB'),
                                        
                                        Forms\Components\FileUpload::make('kegiatan_photo_path')
                                            ->label('Foto Kegiatan')
                                            ->image()
                                            ->directory('bumdes/foto')
                                            ->maxSize(5120)
                                            ->helperText('Untuk background login, max 5MB'),
                                    ])->columns(2),
                                
                                Forms\Components\Section::make('Tanda Tangan')
                                    ->schema([
                                        Forms\Components\FileUpload::make('signature_photo_path')
                                            ->label('Foto Tanda Tangan')
                                            ->image()
                                            ->directory('bumdes/ttd')
                                            ->maxSize(1024)
                                            ->helperText('Untuk tanda tangan digital di PDF'),
                                    ]),
                            ]),
                        
                        // ===== TAB 4: Kop PDF =====
                        Tabs\Tab::make('Kop PDF')
                            ->schema([
                                Forms\Components\Section::make('Header Surat / PDF')
                                    ->description('Data ini digunakan sebagai kop surat saat cetak laporan keuangan')
                                    ->schema([
                                        Forms\Components\TextInput::make('pdf_header_line1')
                                            ->label('Baris 1 (Atas)')
                                            ->placeholder('PEMERINTAH KABUPATEN ACEH SELATAN'),
                                        
                                        Forms\Components\TextInput::make('pdf_header_line2')
                                            ->label('Baris 2 (Tengah)')
                                            ->placeholder('KECAMATAN BAKONGAN'),
                                        
                                        Forms\Components\TextInput::make('pdf_header_line3')
                                            ->label('Baris 3 (Bawah)')
                                            ->placeholder('BUMDes KEUDE BAKONGAN'),
                                        
                                        Forms\Components\TextInput::make('pdf_footer_text')
                                            ->label('Footer PDF')
                                            ->placeholder('Jl. Nasional No. 1, Keude Bakongan'),
                                    ]),
                                
                                Forms\Components\Section::make('Stempel Digital')
                                    ->schema([
                                        Forms\Components\FileUpload::make('pdf_stamp_path')
                                            ->label('Stempel/Gambar')
                                            ->image()
                                            ->directory('bumdes/stempel')
                                            ->maxSize(1024)
                                            ->helperText('Gambar stempel untuk PDF (opsional)'),
                                    ]),
                            ]),
                        
                        // ===== TAB 5: Media Sosial & SEO =====
                        Tabs\Tab::make('Media & SEO')
                            ->schema([
                                Forms\Components\Section::make('Media Sosial')
                                    ->schema([
                                        Forms\Components\TextInput::make('facebook')
                                            ->label('Facebook')
                                            ->url()
                                            ->prefix('https://facebook.com/'),
                                        
                                        Forms\Components\TextInput::make('instagram')
                                            ->label('Instagram')
                                            ->url()
                                            ->prefix('https://instagram.com/'),
                                        
                                        Forms\Components\TextInput::make('youtube')
                                            ->label('YouTube')
                                            ->url()
                                            ->prefix('https://youtube.com/'),
                                        
                                        Forms\Components\TextInput::make('tiktok')
                                            ->label('TikTok')
                                            ->url()
                                            ->prefix('https://tiktok.com/@'),
                                        
                                        Forms\Components\TextInput::make('twitter')
                                            ->label('Twitter/X')
                                            ->url()
                                            ->prefix('https://twitter.com/'),
                                    ])->columns(2),
                                
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
                                
                                Forms\Components\Section::make('Pengaturan')
                                    ->schema([
                                        Forms\Components\Toggle::make('is_active')
                                            ->label('Aktif')
                                            ->default(true),
                                    ]),
                            ]),
                    ])
                    ->columns(1),
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
