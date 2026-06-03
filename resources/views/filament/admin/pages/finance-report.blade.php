<x-filament::page>
    @php
        $data = $this->getReportData();
    @endphp

    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold">Laporan Keuangan Tahun {{ $year }}</h2>
                <p class="text-gray-500">Laporan sesuai PP 11 Tahun 2021</p>
            </div>
            <div class="flex gap-2">
                <select wire:model="year" class="filament-input">
                    @for($y = now()->year - 2; $y <= now()->year + 1; $y++)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-6 bg-white rounded-xl shadow-sm border">
                <p class="text-sm text-gray-500">Total Aset</p>
                <p class="text-xl font-bold text-blue-600">Rp {{ number_format($data['total_assets'], 0, ',', '.') }}</p>
            </div>
            <div class="p-6 bg-white rounded-xl shadow-sm border">
                <p class="text-sm text-gray-500">Total Kewajiban</p>
                <p class="text-xl font-bold text-red-600">Rp {{ number_format($data['total_liabilities'], 0, ',', '.') }}</p>
            </div>
            <div class="p-6 bg-white rounded-xl shadow-sm border">
                <p class="text-sm text-gray-500">Modal</p>
                <p class="text-xl font-bold text-green-600">Rp {{ number_format($data['total_equity'], 0, ',', '.') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="p-6 bg-white rounded-xl shadow-sm border">
                <p class="text-sm text-gray-500">Pendapatan</p>
                <p class="text-2xl font-bold text-emerald-600">Rp {{ number_format($data['total_revenue'], 0, ',', '.') }}</p>
            </div>
            <div class="p-6 bg-white rounded-xl shadow-sm border">
                <p class="text-sm text-gray-500">Beban</p>
                <p class="text-2xl font-bold text-orange-600">Rp {{ number_format($data['total_expense'], 0, ',', '.') }}</p>
            </div>
            <div class="p-6 bg-white rounded-xl shadow-sm border">
                <p class="text-sm text-gray-500">Laba Bersih</p>
                <p class="text-2xl font-bold {{ $data['net_income'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    Rp {{ number_format($data['net_income'], 0, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="p-6 bg-white rounded-xl shadow-sm border">
            <h3 class="text-lg font-bold mb-4">Transaksi Bulanan</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Bulan</th>
                            <th class="text-right py-2">Pemasukan</th>
                            <th class="text-right py-2">Pengeluaran</th>
                            <th class="text-right py-2">Saldo</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['monthly_transactions'] as $month)
                            <tr class="border-b">
                                <td class="py-2">{{ \Carbon\Carbon::create()->month($month->month)->locale('id')->monthName }}</td>
                                <td class="text-right py-2 text-green-600">Rp {{ number_format($month->income, 0, ',', '.') }}</td>
                                <td class="text-right py-2 text-red-600">Rp {{ number_format($month->expense, 0, ',', '.') }}</td>
                                <td class="text-right py-2 font-bold">Rp {{ number_format($month->income - $month->expense, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-filament::page>