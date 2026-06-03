@extends('layouts.village')

@section('title', $unit->name . ' - Unit Usaha BUMDes')
@section('description', $unit->description ?? 'Unit usaha BUMDes Keude Bakongan')

@section('content')
<!-- Hero Section -->
<section class="relative py-20 bg-gradient-to-br from-{{ $unit->color ?? 'indigo' }}-900 via-{{ $unit->color ?? 'indigo' }}-800 to-{{ $unit->color ?? 'indigo' }}-700 text-white overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.4\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>
    <div class="container mx-auto px-4 relative">
        <div class="flex flex-col md:flex-row items-center gap-8">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-4">
                    <a href="{{ route('units.index') }}" class="text-white/70 hover:text-white transition-colors">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <span class="px-3 py-1 bg-white/10 backdrop-blur-sm rounded-full text-sm">
                        Unit Usaha
                    </span>
                </div>
                <h1 class="text-4xl md:text-5xl font-bold mb-4">{{ $unit->name }}</h1>
                <p class="text-xl text-{{ $unit->color ?? 'indigo' }}-100 mb-6">
                    {{ $unit->description ?? 'Unit usaha BUMDes Keude Bakongan' }}
                </p>
                
                <div class="flex flex-wrap gap-4">
                    @if($unit->phone)
                    <a href="https://wa.me/{{ $unit->phone }}" target="_blank" 
                       class="px-6 py-3 bg-green-500 hover:bg-green-600 rounded-full font-semibold transition-colors">
                        <i class="fab fa-whatsapp mr-2"></i>WhatsApp
                    </a>
                    @endif
                    
                    @if($unit->email)
                    <a href="mailto:{{ $unit->email }}" 
                       class="px-6 py-3 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-full font-semibold transition-colors">
                        <i class="fas fa-envelope mr-2"></i>Email
                    </a>
                    @endif
                </div>
            </div>
            
            <div class="flex-shrink-0">
                @if($unit->image)
                    <img src="{{ asset('storage/' . $unit->image) }}" alt="{{ $unit->name }}" 
                         class="w-64 h-64 rounded-2xl shadow-2xl object-cover">
                @else
                    <div class="w-64 h-64 rounded-2xl shadow-2xl bg-white/10 flex items-center justify-center">
                        <i class="fas fa-building text-8xl text-white/30"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- Informasi Unit -->
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-3 gap-8">
            <!-- Info Card -->
            <div class="md:col-span-1">
                <div class="bg-gray-50 rounded-2xl p-6 sticky top-24">
                    <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Unit</h3>
                    
                    <div class="space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-{{ $unit->color ?? 'indigo' }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-tag text-{{ $unit->color ?? 'indigo' }}-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Status</p>
                                <p class="font-medium text-gray-800">
                                    @if($unit->status === 'active')
                                        <span class="text-green-600">Aktif</span>
                                    @else
                                        <span class="text-red-600">Non-Aktif</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        
                        @if($unit->manager)
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-{{ $unit->color ?? 'indigo' }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-user text-{{ $unit->color ?? 'indigo' }}-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Pengelola</p>
                                <p class="font-medium text-gray-800">{{ $unit->manager }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($unit->phone)
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-{{ $unit->color ?? 'indigo' }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-phone text-{{ $unit->color ?? 'indigo' }}-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Telepon</p>
                                <p class="font-medium text-gray-800">{{ $unit->phone }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($unit->email)
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-{{ $unit->color ?? 'indigo' }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope text-{{ $unit->color ?? 'indigo' }}-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Email</p>
                                <p class="font-medium text-gray-800">{{ $unit->email }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($unit->address)
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-{{ $unit->color ?? 'indigo' }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-map-marker-alt text-{{ $unit->color ?? 'indigo' }}-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Alamat</p>
                                <p class="font-medium text-gray-800">{{ $unit->address }}</p>
                            </div>
                        </div>
                        @endif
                        
                        @if($unit->operating_hours)
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 bg-{{ $unit->color ?? 'indigo' }}-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-clock text-{{ $unit->color ?? 'indigo' }}-600"></i>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500">Jam Operasional</p>
                                <p class="font-medium text-gray-800">{{ $unit->operating_hours }}</p>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Main Content -->
            <div class="md:col-span-2">
                <!-- Tentang Unit -->
                <div class="mb-12">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">Tentang {{ $unit->name }}</h2>
                    <div class="prose prose-lg max-w-none text-gray-600">
                        {!! $unit->about ?? '<p>Unit usaha ini bergerak di bidang untuk meningkatkan kesejahteraan masyarakat desa.</p>' !!}
                    </div>
                </div>
                
                <!-- Produk Unit -->
                <div class="mb-12">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-2xl font-bold text-gray-800">Produk & Layanan</h2>
                        @if($products->count() > 0)
                        <a href="{{ route('products.index') }}?unit={{ $unit->id }}" 
                           class="text-{{ $unit->color ?? 'indigo' }}-600 hover:text-{{ $unit->color ?? 'indigo' }}-800 font-medium">
                            Lihat Semua <i class="fas fa-arrow-right ml-1"></i>
                        </a>
                        @endif
                    </div>
                    
                    @if($products->count() > 0)
                    <div class="grid sm:grid-cols-2 gap-6">
                        @foreach($products->take(4) as $product)
                        <div class="bg-white rounded-xl border border-gray-200 overflow-hidden hover:shadow-lg transition-shadow">
                            <div class="h-40 bg-gray-100">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <i class="fas fa-box text-4xl text-gray-300"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="font-bold text-gray-800 mb-2">{{ $product->name }}</h3>
                                <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ $product->description }}</p>
                                <div class="flex items-center justify-between">
                                    <span class="text-lg font-bold text-{{ $unit->color ?? 'indigo' }}-600">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </span>
                                    <a href="https://wa.me/{{ $unit->phone ?? '628123456789' }}?text=Halo, saya tertarik dengan {{ $product->name }}" 
                                       target="_blank" 
                                       class="px-4 py-2 bg-green-500 hover:bg-green-600 text-white text-sm rounded-lg transition-colors">
                                        <i class="fab fa-whatsapp mr-1"></i>Pesan
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="bg-gray-50 rounded-xl p-8 text-center">
                        <i class="fas fa-box-open text-5xl text-gray-300 mb-4"></i>
                        <p class="text-gray-500">Belum ada produk tersedia</p>
                    </div>
                    @endif
                </div>
                
                <!-- Berita Terkait -->
                @if($news->count() > 0)
                <div>
                    <h2 class="text-2xl font-bold text-gray-800 mb-6">Berita Terkait</h2>
                    <div class="space-y-4">
                        @foreach($news as $item)
                        <div class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-md transition-shadow">
                            <div class="flex gap-4">
                                <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded-lg overflow-hidden">
                                    @if($item->image)
                                        <img src="{{ asset('storage/' . $item->image) }}" alt="{{ $item->title }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center">
                                            <i class="fas fa-newspaper text-2xl text-gray-300"></i>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h3 class="font-bold text-gray-800 mb-1">{{ $item->title }}</h3>
                                    <p class="text-sm text-gray-600 mb-2 line-clamp-2">{{ Str::limit(strip_tags($item->content), 100) }}</p>
                                    <span class="text-xs text-gray-400">
                                        <i class="fas fa-calendar mr-1"></i>{{ $item->published_at->format('d M Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-r from-{{ $unit->color ?? 'indigo' }}-600 to-{{ $unit->color ?? 'indigo' }}-800 text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4">Butuh Informasi Lebih Lanjut?</h2>
        <p class="text-xl text-{{ $unit->color ?? 'indigo' }}-100 mb-8 max-w-2xl mx-auto">
            Hubungi kami untuk pertanyaan, pemesanan, atau kerja sama
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            @if($unit->phone)
            <a href="https://wa.me/{{ $unit->phone }}" target="_blank" 
               class="px-8 py-4 bg-green-500 hover:bg-green-600 rounded-full font-semibold transition-colors">
                <i class="fab fa-whatsapp mr-2"></i>WhatsApp Kami
            </a>
            @endif
            <a href="{{ route('village.contact') }}" 
               class="px-8 py-4 bg-white/10 hover:bg-white/20 backdrop-blur-sm rounded-full font-semibold transition-colors">
                <i class="fas fa-envelope mr-2"></i>Kirim Email
            </a>
        </div>
    </div>
</section>
@endsection

@push('styles')
<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.prose {
    line-height: 1.8;
}
.prose p {
    margin-bottom: 1rem;
}
</style>
@endpush
