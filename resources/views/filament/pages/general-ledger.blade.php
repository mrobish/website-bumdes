<x-filament-panels::page>
    <form wire:submit="loadData">
        {!! $this->form !!}
        <x-filament::button type="submit" icon="heroicon-o-magnifying-gang" class="mt-4">
            📊 Tampilkan Buku Besar
        </x-filament::button>
    </form>

    @if(count($entries) > 0)
        <div class="mt-6">
            <x-filament::section>
                <x-slot name="heading">
                    📋 Buku Besar — {{ \App\Models\Account::where('code', $data['account_code'])->first()->name ?? '' }}
                </x-slot>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="px-3 py-2 text-left">Tanggal</th>
                                <th class="px-3 py-2 text-left">No. Ref</th>
                                <th class="px-3 py-2 text-left">Keterangan</th>
                                <th class="px-3 py-2 text-left">Unit</th>
                                <th class="px-3 py-2 text-right">Debet</th>
                                <th class="px-3 py-2 text-right">Kredit</th>
                                <th class="px-3 py-2 text-right">Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $runningBalance = 0; @endphp
                            @foreach($entries as $entry)
                                @php
                                    $runningBalance += $entry['debit'] - $entry['credit'];
                                @endphp
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-3 py-2">{{ \Carbon\Carbon::parse($entry['entry_date'])->format('d/m/Y') }}</td>
                                    <td class="px-3 py-2">{{ $entry['transaction']['transaction_number'] ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $entry['description'] }}</td>
                                    <td class="px-3 py-2">{{ $entry['unit']['name'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-right">
                                        @if($entry['debit'] > 0)
                                            Rp {{ number_format($entry['debit'], 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        @if($entry['credit'] > 0)
                                            Rp {{ number_format($entry['credit'], 0, ',', '.') }}
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-right font-bold {{ $runningBalance >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                        Rp {{ number_format($runningBalance, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 font-bold bg-gray-100">
                                <td colspan="4" class="px-3 py-2">Total</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($balance, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
