<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lupa Password - BUMDes</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .login-bg {
            background: linear-gradient(rgba(30, 58, 95, 0.85), rgba(30, 58, 95, 0.95)), url('https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=1920');
            background-size: cover;
            background-position: center;
        }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center">
    <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md">
        <div class="text-center mb-6">
            <i class="fas fa-lock text-4xl text-blue-500 mb-3"></i>
            <h1 class="text-2xl font-bold text-gray-800">Lupa Password</h1>
            <p class="text-gray-500 text-sm mt-2">Masukkan NIK dan Email Anda</p>
        </div>

        @if(session('status'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-check-circle mr-1"></i> {{ session('status') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <i class="fas fa-exclamation-circle mr-1"></i> {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.request') }}">
            @csrf

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">NIK (16 Digit)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-id-card text-gray-400"></i>
                    </span>
                    <input type="text" name="nik" maxlength="16" pattern="[0-9]{16}" required autofocus
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="3201234567890001" inputmode="numeric">
                </div>
                @error('nik')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                        <i class="fas fa-envelope text-gray-400"></i>
                    </span>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="email@contoh.com">
                </div>
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" 
                class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 transition">
                <i class="fas fa-paper-plane mr-1"></i> Kirim OTP
            </button>
        </form>

        <div class="mt-4 text-center">
            <a href="{{ route('password.menu') }}" class="text-sm text-blue-500 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-1"></i> Kembali
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
