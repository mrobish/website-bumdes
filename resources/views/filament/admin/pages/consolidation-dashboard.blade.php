<x-filament-panels::page>
    {{-- Ringkasan Atas --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Pendapatan --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-success-100 dark:bg-success-900/30 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-currency-dollar class="w-6 h-6 text-success-600 dark:text-success-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Pendapatan</p>
                    <p class="text-xl font-bold text-success-600 dark:text-success-400">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Total Beban --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-danger-100 dark:bg-danger-900/30 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-receipt-refund class="w-6 h-6 text-danger-600 dark:text-danger-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Total Beban</p>
                    <p class="text-xl font-bold text-danger-600 dark:text-danger-400">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</p>
                </div>
            </div>
        </div>

        {{-- Laba Bersih --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-chart-pie class="w-6 h-6 text-primary-600 dark:text-primary-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Laba Bersih</p>
                    <p class="text-xl font-bold {{ $netProfit >= 0 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                        Rp {{ number_format($netProfit, 0, ',', '.') }}
                    </p>
                </div>
            </div>
        </div>

        {{-- Transfer Antar Unit --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 bg-warning-100 dark:bg-warning-900/30 rounded-lg flex items-center justify-center">
                    <x-heroicon-o-arrows-right-left class="w-6 h-6 text-warning-600 dark:text-warning-400" />
                </div>
                <div>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Transfer Antar Unit</p>
                    <p class="text-xl font-bold text-warning-600 dark:text-warning-400">{{ $pendingTransfers }} menunggu</p>
                    <p class="text-xs text-gray-400">{{ $completedTransfers }} selesai (Rp {{ number_format($totalTransferAmount, 0, ',', '.') }})</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Per Unit --}}
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                📊 Laba/Rugi Per Unit Usaha ({{ now()->year }})
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Konsolidasi otomatis dari seluruh unit usaha</p>
        </div>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="text-xs text-gray-700 dark:text-gray-300 bg-gray-50 dark:bg-gray-700/50">
                    <tr>
                        <th class="px-6 py-3">Unit Usaha</th>
                        <th class="px-6 py-3 text-right">Pendapatan</th>
                        <th class="px-6 py-3 text-right">Beban</th>
                        <th class="px-6 py-3 text-right">Laba/Rugi</th>
                        <th class="px-6 py-3 text-center">Transaksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($units as $unit)
                        <tr class="border-b border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/30">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-bold
                                        {{ $unit['type'] === 'induk' ? 'bg-warning-100 text-warning-700' : 'bg-success-100 text-success-700' }}">
                                        {{ $unit['code'] }}
                                    </div>
                                    <div>
                                        <p class="font-medium text-gray-900 dark:text-white">{{ $unit['name'] }}</p>
                                        <p class="text-xs text-gray-500">{{ ucfirst(str_replace('_', ' ', $unit['type'])) }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-success-600">
                                Rp {{ number_format($unit['revenue'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-medium text-danger-600">
                                Rp {{ number_format($unit['expenses'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold {{ $unit['net_profit'] >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                                Rp {{ number_format($unit['net_profit'], 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center text-gray-500">
                                {{ $unit['transaction_count'] }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                Belum ada data unit usaha
                            </td>
                        </tr>
                    @endforelse
                    
                    {{-- Total Row --}}
                    @if(count($units) > 0)
                        <tr class="bg-gray-50 dark:bg-gray-700/50 font-bold">
                            <td class="px-6 py-4 text-gray-900 dark:text-white">TOTAL KONSOLIDASI</td>
                            <td class="px-6 py-4 text-right text-success-600">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right text-danger-600">Rp {{ number_format($totalExpenses, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 text-right {{ $netProfit >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                                Rp {{ number_format($netProfit, 0, ',', '.') }}
                            </td>
                            <td class="px-6 py-4 text-center">{{ collect($units)->sum('transaction_count') }}</td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    {{-- Info Box --}}
    <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-xl p-6">
        <div class="flex items-start gap-3">
            <x-heroicon-o-information-circle class="w-6 h-6 text-primary-600 dark:text-primary-400 mt-0.5" />
            <div>
                <h4 class="font-semibold text-primary-900 dark:text-primary-100">Tentang Dashboard Konsolidasi</h4>
                <p class="text-sm text-primary-700 dark:text-primary-300 mt-1">
                    Dashboard ini menampilkan laporan keuangan yang sudah dikonsolidasi dari seluruh unit usaha BUMDes.
                    Transaksi antar unit (RAK) telah dieliminasi untuk menghindari pencatatan ganda (double counting).
                    Laporan ini sesuai standar SAK EMKM dan dapat digunakan untuk pertanggungjawaban dalam Musyawarah Desa (Musdes).
                </p>
            </div>
        </div>
    </div>
</x-filament-panels::page>
