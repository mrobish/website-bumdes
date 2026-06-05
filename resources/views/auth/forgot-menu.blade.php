<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masalah Masuk Akun - BUMDes</title>
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
            <i class="fas fa-life-ring text-4xl text-blue-500 mb-3"></i>
            <h1 class="text-2xl font-bold text-gray-800">Masalah Masuk Akun?</h1>
            <p class="text-gray-500 text-sm mt-2">Pilih masalah yang kamu alami</p>
        </div>

        <div class="space-y-4">
            <a href="{{ route('password.forgot-username.form') }}" 
                class="block w-full border-2 border-gray-200 rounded-lg p-4 hover:border-blue-500 hover:bg-blue-50 transition text-left">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-user text-blue-500 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">Lupa Username</h3>
                        <p class="text-sm text-gray-500">Ketahui username (email) akun Anda</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 ml-auto"></i>
                </div>
            </a>

            <a href="{{ route('password.request.form') }}" 
                class="block w-full border-2 border-gray-200 rounded-lg p-4 hover:border-blue-500 hover:bg-blue-50 transition text-left">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-lock text-orange-500 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">Lupa Password</h3>
                        <p class="text-sm text-gray-500">Reset password akun Anda via OTP</p>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400 ml-auto"></i>
                </div>
            </a>
        </div>

        <div class="mt-6 text-center">
            <a href="{{ route('login') }}" class="text-sm text-blue-500 hover:text-blue-700">
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
