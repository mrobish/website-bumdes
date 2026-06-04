<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Dashboard;
use App\Filament\Admin\Widgets\StatsOverview;
use App\Filament\Admin\Widgets\WelcomeWidget;
use App\Filament\Pages\AuditLogPage;
use App\Filament\Pages\YearEndClosingPage;
use App\Models\BumdesSetting;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // Load BUMDes settings safely
        try {
            $settings = BumdesSetting::first();
        } catch (\Exception $e) {
            $settings = null;
        }
        
        // Dynamic primary color
        $primaryColor = Color::Amber;
        try {
            if ($settings && $settings->primary_color) {
                $primaryColor = Color::hex($settings->primary_color);
            }
        } catch (\Exception $e) {}

        // Dynamic secondary color
        $secondaryColor = Color::Gray;
        try {
            if ($settings && $settings->secondary_color) {
                $secondaryColor = Color::hex($settings->secondary_color);
            }
        } catch (\Exception $e) {}

        // Brand name
        $brandName = $settings->bumdes_name ?? 'BUMDes Admin';

        // Brand logo
        $brandLogo = null;
        if ($settings && $settings->logo_path) {
            $brandLogo = asset('storage/' . $settings->logo_path);
        }

        return $panel
            ->id('admin')
            ->path('admin')
            ->colors([
                'primary' => $primaryColor,
                'secondary' => $secondaryColor,
            ])
            ->brandName($brandName)
            ->brandLogo($brandLogo)
            ->brandLogoHeight('2.5rem')
            ->darkMode(true)
            ->favicon($settings && $settings->logo_path ? asset('storage/' . $settings->logo_path) : null)
            ->maxContentWidth('full')
            ->middleware([
                \App\Http\Middleware\EnsureBumdesConfigured::class,
            ])
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
            ->pages([
                Dashboard::class,
                AuditLogPage::class,
                YearEndClosingPage::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
            ->widgets([
                WelcomeWidget::class,
                StatsOverview::class,
            ])
            ->renderHook(
                PanelsRenderHook::HEAD_START,
                fn () => $this->getCustomThemeCss($settings)
            )
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn () => $this->getFooterHtml($settings)
            )
            ->middleware([
                \Illuminate\Cookie\Middleware\EncryptCookies::class,
                \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
                \Illuminate\Session\Middleware\StartSession::class,
                \Illuminate\View\Middleware\ShareErrorsFromSession::class,
                \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class,
                \Illuminate\Routing\Middleware\SubstituteBindings::class,
                \Filament\Http\Middleware\DisableBladeIconComponents::class,
                \Filament\Http\Middleware\DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                \Filament\Http\Middleware\Authenticate::class,
            ]);
    }

    protected function getCustomThemeCss(?BumdesSetting $settings): string
    {
        $primary = $settings->primary_color ?? '#F59E0B';
        $secondary = $settings->secondary_color ?? '#6B7280';
        $mottoColor = $settings->motto_color ?? '#1E3A5F';
        $customCss = $settings->custom_css ?? '';

        return <<<HTML
<style>
    :root {
        --primary: {$primary};
        --secondary: {$secondary};
        --motto-color: {$mottoColor};
    }
    .fi-sidebar-brand { transition: all 0.3s ease; }
    .fi-sidebar-brand:hover { transform: scale(1.02); }
    ::-webkit-scrollbar { width: 8px; height: 8px; }
    ::-webkit-scrollbar-track { background: transparent; }
    ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 4px; }
    ::-webkit-scrollbar-thumb:hover { background: var(--secondary); }
    .fi-btn, .fi-card, .fi-table { transition: all 0.2s ease; }
    .fi-input:focus { box-shadow: 0 0 0 2px var(--primary); }
    {$customCss}
</style>
HTML;
    }

    protected function getFooterHtml(?BumdesSetting $settings): string
    {
        $name = $settings ? htmlspecialchars($settings->bumdes_name ?? 'BUMDes') : 'BUMDes';
        $village = $settings ? htmlspecialchars($settings->village_name ?? '') : '';
        $address = $settings ? htmlspecialchars($settings->bumdes_address ?? '') : '';

        return <<<HTML
<div class="py-3 px-6 border-t border-gray-200 dark:border-gray-700 text-center text-sm text-gray-500 dark:text-gray-400">
    <div class="flex items-center justify-center gap-2">
        <span>{$name}</span><span>•</span><span>{$village}</span>
    </div>
    <div class="text-xs mt-1 opacity-75">{$address}</div>
    <div class="text-xs mt-2 opacity-60">
        &copy; {$name} &middot; Dibuat oleh <strong>mrobis</strong> &middot; 
        <a href="https://github.com/mrobis/website-bumdes" target="_blank" class="hover:opacity-100">GitHub</a>
    </div>
</div>
HTML;
    }
}
