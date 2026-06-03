<x-filament-panels::page>
    <form wire:submit="calculateDepreciation">
        <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">💰 Hitung Penyusutan Otomatis</h3>
            <div class="flex items-end gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode (YYYY-MM)</label>
                    <input type="text" wire:model="period" placeholder="2026-06" 
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                    Hitung Penyusutan
                </button>
            </div>
        </div>
    </form>

    @if(count($results) > 0)
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="px-6 py-4 border-b bg-green-50">
                <h3 class="text-lg font-semibold text-green-800">✅ Hasil Penyusutan Periode {{ $period }}</h3>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left">Aset</th>
                        <th class="px-6 py-3 text-right">Penyusutan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($results as $r)
                        <tr class="border-t">
                            <td class="px-6 py-3">{{ $r['asset'] }}</td>
                            <td class="px-6 py-3 text-right font-medium">Rp {{ number_format($r['depreciation'], 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                    <tr class="bg-gray-50 font-bold border-t-2">
                        <td class="px-6 py-3">TOTAL PENYUSUTAN</td>
                        <td class="px-6 py-3 text-right text-blue-600">Rp {{ number_format($totalDepreciation, 0, ',', '.') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @endif

    <div class="mt-6 bg-blue-50 border border-blue-200 rounded-xl p-4">
        <p class="text-sm text-blue-800">
            <strong>Cara kerja:</strong> Sistem menghitung penyusutan otomatis untuk semua aset aktif menggunakan metode Garis Lurus (Straight Line).
            Penyusutan = (Harga Beli - Nilai Sisa) / Umur Ekonomis (bulan). Hasil disimpan sebagai draft dan bisa diposting ke jurnal.
        </p>
    </div>
</x-filament-panels::page>
