<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Share BumdesSetting with all views (primary source)
        View::composer('*', function ($view) {
            $bumdesSetting = \App\Models\BumdesSetting::first();
            $view->with('bumdesSetting', $bumdesSetting);
            
            // Keep villageInfo for backward compatibility (will be removed later)
            $villageInfo = \App\Models\VillageInfo::first();
            $view->with('villageInfo', $villageInfo);
        });
    }
}
