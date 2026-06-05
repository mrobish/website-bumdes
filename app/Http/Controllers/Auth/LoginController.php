<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\BumdesSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect('/admin');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Verify reCAPTCHA if enabled
        $setting = BumdesSetting::first();
        if ($setting && $setting->recaptcha_enabled) {
            $recaptchaResponse = $request->input('g-recaptcha-response');
            
            if (empty($recaptchaResponse)) {
                return back()->withErrors([
                    'g-recaptcha-response' => 'Silakan centang reCAPTCHA terlebih dahulu.',
                ])->onlyInput('email');
            }
            
            // Verify with Google
            $verifyResponse = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $setting->recaptcha_secret_key,
                'response' => $recaptchaResponse,
                'remoteip' => $request->ip(),
            ]);
            
            $result = $verifyResponse->json();
            
            if (!isset($result['success']) || $result['success'] !== true) {
                return back()->withErrors([
                    'g-recaptcha-response' => 'Verifikasi reCAPTCHA gagal. Silakan coba lagi.',
                ])->onlyInput('email');
            }
        }

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/admin');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }
}
