@extends('layouts.village')

@section('title', 'Produk UMKM')

@section('content')
<!-- Hero Section -->
<section class="hero-gradient text-white py-16">
    <div class="max-w-7xl mx-auto px-4">
        <div class="text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Produk UMKM</h1>
            <p class="text-lg opacity-80">Produk unggulan dari masyarakat {{ $villageInfo->village_name ?? 'Desa' }}</p>
        </div>
    </div>
</section>

<!-- Breadcrumb -->
<section class="bg-white py-4 shadow-sm">
    <div class="max-w-7xl mx-auto px-4">
        <nav class="text-sm text-gray-600">
            <a href="/" class="hover:text-primary-600 transition"><i class="fas fa-home mr-1"></i> Beranda</a>
            <span class="mx-2">/</span>
            <span class="text-primary-600 font-medium">Produk</span>
        </nav>
    </div>
</section>

<!-- Search & Filter -->
<section class="bg-white py-6 border-b">
    <div class="max-w-7xl mx-auto px-4">
        <form action="{{ route('products.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <div class="relative">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari produk..." 
                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                </div>
            </div>
            <div class="flex gap-2">
                <select name="category" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }} ({{ $cat->products_count }})
                        </option>
                    @endforeach
                </select>
                <select name="sort" class="px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="">Terbaru</option>
                    <option value="price_low" {{ request('sort') == 'price_low' ? 'selected' : '' }}>Harga: Rendah</option>
                    <option value="price_high" {{ request('sort') == 'price_high' ? 'selected' : '' }}>Harga: Tinggi</option>
                    <option value="popular" {{ request('sort') == 'popular' ? 'selected' : '' }}>Terpopuler</option>
                </select>
                <button type="submit" class="px-6 py-3 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition">
                    <i class="fas fa-filter mr-1"></i> Filter
                </button>
            </div>
        </form>
    </div>
</section>

<!-- Content -->
<section class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4">
        @if($products->count() > 0)
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($products as $product)
                    <div class="bg-white rounded-xl overflow-hidden shadow-md card-hover">
                        <a href="{{ route('products.show', $product->slug) }}">
                            <div class="relative aspect-square">
                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @if($product->has_discount)
                                    <div class="absolute top-2 left-2">
                                        <span class="px-2 py-1 bg-red-500 text-white text-xs font-bold rounded-full">
                                            -{{ round((1 - $product->discount_price / $product->price) * 100) }}%
                                        </span>
                                    </div>
                                @endif
                                @if($product->is_featured)
                                    <div class="absolute top-2 right-2">
                                        <span class="px-2 py-1 bg-accent-500 text-primary-900 text-xs font-bold rounded-full">
                                            <i class="fas fa-star mr-1"></i> Unggulan
                                        </span>
                                    </div>
                                @endif
                                @if(!$product->in_stock)
                                    <div class="absolute inset-0 bg-black/50 flex items-center justify-center">
                                        <span class="px-4 py-2 bg-red-500 text-white font-bold rounded-lg">Stok Habis</span>
                                    </div>
                                @endif
                            </div>
                        </a>
                        <div class="p-4">
                            <span class="text-xs text-primary-600 font-medium">{{ $product->category->name ?? '' }}</span>
                            <a href="{{ route('products.show', $product->slug) }}">
                                <h3 class="font-bold text-gray-800 mt-1 hover:text-primary-600 transition line-clamp-2">
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
                                <span class="text-sm text-gray-500">/{{ $product->unit }}</span>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-xs text-gray-500">
                                    <i class="fas fa-box mr-1"></i> Stok: {{ $product->stock }}
                                </span>
                                @if($product->whatsapp_order && $product->in_stock)
                                    <a href="{{ $product->whatsapp_link }}" target="_blank" 
                                       class="px-3 py-1 bg-green-500 text-white text-xs font-medium rounded-full hover:bg-green-600 transition">
                                        <i class="fab fa-whatsapp mr-1"></i> Pesan
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Pagination -->
            <div class="mt-8">
                {{ $products->links() }}
            </div>
        @else
            <div class="text-center py-16">
                <i class="fas fa-shopping-bag text-6xl text-gray-300 mb-4"></i>
                <h3 class="text-xl font-bold text-gray-600 mb-2">Belum Ada Produk</h3>
                <p class="text-gray-500">Produk akan muncul setelah ditambahkan oleh admin.</p>
            </div>
        @endif
    </div>
</section>
@endsection
