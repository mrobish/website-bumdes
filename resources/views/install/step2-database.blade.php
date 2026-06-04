@extends('install.layout')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-2">Konfigurasi Database</h2>
    <p class="text-gray-600 mb-6">Masukkan koneksi database MySQL/MariaDB</p>

    <form id="dbForm" action="{{ route('install.processStep2') }}" method="POST">
        @csrf
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Database Host</label>
                <input type="text" name="db_host" value="127.0.0.1" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Port</label>
                <input type="number" name="db_port" value="3306" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Database Name</label>
                <input type="text" name="db_database" value="bumdes" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" name="db_username" value="root" required
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" name="db_password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
        </div>

        <div class="mt-4">
            <button type="button" id="testBtn" onclick="testConnection()"
                class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                🔌 Test Koneksi
            </button>
            <span id="testResult" class="ml-3 text-sm"></span>
        </div>

        <div class="mt-6 flex justify-between">
            <a href="{{ route('install.step1') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                ← Kembali
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Lanjut ke Admin →
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function testConnection() {
    const btn = document.getElementById('testBtn');
    const result = document.getElementById('testResult');
    const form = document.getElementById('dbForm');
    const formData = new FormData(form);
    
    btn.disabled = true;
    btn.textContent = 'Testing...';
    result.textContent = '';

    fetch('{{ route('install.testDatabase') }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': formData.get('_token'),
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            db_host: formData.get('db_host'),
            db_port: formData.get('db_port'),
            db_database: formData.get('db_database'),
            db_username: formData.get('db_username'),
            db_password: formData.get('db_password'),
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            result.innerHTML = '<span class="text-green-600">✓ ' + data.message + '</span>';
        } else {
            result.innerHTML = '<span class="text-red-600">✗ ' + data.message + '</span>';
        }
    })
    .catch(err => {
        result.innerHTML = '<span class="text-red-600">✗ Error: ' + err.message + '</span>';
    })
    .finally(() => {
        btn.disabled = false;
        btn.textContent = '🔌 Test Koneksi';
    });
}
</script>
@endpush
@endsection
