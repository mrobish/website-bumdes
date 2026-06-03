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
        </div>
        <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">Tampilkan Konsolidasi</x-filament::button>
    </form>

    @if($loaded)
        <div class="mt-6 space-y-4">
            {{-- Per Unit --}}
            <x-filament::section>
                <x-slot name="heading">Per Unit</x-slot>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="border-b bg-gray-50">
                            <th class="px-3 py-2 text-left">Unit</th>
                            <th class="px-3 py-2 text-right">Kas</th>
                            <th class="px-3 py-2 text-right">Pendapatan</th>
                            <th class="px-3 py-2 text-right">Beban</th>
                            <th class="px-3 py-2 text-right">Laba/Rugi</th>
                        </tr></thead>
                        <tbody>
                            @forelse($units as $u)
                                <tr class="border-b">
                                    <td class="px-3 py-2 font-bold">{{ $u['name'] }}</td>
                                    <td class="px-3 py-2 text-right">Rp {{ number_format($u['cash'], 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-right text-green-600">Rp {{ number_format($u['revenue'], 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-right text-red-600">Rp {{ number_format($u['expense'], 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-right font-bold {{ $u['net'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        Rp {{ number_format($u['net'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-3 py-4 text-center text-gray-500">Belum ada data</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 font-bold bg-gray-100">
                                <td class="px-3 py-2">TOTAL</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($totalCash, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right text-green-700">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right text-red-700">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($totalRevenue - $totalExpense, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-filament::section>

            {{-- Neraca Konsolidasi --}}
            <x-filament::section>
                <x-slot name="heading">Neraca Konsolidasi</x-slot>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-green-50 rounded-lg">
                        <div class="text-sm text-gray-500">Total Aset</div>
                        <div class="text-xl font-bold text-green-600">Rp {{ number_format($totalAsset, 0, ',', '.') }}</div>
                    </div>
                    <div class="text-center p-4 bg-red-50 rounded-lg">
                        <div class="text-sm text-gray-500">Total Kewajiban</div>
                        <div class="text-xl font-bold text-red-600">Rp {{ number_format($totalLiability, 0, ',', '.') }}</div>
                    </div>
                    <div class="text-center p-4 bg-blue-50 rounded-lg">
                        <div class="text-sm text-gray-500">Total Ekuitas</div>
                        <div class="text-xl font-bold text-blue-600">Rp {{ number_format($totalEquity, 0, ',', '.') }}</div>
                    </div>
                </div>
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
