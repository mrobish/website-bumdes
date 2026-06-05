<?php

namespace App\Filament\Admin\Pages;

use App\Models\BumdesSetting;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class MailSettingsPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-envelope';
    protected static ?string $navigationGroup = '⚙️ Pengaturan';
    protected static ?string $navigationLabel = 'Pengaturan Email';
    protected static ?string $title = 'Pengaturan Email (SMTP)';
    protected static ?string $slug = 'settings/mail';
    protected static ?int $navigationSort = 17;
    protected static string $view = 'filament.admin.pages.mail-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $setting = BumdesSetting::first();

        $this->form->fill([
            'mail_mailer' => $setting->mail_mailer ?? 'smtp',
            'mail_host' => $setting->mail_host ?? '',
            'mail_port' => $setting->mail_port ?? 587,
            'mail_username' => $setting->mail_username ?? '',
            'mail_password' => $setting->mail_password ?? '',
            'mail_encryption' => $setting->mail_encryption ?? 'tls',
            'mail_from_address' => $setting->mail_from_address ?? '',
            'mail_from_name' => $setting->mail_from_name ?? '',
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('🚀 Konfigurasi Email Server')
                    ->description('Atur SMTP untuk kirim email (lupa sandi, notifikasi, dll)')
                    ->icon('heroicon-m-server-stack')
                    ->columns(2)
                    ->schema([
                        Select::make('mail_mailer')
                            ->label('Driver Email')
                            ->options([
                                'smtp' => 'SMTP',
                                'mail' => 'PHP Mail()',
                                'sendmail' => 'Sendmail',
                                'log' => 'Log (Testing)',
                            ])
                            ->default('smtp')
                            ->required(),

                        TextInput::make('mail_host')
                            ->label('SMTP Host')
                            ->placeholder('mail.ondesa.id')
                            ->columnSpan(1),

                        TextInput::make('mail_port')
                            ->label('SMTP Port')
                            ->placeholder('587')
                            ->numeric()
                            ->default(587)
                            ->columnSpan(1),

                        Select::make('mail_encryption')
                            ->label('Enkripsi')
                            ->options([
                                'tls' => 'TLS (Port 587)',
                                'ssl' => 'SSL (Port 465)',
                                '' => 'Tanpa Enkripsi',
                            ])
                            ->default('tls'),

                        TextInput::make('mail_username')
                            ->label('Username')
                            ->placeholder('admin@ondesa.id')
                            ->columnSpan(1),

                        TextInput::make('mail_password')
                            ->label('Password')
                            ->placeholder('••••••••')
                            ->password()
                            ->revealable()
                            ->columnSpan(1),
                    ]),

                Section::make('📨 Pengirim Email')
                    ->description('Email yang akan muncul sebagai pengirim')
                    ->icon('heroicon-m-paper-airplane')
                    ->columns(2)
                    ->schema([
                        TextInput::make('mail_from_address')
                            ->label('Email Pengirim')
                            ->email()
                            ->placeholder('admin@ondesa.id')
                            ->required(),

                        TextInput::make('mail_from_name')
                            ->label('Nama Pengirim')
                            ->placeholder('BUMDes Admin')
                            ->required(),
                    ]),

                Card::make()
                    ->schema([
                        Forms\Components\Actions\Action::make('testEmail')
                            ->label('📧 Test Kirim Email')
                            ->color('warning')
                            ->icon('heroicon-m-paper-airplane')
                            ->requiresConfirmation()
                            ->modalHeading('Test Email')
                            ->modalDescription('Kirim email test ke alamat email pengirim yang sudah diisi.')
                            ->action(function () {
                                $data = $this->form->getState();

                                if (empty($data['mail_from_address'])) {
                                    Notification::make()
                                        ->title('Gagal')
                                        ->body('Isi email pengirim terlebih dahulu.')
                                        ->danger()
                                        ->send();
                                    return;
                                }

                                // Save to DB first
                                $this->save();

                                // Apply to env/config temporarily
                                config(['mail.mailers.smtp.host' => $data['mail_host']]);
                                config(['mail.mailers.smtp.port' => $data['mail_port']]);
                                config(['mail.mailers.smtp.username' => $data['mail_username']]);
                                config(['mail.mailers.smtp.password' => $data['mail_password']]);
                                config(['mail.mailers.smtp.encryption' => $data['mail_encryption']]);
                                config(['mail.mailers.smtp.transport' => $data['mail_mailer']]);
                                config(['mail.from.address' => $data['mail_from_address']]);
                                config(['mail.from.name' => $data['mail_from_name']]);

                                try {
                                    \Mail::raw('Ini adalah email test dari BUMDes Admin. Jika kamu menerima email ini, berarti konfigurasi SMTP sudah benar! ✅', function ($message) use ($data) {
                                        $message->to($data['mail_from_address'])
                                            ->subject('Test Email - BUMDes Admin')
                                            ->from($data['mail_from_address'], $data['mail_from_name']);
                                    });

                                    Notification::make()
                                        ->title('Berhasil! ✉️')
                                        ->body('Email test berhasil dikirim ke ' . $data['mail_from_address'])
                                        ->success()
                                        ->send();
                                } catch (\Exception $e) {
                                    Notification::make()
                                        ->title('Gagal Mengirim')
                                        ->body('Error: ' . $e->getMessage())
                                        ->danger()
                                        ->send();
                                }
                            })
                            ->modalSubmitActionLabel('Kirim Test Email'),

                        Forms\Components\Actions\Action::make('save')
                            ->label('💾 Simpan Pengaturan')
                            ->color('primary')
                            ->icon('heroicon-m-check')
                            ->action(function () {
                                $this->save();
                                Notification::make()
                                    ->title('Tersimpan! ✅')
                                    ->body('Pengaturan email berhasil disimpan.')
                                    ->success()
                                    ->send();
                            }),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $setting = BumdesSetting::first();
        if ($setting) {
            $setting->update($data);
        }
    }
}
