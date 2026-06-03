<x-filament-panels::page>
    <form wire:submit.prevent="loadData">
        <div class="fi-fo-field-wrp">{{ $this->form }}</div>
        <div class="mt-4">
            <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">Tampilkan Buku Besar</x-filament::button>
        </div>
    </form>

    @if(count($entries) > 0)
        <div class="mt-6">
            <x-filament::section>
                <x-slot name="heading">Buku Besar — {{ \App\Models\Account::where('code', $data['account_code'])->first()->name ?? '' }}</x-slot>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="border-b bg-gray-50">
                            <th class="px-3 py-2 text-left">Tanggal</th>
                            <th class="px-3 py-2 text-left">No. Ref</th>
                            <th class="px-3 py-2 text-left">Keterangan</th>
                            <th class="px-3 py-2 text-right">Debet</th>
                            <th class="px-3 py-2 text-right">Kredit</th>
                            <th class="px-3 py-2 text-right">Saldo</th>
                        </tr></thead>
                        <tbody>
                            @php $rb = 0; @endphp
                            @foreach($entries as $e)
                                @php $rb += $e['debit'] - $e['credit']; @endphp
                                <tr class="border-b">
                                    <td class="px-3 py-2">{{ \Carbon\Carbon::parse($e['entry_date'])->format('d/m/Y') }}</td>
                                    <td class="px-3 py-2">{{ $e['transaction']['transaction_number'] ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $e['description'] }}</td>
                                    <td class="px-3 py-2 text-right">@if($e['debit'] > 0) Rp {{ number_format($e['debit'], 0, ',', '.') }} @endif</td>
                                    <td class="px-3 py-2 text-right">@if($e['credit'] > 0) Rp {{ number_format($e['credit'], 0, ',', '.') }} @endif</td>
                                    <td class="px-3 py-2 text-right font-bold">Rp {{ number_format($rb, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 font-bold bg-gray-100">
                                <td colspan="3" class="px-3 py-2">Total</td>
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
