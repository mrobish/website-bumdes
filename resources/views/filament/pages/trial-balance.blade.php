<x-filament-panels::page>
    <form wire:submit="loadData">
        <div class="fi-fo-field-wrp">{{ $this->form }}</div>
        <x-filament::button type="submit" icon="heroicon-o-magnifying-glass" class="mt-4">
            📊 Tampilkan Neraca Saldo
        </x-filament::button>
    </form>

    @if(count($accounts) > 0)
        <div class="mt-6">
            <x-filament::section>
                <x-slot name="heading">
                    📋 Neraca Saldo Tahun {{ $data['year'] }}
                    @if($isBalanced)
                        <span class="ml-2 text-green-600">✅ SEIMBANG</span>
                    @else
                        <span class="ml-2 text-red-600">❌ TIDAK SEIMBANG</span>
                    @endif
                </x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="px-3 py-2 text-left">Kode</th>
                                <th class="px-3 py-2 text-left">Nama Akun</th>
                                <th class="px-3 py-2 text-left">Jenis</th>
                                <th class="px-3 py-2 text-right">Debet</th>
                                <th class="px-3 py-2 text-right">Kredit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($accounts as $account)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-3 py-2 font-mono">{{ $account['code'] }}</td>
                                    <td class="px-3 py-2">{{ $account['name'] }}</td>
                                    <td class="px-3 py-2">
                                        <span class="px-2 py-1 text-xs rounded-full
                                            {{ match($account['type']) {
                                                'asset' => 'bg-green-100 text-green-800',
                                                'liability' => 'bg-red-100 text-red-800',
                                                'equity' => 'bg-blue-100 text-blue-800',
                                                'revenue' => 'bg-yellow-100 text-yellow-800',
                                                'expense' => 'bg-gray-100 text-gray-800',
                                                default => 'bg-gray-100',
                                            } }}">
                                            {{ match($account['type']) {
                                                'asset' => 'Aset',
                                                'liability' => 'Kewajiban',
                                                'equity' => 'Ekuitas',
                                                'revenue' => 'Pendapatan',
                                                'expense' => 'Beban',
                                                default => $account['type'],
                                            } }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        @if($account['debit'] > 0) Rp {{ number_format($account['debit'], 0, ',', '.') }} @endif
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        @if($account['credit'] > 0) Rp {{ number_format($account['credit'], 0, ',', '.') }} @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 font-bold bg-gray-100">
                                <td colspan="3" class="px-3 py-2">TOTAL</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
