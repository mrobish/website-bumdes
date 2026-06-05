<?php

namespace App\Filament\Admin\Pages;

use App\Models\BumdesSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Notifications\Notification;

class GoogleOAuthSettingsPage extends Page
{
    protected static string $view = 'filament.admin.pages.google-oauth-settings';

    protected static ?string $navigationIcon = 'heroicon-o-key';
    protected static ?string $navigationGroup = '⚙️ Pengaturan';
    protected static ?string $navigationLabel = 'Google Login';
    protected static ?string $title = 'Pengaturan Google OAuth';
    protected static ?string $slug = 'settings/google-oauth';
    protected static ?int $navigationSort = 15;

    public ?array $data = [];

    public function mount(): void
    {
        $settings = BumdesSetting::first();
        
        $this->form->fill([
            'google_login_enabled' => $settings->google_login_enabled ?? false,
            'google_client_id' => $settings->google_client_id ?? '',
            'google_client_secret' => $settings->google_client_secret ?? '',
            'google_redirect_uri' => $settings->google_redirect_uri ?? url('/auth/google/callback'),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Google OAuth Login')
                    ->description('Konfigurasi login dengan akun Google')
                    ->icon('heroicon-o-globe-alt')
                    ->schema([
                        Forms\Components\Toggle::make('google_login_enabled')
                            ->label('Aktifkan Google Login')
                            ->helperText('Aktifkan jika ingin pengguna bisa login dengan akun Google')
                            ->default(false),

                        Forms\Components\TextInput::make('google_client_id')
                            ->label('Client ID')
                            ->placeholder('xxxx.apps.googleusercontent.com')
                            ->helperText('Dari Google Cloud Console > APIs & Services > Credentials')
                            ->required()
                            ->visible(fn (Forms\Get $get) => $get('google_login_enabled')),

                        Forms\Components\TextInput::make('google_client_secret')
                            ->label('Client Secret')
                            ->placeholder('GOCSPX-xxxx')
                            ->helperText('Dari Google Cloud Console > APIs & Services > Credentials')
                            ->required()
                            ->password()
                            ->revealable()
                            ->visible(fn (Forms\Get $get) => $get('google_login_enabled')),

                        Forms\Components\TextInput::make('google_redirect_uri')
                            ->label('Redirect URI')
                            ->helperText('Copy URL ini ke Google Cloud Console > Authorized redirect URIs')
                            ->disabled()
                            ->dehydrated(false),

                        Forms\Components\Placeholder::make('instructions')
                            ->label('Cara Setting')
                            ->content(function () {
                                return implode("\n", [
                                    '1. Buka https://console.cloud.google.com',
                                    '2. Buat project baru atau pilih project yang ada',
                                    '3. Aktifkan Google+ API di APIs & Services',
                                    '4. Buat OAuth 2.0 credentials di Credentials',
                                    '5. Isi Authorized redirect URIs dengan URL di atas',
                                    '6. Copy Client ID dan Client Secret ke form ini',
                                    '7. Simpan pengaturan',
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
                                if ($settings->google_login_enabled) {
                                    return '✅ Aktif - Pengguna bisa login dengan Google';
                                }
                                return '❌ Nonaktif - Google Login belum dikonfigurasi';
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
            'google_login_enabled' => $data['google_login_enabled'],
            'google_client_id' => $data['google_login_enabled'] ? $data['google_client_id'] : null,
            'google_client_secret' => $data['google_login_enabled'] ? $data['google_client_secret'] : null,
            'google_redirect_uri' => url('/auth/google/callback'),
        ]);

        Notification::make()
            ->title('Berhasil Disimpan')
            ->body('Pengaturan Google OAuth telah diperbarui.')
            ->success()
            ->send();
    }
}