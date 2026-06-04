<?php

namespace App\Filament\Admin\Pages;

use App\Models\BumdesSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class Setup extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.setup';
    protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $title = 'Setup BUMDes';
    protected static ?string $slug = 'setup';

    public ?array $data = [];

    // Hide from navigation after setup is complete
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public function mount(): void
    {
        $setting = BumdesSetting::first();
        if ($setting) {
            $this->form->fill($setting->toArray());
        } else {
            $this->form->fill([
                'pdf_header_line1' => 'PEMERINTAH DESA ...',
                'pdf_header_line2' => 'BADAN USAHA MILIK DESA (BUMDes)',
                'pdf_header_line3' => '...',
            ]);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Header info
                Forms\Components\Section::make('🎉 Selamat Datang!')
                    ->description('Lengkapi identitas BUMDes Anda terlebih dahulu sebelum menggunakan sistem ini.')
                    ->schema([]),

                // Identitas Wajib
                Forms\Components\Section::make('📋 Identitas BUMDes (Wajib)')
                    ->description('Data ini wajib diisi untuk melanjutkan.')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('bumdes_name')
                                ->label('Nama BUMDes')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Contoh: BUMDes Sejahtera'),
                            Forms\Components\TextInput::make('village_name')
                                ->label('Nama Desa')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Contoh: Desa Makmur'),
                        ]),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('bumdes_nib')
                                ->label('NIB (Nomor Induk Berusaha)')
                                ->maxLength(50)
                                ->placeholder('Contoh: 1234567890123'),
                            Forms\Components\TextInput::make('nomor_ahu')
                                ->label('Nomor AHU')
                                ->maxLength(50)
                                ->placeholder('Contoh: AHU-0012345-AH.01'),
                        ]),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\DatePicker::make('established_date')
                                ->label('Tanggal Berdiri'),
                            Forms\Components\TextInput::make('phone')
                                ->label('Telepon')
                                ->tel()
                                ->required()
                                ->maxLength(20)
                                ->placeholder('Contoh: 08123456789'),
                        ]),
                    ]),

                // Alamat
                Forms\Components\Section::make('📍 Alamat')
                    ->schema([
                        Forms\Components\Textarea::make('bumdes_address')
                            ->label('Alamat Lengkap')
                            ->required()
                            ->rows(2)
                            ->placeholder('Jl. Raya No. 1, RT 01/RW 02'),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('bumdes_district')
                                ->label('Kecamatan')
                                ->maxLength(100),
                            Forms\Components\TextInput::make('bumdes_regency')
                                ->label('Kabupaten/Kota')
                                ->maxLength(100),
                        ]),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('bumdes_province')
                                ->label('Provinsi')
                                ->maxLength(100),
                            Forms\Components\TextInput::make('bumdes_postal_code')
                                ->label('Kode Pos')
                                ->maxLength(10),
                        ]),
                    ]),

                // Kontak
                Forms\Components\Section::make('📞 Kontak & Media Sosial')
                    ->schema([
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('email')
                                ->label('Email')
                                ->email()
                                ->maxLength(255)
                                ->placeholder('bumdes@desa.id'),
                            Forms\Components\TextInput::make('whatsapp')
                                ->label('WhatsApp')
                                ->tel()
                                ->maxLength(20)
                                ->placeholder('08123456789'),
                        ]),
                        Forms\Components\Grid::make(2)->schema([
                            Forms\Components\TextInput::make('facebook')
                                ->label('Facebook')
                                ->maxLength(255)
                                ->placeholder('https://facebook.com/bumdes'),
                            Forms\Components\TextInput::make('instagram')
                                ->label('Instagram')
                                ->maxLength(255)
                                ->placeholder('@bumdes'),
                        ]),
                    ]),

                // Kop Surat PDF
                Forms\Components\Section::make('📄 Kop Surat PDF')
                    ->description('Untuk header laporan PDF')
                    ->schema([
                        Forms\Components\TextInput::make('pdf_header_line1')
                            ->label('Baris 1')
                            ->placeholder('PEMERINTAH DESA ...')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('pdf_header_line2')
                            ->label('Baris 2')
                            ->placeholder('BADAN USAHA MILIK DESA (BUMDes)')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('pdf_header_line3')
                            ->label('Baris 3')
                            ->placeholder('Jl. Raya No. 1, Desa Makmur')
                            ->maxLength(255),
                    ]),

                // Logo
                Forms\Components\Section::make('🖼️ Logo')
                    ->schema([
                        Forms\Components\FileUpload::make('logo_path')
                            ->label('Logo BUMDes')
                            ->image()
                            ->directory('bumdes/logo')
                            ->maxSize(2048)
                            ->helperText('Format PNG/JPG, max 2MB'),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        // Validasi field wajib
        if (empty($data['bumdes_name']) || empty($data['village_name']) || empty($data['phone']) || empty($data['bumdes_address'])) {
            Notification::make()
                ->title('Lengkapi Data Wajib!')
                ->body('Nama BUMDes, Nama Desa, Alamat, dan Telepon wajib diisi.')
                ->danger()
                ->send();
            return;
        }

        // Save to database
        $setting = BumdesSetting::first();
        if ($setting) {
            $setting->update($data);
        } else {
            BumdesSetting::create($data);
        }

        Notification::make()
            ->title('✅ Setup Berhasil!')
            ->body('Identitas BUMDes telah disimpan. Selamat menggunakan sistem!')
            ->success()
            ->duration(3000)
            ->send();

        // Redirect to dashboard
        $this->redirect(route('filament.admin.pages.dashboard-charts'));
    }
}
