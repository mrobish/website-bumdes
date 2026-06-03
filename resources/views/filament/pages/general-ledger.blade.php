<x-filament-panels::page>
    <form wire:submit.prevent="loadData">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Akun</label>
                <select wire:model="accountCode" class="w-full rounded-lg border-gray-300 shadow-sm">
                    <option value="">-- Pilih Akun --</option>
                    @foreach(\App\Models\Account::active()->orderBy('code')->get() as $acc)
                        <option value="{{ $acc->code }}">{{ $acc->code }} — {{ $acc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Dari</label>
                <input type="date" wire:model="dateFrom" class="w-full rounded-lg border-gray-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Sampai</label>
                <input type="date" wire:model="dateTo" class="w-full rounded-lg border-gray-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Unit</label>
                <select wire:model="unitId" class="w-full rounded-lg border-gray-300 shadow-sm">
                    <option value="">Semua Unit</option>
                    @foreach(\App\Models\BusinessUnit::pluck('name', 'id') as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">Tampilkan Buku Besar</x-filament::button>
    </form>

    @if($loaded)
        <div class="mt-6">
            <x-filament::section>
                <x-slot name="heading">Buku Besar — {{ \App\Models\Account::where('code', $accountCode)->first()->name ?? '' }}</x-slot>
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
