<x-filament-panels::page>
    <form wire:submit.prevent="loadData">
        <div class="fi-fo-field-wrp">{{ $this->form }}</div>
        <div class="mt-4">
            <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">Tampilkan Arus Kas</x-filament::button>
        </div>
    </form>

    @if(count($incomeByCategory) > 0 || count($expenseByCategory) > 0)
        <div class="mt-6 space-y-4">
            <x-filament::section>
                <x-slot name="heading">Kas Masuk</x-slot>
                <table class="w-full text-sm">
                    <thead><tr class="border-b bg-green-50"><th class="px-3 py-2 text-left">Kategori</th><th class="px-3 py-2 text-right">Jumlah</th></tr></thead>
                    <tbody>
                        @forelse($incomeByCategory as $cat => $amt)
                            <tr class="border-b"><td class="px-3 py-2">{{ $cat }}</td><td class="px-3 py-2 text-right text-green-600">Rp {{ number_format($amt, 0, ',', '.') }}</td></tr>
                        @empty <tr><td colspan="2" class="px-3 py-4 text-center text-gray-500">Belum ada</td></tr>@endforelse
                    </tbody>
                    <tfoot><tr class="border-t-2 font-bold bg-green-100"><td class="px-3 py-2">Total</td><td class="px-3 py-2 text-right">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td></tr></tfoot>
                </table>
            </x-filament::section>
            <x-filament::section>
                <x-slot name="heading">Kas Keluar</x-slot>
                <table class="w-full text-sm">
                    <thead><tr class="border-b bg-red-50"><th class="px-3 py-2 text-left">Kategori</th><th class="px-3 py-2 text-right">Jumlah</th></tr></thead>
                    <tbody>
                        @forelse($expenseByCategory as $cat => $amt)
                            <tr class="border-b"><td class="px-3 py-2">{{ $cat }}</td><td class="px-3 py-2 text-right text-red-600">Rp {{ number_format($amt, 0, ',', '.') }}</td></tr>
                        @empty <tr><td colspan="2" class="px-3 py-4 text-center text-gray-500">Belum ada</td></tr>@endforelse
                    </tbody>
                    <tfoot><tr class="border-t-2 font-bold bg-red-100"><td class="px-3 py-2">Total</td><td class="px-3 py-2 text-right">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td></tr></tfoot>
                </table>
            </x-filament::section>
            <div class="text-center py-4">
                <div class="text-2xl font-bold {{ $netCashFlow >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    Arus Kas Bersih: Rp {{ number_format(abs($netCashFlow), 0, ',', '.') }}
                    ({{ $netCashFlow >= 0 ? 'POSITIF' : 'NEGATIF' }})
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
