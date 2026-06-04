@extends('install.layout')

@section('content')
<div class="bg-white rounded-lg shadow-md p-6">
    <h2 class="text-2xl font-bold text-gray-900 mb-2">System Requirements Check</h2>
    <p class="text-gray-600 mb-6">Memeriksa persyaratan sistem sebelum instalasi...</p>

    <div class="space-y-3">
        @foreach($checks as $check)
            <div class="flex items-center justify-between p-3 rounded-lg {{ $check['passed'] ? 'bg-green-50' : 'bg-red-50' }}">
                <div class="flex items-center gap-3">
                    @if($check['passed'])
                        <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    @else
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    @endif
                    <span class="font-medium {{ $check['passed'] ? 'text-green-800' : 'text-red-800' }}">{{ $check['name'] }}</span>
                </div>
                <span class="text-sm {{ $check['passed'] ? 'text-green-600' : 'text-red-600' }}">{{ $check['current'] }}</span>
            </div>
        @endforeach
    </div>

    @php $allPassed = collect($checks)->every('passed'); @endphp

    <div class="mt-6 flex justify-end">
        @if($allPassed)
            <a href="{{ route('install.step2') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Lanjut ke Database →
            </a>
        @else
            <div class="text-red-600 font-medium">
                ⚠️ Beberapa persyaratan belum terpenuhi. Silakan perbaiki terlebih dahulu.
            </div>
        @endif
    </div>
</div>
@endsection
