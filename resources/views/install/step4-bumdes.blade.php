@extends('install.layout')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-2">Identitas BUMDes</h2>
    <p class="text-gray-600 mb-6">Informasi dasar BUMDes Anda</p>

    <form action="{{ route('install.processStep4') }}" method="POST">
        @csrf
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama BUMDes <span class="text-red-500">*</span></label>
                <input type="text" name="bumdes_name" value="{{ old('bumdes_name') }}" required
                    placeholder="Contoh: BUMDes Karya Mekar"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('bumdes_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Desa <span class="text-red-500">*</span></label>
                <input type="text" name="village_name" value="{{ old('village_name') }}" required
                    placeholder="Contoh: Karangmekar"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                @error('village_name') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Alamat Lengkap <span class="text-red-500">*</span></label>
                <textarea name="bumdes_address" required rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Jl. Raya Desa No. 1, Kecamatan, Kabupaten">{{ old('bumdes_address') }}</textarea>
                @error('bumdes_address') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">No. Telepon <span class="text-red-500">*</span></label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                        placeholder="08xxxxxxxxxx"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    @error('phone') <p class="text-red-500 text-sm mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email BUMDes</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        placeholder="info@bumdes.id"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-between">
            <a href="{{ route('install.step3') }}" class="px-4 py-2 text-gray-600 hover:text-gray-800">
                ← Kembali
            </a>
            <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Lanjut ke Selesai →
            </button>
        </div>
    </form>
</div>
@endsection
