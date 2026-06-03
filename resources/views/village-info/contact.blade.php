@extends('layouts.village')

@section('title', 'Kontak - ' . ($villageInfo->village_name ?? ''))

@section('content')
<!-- Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Kontak Kami</h1>
    <p class="text-gray-600 mt-2">Hubungi pemerintah desa dan BUMDes</p>
</div>

<div class="grid md:grid-cols-2 gap-8">
    <!-- Informasi Kontak -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-blue-600 text-white py-4 px-6">
            <h2 class="text-xl font-bold"><i class="fas fa-address-card mr-2"></i>Informasi Kontak</h2>
        </div>
        <div class="p-6">
            <div class="space-y-4">
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-map-marker-alt text-blue-600"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Alamat</h3>
                        <p class="text-gray-600">{{ $villageInfo->address ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-phone text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Telepon</h3>
                        <p class="text-gray-600">{{ $villageInfo->contact_phone ?? $villageInfo->phone ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-emerald-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fab fa-whatsapp text-emerald-600"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">WhatsApp</h3>
                        <p class="text-gray-600">{{ $villageInfo->contact_whatsapp ?? '-' }}</p>
                    </div>
                </div>
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-envelope text-red-600"></i>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-800">Email</h3>
                        <p class="text-gray-600">{{ $villageInfo->contact_email ?? $villageInfo->email ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Media Sosial -->
            <div class="mt-8 pt-6 border-t">
                <h3 class="font-semibold text-gray-800 mb-4">Media Sosial</h3>
                <div class="flex space-x-4">
                    @if($villageInfo->facebook)
                        <a href="{{ $villageInfo->facebook }}" target="_blank" class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white hover:bg-blue-600 transition">
                            <i class="fab fa-facebook-f text-xl"></i>
                        </a>
                    @endif
                    @if($villageInfo->instagram)
                        <a href="{{ $villageInfo->instagram }}" target="_blank" class="w-12 h-12 bg-pink-500 rounded-full flex items-center justify-center text-white hover:bg-pink-600 transition">
                            <i class="fab fa-instagram text-xl"></i>
                        </a>
                    @endif
                    @if($villageInfo->youtube)
                        <a href="{{ $villageInfo->youtube }}" target="_blank" class="w-12 h-12 bg-red-500 rounded-full flex items-center justify-center text-white hover:bg-red-600 transition">
                            <i class="fab fa-youtube text-xl"></i>
                        </a>
                    @endif
                    @if($villageInfo->tiktok)
                        <a href="{{ $villageInfo->tiktok }}" target="_blank" class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center text-white hover:bg-gray-900 transition">
                            <i class="fab fa-tiktok text-xl"></i>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Peta Lokasi -->
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-green-600 text-white py-4 px-6">
            <h2 class="text-xl font-bold"><i class="fas fa-map mr-2"></i>Peta Lokasi</h2>
        </div>
        <div class="p-6">
            <div class="bg-gray-200 rounded-xl h-80 flex items-center justify-center">
                <div class="text-center text-gray-500">
                    <i class="fas fa-map-marked-alt text-6xl mb-4"></i>
                    <p>Peta lokasi kantor desa</p>
                    <p class="text-sm">(Integrasikan dengan Google Maps)</p>
                </div>
            </div>
            <div class="mt-4 text-sm text-gray-600">
                <p><strong>Alamat:</strong> {{ $villageInfo->address ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Jam Operasional -->
<section class="mt-8">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-amber-600 text-white py-4 px-6">
            <h2 class="text-xl font-bold"><i class="fas fa-clock mr-2"></i>Jam Operasional</h2>
        </div>
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold text-gray-800 mb-2">Kantor Desa</h3>
                    <div class="space-y-1 text-gray-600">
                        <p>Senin - Jumat: 08.00 - 16.00 WIB</p>
                        <p>Sabtu: 08.00 - 12.00 WIB</p>
                        <p>Minggu & Hari Libur: Tutup</p>
                    </div>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 mb-2">{{ $villageInfo->bumdes_name ?? 'BUMDes' }}</h3>
                    <div class="space-y-1 text-gray-600">
                        <p>Senin - Sabtu: 08.00 - 17.00 WIB</p>
                        <p>Minggu: 08.00 - 12.00 WIB</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
