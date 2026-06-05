<?php

namespace App\Filament\Admin\Pages;

use App\Models\BumdesSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class ReCaptchaSettingsPage extends Page
{
    protected static string $view = 'filament.admin.pages.recaptcha-settings';

    protected static ?string $navigationIcon = 'heroicon-o-shield-check';
    protected static ?string $navigationGroup = '⚙️ Pengaturan';
    protected static ?string $navigationLabel = 'reCAPTCHA';
    protected static ?string $title = 'Pengaturan reCAPTCHA';
    protected static ?string $slug = 'settings/recaptcha';
    protected static ?int $navigationSort = 16;

    public ?array $data = [];

    public function mount(): void
    {
        $settings = BumdesSetting::first();

        $this->form->fill([
            'recaptcha_enabled' => $settings->recaptcha_enabled ?? false,
            'recaptcha_site_key' => $settings->recaptcha_site_key ?? '',
            'recaptcha_secret_key' => $settings->recaptcha_secret_key ?? '',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Google reCAPTCHA v2')
                    ->description('Konfigurasi keamanan login dengan reCAPTCHA')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Forms\Components\Toggle::make('recaptcha_enabled')
                            ->label('Aktifkan reCAPTCHA')
                            ->helperText('Aktifkan jika ingin menambahkan verifikasi reCAPTCHA pada halaman login')
                            ->default(false),

                        Forms\Components\TextInput::make('recaptcha_site_key')
                            ->label('Site Key')
                            ->placeholder('6Lxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx')
                            ->helperText('Dari Google reCAPTCHA Admin Console')
                            ->required()
                            ->visible(fn (Forms\Get $get) => $get('recaptcha_enabled')),

                        Forms\Components\TextInput::make('recaptcha_secret_key')
                            ->label('Secret Key')
                            ->placeholder('6Lxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx')
                            ->helperText('Dari Google reCAPTCHA Admin Console')
                            ->required()
                            ->password()
                            ->revealable()
                            ->visible(fn (Forms\Get $get) => $get('recaptcha_enabled')),

                        Forms\Components\Placeholder::make('recaptcha_url')
                            ->label('Link ke Admin Console')
                            ->content('https://www.google.com/recaptcha/admin'),

                        Forms\Components\Placeholder::make('instructions')
                            ->label('Cara Setting')
                            ->content(function () {
                                return implode("\n", [
                                    '1. Buka https://www.google.com/recaptcha/admin',
                                    '2. Pilih reCAPTCHA v2 → "I\'m not a robot" Checkbox',
                                    '3. Isi domain website (contoh: ondesa.id)',
                                    '4. Copy Site Key dan Secret Key',
                                    '5. Simpan pengaturan di sini',
                                ]);
                            }),
                    ]),

                Forms\Components\Section::make('Status')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Forms\Components\Placeholder::make('status')
                            ->label('Status')
                            ->content(function () {
                                $settings = BumdesSetting::first();
                                if ($settings && $settings->recaptcha_enabled) {
                                    return '✅ Aktif - reCAPTCHA aktif pada halaman login';
                                }
                                return '❌ Nonaktif - reCAPTCHA belum dikonfigurasi';
                            }),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $settings = BumdesSetting::first();

        if (!$settings) {
            Notification::make()
                ->title('Error')
                ->body('Konfigurasi BUMDes belum ada. Silakan isi identitas BUMDes terlebih dahulu.')
                ->danger()
                ->send();
            return;
        }

        $data = $this->form->getState();

        $settings->update([
            'recaptcha_enabled' => $data['recaptcha_enabled'],
            'recaptcha_site_key' => $data['recaptcha_enabled'] ? $data['recaptcha_site_key'] : null,
            'recaptcha_secret_key' => $data['recaptcha_enabled'] ? $data['recaptcha_secret_key'] : null,
        ]);

        Notification::make()
            ->title('Berhasil Disimpan')
            ->body('Pengaturan reCAPTCHA telah diperbarui.')
            ->success()
            ->send();
    }
}
