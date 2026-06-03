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
                Tabs::make('Identitas BUMDes')
                    ->icon('heroicon-o-building-office-2')
                    ->schema([
                        // Tab 1: Identitas Dasar
                        Tabs\Tab::make('Identitas Dasar')
                            ->icon('heroicon-o-information-circle')
                            ->schema([
                                Forms\Components\Section::make('Informasi BUMDes')
                                    ->schema([
                                        Forms\Components\TextInput::make('bumdes_name')
                                            ->label('Nama BUMDes')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Contoh: BUMDes Keude Bakongan'),
                                        
                                        Forms\Components\TextInput::make('bumdes_nib')
                                            ->label('NIB (Nomor Induk Berusaha)')
                                            ->maxLength(255),
                                        
                                        Forms\Components\TextInput::make('bumdes_npwd')
                                            ->label('NPWD (Nomor Pokok Wajib Daftar)')
                                            ->maxLength(255),
                                        
                                        Forms\Components\DatePicker::make('established_date')
                                            ->label('Tanggal Berdiri'),
                                    ])->columns(2),
                                
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
                        
                        // Tab 2: Alamat & Kontak
                        Tabs\Tab::make('Alamat & Kontak')
                            ->icon('heroicon-o-map-pin')
                            ->schema([
                                Forms\Components\Section::make('Alamat')
                                    ->schema([
                                        Forms\Components\Textarea::make('bumdes_address')
                                            ->label('Alamat Lengkap')
                                            ->required()
                                            ->rows(2),
                                        
                                        Forms\Components\TextInput::make('bumdes_village')
                                            ->label('Desa/Kelurahan')
                                            ->required()
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
                        
                        // Tab 3: Media Sosial
                        Tabs\Tab::make('Media Sosial')
                            ->icon('heroicon-o-globe-alt')
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
                            ]),
                    ]),
                
                // Tab Logo & Foto
                Tabs\Tab::make('Logo & Foto')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\Section::make('Logo BUMDes')
                            ->schema([
                                Forms\Components\FileUpload::make('logo_path')
                                    ->label('Logo BUMDes (Warna)')
                                    ->image()
                                    ->directory('bumdes/logo')
                                    ->maxSize(2048)
                                    ->helperText('Format: PNG/JPG, Ukuran: max 2MB'),
                                
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
                                    ->helperText('Untuk background website, ukuran: max 5MB'),
                                
                                Forms\Components\FileUpload::make('kegiatan_photo_path')
                                    ->label('Foto Kegiatan')
                                    ->image()
                                    ->directory('bumdes/foto')
                                    ->maxSize(5120)
                                    ->helperText('Untuk background halaman login, ukuran: max 5MB'),
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
                
                // Tab Kop PDF
                Tabs\Tab::make('Kop PDF')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Forms\Components\Section::make('Header Surat/PDF')
                            ->description('Data ini akan digunakan sebagai kop surat saat cetak laporan keuangan')
                            ->schema([
                                Forms\Components\TextInput::make('pdf_header_line1')
                                    ->label('Baris 1 (Atas)')
                                    ->placeholder('Contoh: PEMERINTAH KABUPATEN ACEH SELATAN'),
                                
                                Forms\Components\TextInput::make('pdf_header_line2')
                                    ->label('Baris 2 (Tengah)')
                                    ->placeholder('Contoh: KECAMATAN BAKONGAN'),
                                
                                Forms\Components\TextInput::make('pdf_header_line3')
                                    ->label('Baris 3 (Bawah)')
                                    ->placeholder('Contoh: BUMDes KEUDE BAKONGAN'),
                                
                                Forms\Components\TextInput::make('pdf_footer_text')
                                    ->label('Footer PDF')
                                    ->placeholder('Contoh: Jl. Nasional No. 1, Keude Bakongan'),
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
                
                // Tab SEO
                Tabs\Tab::make('SEO & Pengaturan')
                    ->icon('heroicon-o-magnifying-glass')
                    ->schema([
                        Forms\Components\Section::make('SEO')
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label('Meta Title')
                                    ->maxLength(255)
                                    ->helperText('Judul website di search engine'),
                                
                                Forms\Components\Textarea::make('meta_description')
                                    ->label('Meta Description')
                                    ->rows(3)
                                    ->helperText('Deskripsi website di search engine'),
                                
                                Forms\Components\Textarea::make('meta_keywords')
                                    ->label('Meta Keywords')
                                    ->rows(2)
                                    ->helperText('Kata kunci untuk SEO (pisahkan dengan koma)'),
                            ]),
                        
                        Forms\Components\Section::make('Pengaturan')
                            ->schema([
                                Forms\Components\Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->default(true),
                            ]),
                    ]),
            ])
            ->columns(1);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBumdesSettings::route('/'),
            'edit' => Pages\EditBumdesSetting::route('/{record}/edit'),
        ];
    }

    /**
     * Redirect navigation to edit page directly
     */
    public static function getNavigationUrl(): string
    {
        $settings = BumdesSetting::getSettings();
        return static::getUrl('edit', ['record' => $settings]);
    }
}
