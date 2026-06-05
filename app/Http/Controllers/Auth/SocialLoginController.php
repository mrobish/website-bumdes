<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    /**
     * Redirect ke provider OAuth (Google/Facebook)
     */
    public function redirect(string $driver)
    {
        $this->validateDriver($driver);
        return Socialite::driver($driver)->redirect();
    }

    /**
     * Callback dari provider OAuth
     */
    public function callback(string $driver)
    {
        $this->validateDriver($driver);

        try {
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

        } catch (\Exception $e) {
            return redirect('/login')->withErrors([
                'email' => 'Gagal login dengan ' . ucfirst($driver) . ': ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Validate driver
     */
    protected function validateDriver(string $driver): void
    {
        if (!in_array($driver, ['google', 'facebook'])) {
            abort(404);
        }
    }
}
