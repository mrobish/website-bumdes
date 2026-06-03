@extends('layouts.village')

@section('title', 'Potensi Desa - ' . ($villageInfo->village_name ?? ''))

@section('content')
<!-- Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">Potensi Desa</h1>
    <p class="text-gray-600 mt-2">Keunggulan dan potensi yang dimiliki desa kami</p>
</div>

<!-- Potensi Unggulan -->
<section class="mb-12">
    <div class="grid md:grid-cols-2 gap-8">
        <!-- Wisata -->
        @if($villageInfo->tourism_potential)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-blue-600 text-white py-4 px-6">
                <h2 class="text-xl font-bold"><i class="fas fa-mountain mr-2"></i>Potensi Wisata</h2>
            </div>
            <div class="p-6">
                <div class="text-gray-600 whitespace-pre-line">{{ $villageInfo->tourism_potential }}</div>
            </div>
        </div>
        @endif

        <!-- Pertanian -->
        @if($villageInfo->agriculture_potential)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-green-600 text-white py-4 px-6">
                <h2 class="text-xl font-bold"><i class="fas fa-seedling mr-2"></i>Potensi Pertanian</h2>
            </div>
            <div class="p-6">
                <div class="text-gray-600 whitespace-pre-line">{{ $villageInfo->agriculture_potential }}</div>
            </div>
        </div>
        @endif

        <!-- Peternakan -->
        @if($villageInfo->livestock_potential)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-amber-600 text-white py-4 px-6">
                <h2 class="text-xl font-bold"><i class="fas fa-paw mr-2"></i>Potensi Peternakan</h2>
            </div>
            <div class="p-6">
                <div class="text-gray-600 whitespace-pre-line">{{ $villageInfo->livestock_potential }}</div>
            </div>
        </div>
        @endif

        <!-- Perikanan -->
        @if($villageInfo->fishery_potential)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-cyan-600 text-white py-4 px-6">
                <h2 class="text-xl font-bold"><i class="fas fa-fish mr-2"></i>Potensi Perikanan</h2>
            </div>
            <div class="p-6">
                <div class="text-gray-600 whitespace-pre-line">{{ $villageInfo->fishery_potential }}</div>
            </div>
        </div>
        @endif

        <!-- Kerajinan -->
        @if($villageInfo->craft_potential)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-pink-600 text-white py-4 px-6">
                <h2 class="text-xl font-bold"><i class="fas fa-paint-brush mr-2"></i>Potensi Kerajinan</h2>
            </div>
            <div class="p-6">
                <div class="text-gray-600 whitespace-pre-line">{{ $villageInfo->craft_potential }}</div>
            </div>
        </div>
        @endif

        <!-- Budaya -->
        @if($villageInfo->cultural_potential)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-purple-600 text-white py-4 px-6">
                <h2 class="text-xl font-bold"><i class="fas fa-masks-theater mr-2"></i>Potensi Budaya</h2>
            </div>
            <div class="p-6">
                <div class="text-gray-600 whitespace-pre-line">{{ $villageInfo->cultural_potential }}</div>
            </div>
        </div>
        @endif

        <!-- SDA -->
        @if($villageInfo->natural_potential)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-teal-600 text-white py-4 px-6">
                <h2 class="text-xl font-bold"><i class="fas fa-leaf mr-2"></i>Potensi Sumber Daya Alam</h2>
            </div>
            <div class="p-6">
                <div class="text-gray-600 whitespace-pre-line">{{ $villageInfo->natural_potential }}</div>
            </div>
        </div>
        @endif

        <!-- SDM -->
        @if($villageInfo->human_resource_potential)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-indigo-600 text-white py-4 px-6">
                <h2 class="text-xl font-bold"><i class="fas fa-user-graduate mr-2"></i>Potensi SDM</h2>
            </div>
            <div class="p-6">
                <div class="text-gray-600 whitespace-pre-line">{{ $villageInfo->human_resource_potential }}</div>
            </div>
        </div>
        @endif
    </div>
</section>

<!-- Ekonomi -->
<section class="mb-12">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-emerald-600 text-white py-4 px-6">
            <h2 class="text-xl font-bold"><i class="fas fa-chart-bar mr-2"></i>Data Ekonomi</h2>
        </div>
        <div class="p-6">
            <div class="grid md:grid-cols-4 gap-6 mb-6">
                <div class="bg-emerald-50 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-emerald-600">{{ number_format($villageInfo->total_umskm ?? 0) }}</div>
                    <div class="text-gray-600">UMKM</div>
                </div>
                <div class="bg-blue-50 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-blue-600">{{ number_format($villageInfo->total_market ?? 0) }}</div>
                    <div class="text-gray-600">Pasar</div>
                </div>
                <div class="bg-amber-50 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-amber-600">Rp {{ number_format(($villageInfo->avg_income ?? 0) / 1000000, 1) }}M</div>
                    <div class="text-gray-600">Penghasilan Rata-rata</div>
                </div>
                <div class="bg-red-50 rounded-xl p-4 text-center">
                    <div class="text-2xl font-bold text-red-600">{{ $villageInfo->poverty_rate ?? '-' }}%</div>
                    <div class="text-gray-600">Tingkat Kemiskinan</div>
                </div>
            </div>
            <div class="grid md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-bold text-gray-800 mb-2">Komoditas Utama</h3>
                    <p class="text-gray-600">{{ $villageInfo->main_commodities ?? '-' }}</p>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 mb-2">Kegiatan Ekonomi</h3>
                    <p class="text-gray-600">{{ $villageInfo->economic_activities ?? '-' }}</p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
