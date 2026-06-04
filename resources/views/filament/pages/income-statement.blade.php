<x-filament-panels::page>
    <form wire:submit.prevent="loadData">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
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
                    <option value="0">📅 Semua Bulan (Tahun Penuh)</option>
                    @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $m => $nama)
                        <option value="{{ $m }}">{{ $nama }}</option>
                    @endforeach
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
        <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">Tampilkan Laba/Rugi</x-filament::button>
    </form>

    @if($loaded)
        @php
            $bulanNama = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $periode = $month > 0 ? $bulanNama[$month] . ' ' . $year : 'Tahun ' . $year;
        @endphp
        <div class="mt-6">
            <x-filament::section>
                <x-slot name="heading">
                    Laba/Rugi — {{ $periode }}
                    @php $fy = \App\Models\FiscalYear::where('year', $year)->first(); @endphp
                    @if($fy)
                    <a href="{{ route('pdf.laba-rugi', ['fiscal_year_id' => $fy->id, 'month' => $month > 0 ? $month : null]) }}" target="_blank" class="ml-3 inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">📄 Download PDF</a>
                    @endif
                </x-slot>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="border-b bg-gray-50">
                            <th class="px-3 py-2 text-left">Kode</th>
                            <th class="px-3 py-2 text-left">Nama Akun</th>
                            <th class="px-3 py-2 text-right">Jumlah</th>
                        </tr></thead>
                        <tbody>
                            <tr><td colspan="3" class="px-3 py-2 font-bold bg-green-50">PENDAPATAN</td></tr>
                            @foreach($revenues as $r)
                            <tr class="border-b">
                                <td class="px-3 py-2 font-mono">{{ $r['code'] }}</td>
                                <td class="px-3 py-2">{{ $r['name'] }}</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($r['amount'], 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                            <tr class="font-bold bg-green-100">
                                <td colspan="2" class="px-3 py-2 text-right">Total Pendapatan</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                            </tr>

                            <tr><td colspan="3" class="px-3 py-2 font-bold bg-red-50">BEBAN / BIAYA</td></tr>
                            @foreach($expenses as $e)
                            <tr class="border-b">
                                <td class="px-3 py-2 font-mono">{{ $e['code'] }}</td>
                                <td class="px-3 py-2">{{ $e['name'] }}</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($e['amount'], 0, ',', '.') }}</td>
                            </tr>
                            @endforeach
                            <tr class="font-bold bg-red-100">
                                <td colspan="2" class="px-3 py-2 text-right">Total Beban</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td>
                            </tr>

                            <tr class="border-t-2 font-bold {{ $netIncome >= 0 ? 'bg-green-200' : 'bg-red-200' }}">
                                <td colspan="2" class="px-3 py-2 text-right text-lg">LABA / (RUGI) BERSIH</td>
                                <td class="px-3 py-2 text-right text-lg {{ $netIncome >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                    {{ $netIncome < 0 ? '(' : '' }}Rp {{ number_format(abs($netIncome), 0, ',', '.') }}{{ $netIncome < 0 ? ')' : '' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
