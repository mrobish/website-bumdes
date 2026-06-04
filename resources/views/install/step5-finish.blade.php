@extends('install.layout')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <div id="installing" class="text-center py-8">
        <div class="animate-spin w-16 h-16 border-4 border-blue-500 border-t-transparent rounded-full mx-auto mb-4"></div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Menginstalasi Sistem...</h2>
        <p class="text-gray-600">Mohon tunggu, proses ini mungkin memakan waktu beberapa menit.</p>
        <div id="progress" class="mt-4 text-sm text-gray-500"></div>
    </div>

    <div id="success" class="hidden text-center py-8">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Instalasi Berhasil!</h2>
        <p class="text-gray-600 mb-6">Sistem BUMDes Management siap digunakan.</p>
        
        <div class="bg-gray-50 rounded-lg p-4 mb-6 text-left max-w-md mx-auto">
            <h3 class="font-semibold text-gray-900 mb-3">Ringkasan Instalasi:</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Database:</span>
                    <span class="font-medium">{{ session('install.db_database') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Admin:</span>
                    <span class="font-medium">{{ session('install.admin_email') }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">BUMDes:</span>
                    <span class="font-medium">{{ session('install.bumdes_name') }}</span>
                </div>
            </div>
        </div>

        <a href="/admin" class="inline-block px-8 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-lg font-medium">
            🚀 Masuk ke Dashboard
        </a>
    </div>

    <div id="error" class="hidden text-center py-8">
        <div class="w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-bold text-gray-900 mb-2">Instalasi Gagal</h2>
        <p id="errorMessage" class="text-red-600 mb-6"></p>
        <a href="{{ route('install.step1') }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
            ← Mulai Ulang
        </a>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const progress = document.getElementById('progress');
    const steps = [
        'Menulis konfigurasi .env...',
        'Menjalankan migrasi database...',
        'Membuat data awal...',
        'Membuat akun admin...',
        'Menyimpan identitas BUMDes...',
        'Membersihkan cache...',
    ];

    let i = 0;
    const interval = setInterval(() => {
        if (i < steps.length) {
            progress.textContent = steps[i];
            i++;
        }
    }, 500);

    fetch('{{ route('install.processInstall') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json',
        },
    })
    .then(res => res.json())
    .then(data => {
        clearInterval(interval);
        document.getElementById('installing').classList.add('hidden');
        
        if (data.success) {
            document.getElementById('success').classList.remove('hidden');
        } else {
            document.getElementById('errorMessage').textContent = data.message;
            document.getElementById('error').classList.remove('hidden');
        }
    })
    .catch(err => {
        clearInterval(interval);
        document.getElementById('installing').classList.add('hidden');
        document.getElementById('errorMessage').textContent = 'Error: ' + err.message;
        document.getElementById('error').classList.remove('hidden');
    });
});
</script>
@endpush
@endsection
