<?php

namespace App\Providers;

use App\Models\BumdesSetting;
use Illuminate\Support\ServiceProvider;

class MailSettingsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMailSettings();
    }

    protected function loadMailSettings(): void
    {
        try {
            if (!\Illuminate\Support\Facades\Schema::hasTable('bumdes_settings')) {
                return;
            }

            $setting = BumdesSetting::first();

            if (!$setting) {
                return;
            }

            // Apply mail settings from database to config
            $mailConfig = [
                'default' => $setting->mail_mailer ?? config('mail.default'),
                'mailers.smtp.host' => $setting->mail_host ?? config('mail.mailers.smtp.host'),
                'mail.mailers.smtp.port' => (int) ($setting->mail_port ?? config('mail.mailers.smtp.port')),
                'mail.mailers.smtp.username' => $setting->mail_username ?? config('mail.mailers.smtp.username'),
                'mail.mailers.smtp.password' => $setting->mail_password ?? config('mail.mailers.smtp.password'),
                'mail.mailers.smtp.encryption' => $setting->mail_encryption ?? config('mail.mailers.smtp.encryption'),
                'mail.from.address' => $setting->mail_from_address ?? config('mail.from.address'),
                'mail.from.name' => $setting->mail_from_name ?? config('mail.from.name'),
            ];

            foreach ($mailConfig as $key => $value) {
                config([$key => $value]);
            }
        } catch (\Exception $e) {
            // Silently fail if table doesn't exist yet (during migration)
        }
    }
}
