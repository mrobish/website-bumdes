<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - BUMDes Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .login-bg {
            @if($bumdesSetting && $bumdesSetting->kegiatan_photo_path)
                background: linear-gradient(rgba(30, 58, 95, 0.8), rgba(30, 58, 95, 0.9)), url('{{ $bumdesSetting->kegiatan_photo_url }}');
                background-size: cover;
                background-position: center;
            @else
                background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 50%, #1e40af 100%);
            @endif
        }
    </style>
</head>
<body class="login-bg min-h-screen flex items-center justify-center">
    <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
        <div class="text-center mb-6">
            @if($bumdesSetting && $bumdesSetting->logo_path)
                <img src="{{ $bumdesSetting->logo_url }}" alt="Logo" class="h-20 mx-auto mb-4">
            @else
                <div class="w-20 h-20 bg-blue-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-store text-white text-3xl"></i>
                </div>
            @endif
            <h1 class="text-2xl font-bold text-gray-800">{{ $bumdesSetting->bumdes_name ?? 'BUMDes' }}</h1>
            <p class="text-sm text-gray-500">Admin Panel</p>
        </div>
        
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                {{ $errors->first() }}
            </div>
        @endif
        
        <form method="POST" action="{{ route('login.post') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                <input type="password" name="password" required
                    class="w-full px-3 py-2 border border-gray-300 rounded focus:outline-none focus:border-blue-500">
            </div>
            
            <div class="mb-6">
                <label class="flex items-center">
                    <input type="checkbox" name="remember" class="mr-2">
                    <span class="text-sm text-gray-600">Ingat saya</span>
                </label>
            </div>
            
            <button type="submit" 
                class="w-full bg-blue-500 text-white font-bold py-2 px-4 rounded hover:bg-blue-600 transition">
                Login
            </button>
        </form>
        
        @if($bumdesSetting && $bumdesSetting->google_login_enabled)
        <div class="flex items-center my-6">
            <div class="flex-1 border-t border-gray-300"></div>
            <span class="px-4 text-gray-500 text-sm">atau login dengan</span>
            <div class="flex-1 border-t border-gray-300"></div>
        </div>
        
        <div class="space-y-3">
            <a href="{{ route('social.redirect', 'google') }}" 
                class="flex items-center justify-center w-full bg-white border border-gray-300 text-gray-700 font-medium py-2 px-4 rounded hover:bg-gray-50 transition">
                <svg class="w-5 h-5 mr-2" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/>
                </svg>
                Google
            </a>
        </div>
        @endif
        
        <div class="mt-4 text-center">
            <a href="/" class="text-sm text-blue-500 hover:text-blue-700">
                <i class="fas fa-arrow-left mr-1"></i> Kembali ke Website
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
