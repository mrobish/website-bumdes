@extends('layouts.village')

@section('title', $article->title . ' - Berita')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient text-white py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center">
            <span class="px-4 py-1 bg-white/20 rounded-full text-sm mb-4 inline-block">{{ $article->category }}</span>
            <h1 class="text-3xl md:text-4xl font-bold mb-4">{{ $article->title }}</h1>
            <div class="flex items-center justify-center text-sm opacity-80">
                <span><i class="fas fa-calendar mr-1"></i> {{ $article->published_at->format('d M Y H:i') }}</span>
                <span class="mx-3">•</span>
                <span><i class="fas fa-user mr-1"></i> {{ $article->author ?? 'Admin' }}</span>
                <span class="mx-3">•</span>
                <span><i class="fas fa-eye mr-1"></i> {{ $article->views }} views</span>
            </div>
        </div>
    </div>
</section>

<!-- Breadcrumb -->
<section class="bg-white py-4 shadow-sm">
    <div class="max-w-7xl mx-auto px-4">
        <nav class="text-sm text-gray-600">
            <a href="/" class="hover:text-primary-600 transition"><i class="fas fa-home mr-1"></i> Beranda</a>
            <span class="mx-2">/</span>
            <a href="{{ route('news.index') }}" class="hover:text-primary-600 transition">Berita</a>
            <span class="mx-2">/</span>
            <span class="text-primary-600 font-medium truncate">{{ Str::limit($article->title, 40) }}</span>
        </nav>
    </div>
</section>

<!-- Content -->
<section class="bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4">
        <article class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <!-- Featured Image -->
            <div class="relative h-64 md:h-96">
                <img src="{{ $article->image_url }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
            </div>
            
            <!-- Article Content -->
            <div class="p-8">
                <div class="prose prose-lg max-w-none">
                    {!! $article->content !!}
                </div>
                
                <!-- Share -->
                <div class="mt-8 pt-6 border-t">
                    <p class="text-sm text-gray-600 mb-3">Bagikan artikel ini:</p>
                    <div class="flex space-x-3">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($article->title) }}" target="_blank" class="w-10 h-10 bg-sky-500 text-white rounded-full flex items-center justify-center hover:bg-sky-600 transition">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($article->title . ' - ' . request()->fullUrl()) }}" target="_blank" class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center hover:bg-green-600 transition">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
        </article>
        
        <!-- Related News -->
        @if($related->count() > 0)
            <div class="mt-12">
                <h3 class="text-2xl font-bold text-gray-800 mb-6">Berita Terkait</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach($related as $item)
                        <article class="bg-white rounded-xl overflow-hidden shadow-md card-hover">
                            <a href="{{ route('news.show', $item->slug) }}">
                                <div class="h-40">
                                    <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                                </div>
                            </a>
                            <div class="p-4">
                                <span class="text-xs text-primary-600 font-medium">{{ $item->category }}</span>
                                <a href="{{ route('news.show', $item->slug) }}">
                                    <h4 class="font-bold text-gray-800 mt-1 hover:text-primary-600 transition line-clamp-2">
                                        {{ $item->title }}
                                    </h4>
                                </a>
                                <p class="text-sm text-gray-500 mt-2">{{ $item->published_at->format('d M Y') }}</p>
                            </div>
                        </article>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
