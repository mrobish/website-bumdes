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
        // Share villageInfo with all views
        View::composer('*', function ($view) {
            $villageInfo = \App\Models\VillageInfo::first();
            $view->with('villageInfo', $villageInfo);
        });
    }
}
