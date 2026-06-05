<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\BumdesSetting;
use App\Models\User;
use App\Mail\OtpMail;
use App\Mail\UsernameMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class ForgotPasswordController extends Controller
{
    /**
     * Show account recovery menu (Lupa Username / Lupa Password)
     */
    public function showMenu()
    {
        return view('auth.forgot-menu');
    }

    // ============================================
    // LUPA USERNAME
    // ============================================

    /**
     * Show forgot username form
     */
    public function showForgotUsernameForm()
    {
        return view('auth.forgot-username');
    }

    /**
     * Send username to email
     */
    public function sendUsername(Request $request)
    {
        $request->validate([
            'nik' => 'required|numeric|size:16',
            'email' => 'required|email',
        ]);

        $user = User::where('nik', $request->nik)
                     ->where('email', $request->email)
                     ->first();

        // Always show success to prevent user enumeration
        $maskedEmail = $this->maskEmail($request->email);

        if ($user) {
            try {
                Mail::to($user->email)->send(new UsernameMail($user->name, $user->email));
            } catch (\Exception $e) {
                // Log error but still show success message
            }
        }

        return back()->with('status', "Jika data sesuai, username akan dikirimkan ke {$maskedEmail}");
    }

    // ============================================
    // LUPA PASSWORD
    // ============================================

    /**
     * Show forgot password form (NIK + Email)
     */
    public function showRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Validate NIK + Email, then send OTP
     */
    public function sendOtp(Request $request)
    {
        $request->validate([
            'nik' => 'required|numeric|size:16',
            'email' => 'required|email',
        ]);

        $user = User::where('nik', $request->nik)
                     ->where('email', $request->email)
                     ->first();

        if (!$user) {
            return back()->withErrors([
                'nik' => 'Kombinasi NIK dan Email tidak cocok dengan records kami.',
            ])->withInput(['email' => $request->email]);
        }

        // Check rate limiting — max 3 OTP sends per hour per email
        $recentSends = DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->where('purpose', 'reset_password')
            ->where('otp_last_send_at', '>=', now()->subHour())
            ->count();

        if ($recentSends >= 3) {
            return back()->withErrors([
                'email' => 'Terlalu banyak permintaan OTP. Silakan tunggu 1 jam lagi.',
            ])->withInput(['email' => $request->email]);
        }

        // Check if account is locked
        $existing = DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->where('purpose', 'reset_password')
            ->first();

        if ($existing && $existing->otp_locked_until && now()->lessThan($existing->otp_locked_until)) {
            $minutesLeft = now()->diffInMinutes($existing->otp_locked_until);
            return back()->withErrors([
                'email' => "Akun terkunci. Silakan coba lagi dalam {$minutesLeft} menit.",
            ])->withInput(['email' => $request->email]);
        }

        // Generate 6-digit OTP
        $otp = str_pad(random_int(100000, 999999), 6, '0', STR_PAD_LEFT);

        // Store OTP
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $user->email, 'purpose' => 'reset_password'],
            [
                'token' => Str::random(64),
                'otp' => $otp,
                'otp_attempts' => 0,
                'otp_locked_until' => null,
                'otp_send_count' => $recentSends + 1,
                'otp_last_send_at' => now(),
                'created_at' => now(),
            ]
        );

        // Send OTP email
        try {
            Mail::to($user->email)->send(new OtpMail($otp, $user->name));

            $maskedEmail = $this->maskEmail($user->email);

            return redirect()->route('password.verify-otp.form')
                ->with('status', "OTP sudah dikirim ke {$maskedEmail}. Silakan cek inbox atau spam folder.")
                ->with('email', $user->email);
        } catch (\Exception $e) {
            return back()->withErrors([
                'email' => 'Gagal mengirim email. Silakan coba lagi atau hubungi administrator.',
            ])->withInput(['email' => $request->email]);
        }
    }

    /**
     * Show OTP verification form
     */
    public function showVerifyOtpForm()
    {
        $email = session('email');
        if (!$email) {
            return redirect()->route('password.request.form');
        }
        return view('auth.verify-otp', ['email' => $email]);
    }

    /**
     * Verify OTP with brute force protection
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|string|size:6',
        ]);

        $resetToken = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('purpose', 'reset_password')
            ->first();

        if (!$resetToken) {
            return back()->withErrors(['otp' => 'OTP tidak valid. Silakan minta OTP baru.'])
                         ->withInput(['email' => $request->email]);
        }

        // Check if account is locked
        if ($resetToken->otp_locked_until && now()->lessThan($resetToken->otp_locked_until)) {
            $minutesLeft = now()->diffInMinutes($resetToken->otp_locked_until);
            return back()->withErrors(['otp' => "Akun terkunci. Silakan coba lagi dalam {$minutesLeft} menit."])
                         ->withInput(['email' => $request->email]);
        }

        // Check OTP expiry (5 minutes)
        $createdAt = $resetToken->created_at;
        if ($createdAt && now()->diffInMinutes($createdAt) > 5) {
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->where('purpose', 'reset_password')
                ->delete();
            return back()->withErrors(['otp' => 'Kode OTP telah kedaluwarsa. Silakan minta kode baru.'])
                         ->withInput(['email' => $request->email]);
        }

        // Verify OTP
        if ($resetToken->otp !== $request->otp) {
            $attempts = $resetToken->otp_attempts + 1;

            $updateData = ['otp_attempts' => $attempts];

            // Lock after 3 wrong attempts for 30 minutes
            if ($attempts >= 3) {
                $updateData['otp_locked_until'] = now()->addMinutes(30);
                $updateData['otp'] = null; // Invalidate OTP
            }

            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->where('purpose', 'reset_password')
                ->update($updateData);

            if ($attempts >= 3) {
                return back()->withErrors(['otp' => 'Terlalu banyak percobaan salah. Akun dikunci selama 30 menit.'])
                             ->withInput(['email' => $request->email]);
            }

            $remaining = 3 - $attempts;
            return back()->withErrors(['otp' => "OTP salah. Sisa percobaan: {$remaining} kali."])
                         ->withInput(['email' => $request->email]);
        }

        // OTP valid — redirect to reset password
        return redirect()->route('password.reset.form')
            ->with('email', $request->email)
            ->with('otp_verified', true);
    }

    /**
     * Show reset password form
     */
    public function showResetForm()
    {
        $email = session('email');
        $otpVerified = session('otp_verified');

        if (!$email || !$otpVerified) {
            return redirect()->route('password.request.form');
        }

        return view('auth.reset-password', ['email' => $email]);
    }

    /**
     * Reset password
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'User tidak ditemukan.']);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // Delete reset token
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('purpose', 'reset_password')
            ->delete();

        // Flush session
        session()->flush();

        return redirect()->route('login')
            ->with('status', 'Password berhasil diubah! Silakan login dengan password baru.');
    }

    /**
     * Mask email for display (e.g., an***@email.com)
     */
    protected function maskEmail(string $email): string
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) return $email;

        $name = $parts[0];
        $domain = $parts[1];

        if (strlen($name) <= 2) {
            $masked = $name[0] . '***';
        } else {
            $masked = substr($name, 0, 2) . str_repeat('*', strlen($name) - 2);
        }

        return $masked . '@' . $domain;
    }
}
