<x-filament-panels::page>
    <form wire:submit.prevent="loadData">
        <div class="fi-fo-field-wrp">{{ $this->form }}</div>
        <div class="mt-4">
            <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">Tampilkan Neraca</x-filament::button>
        </div>
    </form>

    @if(count($assets) > 0 || count($liabilities) > 0 || count($equity) > 0)
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
                        @forelse($liabilities as $l)
                            <tr class="border-b"><td class="px-3 py-2 font-mono">{{ $l['code'] }}</td><td class="px-3 py-2">{{ $l['name'] }}</td><td class="px-3 py-2 text-right">Rp {{ number_format($l['amount'], 0, ',', '.') }}</td></tr>
                        @endforeach
                        @forelse($equity as $e)
                            <tr class="border-b"><td class="px-3 py-2 font-mono">{{ $e['code'] }}</td><td class="px-3 py-2">{{ $e['name'] }}</td><td class="px-3 py-2 text-right">Rp {{ number_format($e['amount'], 0, ',', '.') }}</td></tr>
                        @endforeach
                    </tbody>
                    <tfoot><tr class="border-t-2 font-bold bg-red-100"><td colspan="2" class="px-3 py-2">Total</td><td class="px-3 py-2 text-right">Rp {{ number_format($totalLiability + $totalEquity, 0, ',', '.') }}</td></tr></tfoot>
                </table>
            </x-filament::section>
            <div class="text-center py-4">
                @if(abs($totalAsset - ($totalLiability + $totalEquity)) < 0.01)
                    <span class="text-green-600 font-bold">NERACA SEIMBANG</span>
                @else
                    <span class="text-red-600 font-bold">NERACA TIDAK SEIMBANG</span>
                @endif
            </div>
        </div>
    @endif
</x-filament-panels::page>
