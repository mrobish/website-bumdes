@extends('layouts.village')

@section('title', $item->title . ' - Galeri')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient text-white py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center">
            <span class="px-4 py-1 bg-white/20 rounded-full text-sm mb-4 inline-block">
                @if($item->type == 'video')
                    <i class="fas fa-video mr-1"></i> Video
                @else
                    <i class="fas fa-camera mr-1"></i> Foto
                @endif
                • {{ ucfirst($item->category) }}
            </span>
            <h1 class="text-3xl md:text-4xl font-bold mb-4">{{ $item->title }}</h1>
            <div class="flex items-center justify-center text-sm opacity-80">
                <span><i class="fas fa-eye mr-1"></i> {{ $item->views }} views</span>
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
            <a href="{{ route('gallery.index') }}" class="hover:text-primary-600 transition">Galeri</a>
            <span class="mx-2">/</span>
            <span class="text-primary-600 font-medium">{{ Str::limit($item->title, 40) }}</span>
        </nav>
    </div>
</section>

<!-- Content -->
<section class="bg-gray-50 py-12">
    <div class="max-w-4xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="relative">
                <img src="{{ $item->image_url }}" alt="{{ $item->title }}" class="w-full">
            </div>
            
            <div class="p-8">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">{{ $item->title }}</h2>
                
                @if($item->description)
                    <p class="text-gray-600 leading-relaxed">{{ $item->description }}</p>
                @endif
                
                <div class="mt-6 flex items-center text-sm text-gray-500">
                    <span class="mr-4"><i class="fas fa-tag mr-1"></i> {{ ucfirst($item->category) }}</span>
                    <span class="mr-4"><i class="fas fa-clock mr-1"></i> {{ $item->created_at->format('d M Y') }}</span>
                    <span><i class="fas fa-eye mr-1"></i> {{ $item->views }} views</span>
                </div>
                
                <!-- Share -->
                <div class="mt-6 pt-6 border-t">
                    <p class="text-sm text-gray-600 mb-3">Bagikan:</p>
                    <div class="flex space-x-3">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}" target="_blank" class="w-10 h-10 bg-blue-600 text-white rounded-full flex items-center justify-center hover:bg-blue-700 transition">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($item->title . ' - ' . request()->fullUrl()) }}" target="_blank" class="w-10 h-10 bg-green-500 text-white rounded-full flex items-center justify-center hover:bg-green-600 transition">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Back Button -->
        <div class="mt-8 text-center">
            <a href="{{ route('gallery.index') }}" class="inline-block px-6 py-3 bg-primary-600 text-white rounded-lg font-medium hover:bg-primary-700 transition">
                <i class="fas fa-arrow-left mr-2"></i> Kembali ke Galeri
            </a>
        </div>
    </div>
</section>
@endsection
