<x-filament-panels::page>
    <form wire:submit="loadData">
        {!! $this->form !!}
        <x-filament::button type="submit" icon="heroicon-o-magnifying-gang" class="mt-4">
            📊 Tampilkan Neraca
        </x-filament::button>
    </form>

    @if(count($assets) > 0 || count($liabilities) > 0 || count($equity) > 0)
        <div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-4">
            {{-- ASET --}}
            <x-filament::section>
                <x-slot name="heading">🏢 Aset</x-slot>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b bg-green-50">
                            <th class="px-3 py-2 text-left">Kode</th>
                            <th class="px-3 py-2 text-left">Nama Akun</th>
                            <th class="px-3 py-2 text-right">Jumlah</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                            <tr class="border-b">
                                <td class="px-3 py-2 font-mono">{{ $asset['code'] }}</td>
                                <td class="px-3 py-2">{{ $asset['name'] }}</td>
                                <td class="px-3 py-2 text-right {{ $asset['is_negative'] ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $asset['is_negative'] ? '(' : '' }}Rp {{ number_format($asset['amount'], 0, ',', '.') }}{{ $asset['is_negative'] ? ')' : '' }}
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="px-3 py-4 text-center text-gray-500">Belum ada aset</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="border-t-2 font-bold bg-green-100">
                            <td colspan="2" class="px-3 py-2">Total Aset</td>
                            <td class="px-3 py-2 text-right text-green-700">Rp {{ number_format($totalAsset, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </x-filament::section>

            <div class="space-y-4">
                {{-- KEWAJIBAN --}}
                <x-filament::section>
                    <x-slot name="heading">📋 Kewajiban</x-slot>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-red-50">
                                <th class="px-3 py-2 text-left">Kode</th>
                                <th class="px-3 py-2 text-left">Nama Akun</th>
                                <th class="px-3 py-2 text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($liabilities as $lib)
                                <tr class="border-b">
                                    <td class="px-3 py-2 font-mono">{{ $lib['code'] }}</td>
                                    <td class="px-3 py-2">{{ $lib['name'] }}</td>
                                    <td class="px-3 py-2 text-right text-red-600">Rp {{ number_format($lib['amount'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-3 py-4 text-center text-gray-500">Belum ada kewajiban</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 font-bold bg-red-100">
                                <td colspan="2" class="px-3 py-2">Total Kewajiban</td>
                                <td class="px-3 py-2 text-right text-red-700">Rp {{ number_format($totalLiability, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </x-filament::section>

                {{-- EKUITAS --}}
                <x-filament::section>
                    <x-slot name="heading">💎 Ekuitas</x-slot>
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-blue-50">
                                <th class="px-3 py-2 text-left">Kode</th>
                                <th class="px-3 py-2 text-left">Nama Akun</th>
                                <th class="px-3 py-2 text-right">Jumlah</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($equity as $eq)
                                <tr class="border-b">
                                    <td class="px-3 py-2 font-mono">{{ $eq['code'] }}</td>
                                    <td class="px-3 py-2">{{ $eq['name'] }}</td>
                                    <td class="px-3 py-2 text-right text-blue-600">Rp {{ number_format($eq['amount'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="px-3 py-4 text-center text-gray-500">Belum ada ekuitas</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 font-bold bg-blue-100">
                                <td colspan="2" class="px-3 py-2">Total Ekuitas</td>
                                <td class="px-3 py-2 text-right text-blue-700">Rp {{ number_format($totalEquity, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </x-filament::section>
            </div>
        </div>

        {{-- TOTAL KEWAJIBAN + EKUITAS --}}
        <div class="mt-4">
            <x-filament::section>
                <div class="text-center py-4">
                    <div class="text-sm text-gray-500">Total Kewajiban + Ekuitas</div>
                    <div class="text-2xl font-bold text-blue-600">Rp {{ number_format($totalLiability + $totalEquity, 0, ',', '.') }}</div>
                    <div class="text-sm text-gray-500 mt-2">Total Aset</div>
                    <div class="text-2xl font-bold text-green-600">Rp {{ number_format($totalAsset, 0, ',', '.') }}</div>
                    @if(abs($totalAsset - ($totalLiability + $totalEquity)) < 0.01)
                        <div class="mt-2 text-green-600 font-bold">✅ NERACA SEIMBANG</div>
                    @else
                        <div class="mt-2 text-red-600 font-bold">❌ NERACA TIDAK SEIMBANG</div>
                    @endif
                </div>
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
