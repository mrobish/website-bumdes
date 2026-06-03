@extends('layouts.village')

@section('title', 'Berita Desa')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient text-white py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Berita Desa</h1>
            <p class="text-lg opacity-80">Informasi terkini dari {{ $villageInfo->village_name ?? 'Desa' }}</p>
        </div>
    </div>
</section>

<!-- Breadcrumb -->
<section class="bg-white py-4 shadow-sm">
    <div class="max-w-7xl mx-auto px-4">
        <nav class="text-sm text-gray-600">
            <a href="/" class="hover:text-primary-600 transition"><i class="fas fa-home mr-1"></i> Beranda</a>
            <span class="mx-2">/</span>
            <span class="text-primary-600 font-medium">Berita</span>
        </nav>
    </div>
</section>

<!-- Content -->
<section class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4">
        @if($news->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($news as $item)
                    <article class="bg-white rounded-xl overflow-hidden shadow-md card-hover">
                        <a href="{{ route('news.show', $item->slug) }}">
                            <div class="relative h-48">
                                <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                                <div class="absolute top-4 left-4">
                                    <span class="px-3 py-1 bg-primary-600 text-white text-xs font-bold rounded-full uppercase">
                                        {{ $item->category }}
                                    </span>
                                </div>
                                @if($item->is_featured)
                                    <div class="absolute top-4 right-4">
                                        <span class="px-3 py-1 bg-accent-500 text-primary-900 text-xs font-bold rounded-full">
                                            <i class="fas fa-star mr-1"></i> Utama
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </a>
                        <div class="p-6">
                            <div class="flex items-center text-sm text-gray-500 mb-3">
                                <span><i class="fas fa-calendar mr-1"></i> {{ $item->published_at->format('d M Y') }}</span>
                                <span class="mx-2">•</span>
                                <span><i class="fas fa-eye mr-1"></i> {{ $item->views }}</span>
                            </div>
                            <a href="{{ route('news.show', $item->slug) }}">
                                <h2 class="font-bold text-xl text-gray-800 mb-3 hover:text-primary-600 transition line-clamp-2">
                                    {{ $item->title }}
                                </h2>
                            </a>
                            <p class="text-gray-600 text-sm line-clamp-3">{{ $item->excerpt }}</p>
                            <a href="{{ route('news.show', $item->slug) }}" class="inline-block mt-4 text-primary-600 font-medium text-sm hover:text-primary-700 transition">
                                Baca Selengkapnya <i class="fas fa-arrow-right ml-1"></i>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $news->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <i class="fas fa-newspaper text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-600 mb-2">Belum Ada Berita</h3>
                <p class="text-gray-500">Berita akan muncul setelah ditambahkan oleh admin.</p>
            </div>
        @endif
    </div>
</section>
@endsection
