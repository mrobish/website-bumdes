<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0;padding:0;background-color:#f3f4f6;font-family:Arial,sans-serif;">
    <div style="max-width:600px;margin:0 auto;padding:20px;">
        <div style="background-color:#ffffff;border-radius:8px;padding:30px;box-shadow:0 2px 4px rgba(0,0,0,0.1);">
            <div style="text-align:center;margin-bottom:20px;">
                <div style="background-color:#1e3a5f;color:#ffffff;padding:15px;border-radius:8px 8px 0 0;">
                    <h1 style="margin:0;font-size:20px;">🔐 Kode OTP Reset Password</h1>
                </div>
            </div>
            
            <p style="color:#374151;font-size:16px;">Halo <strong>{{ $userName }}</strong>,</p>
            
            <p style="color:#374151;font-size:16px;">Kami menerima permintaan reset password untuk akun Anda. Berikut adalah kode OTP Anda:</p>
            
            <div style="text-align:center;margin:30px 0;">
                <div style="background-color:#f3f4f6;border:2px dashed #1e3a5f;border-radius:8px;padding:20px;">
                    <span style="font-size:36px;font-weight:bold;color:#1e3a5f;letter-spacing:8px;">{{ $otp }}</span>
                </div>
            </div>
            
            <p style="color:#6b7280;font-size:14px;text-align:center;">⏰ Kode ini berlaku selama <strong>10 menit</strong>.</p>
            
            <hr style="border:none;border-top:1px solid #e5e7eb;margin:20px 0;">
            
            <p style="color:#6b7280;font-size:14px;">Jika Anda tidak meminta reset password, abaikan email ini. Akun Anda tetap aman.</p>
            
            <p style="color:#374151;font-size:16px;">Terima kasih,<br><strong>{{ config('app.name') }}</strong></p>
        </div>
        
        <div style="text-align:center;margin-top:20px;">
            <p style="color:#9ca3af;font-size:12px;">
                &copy; {{ date('Y') }} &middot; Dibuat oleh <strong>mrobis</strong>
            </p>
        </div>
    </div>
</body>
</html>
