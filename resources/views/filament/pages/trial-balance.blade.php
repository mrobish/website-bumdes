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
        <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">Tampilkan Neraca Saldo</x-filament::button>
    </form>

    @if($loaded)
        <div class="mt-6">
            <x-filament::section>
                <x-slot name="heading">
                    Neraca Saldo Tahun {{ $year }}
                    @if($isBalanced) <span class="ml-2 text-green-600">SEIMBANG</span> @else <span class="ml-2 text-red-600">TIDAK SEIMBANG</span> @endif
                </x-slot>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="border-b bg-gray-50">
                            <th class="px-3 py-2 text-left">Kode</th>
                            <th class="px-3 py-2 text-left">Nama Akun</th>
                            <th class="px-3 py-2 text-right">Debet</th>
                            <th class="px-3 py-2 text-right">Kredit</th>
                        </tr></thead>
                        <tbody>
                            @foreach($accounts as $a)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-3 py-2 font-mono">{{ $a['code'] }}</td>
                                    <td class="px-3 py-2">{{ $a['name'] }}</td>
                                    <td class="px-3 py-2 text-right">@if($a['debit'] > 0) Rp {{ number_format($a['debit'], 0, ',', '.') }} @endif</td>
                                    <td class="px-3 py-2 text-right">@if($a['credit'] > 0) Rp {{ number_format($a['credit'], 0, ',', '.') }} @endif</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 font-bold bg-gray-100">
                                <td colspan="2" class="px-3 py-2">TOTAL</td>
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
