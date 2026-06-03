@extends('layouts.village')

@section('title', 'Unit Usaha - ' . $villageInfo->village_name ?? 'BUMDes')
@section('description', 'Daftar unit usaha BUMDes Keude Bakongan')

@section('content')
<!-- Hero Section -->
<section class="relative py-20 bg-gradient-to-br from-indigo-900 via-purple-900 to-pink-800 text-white overflow-hidden">
    <div class="absolute inset-0 opacity-10">
        <div class="absolute inset-0" style="background-image: url('data:image/svg+xml,%3Csvg width=\"60\" height=\"60\" viewBox=\"0 0 60 60\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"none\" fill-rule=\"evenodd\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.4\"%3E%3Cpath d=\"M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z\"/%3E%3C/g%3E%3C/g%3E%3C/svg%3E');"></div>
    </div>
    <div class="container mx-auto px-4 relative">
        <div class="text-center">
            <span class="inline-block px-4 py-2 bg-white/10 backdrop-blur-sm rounded-full text-sm font-medium mb-4">
                Badan Usaha Milik Desa
            </span>
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Unit Usaha Kami</h1>
            <p class="text-xl text-purple-100 max-w-3xl mx-auto">
                BUMDes Keude Bakongan mengelola berbagai unit usaha untuk kemajuan desa
            </p>
        </div>
    </div>
</section>

<!-- Unit Usaha Grid -->
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($units as $unit)
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                <!-- Unit Image -->
                <div class="relative h-48 bg-gradient-to-br from-{{ $unit->color ?? 'indigo' }}-500 to-{{ $unit->color ?? 'indigo' }}-700">
                    @if($unit->image)
                        <img src="{{ asset('storage/' . $unit->image) }}" alt="{{ $unit->name }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center">
                            <i class="fas fa-building text-6xl text-white/30"></i>
                        </div>
                    @endif
                    <div class="absolute top-4 right-4">
                        @if($unit->status === 'active')
                            <span class="px-3 py-1 bg-green-500 text-white text-sm rounded-full">Aktif</span>
                        @else
                            <span class="px-3 py-1 bg-red-500 text-white text-sm rounded-full">Non-Aktif</span>
                        @endif
                    </div>
                </div>
                
                <!-- Unit Info -->
                <div class="p-6">
                    <h3 class="text-xl font-bold text-gray-800 mb-2">{{ $unit->name }}</h3>
                    <p class="text-gray-600 mb-4 line-clamp-2">{{ $unit->description ?? 'Unit usaha BUMDes' }}</p>
                    
                    <div class="flex items-center justify-between">
                        <a href="{{ route('units.show', $unit->slug) }}" 
                           class="inline-flex items-center text-{{ $unit->color ?? 'indigo' }}-600 hover:text-{{ $unit->color ?? 'indigo' }}-800 font-medium">
                            Lihat Detail
                            <i class="fas fa-arrow-right ml-2"></i>
                        </a>
                        
                        @if($unit->phone)
                        <a href="https://wa.me/{{ $unit->phone }}" target="_blank" 
                           class="text-green-500 hover:text-green-700">
                            <i class="fab fa-whatsapp text-xl"></i>
                        </a>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-16 bg-gradient-to-r from-indigo-600 to-purple-600 text-white">
    <div class="container mx-auto px-4 text-center">
        <h2 class="text-3xl font-bold mb-4">Tertarik dengan Unit Usaha Kami?</h2>
        <p class="text-xl text-indigo-100 mb-8 max-w-2xl mx-auto">
            Hubungi kami untuk informasi lebih lanjut atau kerja sama
        </p>
        <div class="flex flex-col sm:flex-row gap-4 justify-center">
            <a href="https://wa.me/{{ $villageInfo->phone ?? '628123456789' }}" target="_blank" 
               class="px-8 py-4 bg-green-500 hover:bg-green-600 rounded-full font-semibold transition-colors">
                <i class="fab fa-whatsapp mr-2"></i>WhatsApp Kami
            </a>
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
</style>
@endpush
