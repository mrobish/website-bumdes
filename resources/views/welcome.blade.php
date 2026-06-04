@extends('layouts.village')

@section('title', 'Beranda')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient text-white relative overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.15\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 py-20 md:py-28 relative">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
                <div class="inline-block px-4 py-2 bg-white/10 rounded-full text-sm mb-6">
                    <i class="fas fa-map-marker-alt mr-2"></i>
                    {{ $bumdesSetting->bumdes_district ?? '' }}, {{ $bumdesSetting->bumdes_regency ?? '' }}
                </div>
                <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold mb-6 leading-tight">
                    {{ $bumdesSetting->bumdes_name ?? 'BUMDes' }}
                </h1>
                @if($bumdesSetting && $bumdesSetting->motto)
                    <p class="text-xl md:text-2xl mb-6 opacity-90 italic font-display">"{{ $bumdesSetting->motto }}"</p>
                @endif
                <p class="text-lg mb-8 opacity-80 leading-relaxed">
                    {{ Str::limit($bumdesSetting->about ?? 'Selamat datang di website resmi BUMDes kami. Portal informasi dan layanan untuk seluruh masyarakat desa.', 200) }}
                </p>
                <div class="flex flex-wrap gap-4">
                    <a href="/profil-desa" class="px-8 py-3 bg-accent-500 text-primary-900 rounded-lg font-bold hover:bg-accent-400 transition shadow-lg">
                        <i class="fas fa-building mr-2"></i> Profil BUMDes
                    </a>
                    <a href="/bumdes" class="px-8 py-3 bg-white/10 border-2 border-white rounded-lg font-bold hover:bg-white hover:text-primary-800 transition">
                        <i class="fas fa-store mr-2"></i> Kunjungi BUMDes
                    </a>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="grid grid-cols-2 gap-4">
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center card-hover">
                    <div class="w-16 h-16 bg-accent-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-users text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold mb-1">{{ number_format($villageInfo->total_population ?? 0) }}</h3>
                    <p class="text-sm opacity-80">Penduduk</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center card-hover">
                    <div class="w-16 h-16 bg-accent-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-home text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold mb-1">{{ number_format($villageInfo->total_family ?? 0) }}</h3>
                    <p class="text-sm opacity-80">Kepala Keluarga</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center card-hover">
                    <div class="w-16 h-16 bg-accent-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-map text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold mb-1">{{ number_format($villageInfo->area_total ?? 0, 0, ',', '.') }}</h3>
                    <p class="text-sm opacity-80">Hektar</p>
                </div>
                <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center card-hover">
                    <div class="w-16 h-16 bg-accent-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-store text-2xl text-white"></i>
                    </div>
                    <h3 class="text-3xl font-bold mb-1">{{ number_format($villageInfo->total_umskm ?? 0) }}</h3>
                    <p class="text-sm opacity-80">UMKM</p>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Wave Divider -->
    <div class="absolute bottom-0 left-0 right-0">
        <svg viewBox="0 0 1440 120" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M0 120L60 105C120 90 240 60 360 45C480 30 600 30 720 37.5C840 45 960 60 1080 67.5C1200 75 1320 75 1380 75L1440 75V120H1380C1320 120 1200 120 1080 120C960 120 840 120 720 120C600 120 480 120 360 120C240 120 120 120 60 120H0Z" fill="#f9fafb"/>
        </svg>
    </div>
</section>

<!-- Quick Access Section -->
<section class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Layanan Kami</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Akses informasi dan layanan BUMDes dengan mudah</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <a href="/profil-desa" class="bg-white rounded-xl p-6 shadow-md card-hover text-center" data-aos="fade-up" data-aos-delay="100">
                <div class="w-16 h-16 bg-primary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-building text-2xl text-primary-600"></i>
                </div>
                <h3 class="font-bold text-gray-800 mb-2">Profil BUMDes</h3>
                <p class="text-sm text-gray-600">Informasi lengkap tentang BUMDes</p>
            </a>
            
            <a href="/bumdes" class="bg-white rounded-xl p-6 shadow-md card-hover text-center" data-aos="fade-up" data-aos-delay="200">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-store text-2xl text-green-600"></i>
                </div>
                <h3 class="font-bold text-gray-800 mb-2">Unit Usaha</h3>
                <p class="text-sm text-gray-600">Lihat unit usaha kami</p>
            </a>
            
            <a href="/potensi" class="bg-white rounded-xl p-6 shadow-md card-hover text-center">
                <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-mountain text-2xl text-amber-600"></i>
                </div>
                <h3 class="font-bold text-gray-800 mb-2">Potensi Desa</h3>
                <p class="text-sm text-gray-600">Sumber daya dan keunggulan desa</p>
            </a>
            
            <a href="/kontak" class="bg-white rounded-xl p-6 shadow-md card-hover text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-envelope text-2xl text-blue-600"></i>
                </div>
                <h3 class="font-bold text-gray-800 mb-2">Kontak</h3>
                <p class="text-sm text-gray-600">Hubungi kami</p>
            </a>
        </div>
    </div>
</section>

<!-- About Section -->
@if($bumdesSetting && $bumdesSetting->about)
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div data-aos="fade-right">
                <div class="inline-block px-4 py-2 bg-primary-100 text-primary-700 rounded-full text-sm font-medium mb-4">
                    Tentang BUMDes
                </div>
                <h2 class="text-3xl font-bold text-gray-800 mb-6">{{ $bumdesSetting->bumdes_name ?? 'BUMDes' }}</h2>
                <div class="prose prose-lg text-gray-600">
                    {!! nl2br(e($bumdesSetting->about)) !!}
                </div>
                @if($villageInfo && $villageInfo->village_history)
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <h4 class="font-bold text-gray-800 mb-2"><i class="fas fa-history mr-2 text-primary-600"></i> Sejarah Singkat</h4>
                        <p class="text-sm text-gray-600">{{ Str::limit($villageInfo->village_history, 200) }}</p>
                    </div>
                @endif
            </div>
            <div class="relative" data-aos="fade-left">
                @if($bumdesSetting->kantor_photo_url)
                    <img src="{{ $bumdesSetting->kantor_photo_url }}" alt="{{ $bumdesSetting->bumdes_name }}" class="rounded-xl shadow-lg w-full">
                @else
                    <div class="bg-gradient-to-br from-primary-100 to-primary-200 rounded-xl p-12 text-center">
                        <i class="fas fa-image text-6xl text-primary-300 mb-4"></i>
                        <p class="text-primary-600">Foto BUMDes</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
@endif

<!-- Visi & Misi Section -->
@if($bumdesSetting && ($bumdesSetting->vision || $bumdesSetting->mission))
<section class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Visi & Misi</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Arah dan tujuan BUMDes dalam melayani masyarakat</p>
        </div>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            @if($bumdesSetting->vision)
                <div class="bg-white rounded-2xl p-8 shadow-md" data-aos="fade-right">
                    <div class="flex items-center mb-6">
                        <div class="w-14 h-14 bg-primary-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-eye text-2xl text-primary-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800">Visi</h3>
                    </div>
                    <div class="prose text-gray-600">
                        {!! nl2br(e($bumdesSetting->vision)) !!}
                    </div>
                </div>
            @endif
            
            @if($bumdesSetting->mission)
                <div class="bg-white rounded-2xl p-8 shadow-md" data-aos="fade-left">
                    <div class="flex items-center mb-6">
                        <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center mr-4">
                            <i class="fas fa-bullseye text-2xl text-green-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-800">Misi</h3>
                    </div>
                    <div class="prose text-gray-600">
                        {!! nl2br(e($bumdesSetting->mission)) !!}
                    </div>
                </div>
            @endif
        </div>
    </div>
</section>
@endif

<!-- Pelaksana Operasional BUMDes -->
<section class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12" data-aos="fade-up">
            <div class="inline-block px-4 py-2 bg-primary-100 rounded-full text-primary-700 text-sm mb-4">
                <i class="fas fa-users mr-2"></i> Struktur Organisasi
            </div>
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Pelaksana Operasional BUMDes</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Berdasarkan PP 11/2021 tentang Badan Usaha Milik Desa</p>
        </div>

        @php
            $pejabat = [
                ['field' => 'kepala_desa_name', 'phone' => 'kepala_desa_phone', 'title' => 'Penasihat', 'icon' => 'fa-user-tie', 'color' => 'primary'],
                ['field' => 'direktur_name', 'phone' => 'direktur_phone', 'title' => 'Direktur', 'icon' => 'fa-user-crown', 'color' => 'amber'],
                ['field' => 'sekretaris_name', 'phone' => 'sekretaris_phone', 'title' => 'Sekretaris', 'icon' => 'fa-user-pen', 'color' => 'blue'],
                ['field' => 'bendahara_umum_name', 'phone' => 'bendahara_umum_phone', 'title' => 'Bendahara', 'icon' => 'fa-coins', 'color' => 'green'],
                ['field' => 'pengawas1_name', 'phone' => 'pengawas1_phone', 'title' => 'Pengawas 1', 'icon' => 'fa-eye', 'color' => 'purple'],
                ['field' => 'pengawas2_name', 'phone' => 'pengawas2_phone', 'title' => 'Pengawas 2', 'icon' => 'fa-eye', 'color' => 'purple'],
            ];
        @endphp

        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            @foreach($pejabat as $p)
                @if($bumdesSetting && $bumdesSetting->{$p['field']})
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 text-center hover:shadow-md transition" data-aos="fade-up" data-aos-delay="{{ $loop->index * 100 }}">
                        <div class="w-20 h-20 bg-{{ $p['color'] }}-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas {{ $p['icon'] }} text-2xl text-{{ $p['color'] }}-600"></i>
                        </div>
                        <h4 class="font-bold text-gray-800 text-sm mb-1">{{ $bumdesSetting->{$p['field']} }}</h4>
                        <p class="text-xs text-{{ $p['color'] }}-600 font-semibold mb-2">{{ $p['title'] }}</p>
                        @if($bumdesSetting->{$p['phone']})
                            <p class="text-xs text-gray-500">
                                <i class="fas fa-phone mr-1"></i>{{ $bumdesSetting->{$p['phone']} }}
                            </p>
                        @endif
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</section>

<!-- BUMDes Highlight -->
<section class="bg-gradient-to-r from-primary-800 to-primary-600 py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div class="text-white" data-aos="fade-right">
                <div class="inline-block px-4 py-2 bg-white/10 rounded-full text-sm mb-4">
                    <i class="fas fa-store mr-2"></i> BUMDes
                </div>
                <h2 class="text-3xl font-bold mb-6">{{ $bumdesSetting->bumdes_name ?? 'BUMDes' }}</h2>
                @if($bumdesSetting && $bumdesSetting->vision)
                    <p class="text-lg mb-4 opacity-90 italic">"{{ $bumdesSetting->vision }}"</p>
                @endif
                @if($bumdesSetting && $bumdesSetting->about)
                    <p class="mb-6 opacity-80">{{ Str::limit($bumdesSetting->about, 200) }}</p>
                @endif
                <div class="grid grid-cols-2 gap-4 mb-8">
                    <div class="bg-white/10 rounded-lg p-4 text-center">
                        <h4 class="text-2xl font-bold">{{ number_format($villageInfo->bumdes_employees ?? 0) }}</h4>
                        <p class="text-sm opacity-80">Karyawan</p>
                    </div>
                    <div class="bg-white/10 rounded-lg p-4 text-center">
                        <h4 class="text-2xl font-bold">{{ number_format($villageInfo->bumdes_partners ?? 0) }}</h4>
                        <p class="text-sm opacity-80">Mitra</p>
                    </div>
                </div>
                <a href="/bumdes" class="inline-block px-8 py-3 bg-accent-500 text-primary-900 rounded-lg font-bold hover:bg-accent-400 transition">
                    Pelajari Lebih Lanjut <i class="fas fa-arrow-right ml-2"></i>
                </a>
            </div>
            <div class="grid grid-cols-2 gap-4" data-aos="fade-left">
                @if($villageInfo && $villageInfo->bumdes_initial_capital)
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center card-hover">
                        <i class="fas fa-coins text-3xl text-accent-400 mb-3"></i>
                        <h4 class="text-xl font-bold mb-1">Modal Awal</h4>
                        <p class="text-sm opacity-80">Rp {{ number_format($villageInfo->bumdes_initial_capital, 0, ',', '.') }}</p>
                    </div>
                @endif
                @if($villageInfo && $villageInfo->bumdes_annual_revenue)
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 text-center card-hover">
                        <i class="fas fa-chart-line text-3xl text-accent-400 mb-3"></i>
                        <h4 class="text-xl font-bold mb-1">Omzet Tahunan</h4>
                        <p class="text-sm opacity-80">Rp {{ number_format($villageInfo->bumdes_annual_revenue, 0, ',', '.') }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Potential Section -->
@if($villageInfo && ($villageInfo->tourism_potential || $villageInfo->agriculture_potential || $villageInfo->fishery_potential))
<section class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Potensi Unggulan</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Sumber daya alam dan keunggulan desa kami</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @if($villageInfo->tourism_potential)
                <div class="bg-white rounded-xl overflow-hidden shadow-md card-hover" data-aos="fade-up" data-aos-delay="100">
                    <div class="h-48 bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                        <i class="fas fa-mountain text-6xl text-white/80"></i>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-xl text-gray-800 mb-3"><i class="fas fa-umbrella-beach mr-2 text-blue-600"></i> Pariwisata</h3>
                        <p class="text-gray-600 text-sm">{{ Str::limit($villageInfo->tourism_potential, 150) }}</p>
                    </div>
                </div>
            @endif
            
            @if($villageInfo->agriculture_potential)
                <div class="bg-white rounded-xl overflow-hidden shadow-md card-hover" data-aos="fade-up" data-aos-delay="200">
                    <div class="h-48 bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center">
                        <i class="fas fa-seedling text-6xl text-white/80"></i>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-xl text-gray-800 mb-3"><i class="fas fa-tractor mr-2 text-green-600"></i> Pertanian</h3>
                        <p class="text-gray-600 text-sm">{{ Str::limit($villageInfo->agriculture_potential, 150) }}</p>
                    </div>
                </div>
            @endif
            
            @if($villageInfo->fishery_potential)
                <div class="bg-white rounded-xl overflow-hidden shadow-md card-hover" data-aos="fade-up" data-aos-delay="300">
                    <div class="h-48 bg-gradient-to-br from-cyan-400 to-cyan-600 flex items-center justify-center">
                        <i class="fas fa-fish text-6xl text-white/80"></i>
                    </div>
                    <div class="p-6">
                        <h3 class="font-bold text-xl text-gray-800 mb-3"><i class="fas fa-water mr-2 text-cyan-600"></i> Perikanan</h3>
                        <p class="text-gray-600 text-sm">{{ Str::limit($villageInfo->fishery_potential, 150) }}</p>
                    </div>
                </div>
            @endif
        </div>
        
        <div class="text-center mt-8">
            <a href="/potensi" class="inline-block px-8 py-3 bg-primary-600 text-white rounded-lg font-bold hover:bg-primary-700 transition">
                Lihat Semua Potensi <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Statistics Section -->
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Statistik Desa</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Data terkini tentang desa kami</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            <div class="stat-card rounded-xl p-6 text-center text-white card-hover" data-aos="zoom-in" data-aos-delay="100">
                <i class="fas fa-users text-3xl mb-3 opacity-80"></i>
                <h3 class="text-3xl font-bold mb-1">{{ number_format($villageInfo->total_population ?? 0) }}</h3>
                <p class="text-sm opacity-80">Total Penduduk</p>
            </div>
            <div class="stat-card rounded-xl p-6 text-center text-white card-hover" data-aos="zoom-in" data-aos-delay="200">
                <i class="fas fa-venus-mars text-3xl mb-3 opacity-80"></i>
                <h3 class="text-3xl font-bold mb-1">{{ number_format($villageInfo->male_population ?? 0) }} / {{ number_format($villageInfo->female_population ?? 0) }}</h3>
                <p class="text-sm opacity-80">Laki-laki / Perempuan</p>
            </div>
            <div class="stat-card rounded-xl p-6 text-center text-white card-hover" data-aos="zoom-in" data-aos-delay="300">
                <i class="fas fa-home text-3xl mb-3 opacity-80"></i>
                <h3 class="text-3xl font-bold mb-1">{{ number_format($villageInfo->total_family ?? 0) }}</h3>
                <p class="text-sm opacity-80">Kepala Keluarga</p>
            </div>
            <div class="stat-card rounded-xl p-6 text-center text-white card-hover" data-aos="zoom-in" data-aos-delay="400">
                <i class="fas fa-map-marked-alt text-3xl mb-3 opacity-80"></i>
                <h3 class="text-3xl font-bold mb-1">{{ number_format($villageInfo->area_total ?? 0, 0, ',', '.') }}</h3>
                <p class="text-sm opacity-80">Luas (Ha)</p>
            </div>
        </div>
        
        <!-- Additional Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-6">
            <div class="bg-gray-50 rounded-xl p-6 text-center card-hover" data-aos="fade-up" data-aos-delay="100">
                <i class="fas fa-school text-3xl text-primary-600 mb-3"></i>
                <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ $villageInfo->total_schools ?? 0 }}</h3>
                <p class="text-sm text-gray-600">Fasilitas Pendidikan</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-6 text-center card-hover" data-aos="fade-up" data-aos-delay="200">
                <i class="fas fa-hospital text-3xl text-red-600 mb-3"></i>
                <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ $villageInfo->total_health_facilities ?? 0 }}</h3>
                <p class="text-sm text-gray-600">Fasilitas Kesehatan</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-6 text-center card-hover" data-aos="fade-up" data-aos-delay="300">
                <i class="fas fa-mosque text-3xl text-green-600 mb-3"></i>
                <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ $villageInfo->total_mosques ?? 0 }}</h3>
                <p class="text-sm text-gray-600">Masjid / Musholla</p>
            </div>
            <div class="bg-gray-50 rounded-xl p-6 text-center card-hover" data-aos="fade-up" data-aos-delay="400">
                <i class="fas fa-bolt text-3xl text-yellow-600 mb-3"></i>
                <h3 class="text-2xl font-bold text-gray-800 mb-1">{{ $villageInfo->electricity_coverage ?? 0 }}%</h3>
                <p class="text-sm text-gray-600">Cakupan Listrik</p>
            </div>
        </div>
    </div>
</section>

<!-- Latest News Section -->
@if(isset($latestNews) && $latestNews->count() > 0)
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Berita Terbaru</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Informasi terkini dari desa kami</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            @foreach($latestNews as $item)
                <article class="bg-gray-50 rounded-xl overflow-hidden card-hover" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                    <a href="{{ route('news.show', $item->slug) }}">
                        <div class="relative h-48">
                            <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover" loading="lazy">
                            <div class="absolute top-4 left-4">
                                <span class="px-3 py-1 bg-primary-600 text-white text-xs font-bold rounded-full">{{ $item->category }}</span>
                            </div>
                        </div>
                    </a>
                    <div class="p-5">
                        <div class="flex items-center text-xs text-gray-500 mb-2">
                            <span><i class="fas fa-calendar mr-1"></i> {{ $item->published_at->format('d M Y') }}</span>
                            <span class="mx-2">•</span>
                            <span><i class="fas fa-eye mr-1"></i> {{ $item->views }}</span>
                        </div>
                        <a href="{{ route('news.show', $item->slug) }}">
                            <h3 class="font-bold text-lg text-gray-800 hover:text-primary-600 transition line-clamp-2">
                                {{ $item->title }}
                            </h3>
                        </a>
                        <p class="text-sm text-gray-600 mt-2 line-clamp-2">{{ $item->excerpt }}</p>
                    </div>
                </article>
            @endforeach
        </div>
        
        <div class="text-center mt-8">
            <a href="{{ route('news.index') }}" class="inline-block px-8 py-3 bg-primary-600 text-white rounded-lg font-bold hover:bg-primary-700 transition">
                Lihat Semua Berita <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Featured Products Section -->
@if(isset($featuredProducts) && $featuredProducts->count() > 0)
<section class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Produk Unggulan</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Produk terbaik dari UMKM desa kami</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($featuredProducts as $product)
                <div class="bg-white rounded-xl overflow-hidden shadow-md card-hover" data-aos="fade-up" data-aos-delay="{{ $loop->iteration * 100 }}">
                    <a href="{{ route('products.show', $product->slug) }}">
                        <div class="relative aspect-square">
                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover" loading="lazy">
                            @if($product->has_discount)
                                <div class="absolute top-2 left-2">
                                    <span class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-full">
                                        -{{ round((1 - $product->discount_price / $product->price) * 100) }}%
                                    </span>
                                </div>
                            @endif
                        </div>
                    </a>
                    <div class="p-4">
                        <span class="text-xs text-primary-600 font-medium">{{ $product->category->name ?? '' }}</span>
                        <a href="{{ route('products.show', $product->slug) }}">
                            <h3 class="font-bold text-sm text-gray-800 mt-1 hover:text-primary-600 transition line-clamp-2">
                                {{ $product->name }}
                            </h3>
                        </a>
                        <div class="mt-2">
                            @if($product->has_discount)
                                <span class="text-sm text-gray-400 line-through">{{ $product->formatted_price }}</span>
                                <span class="text-lg font-bold text-red-600 ml-2">{{ $product->formatted_discount_price }}</span>
                            @else
                                <span class="text-lg font-bold text-primary-600">{{ $product->formatted_price }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="text-center mt-8" data-aos="fade-up">
            <a href="{{ route('products.index') }}" class="inline-block px-8 py-3 bg-primary-600 text-white rounded-lg font-bold hover:bg-primary-700 transition">
                Lihat Semua Produk <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Latest Gallery Section -->
@if(isset($latestGallery) && $latestGallery->count() > 0)
<section class="bg-white py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center mb-12" data-aos="fade-up">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">Galeri Terbaru</h2>
            <p class="text-gray-600 max-w-2xl mx-auto">Dokumentasi kegiatan dan potensi desa</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            @foreach($latestGallery as $item)
                <a href="{{ route('gallery.show', $item->id) }}" class="relative group rounded-xl overflow-hidden aspect-square" data-aos="zoom-in" data-aos-delay="{{ $loop->iteration * 100 }}">
                    <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-300" loading="lazy">
                    <div class="absolute inset-0 bg-black/0 group-hover:bg-black/40 transition flex items-center justify-center">
                        <div class="opacity-0 group-hover:opacity-100 transition text-center px-2">
                            <i class="fas fa-expand text-white text-xl mb-1"></i>
                            <p class="text-white text-xs font-medium line-clamp-2">{{ $item->title }}</p>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        
        <div class="text-center mt-8" data-aos="fade-up">
            <a href="{{ route('gallery.index') }}" class="inline-block px-8 py-3 bg-primary-600 text-white rounded-lg font-bold hover:bg-primary-700 transition">
                Lihat Semua Galeri <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
</section>
@endif

<!-- Contact Section -->
@if($bumdesSetting && ($bumdesSetting->phone || $bumdesSetting->email))
<section class="bg-gray-50 py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <!-- Map -->
                <div class="bg-gray-200 min-h-[400px]">
                    @if($villageInfo && $villageInfo->lat && $villageInfo->lng)
                        <iframe 
                            width="100%" 
                            height="100%" 
                            style="border:0; min-height: 400px;" 
                            loading="lazy" 
                            src="https://www.openstreetmap.org/export/embed.html?bbox={{ $villageInfo->lng - 0.01 }},{{ $villageInfo->lat - 0.01 }},{{ $villageInfo->lng + 0.01 }},{{ $villageInfo->lat + 0.01 }}&layer=mapnik&marker={{ $villageInfo->lat }},{{ $villageInfo->lng }}">
                        </iframe>
                    @else
                        <div class="flex items-center justify-center h-full">
                            <div class="text-center text-gray-500">
                                <i class="fas fa-map-marked-alt text-6xl mb-4 opacity-50"></i>
                                <p>Peta Lokasi</p>
                            </div>
                        </div>
                    @endif
                </div>
                
                <!-- Contact Info -->
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-gray-800 mb-6">Hubungi Kami</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start space-x-4">
                            <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-primary-600"></i>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">Alamat</h4>
                                <p class="text-gray-600">{{ $bumdesSetting->full_address }}</p>
                            </div>
                        </div>
                        
                        @if($bumdesSetting->phone)
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-phone text-green-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">Telepon</h4>
                                    <p class="text-gray-600">{{ $bumdesSetting->phone }}</p>
                                </div>
                            </div>
                        @endif
                        
                        @if($bumdesSetting->email)
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-envelope text-blue-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">Email</h4>
                                    <p class="text-gray-600">{{ $bumdesSetting->email }}</p>
                                </div>
                            </div>
                        @endif
                        
                        @if($bumdesSetting->whatsapp)
                            <div class="flex items-start space-x-4">
                                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fab fa-whatsapp text-green-600"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800">WhatsApp</h4>
                                    <p class="text-gray-600">{{ $bumdesSetting->whatsapp }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    @if($bumdesSetting->whatsapp)
                        <a href="https://wa.me/{{ $bumdesSetting->whatsapp }}" target="_blank" class="inline-block mt-6 px-6 py-3 bg-green-500 text-white rounded-lg font-bold hover:bg-green-600 transition">
                            <i class="fab fa-whatsapp mr-2"></i> Chat WhatsApp
                        </a>
                    @endif
                    
                    <a href="/kontak" class="inline-block mt-6 ml-4 px-6 py-3 bg-primary-600 text-white rounded-lg font-bold hover:bg-primary-700 transition">
                        Lihat Semua Kontak <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
@endsection