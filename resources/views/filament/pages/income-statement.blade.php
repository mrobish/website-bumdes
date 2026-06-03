<x-filament-panels::page>
    <form wire:submit.prevent="loadData">
        <div class="fi-fo-field-wrp">{{ $this->form }}</div>
        <div class="mt-4">
            <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">Tampilkan Laba/Rugi</x-filament::button>
        </div>
    </form>

    @if(count($revenues) > 0 || count($expenses) > 0)
        <div class="mt-6 space-y-4">
            <x-filament::section>
                <x-slot name="heading">Pendapatan</x-slot>
                <table class="w-full text-sm">
                    <thead><tr class="border-b bg-green-50"><th class="px-3 py-2 text-left">Kode</th><th class="px-3 py-2 text-left">Nama</th><th class="px-3 py-2 text-right">Jumlah</th></tr></thead>
                    <tbody>
                        @forelse($revenues as $r)
                            <tr class="border-b"><td class="px-3 py-2 font-mono">{{ $r['code'] }}</td><td class="px-3 py-2">{{ $r['name'] }}</td><td class="px-3 py-2 text-right text-green-600">Rp {{ number_format($r['amount'], 0, ',', '.') }}</td></tr>
                        @empty <tr><td colspan="3" class="px-3 py-4 text-center text-gray-500">Belum ada</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot><tr class="border-t-2 font-bold bg-green-100"><td colspan="2" class="px-3 py-2">Total</td><td class="px-3 py-2 text-right text-green-700">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td></tr></tfoot>
                </table>
            </x-filament::section>
            <x-filament::section>
                <x-slot name="heading">Beban</x-slot>
                <table class="w-full text-sm">
                    <thead><tr class="border-b bg-red-50"><th class="px-3 py-2 text-left">Kode</th><th class="px-3 py-2 text-left">Nama</th><th class="px-3 py-2 text-right">Jumlah</th></tr></thead>
                    <tbody>
                        @forelse($expenses as $e)
                            <tr class="border-b"><td class="px-3 py-2 font-mono">{{ $e['code'] }}</td><td class="px-3 py-2">{{ $e['name'] }}</td><td class="px-3 py-2 text-right text-red-600">Rp {{ number_format($e['amount'], 0, ',', '.') }}</td></tr>
                        @empty <tr><td colspan="3" class="px-3 py-4 text-center text-gray-500">Belum ada</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot><tr class="border-t-2 font-bold bg-red-100"><td colspan="2" class="px-3 py-2">Total</td><td class="px-3 py-2 text-right text-red-700">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td></tr></tfoot>
                </table>
            </x-filament::section>
            <x-filament::section>
                <x-slot name="heading">Laba / Rugi Bersih</x-slot>
                <div class="text-center py-4">
                    <div class="text-2xl font-bold {{ $netIncome >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $netIncome >= 0 ? 'LABA' : 'RUGI' }} Rp {{ number_format(abs($netIncome), 0, ',', '.') }}
                    </div>
                </div>
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
