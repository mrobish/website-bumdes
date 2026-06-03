@extends('layouts.village')

@section('title', 'BUMDes - ' . ($villageInfo->village_name ?? ''))

@section('content')
<!-- Header -->
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">{{ $villageInfo->bumdes_name ?? 'BUMDes' }}</h1>
    <p class="text-gray-600 mt-2">Badan Usaha Milik Desa</p>
</div>

<!-- Profil BUMDes -->
<section class="mb-12">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-green-600 text-white py-4 px-6">
            <h2 class="text-xl font-bold"><i class="fas fa-store mr-2"></i>Profil BUMDes</h2>
        </div>
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-8">
                <div>
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <span class="font-semibold text-gray-700 w-40">Nama BUMDes</span>
                            <span class="text-gray-600">: {{ $villageInfo->bumdes_name ?? '-' }}</span>
                        </div>
                        <div class="flex items-start">
                            <span class="font-semibold text-gray-700 w-40">No. Legalitas</span>
                            <span class="text-gray-600">: {{ $villageInfo->bumdes_legal_number ?? '-' }}</span>
                        </div>
                        <div class="flex items-start">
                            <span class="font-semibold text-gray-700 w-40">Tanggal Pendirian</span>
                            <span class="text-gray-600">: {{ $villageInfo->bumdes_established ? \Carbon\Carbon::parse($villageInfo->bumdes_established)->format('d F Y') : '-' }}</span>
                        </div>
                        <div class="flex items-start">
                            <span class="font-semibold text-gray-700 w-40">Jumlah Karyawan</span>
                            <span class="text-gray-600">: {{ $villageInfo->bumdes_employees ?? '-' }} orang</span>
                        </div>
                        <div class="flex items-start">
                            <span class="font-semibold text-gray-700 w-40">Jumlah Mitra</span>
                            <span class="text-gray-600">: {{ $villageInfo->bumdes_partners ?? '-' }} mitra</span>
                        </div>
                    </div>
                </div>
                <div>
                    <h3 class="font-bold text-gray-800 mb-4">Visi BUMDes</h3>
                    <div class="bg-green-50 rounded-xl p-4">
                        <p class="text-gray-700 italic">"{{ $villageInfo->bumdes_vision ?? '-' }}"</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Keuangan BUMDes -->
<section class="mb-12">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-blue-600 text-white py-4 px-6">
            <h2 class="text-xl font-bold"><i class="fas fa-chart-line mr-2"></i>Keuangan BUMDes</h2>
        </div>
        <div class="p-6">
            <div class="grid md:grid-cols-4 gap-6">
                <div class="bg-blue-50 rounded-xl p-6 text-center">
                    <div class="text-2xl font-bold text-blue-600">Rp {{ number_format(($villageInfo->bumdes_initial_capital ?? 0) / 1000000, 0) }}M</div>
                    <div class="text-gray-600 mt-2">Modal Awal</div>
                </div>
                <div class="bg-green-50 rounded-xl p-6 text-center">
                    <div class="text-2xl font-bold text-green-600">Rp {{ number_format(($villageInfo->bumdes_current_capital ?? 0) / 1000000, 0) }}M</div>
                    <div class="text-gray-600 mt-2">Modal Saat Ini</div>
                </div>
                <div class="bg-amber-50 rounded-xl p-6 text-center">
                    <div class="text-2xl font-bold text-amber-600">Rp {{ number_format(($villageInfo->bumdes_annual_revenue ?? 0) / 1000000, 0) }}M</div>
                    <div class="text-gray-600 mt-2">Omset/Tahun</div>
                </div>
                <div class="bg-purple-50 rounded-xl p-6 text-center">
                    <div class="text-2xl font-bold text-purple-600">Rp {{ number_format(($villageInfo->bumdes_profit ?? 0) / 1000000, 0) }}M</div>
                    <div class="text-gray-600 mt-2">Laba/Tahun</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Misi & Layanan -->
<section class="mb-12">
    <div class="grid md:grid-cols-2 gap-8">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-purple-600 text-white py-4 px-6">
                <h2 class="text-xl font-bold"><i class="fas fa-rocket mr-2"></i>Misi BUMDes</h2>
            </div>
            <div class="p-6">
                <div class="text-gray-600 whitespace-pre-line">{{ $villageInfo->bumdes_mission ?? '-' }}</div>
            </div>
        </div>
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="bg-amber-600 text-white py-4 px-6">
                <h2 class="text-xl font-bold"><i class="fas fa-concierge-bell mr-2"></i>Layanan BUMDes</h2>
            </div>
            <div class="p-6">
                <div class="text-gray-600 whitespace-pre-line">{{ $villageInfo->bumdes_services ?? '-' }}</div>
            </div>
        </div>
    </div>
</section>

<!-- Pencapaian -->
@if($villageInfo->bumdes_achievements)
<section class="mb-12">
    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="bg-indigo-600 text-white py-4 px-6">
            <h2 class="text-xl font-bold"><i class="fas fa-trophy mr-2"></i>Pencapaian</h2>
        </div>
        <div class="p-6">
            <div class="text-gray-600 whitespace-pre-line">{{ $villageInfo->bumdes_achievements }}</div>
        </div>
    </div>
</section>
@endif
@endsection
