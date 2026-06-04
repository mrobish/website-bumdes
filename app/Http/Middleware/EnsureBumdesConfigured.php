<?php

namespace App\Http\Middleware;

use App\Models\BumdesSetting;
use Closure;
use Illuminate\Http\Request;

class EnsureBumdesConfigured
{
    /**
     * Handle an incoming request.
     *
     * If BUMDes identity is not configured, redirect to setup page.
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip if already on setup page
        if ($request->is('admin/setup') || $request->is('admin/setup/*')) {
            return $next($request);
        }

        // Check if BUMDes identity is configured
        $setting = BumdesSetting::first();

        if (!$setting || empty($setting->bumdes_name) || empty($setting->village_name)) {
            // Redirect to setup page
            return redirect()->route('filament.admin.pages.setup');
        }

        return $next($request);
    }
}
