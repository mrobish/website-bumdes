<x-filament-panels::page>
    <form wire:submit.prevent="loadData">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Tahun</label>
                <select wire:model="year" class="w-full rounded-lg border-gray-300 shadow-sm">
                    @for($y = now()->year + 1; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Unit</label>
                <select wire:model="unitId" class="w-full rounded-lg border-gray-300 shadow-sm">
                    <option value="">Semua Unit</option>
                    @foreach(\App\Models\BusinessUnit::pluck('name', 'id') as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">Tampilkan Neraca</x-filament::button>
    </form>

    @if($loaded)
        <div class="mt-6 space-y-4">
            <x-filament::section>
                <x-slot name="heading">Aset</x-slot>
                <table class="w-full text-sm">
                    <thead><tr class="border-b bg-green-50"><th class="px-3 py-2 text-left">Kode</th><th class="px-3 py-2 text-left">Nama</th><th class="px-3 py-2 text-right">Jumlah</th></tr></thead>
                    <tbody>
                        @forelse($assets as $a)
                            <tr class="border-b"><td class="px-3 py-2 font-mono">{{ $a['code'] }}</td><td class="px-3 py-2">{{ $a['name'] }}</td><td class="px-3 py-2 text-right">Rp {{ number_format($a['amount'], 0, ',', '.') }}</td></tr>
                        @empty <tr><td colspan="3" class="px-3 py-4 text-center text-gray-500">Belum ada</td></tr>@endforelse
                    </tbody>
                    <tfoot><tr class="border-t-2 font-bold bg-green-100"><td colspan="2" class="px-3 py-2">Total Aset</td><td class="px-3 py-2 text-right">Rp {{ number_format($totalAsset, 0, ',', '.') }}</td></tr></tfoot>
                </table>
            </x-filament::section>
            <x-filament::section>
                <x-slot name="heading">Kewajiban + Ekuitas</x-slot>
                <table class="w-full text-sm">
                    <thead><tr class="border-b bg-red-50"><th class="px-3 py-2 text-left">Kode</th><th class="px-3 py-2 text-left">Nama</th><th class="px-3 py-2 text-right">Jumlah</th></tr></thead>
                    <tbody>
                        @foreach($liabilities as $l)
                            <tr class="border-b"><td class="px-3 py-2 font-mono">{{ $l['code'] }}</td><td class="px-3 py-2">{{ $l['name'] }}</td><td class="px-3 py-2 text-right">Rp {{ number_format($l['amount'], 0, ',', '.') }}</td></tr>
                        @endforeach
                        @foreach($equity as $e)
                            <tr class="border-b"><td class="px-3 py-2 font-mono">{{ $e['code'] }}</td><td class="px-3 py-2">{{ $e['name'] }}</td><td class="px-3 py-2 text-right">Rp {{ number_format($e['amount'], 0, ',', '.') }}</td></tr>
                        @endforeach
                    </tbody>
                    <tfoot><tr class="border-t-2 font-bold bg-red-100"><td colspan="2" class="px-3 py-2">Total</td><td class="px-3 py-2 text-right">Rp {{ number_format($totalLiability + $totalEquity, 0, ',', '.') }}</td></tr></tfoot>
                </table>
            </x-filament::section>
            <div class="text-center py-4 bg-gray-50 rounded-lg">
                @if(abs($totalAsset - ($totalLiability + $totalEquity)) < 0.01)
                    <span class="text-green-600 font-bold text-lg">NERACA SEIMBANG</span>
                @else
                    <span class="text-red-600 font-bold text-lg">NERACA TIDAK SEIMBANG</span>
                @endif
            </div>
        </div>
    @endif
</x-filament-panels::page>
