@extends('layouts.village')

@section('title', $product->name . ' - Produk')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient text-white py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center">
            <span class="px-4 py-1 bg-white/20 rounded-full text-sm mb-4 inline-block">{{ $product->category->name ?? '' }}</span>
            <h1 class="text-3xl md:text-4xl font-bold mb-4">{{ $product->name }}</h1>
        </div>
    </div>
</section>

<!-- Breadcrumb -->
<section class="bg-white py-4 shadow-sm">
    <div class="max-w-7xl mx-auto px-4">
        <nav class="text-sm text-gray-600">
            <a href="/" class="hover:text-primary-600 transition"><i class="fas fa-home mr-1"></i> Beranda</a>
            <span class="mx-2">/</span>
            <a href="{{ route('products.index') }}" class="hover:text-primary-600 transition">Produk</a>
            <span class="mx-2">/</span>
            <span class="text-primary-600 font-medium">{{ Str::limit($product->name, 40) }}</span>
        </nav>
    </div>
</section>

<!-- Content -->
<section class="bg-gray-50 py-12">
    <div class="max-w-6xl mx-auto px-4">
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-2">
                <!-- Image -->
                <div class="relative">
                    <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover min-h-[400px]">
                    @if($product->has_discount)
                        <div class="absolute top-4 left-4">
                            <span class="px-4 py-2 bg-red-500 text-white font-bold rounded-full">
                                -{{ round((1 - $product->discount_price / $product->price) * 100) }}% OFF
                            </span>
                        </div>
                    @endif
                    @if($product->is_featured)
                        <div class="absolute top-4 right-4">
                            <span class="px-4 py-2 bg-accent-500 text-primary-900 font-bold rounded-full">
                                <i class="fas fa-star mr-1"></i> Unggulan
                            </span>
                        </div>
                    @endif
                </div>
                
                <!-- Info -->
                <div class="p-8">
                    <span class="text-sm text-primary-600 font-medium">{{ $product->category->name ?? '' }}</span>
                    <h1 class="text-3xl font-bold text-gray-800 mt-2">{{ $product->name }}</h1>
                    
                    <!-- Price -->
                    <div class="mt-6">
                        @if($product->has_discount)
                            <span class="text-lg text-gray-400 line-through">{{ $product->formatted_price }}</span>
                            <span class="text-3xl font-bold text-red-600 ml-3">{{ $product->formatted_discount_price }}</span>
                        @else
                            <span class="text-3xl font-bold text-primary-600">{{ $product->formatted_price }}</span>
                        @endif
                        <span class="text-gray-500">/{{ $product->unit }}</span>
                    </div>
                    
                    <!-- Stock -->
                    <div class="mt-4 flex items-center">
                        <span class="text-sm text-gray-600 mr-2">Stok:</span>
                        @if($product->in_stock)
                            <span class="px-3 py-1 bg-green-100 text-green-700 text-sm font-medium rounded-full">
                                <i class="fas fa-check-circle mr-1"></i> Tersedia ({{ $product->stock }})
                            </span>
                        @else
                            <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-medium rounded-full">
                                <i class="fas fa-times-circle mr-1"></i> Stok Habis
                            </span>
                        @endif
                    </div>
                    
                    <!-- Sold Count -->
                    <div class="mt-2">
                        <span class="text-sm text-gray-500">
                            <i class="fas fa-shopping-cart mr-1"></i> {{ $product->sold_count }} terjual
                        </span>
                    </div>
                    
                    <!-- Description -->
                    <div class="mt-6">
                        <h3 class="font-bold text-gray-800 mb-2">Deskripsi</h3>
                        <p class="text-gray-600 leading-relaxed">{{ $product->description ?? '-' }}</p>
                    </div>
                    
                    <!-- Location -->
                    @if($product->location)
                        <div class="mt-4 flex items-center text-sm text-gray-600">
                            <i class="fas fa-map-marker-alt mr-2 text-primary-600"></i>
                            {{ $product->location }}
                        </div>
                    @endif
                    
                    <!-- Order Button -->
                    <div class="mt-8 space-y-3">
                        @if($product->whatsapp_order && $product->in_stock)
                            <a href="{{ $product->whatsapp_link }}" target="_blank" 
                               class="block w-full py-4 bg-green-500 text-white text-center font-bold rounded-xl hover:bg-green-600 transition text-lg">
                                <i class="fab fa-whatsapp mr-2"></i> Pesan via WhatsApp
                            </a>
                        @endif
                        <a href="{{ route('products.index') }}" 
                           class="block w-full py-4 bg-gray-100 text-gray-700 text-center font-bold rounded-xl hover:bg-gray-200 transition">
                            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar Produk
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Details -->
            @if($product->details)
                <div class="p-8 border-t">
                    <h3 class="font-bold text-xl text-gray-800 mb-4">Detail Produk</h3>
                    <div class="prose prose-lg max-w-none">
                        {!! $product->details !!}
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Related Products -->
        @if($related->count() > 0)
            <div class="mt-12">
                <h3 class="text-2xl font-bold text-gray-800 mb-6">Produk Terkait</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                    @foreach($related as $item)
                        <div class="bg-white rounded-xl overflow-hidden shadow-md card-hover">
                            <a href="{{ route('products.show', $item->slug) }}">
                                <div class="aspect-square">
                                    <img src="{{ $item->image_url }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                                </div>
                            </a>
                            <div class="p-4">
                                <span class="text-xs text-primary-600 font-medium">{{ $item->category->name ?? '' }}</span>
                                <a href="{{ route('products.show', $item->slug) }}">
                                    <h4 class="font-bold text-gray-800 mt-1 hover:text-primary-600 transition line-clamp-2">
                                        {{ $item->name }}
                                    </h4>
                                </a>
                                <span class="text-lg font-bold text-primary-600 mt-2 block">{{ $item->formatted_price }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</section>
@endsection
