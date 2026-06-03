@extends('layouts.village')

@section('title', 'Profil Desa')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient text-white py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Profil Desa</h1>
            <p class="text-lg opacity-80">{{ $villageInfo->village_name }}, {{ $villageInfo->district_name }}, {{ $villageInfo->regency_name }}</p>
        </div>
    </div>
</section>

<!-- Breadcrumb -->
<section class="bg-white py-4 shadow-sm">
    <div class="max-w-7xl mx-auto px-4">
        <nav class="text-sm text-gray-600">
            <a href="/" class="hover:text-primary-600 transition"><i class="fas fa-home mr-1"></i> Beranda</a>
            <span class="mx-2">/</span>
            <span class="text-primary-600 font-medium">Profil Desa</span>
        </nav>
    </div>
</section>

<!-- Content -->
<section class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Identity Card -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
            <div class="bg-primary-600 text-white p-6">
                <h2 class="text-2xl font-bold"><i class="fas fa-building mr-3"></i>Identitas Desa</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-home w-8 text-primary-600"></i>
                            <div class="ml-3">
                                <p class="text-sm text-gray-500">Nama Desa</p>
                                <p class="font-bold text-gray-800">{{ $villageInfo->village_name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-code w-8 text-primary-600"></i>
                            <div class="ml-3">
                                <p class="text-sm text-gray-500">Kode Desa</p>
                                <p class="font-bold text-gray-800">{{ $villageInfo->village_code ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-map-marker-alt w-8 text-primary-600"></i>
                            <div class="ml-3">
                                <p class="text-sm text-gray-500">Kecamatan</p>
                                <p class="font-bold text-gray-800">{{ $villageInfo->district_name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-map w-8 text-primary-600"></i>
                            <div class="ml-3">
                                <p class="text-sm text-gray-500">Kabupaten</p>
                                <p class="font-bold text-gray-800">{{ $villageInfo->regency_name }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="space-y-4">
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-province w-8 text-primary-600"></i>
                            <div class="ml-3">
                                <p class="text-sm text-gray-500">Provinsi</p>
                                <p class="font-bold text-gray-800">{{ $villageInfo->province_name }}</p>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-mail-bulk w-8 text-primary-600"></i>
                            <div class="ml-3">
                                <p class="text-sm text-gray-500">Kode Pos</p>
                                <p class="font-bold text-gray-800">{{ $villageInfo->postal_code ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-envelope w-8 text-primary-600"></i>
                            <div class="ml-3">
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-bold text-gray-800">{{ $villageInfo->email ?? '-' }}</p>
                            </div>
                        </div>
                        <div class="flex items-center p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-phone w-8 text-primary-600"></i>
                            <div class="ml-3">
                                <p class="text-sm text-gray-500">Telepon</p>
                                <p class="font-bold text-gray-800">{{ $villageInfo->phone ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-500 mb-1"><i class="fas fa-map-pin mr-2"></i>Alamat Lengkap</p>
                    <p class="text-gray-800">{{ $villageInfo->address }}</p>
                </div>
            </div>
        </div>

        <!-- Government Section -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
            <div class="bg-primary-600 text-white p-6">
                <h2 class="text-2xl font-bold"><i class="fas fa-landmark mr-3"></i>Pemerintahan Desa</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div class="p-4 bg-gray-50 rounded-lg">
                            <div class="flex items-center mb-2">
                                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                                    <i class="fas fa-user-tie text-primary-600"></i>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-gray-500">Kepala Desa</p>
                                    <p class="font-bold text-gray-800">{{ $villageInfo->head_village_name }}</p>
                                </div>
                            </div>
                            @if($villageInfo->head_village_nip)
                                <p class="text-sm text-gray-600 ml-15">NIP: {{ $villageInfo->head_village_nip }}</p>
                            @endif
                        </div>
                        
                        @if($villageInfo->village_secretary_name)
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                                        <i class="fas fa-user text-primary-600"></i>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-gray-500">Sekretaris Desa</p>
                                        <p class="font-bold text-gray-800">{{ $villageInfo->village_secretary_name }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <div class="space-y-4">
                        @if($villageInfo->total_staff)
                            <div class="p-4 bg-gray-50 rounded-lg text-center">
                                <i class="fas fa-users text-3xl text-primary-600 mb-2"></i>
                                <h4 class="text-2xl font-bold text-gray-800">{{ $villageInfo->total_staff }}</h4>
                                <p class="text-sm text-gray-600">Total Perangkat Desa</p>
                            </div>
                        @endif
                        
                        @if($villageInfo->total_bpd_members)
                            <div class="p-4 bg-gray-50 rounded-lg text-center">
                                <i class="fas fa-gavel text-3xl text-primary-600 mb-2"></i>
                                <h4 class="text-2xl font-bold text-gray-800">{{ $villageInfo->total_bpd_members }}</h4>
                                <p class="text-sm text-gray-600">Anggota BPD</p>
                            </div>
                        @endif
                    </div>
                </div>
                
                @if($villageInfo->head_village_start && $villageInfo->head_village_end)
                    <div class="mt-6 p-4 bg-primary-50 rounded-lg">
                        <p class="text-sm text-primary-700">
                            <i class="fas fa-calendar mr-2"></i>
                            Masa Jabatan: {{ \Carbon\Carbon::parse($villageInfo->head_village_start)->format('d M Y') }} - {{ \Carbon\Carbon::parse($villageInfo->head_village_end)->format('d M Y') }}
                        </p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Geography Section -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
            <div class="bg-primary-600 text-white p-6">
                <h2 class="text-2xl font-bold"><i class="fas fa-mountain mr-3"></i>Geografi Desa</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <i class="fas fa-ruler-combined text-2xl text-primary-600 mb-2"></i>
                        <h4 class="text-xl font-bold text-gray-800">{{ number_format($villageInfo->area_total ?? 0, 2, ',', '.') }}</h4>
                        <p class="text-xs text-gray-600">Luas Total (Ha)</p>
                    </div>
                    @if($villageInfo->elevation)
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <i class="fas fa-mountain text-2xl text-primary-600 mb-2"></i>
                            <h4 class="text-xl font-bold text-gray-800">{{ $villageInfo->elevation }}</h4>
                            <p class="text-xs text-gray-600">Ketinggian (mdpl)</p>
                        </div>
                    @endif
                    @if($villageInfo->rainfall)
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <i class="fas fa-cloud-rain text-2xl text-primary-600 mb-2"></i>
                            <h4 class="text-xl font-bold text-gray-800">{{ $villageInfo->rainfall }}</h4>
                            <p class="text-xs text-gray-600">Curah Hujan (mm)</p>
                        </div>
                    @endif
                    @if($villageInfo->climate_type)
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <i class="fas fa-sun text-2xl text-primary-600 mb-2"></i>
                            <h4 class="text-xl font-bold text-gray-800">{{ $villageInfo->climate_type }}</h4>
                            <p class="text-xs text-gray-600">Jenis Iklim</p>
                        </div>
                    @endif
                </div>
                
                <!-- Borders -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if($villageInfo->border_north)
                        <div class="flex items-center p-3 bg-red-50 rounded-lg">
                            <i class="fas fa-arrow-up w-6 text-red-500"></i>
                            <div class="ml-3">
                                <p class="text-xs text-gray-500">Batas Utara</p>
                                <p class="text-sm font-medium text-gray-800">{{ $villageInfo->border_north }}</p>
                            </div>
                        </div>
                    @endif
                    @if($villageInfo->border_south)
                        <div class="flex items-center p-3 bg-blue-50 rounded-lg">
                            <i class="fas fa-arrow-down w-6 text-blue-500"></i>
                            <div class="ml-3">
                                <p class="text-xs text-gray-500">Batas Selatan</p>
                                <p class="text-sm font-medium text-gray-800">{{ $villageInfo->border_south }}</p>
                            </div>
                        </div>
                    @endif
                    @if($villageInfo->border_east)
                        <div class="flex items-center p-3 bg-green-50 rounded-lg">
                            <i class="fas fa-arrow-right w-6 text-green-500"></i>
                            <div class="ml-3">
                                <p class="text-xs text-gray-500">Batas Timur</p>
                                <p class="text-sm font-medium text-gray-800">{{ $villageInfo->border_east }}</p>
                            </div>
                        </div>
                    @endif
                    @if($villageInfo->border_west)
                        <div class="flex items-center p-3 bg-yellow-50 rounded-lg">
                            <i class="fas fa-arrow-left w-6 text-yellow-500"></i>
                            <div class="ml-3">
                                <p class="text-xs text-gray-500">Batas Barat</p>
                                <p class="text-sm font-medium text-gray-800">{{ $villageInfo->border_west }}</p>
                            </div>
                        </div>
                    @endif
                </div>
                
                @if($villageInfo->topography)
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-500 mb-1"><i class="fas fa-mountain mr-2"></i>Topografi</p>
                        <p class="text-gray-800">{{ $villageInfo->topography }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Demographics Section -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
            <div class="bg-primary-600 text-white p-6">
                <h2 class="text-2xl font-bold"><i class="fas fa-users mr-3"></i>Demografi</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="stat-card rounded-xl p-4 text-center text-white">
                        <i class="fas fa-users text-2xl mb-2 opacity-80"></i>
                        <h4 class="text-2xl font-bold">{{ number_format($villageInfo->total_population ?? 0) }}</h4>
                        <p class="text-xs opacity-80">Total Penduduk</p>
                    </div>
                    <div class="bg-blue-50 rounded-xl p-4 text-center">
                        <i class="fas fa-mars text-2xl text-blue-600 mb-2"></i>
                        <h4 class="text-2xl font-bold text-blue-800">{{ number_format($villageInfo->male_population ?? 0) }}</h4>
                        <p class="text-xs text-blue-600">Laki-laki</p>
                    </div>
                    <div class="bg-pink-50 rounded-xl p-4 text-center">
                        <i class="fas fa-venus text-2xl text-pink-600 mb-2"></i>
                        <h4 class="text-2xl font-bold text-pink-800">{{ number_format($villageInfo->female_population ?? 0) }}</h4>
                        <p class="text-xs text-pink-600">Perempuan</p>
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 text-center">
                        <i class="fas fa-home text-2xl text-gray-600 mb-2"></i>
                        <h4 class="text-2xl font-bold text-gray-800">{{ number_format($villageInfo->total_family ?? 0) }}</h4>
                        <p class="text-xs text-gray-600">Kepala Keluarga</p>
                    </div>
                </div>
                
                <div class="grid grid-cols-3 md:grid-cols-6 gap-4 mt-4">
                    @if($villageInfo->total_dusun)
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <h5 class="text-lg font-bold text-gray-800">{{ $villageInfo->total_dusun }}</h5>
                            <p class="text-xs text-gray-600">Dusun</p>
                        </div>
                    @endif
                    @if($villageInfo->total_rt)
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <h5 class="text-lg font-bold text-gray-800">{{ $villageInfo->total_rt }}</h5>
                            <p class="text-xs text-gray-600">RT</p>
                        </div>
                    @endif
                    @if($villageInfo->total_rw)
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <h5 class="text-lg font-bold text-gray-800">{{ $villageInfo->total_rw }}</h5>
                            <p class="text-xs text-gray-600">RW</p>
                        </div>
                    @endif
                    @if($villageInfo->population_density)
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <h5 class="text-lg font-bold text-gray-800">{{ number_format($villageInfo->population_density) }}</h5>
                            <p class="text-xs text-gray-600">Kepadatan (/Ha)</p>
                        </div>
                    @endif
                    @if($villageInfo->birth_rate)
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <h5 class="text-lg font-bold text-gray-800">{{ $villageInfo->birth_rate }}</h5>
                            <p class="text-xs text-gray-600">Angka Kelahiran</p>
                        </div>
                    @endif
                    @if($villageInfo->death_rate)
                        <div class="bg-gray-50 rounded-lg p-3 text-center">
                            <h5 class="text-lg font-bold text-gray-800">{{ $villageInfo->death_rate }}</h5>
                            <p class="text-xs text-gray-600">Angka Kematian</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Infrastructure Section -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
            <div class="bg-primary-600 text-white p-6">
                <h2 class="text-2xl font-bold"><i class="fas fa-road mr-3"></i>Infrastruktur</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        @if($villageInfo->total_road_length)
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-gray-600"><i class="fas fa-road mr-2"></i>Total Jalan</span>
                                    <span class="font-bold">{{ $villageInfo->total_road_length }} Km</span>
                                </div>
                                @if($villageInfo->road_paved)
                                    <div class="w-full bg-gray-200 rounded-full h-2">
                                        <div class="bg-green-500 h-2 rounded-full" style="width: {{ ($villageInfo->road_paved / $villageInfo->total_road_length) * 100 }}%"></div>
                                    </div>
                                    <p class="text-xs text-gray-500 mt-1">Aspal/Beton: {{ $villageInfo->road_paved }} Km</p>
                                @endif
                            </div>
                        @endif
                    </div>
                    
                    <div class="space-y-4">
                        @if($villageInfo->electricity_coverage)
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-gray-600"><i class="fas fa-bolt mr-2"></i>Cakupan Listrik</span>
                                    <span class="font-bold">{{ $villageInfo->electricity_coverage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-yellow-500 h-2 rounded-full" style="width: {{ $villageInfo->electricity_coverage }}%"></div>
                                </div>
                            </div>
                        @endif
                        
                        @if($villageInfo->clean_water_coverage)
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-gray-600"><i class="fas fa-tint mr-2"></i>Cakupan Air Bersih</span>
                                    <span class="font-bold">{{ $villageInfo->clean_water_coverage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-500 h-2 rounded-full" style="width: {{ $villageInfo->clean_water_coverage }}%"></div>
                                </div>
                            </div>
                        @endif
                        
                        @if($villageInfo->internet_coverage)
                            <div class="p-4 bg-gray-50 rounded-lg">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm text-gray-600"><i class="fas fa-wifi mr-2"></i>Cakupan Internet</span>
                                    <span class="font-bold">{{ $villageInfo->internet_coverage }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-primary-500 h-2 rounded-full" style="width: {{ $villageInfo->internet_coverage }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Economy Section -->
        @if($villageInfo->total_umskm || $villageInfo->avg_income || $villageInfo->main_commodities)
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden mb-8">
            <div class="bg-primary-600 text-white p-6">
                <h2 class="text-2xl font-bold"><i class="fas fa-chart-line mr-3"></i>Perekonomian</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                    @if($villageInfo->total_umskm)
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <i class="fas fa-store text-2xl text-primary-600 mb-2"></i>
                            <h4 class="text-xl font-bold text-gray-800">{{ number_format($villageInfo->total_umskm) }}</h4>
                            <p class="text-xs text-gray-600">UMKM</p>
                        </div>
                    @endif
                    @if($villageInfo->total_market)
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <i class="fas fa-shopping-cart text-2xl text-primary-600 mb-2"></i>
                            <h4 class="text-xl font-bold text-gray-800">{{ number_format($villageInfo->total_market) }}</h4>
                            <p class="text-xs text-gray-600">Pasar</p>
                        </div>
                    @endif
                    @if($villageInfo->avg_income)
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <i class="fas fa-money-bill text-2xl text-primary-600 mb-2"></i>
                            <h4 class="text-xl font-bold text-gray-800">Rp {{ number_format($villageInfo->avg_income, 0, ',', '.') }}</h4>
                            <p class="text-xs text-gray-600">Penghasilan Rata-rata</p>
                        </div>
                    @endif
                    @if($villageInfo->poverty_rate)
                        <div class="bg-gray-50 rounded-lg p-4 text-center">
                            <i class="fas fa-heart-broken text-2xl text-red-600 mb-2"></i>
                            <h4 class="text-xl font-bold text-gray-800">{{ $villageInfo->poverty_rate }}%</h4>
                            <p class="text-xs text-gray-600">Tingkat Kemiskinan</p>
                        </div>
                    @endif
                </div>
                
                @if($villageInfo->main_commodities)
                    <div class="p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-bold text-gray-800 mb-2"><i class="fas fa-leaf mr-2 text-green-600"></i>Komoditas Utama</h4>
                        <p class="text-gray-600">{{ $villageInfo->main_commodities }}</p>
                    </div>
                @endif
            </div>
        </div>
        @endif
    </div>
</section>
@endsection
