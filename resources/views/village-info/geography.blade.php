@extends('layouts.village')

@section('title', ucfirst('geography') . ' - ' . ($villageInfo->village_name ?? ''))

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-800">ucfirst('geography')</h1>
    <p class="text-gray-600 mt-2">Informasi geography desa</p>
</div>

<div class="bg-white rounded-2xl shadow-lg p-8">
    <p class="text-gray-600">Halaman geography sedang dalam pengembangan.</p>
    <a href="/" class="inline-block mt-4 text-blue-600 hover:underline">&larr; Kembali ke Beranda</a>
</div>
@endsection
