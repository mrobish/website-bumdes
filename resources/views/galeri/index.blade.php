@extends('layouts.village')

@section('title', 'Galeri Desa')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient text-white py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Galeri Desa</h1>
            <p class="text-lg opacity-80">Dokumentasi kegiatan dan potensi {{ $villageInfo->village_name ?? 'Desa' }}</p>
        </div>
    </div>
</section>

<!-- Breadcrumb -->
<section class="bg-white py-4 shadow-sm">
    <div class="max-w-7xl mx-auto px-4">
        <nav class="text-sm text-gray-600">
            <a href="/" class="hover:text-primary-600 transition"><i class="fas fa-home mr-1"></i> Beranda</a>
            <span class="mx-2">/</span>
            <span class="text-primary-600 font-medium">Galeri</span>
        </nav>
    </div>
</section>

<!-- Filter -->
<section class="bg-white py-4 border-b">
    <div class="max-w-7xl mx-auto px-4">
        <div class="flex flex-wrap gap-2">
            <a href="{{ route('gallery.index') }}" 
               class="px-4 py-2 rounded-full text-sm font-medium transition {{ !request('category') ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                Semua
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('gallery.index', ['category' => $cat]) }}" 
                   class="px-4 py-2 rounded-full text-sm font-medium transition {{ request('category') == $cat ? 'bg-primary-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ ucfirst($cat) }}
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Content -->
<section class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4">
        @if($galleries->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                @foreach($galleries as $item)
                    <div class="bg-white rounded-xl overflow-hidden shadow-md card-hover group">
                        <a href="{{ route('gallery.show', $item->id) }}">
                            <div class="relative aspect-square">
                                <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                                <div class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition flex items-center justify-center">
                                    <div class="opacity-0 group-hover:opacity-100 transition">
                                        @if($item->type == 'video')
                                            <i class="fas fa-play-circle text-4xl text-white"></i>
                                        @else
                                            <i class="fas fa-expand text-2xl text-white"></i>
                                        @endif
                                    </div>
                                </div>
                                <div class="absolute top-2 left-2">
                                    <span class="px-2 py-1 bg-primary-600 text-white text-xs rounded-full">
                                        @if($item->type == 'video')
                                            <i class="fas fa-video mr-1"></i> Video
                                        @else
                                            <i class="fas fa-camera mr-1"></i> Foto
                                        @endif
                                    </span>
                                </div>
                            </div>
                            <div class="p-3">
                                <h3 class="font-bold text-sm text-gray-800 line-clamp-2">{{ $item->title }}</h3>
                                <p class="text-xs text-gray-500 mt-1">{{ ucfirst($item->category) }}</p>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $galleries->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <i class="fas fa-images text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-600 mb-2">Belum Ada Galeri</h3>
                <p class="text-gray-500">Foto dan video akan muncul setelah ditambahkan oleh admin.</p>
            </div>
        @endif
    </div>
</section>
@endsection
