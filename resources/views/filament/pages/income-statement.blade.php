<x-filament-panels::page>
    <form wire:submit="loadData">
        <div class="fi-fo-field-wrp">{{ $this->form }}</div>
        <x-filament::button type="submit" icon="heroicon-o-magnifying-gang" class="mt-4">
            📊 Tampilkan Laba/Rugi
        </x-filament::button>
    </form>

    @if(count($revenues) > 0 || count($expenses) > 0)
        <div class="mt-6 space-y-4">
            <x-filament::section>
                <x-slot name="heading">💰 Pendapatan</x-slot>
                <table class="w-full text-sm">
                    <thead><tr class="border-b bg-green-50">
                        <th class="px-3 py-2 text-left">Kode</th>
                        <th class="px-3 py-2 text-left">Nama Akun</th>
                        <th class="px-3 py-2 text-right">Jumlah</th>
                    </tr></thead>
                    <tbody>
                        @forelse($revenues as $rev)
                            <tr class="border-b">
                                <td class="px-3 py-2 font-mono">{{ $rev['code'] }}</td>
                                <td class="px-3 py-2">{{ $rev['name'] }}</td>
                                <td class="px-3 py-2 text-right text-green-600">Rp {{ number_format($rev['amount'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-3 py-4 text-center text-gray-500">Belum ada pendapatan</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 font-bold bg-green-100">
                            <td colspan="2" class="px-3 py-2">Total Pendapatan</td>
                            <td class="px-3 py-2 text-right text-green-700">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">💸 Beban / Pengeluaran</x-slot>
                <table class="w-full text-sm">
                    <thead><tr class="border-b bg-red-50">
                        <th class="px-3 py-2 text-left">Kode</th>
                        <th class="px-3 py-2 text-left">Nama Akun</th>
                        <th class="px-3 py-2 text-right">Jumlah</th>
                    </tr></thead>
                    <tbody>
                        @forelse($expenses as $exp)
                            <tr class="border-b">
                                <td class="px-3 py-2 font-mono">{{ $exp['code'] }}</td>
                                <td class="px-3 py-2">{{ $exp['name'] }}</td>
                                <td class="px-3 py-2 text-right text-red-600">Rp {{ number_format($exp['amount'], 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-3 py-4 text-center text-gray-500">Belum ada beban</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 font-bold bg-red-100">
                            <td colspan="2" class="px-3 py-2">Total Beban</td>
                            <td class="px-3 py-2 text-right text-red-700">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">📊 Laba / Rugi Bersih</x-slot>
                <div class="text-center py-6">
                    <div class="text-3xl font-bold {{ $netIncome >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $netIncome >= 0 ? '✅ LABA' : '❌ RUGI' }} Rp {{ number_format(abs($netIncome), 0, ',', '.') }}
                    </div>
                    <div class="text-sm text-gray-500 mt-2">
                        = Pendapatan Rp {{ number_format($totalRevenue, 0, ',', '.') }} − Beban Rp {{ number_format($totalExpense, 0, ',', '.') }}
                    </div>
                </div>
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
