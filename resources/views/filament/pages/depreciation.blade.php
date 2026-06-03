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
                <label class="block text-sm font-medium mb-1">Bulan</label>
                <select wire:model="month" class="w-full rounded-lg border-gray-300 shadow-sm">
                    @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $m => $n)
                        <option value="{{ $m }}">{{ $n }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="flex gap-2">
            <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">Lihat Aset</x-filament::button>
            <x-filament::button wire:click="runDepreciation" icon="heroicon-o-play" color="warning">Jalankan Depresiasi</x-filament::button>
        </div>
    </form>

    @if($loaded)
        <div class="mt-6">
            <x-filament::section>
                <x-slot name="heading">Daftar Aset Aktif</x-slot>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="border-b bg-gray-50">
                            <th class="px-3 py-2 text-left">Nama</th>
                            <th class="px-3 py-2 text-left">Unit</th>
                            <th class="px-3 py-2 text-right">Harga Beli</th>
                            <th class="px-3 py-2 text-right">Akum. Penyusutan</th>
                            <th class="px-3 py-2 text-right">Nilai Buku</th>
                            <th class="px-3 py-2 text-right">Depresiasi/Bulan</th>
                        </tr></thead>
                        <tbody>
                            @forelse($assets as $a)
                                <tr class="border-b">
                                    <td class="px-3 py-2">{{ $a['name'] }}</td>
                                    <td class="px-3 py-2">{{ $a['unit'] }}</td>
                                    <td class="px-3 py-2 text-right">Rp {{ number_format($a['purchase_price'], 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-right">Rp {{ number_format($a['accumulated'], 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-right">Rp {{ number_format($a['book_value'], 0, ',', '.') }}</td>
                                    <td class="px-3 py-2 text-right text-orange-600">Rp {{ number_format($a['monthly'], 0, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="px-3 py-4 text-center text-gray-500">Belum ada aset aktif</td></tr>
                            @endforelse
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 font-bold bg-gray-100">
                                <td colspan="5" class="px-3 py-2">Total Depresiasi/Bulan</td>
                                <td class="px-3 py-2 text-right text-orange-700">Rp {{ number_format($totalDepreciation, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
