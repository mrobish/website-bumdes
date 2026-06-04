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
        <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">Tampilkan Neraca Saldo</x-filament::button>
    </form>

    @if($loaded)
        @php
            $bulanNama = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
            $periode = $month > 0 ? $bulanNama[$month] . ' ' . $year : 'Tahun ' . $year;
        @endphp
        <div class="mt-6">
            <x-filament::section>
                <x-slot name="heading">
                    Neraca Saldo — {{ $periode }}
                    @if($isBalanced) <span class="ml-2 text-green-600">✅ SEIMBANG</span> @else <span class="ml-2 text-red-600">❌ TIDAK SEIMBANG</span> @endif
                    @php $fy = \App\Models\FiscalYear::where('year', $year)->first(); @endphp
                    @if($fy)
                    <a href="{{ route('pdf.neraca-saldo', ['fiscal_year_id' => $fy->id, 'month' => $month > 0 ? $month : null]) }}" target="_blank" class="ml-3 inline-flex items-center gap-1 px-3 py-1.5 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">📄 Download PDF</a>
                    @endif
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
