<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\BumdesSetting;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;

class SocialLoginController extends Controller
{
    /**
     * Redirect ke provider OAuth (Google)
     */
    public function redirect(string $driver)
    {
        $this->validateDriver($driver);
        
        $settings = BumdesSetting::first();
        
        // Cek apakah Google Login aktif
        if ($driver === 'google' && (!$settings || !$settings->google_login_enabled)) {
            return redirect('/login')->withErrors([
                'email' => 'Google Login belum dikonfigurasi.'
            ]);
        }

        // Set config dari database
        $this->setProviderConfig($driver, $settings);

        return Socialite::driver($driver)->redirect();
    }

    /**
     * Callback dari provider OAuth
     */
    public function callback(string $driver)
    {
        $this->validateDriver($driver);

        try {
            $settings = BumdesSetting::first();
            
            // Set config dari database
            $this->setProviderConfig($driver, $settings);

            $socialUser = Socialite::driver($driver)->user();
            
            // Cari user berdasarkan email
            $user = User::where('email', $socialUser->getEmail())->first();

            if ($user) {
                // User sudah ada, update social info
                $user->update([
                    'social_id' => $socialUser->getId(),
                    'social_driver' => $driver,
                    'avatar' => $socialUser->getAvatar(),
                ]);
            } else {
                // Buat user baru
                $user = User::create([
                    'name' => $socialUser->getName() ?? $socialUser->getNickname() ?? 'User',
                    'email' => $socialUser->getEmail(),
                    'password' => Hash::make(Str::random(32)),
                    'role' => 'viewer',
                    'social_id' => $socialUser->getId(),
                    'social_driver' => $driver,
                    'avatar' => $socialUser->getAvatar(),
                    'email_verified_at' => now(),
                ]);
            }

            // Login user
            Auth::login($user, true);

            return redirect()->intended('/admin');

        } catch (InvalidStateException $e) {
            return redirect('/login')->withErrors([
                'email' => 'Terjadi kesalahan saat login. Silakan coba lagi.'
            ]);
        } catch (\Exception $e) {
            return redirect('/login')->withErrors([
                'email' => 'Gagal login dengan ' . ucfirst($driver) . ': ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Set provider config dari database
     */
    protected function setProviderConfig(string $driver, ?BumdesSetting $settings): void
    {
        if ($driver === 'google' && $settings) {
            config([
                'services.google.client_id' => $settings->google_client_id,
                'services.google.client_secret' => $settings->google_client_secret,
                'services.google.redirect' => $settings->google_redirect_uri ?? url('/auth/google/callback'),
            ]);
        }
    }

    /**
     * Validate driver
     */
    protected function validateDriver(string $driver): void
    {
        if (!in_array($driver, ['google'])) {
            abort(404);
        }
    }
}
