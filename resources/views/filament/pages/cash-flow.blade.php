<x-filament-panels::page>
    <form wire:submit="loadData">
        <div class="fi-fo-field-wrp">{{ $this->form }}</div>
        <x-filament::button type="submit" icon="heroicon-o-magnifying-glass" class="mt-4">
            📊 Tampilkan Arus Kas
        </x-filament::button>
    </form>

    @if(count($incomeByCategory) > 0 || count($expenseByCategory) > 0)
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-4">
            <x-filament::section>
                <x-slot name="heading">💰 Kas Masuk (Pendapatan)</x-slot>
                <table class="w-full text-sm">
                    <thead><tr class="border-b bg-green-50">
                        <th class="px-3 py-2 text-left">Kategori</th>
                        <th class="px-3 py-2 text-right">Jumlah</th>
                    </tr></thead>
                    <tbody>
                        @forelse($incomeByCategory as $cat => $amount)
                            <tr class="border-b">
                                <td class="px-3 py-2">{{ $cat }}</td>
                                <td class="px-3 py-2 text-right text-green-600">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-3 py-4 text-center text-gray-500">Belum ada pendapatan</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 font-bold bg-green-100">
                            <td class="px-3 py-2">Total Kas Masuk</td>
                            <td class="px-3 py-2 text-right text-green-700">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">💸 Kas Keluar (Pengeluaran)</x-slot>
                <table class="w-full text-sm">
                    <thead><tr class="border-b bg-red-50">
                        <th class="px-3 py-2 text-left">Kategori</th>
                        <th class="px-3 py-2 text-right">Jumlah</th>
                    </tr></thead>
                    <tbody>
                        @forelse($expenseByCategory as $cat => $amount)
                            <tr class="border-b">
                                <td class="px-3 py-2">{{ $cat }}</td>
                                <td class="px-3 py-2 text-right text-red-600">Rp {{ number_format($amount, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="2" class="px-3 py-4 text-center text-gray-500">Belum ada pengeluaran</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 font-bold bg-red-100">
                            <td class="px-3 py-2">Total Kas Keluar</td>
                            <td class="px-3 py-2 text-right text-red-700">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </x-filament::section>
        </div>

        <div class="mt-4">
            <x-filament::section>
                <div class="text-center py-4">
                    <div class="text-sm text-gray-500">Arus Kas Bersih</div>
                    <div class="text-3xl font-bold {{ $netCashFlow >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $netCashFlow >= 0 ? '📈' : '📉' }} Rp {{ number_format(abs($netCashFlow), 0, ',', '.') }}
                        <span class="text-lg">({{ $netCashFlow >= 0 ? 'POSITIF' : 'NEGATIF' }})</span>
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        = Kas Masuk Rp {{ number_format($totalIncome, 0, ',', '.') }} − Kas Keluar Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </div>
                </div>
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
