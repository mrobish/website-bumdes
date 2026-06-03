<?php

namespace App\Providers\Filament;

use App\Filament\Admin\Pages\Dashboard;
use App\Filament\Admin\Widgets\StatsOverview;
use App\Filament\Admin\Widgets\WelcomeWidget;
use App\Filament\Pages\AuditLogPage;
use App\Filament\Pages\YearEndClosingPage;
use App\Models\BumdesSetting;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\View\PanelsRenderHook;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        // Load BUMDes settings for dynamic theming
        $settings = BumdesSetting::first();
        
        // Parse primary color from hex (e.g., '#F59E0B' → Color::Amber)
        $primaryColor = Color::Amber; // default
        if ($settings && $settings->primary_color) {
            $primaryColor = Color::hex($settings->primary_color);
        }

        // Parse secondary color
        $secondaryColor = Color::Gray; // default
        if ($settings && $settings->secondary_color) {
            $secondaryColor = Color::hex($settings->secondary_color);
        }

        // Build brand name
        $brandName = $settings->bumdes_name ?? 'BUMDes Admin';

        // Build brand logo HTML
        $brandLogo = null;
        if ($settings && $settings->logo_path) {
            $brandLogo = '<img src="' . asset('storage/' . $settings->logo_path) . '" alt="Logo" class="h-10 w-auto">';
        }

        return $panel
            ->id('admin')
            ->path('admin')
            // Dynamic colors from BumdesSetting
            ->colors([
                'primary' => $primaryColor,
                'secondary' => $secondaryColor,
            ])
            // Brand name & logo
            ->brandName($brandName)
            ->brandLogo($brandLogo)
            ->brandLogoHeight('2.5rem')
            // Dark mode support
            ->darkMode(true)
            // Favicon from BumdesSetting
            ->favicon($settings && $settings->logo_path ? asset('storage/' . $settings->logo_path) : null)
            ->maxContentWidth('full')
            ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\\\Filament\\\\Admin\\\\Resources')
            ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\\\Filament\\\\Admin\\\\Pages')
            ->pages([
                Dashboard::class,
                AuditLogPage::class,
                YearEndClosingPage::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\\\Filament\\\\Admin\\\\Widgets')
            ->widgets([
                WelcomeWidget::class,
                StatsOverview::class,
                AccountWidget::class,
            ])
            // Inject custom CSS based on BumdesSetting colors
            ->renderHook(
                PanelsRenderHook::HEAD_START,
                fn () => $this->getCustomThemeCss($settings)
            )
            // Inject BUMDes identity in footer
            ->renderHook(
                PanelsRenderHook::FOOTER,
                fn () => $this->getFooterHtml($settings)
            )
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }

    /**
     * Generate custom CSS based on BumdesSetting colors
     */
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
    
    /* Custom sidebar branding */
    .fi-sidebar-brand {
        transition: all 0.3s ease;
    }
    
    .fi-sidebar-brand:hover {
        transform: scale(1.02);
    }
    
    /* Custom scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    
    ::-webkit-scrollbar-track {
        background: transparent;
    }
    
    ::-webkit-scrollbar-thumb {
        background: var(--primary);
        border-radius: 4px;
    }
    
    ::-webkit-scrollbar-thumb:hover {
        background: var(--secondary);
    }
    
    /* Smooth transitions */
    .fi-btn, .fi-card, .fi-table {
        transition: all 0.2s ease;
    }
    
    /* Custom focus ring color */
    .fi-input:focus {
        box-shadow: 0 0 0 2px var(--primary);
    }
    /* User custom CSS */
    {$customCss}
</style>
HTML;
    }

    /**
     * Generate footer HTML with BUMDes identity
     */
    protected function getFooterHtml(?BumdesSetting $settings): string
    {
        if (!$settings) {
            return '';
        }

        $name = htmlspecialchars($settings->bumdes_name ?? 'BUMDes');
        $village = htmlspecialchars($settings->village_name ?? '');
        $address = htmlspecialchars($settings->bumdes_address ?? '');

        return <<<HTML
        <div class="fi-footer py-3 px-6 border-t border-gray-200 dark:border-gray-700 text-center text-sm text-gray-500 dark:text-gray-400">
            <div class="flex items-center justify-center gap-2">
                <span>{$name}</span>
                <span>•</span>
                <span>{$village}</span>
            </div>
            <div class="text-xs mt-1 opacity-75">{$address}</div>
        </div>
HTML;
    }
}
