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
                                    ])->columns(2),
                                
                                Forms\Components\Section::make('Legalitas BUMDes')
                                    ->schema([
                                        Forms\Components\TextInput::make('bumdes_nib')
                                            ->label('NIB')
                                            ->maxLength(255)
                                            ->helperText('Nomor Induk Berusaha'),
                                        
                                        Forms\Components\TextInput::make('nomor_ahu')
                                            ->label('Nomor AHU')
                                            ->maxLength(255)
                                            ->helperText('Jika sudah terbit SK Kemenkumham'),
                                        
                                        Forms\Components\DatePicker::make('established_date')
                                            ->label('Tanggal Berdiri'),
                                    ])->columns(3),
                                
                                Forms\Components\Section::make('Legalitas Pendirian (Perdes)')
                                    ->schema([
                                        Forms\Components\TextInput::make('nomor_perdes')
                                            ->label('Nomor Perdes')
                                            ->maxLength(255)
                                            ->placeholder('03/PERDES/2020'),
                                        
                                        Forms\Components\DatePicker::make('tanggal_perdes')
                                            ->label('Tanggal Perdes'),
                                        
                                        Forms\Components\FileUpload::make('file_perdes_path')
                                            ->label('File Perdes')
                                            ->acceptedFileTypes(['application/pdf'])
                                            ->maxSize(5120)
                                            ->directory('bumdes/dokumen')
                                            ->helperText('Upload PDF'),
                                    ])->columns(3),
                                
                                Forms\Components\Section::make('AD/ART BUMDes')
                                    ->schema([
                                        Forms\Components\FileUpload::make('file_adart_path')
                                            ->label('File AD/ART')
                                            ->acceptedFileTypes(['application/pdf'])
                                            ->maxSize(5120)
                                            ->directory('bumdes/dokumen')
                                            ->helperText('Upload PDF Anggaran Dasar / Anggaran Rumah Tangga'),
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
                                            ->label('Tentang BUMDes')
                                            ->columnSpanFull(),
                                    ]),
                            ]),
                        
                        // ===== TAB 2: Alamat & Kontak =====
                        Tabs\Tab::make('Alamat & Kontak')
                            ->schema([
                                Forms\Components\Section::make('Alamat')
                                    ->schema([
                                        Forms\Components\Textarea::make('bumdes_address')
                                            ->label('Alamat Lengkap')
                                            ->required()
                                            ->rows(2),
                                        
                                        Forms\Components\TextInput::make('bumdes_district')
                                            ->label('Kecamatan')
                                            ->required(),
                                        
                                        Forms\Components\TextInput::make('bumdes_regency')
                                            ->label('Kabupaten/Kota')
                                            ->required(),
                                        
                                        Forms\Components\TextInput::make('bumdes_province')
                                            ->label('Provinsi')
                                            ->required(),
                                        
                                        Forms\Components\TextInput::make('bumdes_postal_code')
                                            ->label('Kode Pos')
                                            ->maxLength(10),
                                    ])->columns(2),
                                
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
                                    ])->columns(2),
                            ]),
                        
                        // ===== TAB 3: Logo & Foto =====
                        Tabs\Tab::make('Logo & Foto')
                            ->schema([
                                Forms\Components\Section::make('Logo')
                                    ->schema([
                                        Forms\Components\FileUpload::make('logo_path')
                                            ->label('Logo (Warna)')
                                            ->image()
                                            ->directory('bumdes/logo')
                                            ->maxSize(2048),
                                        
                                        Forms\Components\FileUpload::make('logo_white_path')
                                            ->label('Logo (Putih)')
                                            ->image()
                                            ->directory('bumdes/logo')
                                            ->maxSize(2048),
                                    ])->columns(2),
                                
                                Forms\Components\Section::make('Foto Latar')
                                    ->schema([
                                        Forms\Components\FileUpload::make('kantor_photo_path')
                                            ->label('Foto Kantor (Background Web)')
                                            ->image()
                                            ->directory('bumdes/foto')
                                            ->maxSize(5120),
                                        
                                        Forms\Components\FileUpload::make('kegiatan_photo_path')
                                            ->label('Foto Kegiatan (Background Login)')
                                            ->image()
                                            ->directory('bumdes/foto')
                                            ->maxSize(5120),
                                    ])->columns(2),
                                
                                Forms\Components\Section::make('Tanda Tangan')
                                    ->schema([
                                        Forms\Components\FileUpload::make('signature_photo_path')
                                            ->label('Foto Tanda Tangan')
                                            ->image()
                                            ->directory('bumdes/ttd')
                                            ->maxSize(1024),
                                    ]),
                            ]),
                        
                        // ===== TAB 4: Kop PDF =====
                        Tabs\Tab::make('Kop PDF')
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
                                
                                Forms\Components\Section::make('Stempel')
                                    ->schema([
                                        Forms\Components\FileUpload::make('pdf_stamp_path')
                                            ->label('Stempel')
                                            ->image()
                                            ->directory('bumdes/stempel')
                                            ->maxSize(1024),
                                    ]),
                            ]),
                        
                        // ===== TAB 5: Media & SEO =====
                        Tabs\Tab::make('Media & SEO')
                            ->schema([
                                Forms\Components\Section::make('Media Sosial')
                                    ->schema([
                                        Forms\Components\TextInput::make('facebook')->label('Facebook'),
                                        Forms\Components\TextInput::make('instagram')->label('Instagram'),
                                        Forms\Components\TextInput::make('youtube')->label('YouTube'),
                                        Forms\Components\TextInput::make('tiktok')->label('TikTok'),
                                        Forms\Components\TextInput::make('twitter')->label('Twitter/X'),
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
