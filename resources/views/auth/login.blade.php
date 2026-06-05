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
            @if($bumdesSetting && $bumdesSetting->recaptcha_enabled)
                <div class="mt-4 flex justify-center">
                    <div class="g-recaptcha" data-sitekey="{{ $bumdesSetting->recaptcha_site_key }}"></div>
                </div>
                @error('g-recaptcha-response')
                    <p class="text-red-500 text-xs mt-1 text-center">{{ $message }}</p>
                @enderror
            @endif
        </form>
        
                
        @if($bumdesSetting && $bumdesSetting->recaptcha_enabled)
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
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
