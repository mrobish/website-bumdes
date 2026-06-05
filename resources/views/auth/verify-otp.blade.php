<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi OTP - BUMDes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .login-bg {
            background: linear-gradient(rgba(30, 58, 95, 0.85), rgba(30, 58, 95, 0.95)), url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=1920');
            background-size: cover;
            background-position: center;
        }
        .otp-input {
            letter-spacing: 0.5em;
            text-align: center;
            font-size: 1.5rem;
            font-weight: bold;
        }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-6">
            <i class="fas fa-shield-alt text-4xl text-blue-500 mb-3"></i>
            <h1 class="text-2xl font-bold text-gray-800">Verifikasi OTP</h1>
            <p class="text-gray-500 text-sm mt-2">Masukkan kode 6 digit yang dikirim ke</p>
            @php
                $maskedEmail = '';
                if ($email) {
                    $parts = explode('@', $email);
                    if (count($parts) === 2) {
                        $name = $parts[0];
                        $domain = $parts[1];
                        $maskedEmail = (strlen($name) <= 2 ? $name[0] . '***' : substr($name, 0, 2) . str_repeat('*', strlen($name) - 2)) . '@' . $domain;
                    }
                }
            @endphp
            <p class="text-blue-600 font-semibold text-sm">{{ $maskedEmail }}</p>
        </div>

        @if(session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-check-circle mr-1"></i> {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.verify-otp') }}">
            @csrf

            <input type="hidden" name="email" value="{{ $email }}">

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2 text-center">Kode OTP</label>
                <input type="text" name="otp" maxlength="6" pattern="[0-9]{6}" required autofocus
                    class="otp-input w-full py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="000000">
                @error('otp')
                    <p class="text-red-500 text-xs mt-1 text-center">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" 
                class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 transition">
                <i class="fas fa-check mr-1"></i> Verifikasi
            </button>
        </form>

        <div class="mt-4 text-center">
            <p class="text-gray-500 text-sm">
                Tidak menerima kode? 
                <form method="POST" action="{{ route('password.request') }}" class="inline">
                    @csrf
                    <input type="hidden" name="nik" value="">
                    <input type="hidden" name="email" value="{{ $email }}">
                    <button type="submit" class="text-blue-500 hover:text-blue-700 font-semibold">Kirim Ulang</button>
                </form>
            </p>
            <a href="{{ route('login') }}" class="text-sm text-blue-500 hover:text-blue-700 mt-2 inline-block">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Login
            </a>
        </div>
    </div>

    <div class="absolute bottom-4 left-0 right-0 text-center">
        <p class="text-white text-xs opacity-70">
            &copy; {{ date('Y') }} &middot; Dibuat oleh <strong>mrobis</strong> &middot; 
            <a href="https://github.com/mrobis/website-bumdes" target="_blank" class="hover:opacity-100">GitHub</a>
        </p>
    </div>
</body>
</html>
