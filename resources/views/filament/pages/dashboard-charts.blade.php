<x-filament-panels::page>
    <style>
        .chart-container {
            display: grid;
            grid-template-columns: repeat(12, 1fr);
            gap: 4px;
            align-items: end;
            height: 200px;
            padding: 16px 0;
        }
        .chart-bar-group {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 2px;
            height: 100%;
            justify-content: flex-end;
        }
        .chart-bar {
            width: 100%;
            max-width: 24px;
            border-radius: 3px 3px 0 0;
            min-height: 2px;
            transition: height 0.3s ease;
        }
        .chart-bar-income {
            background-color: #22c55e;
        }
        .chart-bar-expense {
            background-color: #ef4444;
        }
        .chart-label {
            font-size: 10px;
            color: #6b7280;
            text-align: center;
            margin-top: 4px;
        }
        .summary-card {
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }
        .category-bar {
            height: 8px;
            border-radius: 4px;
            background: linear-gradient(90deg, #3b82f6, #1d4ed8);
            transition: width 0.5s ease;
        }
        .budget-pill {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 600;
        }
        .txn-table {
            width: 100%;
            border-collapse: collapse;
        }
        .txn-table th {
            text-align: left;
            padding: 10px 12px;
            font-size: 12px;
            font-weight: 600;
            color: #6b7280;
            border-bottom: 2px solid #e5e7eb;
        }
        .txn-table td {
            padding: 10px 12px;
            font-size: 13px;
            border-bottom: 1px solid #f3f4f6;
        }
        .txn-table tr:hover td {
            background-color: #f9fafb;
        }
    </style>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        {{-- Total Pemasukan --}}
        <div class="summary-card bg-white dark:bg-gray-800 shadow-sm border border-success-200 dark:border-success-700">
            <div class="flex items-center justify-center mb-2">
                <x-heroicon-o-arrow-trending-up class="w-6 h-6 text-success-500" />
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Total Pemasukan</div>
            <div class="text-xl font-bold text-success-600 dark:text-success-400 mt-1">
                Rp {{ number_format($totalPemasukan, 0, ',', '.') }}
            </div>
        </div>

        {{-- Total Pengeluaran --}}
        <div class="summary-card bg-white dark:bg-gray-800 shadow-sm border border-danger-200 dark:border-danger-700">
            <div class="flex items-center justify-center mb-2">
                <x-heroicon-o-arrow-trending-down class="w-6 h-6 text-danger-500" />
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Total Pengeluaran</div>
            <div class="text-xl font-bold text-danger-600 dark:text-danger-400 mt-1">
                Rp {{ number_format($totalPengeluaran, 0, ',', '.') }}
            </div>
        </div>

        {{-- Saldo Kas --}}
        <div class="summary-card bg-white dark:bg-gray-800 shadow-sm border border-primary-200 dark:border-primary-700">
            <div class="flex items-center justify-center mb-2">
                <x-heroicon-o-wallet class="w-6 h-6 text-primary-500" />
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Saldo Kas</div>
            <div class="text-xl font-bold {{ $saldoKas >= 0 ? 'text-primary-600 dark:text-primary-400' : 'text-danger-600 dark:text-danger-400' }} mt-1">
                Rp {{ number_format($saldoKas, 0, ',', '.') }}
            </div>
        </div>

        {{-- Jumlah Transaksi --}}
        <div class="summary-card bg-white dark:bg-gray-800 shadow-sm border border-warning-200 dark:border-warning-700">
            <div class="flex items-center justify-center mb-2">
                <x-heroicon-o-document-text class="w-6 h-6 text-warning-500" />
            </div>
            <div class="text-sm text-gray-500 dark:text-gray-400">Jumlah Transaksi</div>
            <div class="text-xl font-bold text-warning-600 dark:text-warning-400 mt-1">
                {{ number_format($jumlahTransaksi) }} transaksi
            </div>
        </div>
    </div>

    {{-- Monthly Chart --}}
    <x-filament::section>
        <x-slot name="heading">
            <x-heroicon-o-chart-bar class="w-5 h-5 mr-2 inline" />
            Pemasukan vs Pengeluaran (12 Bulan Terakhir)
        </x-slot>

        <x-slot name="description">
            <div class="flex items-center gap-4 text-xs">
                <span class="flex items-center gap-1">
                    <span class="inline-block w-3 h-3 rounded" style="background-color: #22c55e;"></span> Pemasukan
                </span>
                <span class="flex items-center gap-1">
                    <span class="inline-block w-3 h-3 rounded" style="background-color: #ef4444;"></span> Pengeluaran
                </span>
            </div>
        </x-slot>

        @php
            $maxAmount = $getMaxMonthlyAmount();
        @endphp

        <div class="chart-container">
            @foreach($monthlyData as $monthData)
                @php
                    $incomeHeight = $maxAmount > 0 ? ($monthData['pemasukan'] / $maxAmount) * 100 : 0;
                    $expenseHeight = $maxAmount > 0 ? ($monthData['pengeluaran'] / $maxAmount) * 100 : 0;
                @endphp
                <div class="chart-bar-group" title="{{ $monthData['month'] }}&#10;Pemasukan: Rp {{ number_format($monthData['pemasukan'], 0, ',', '.') }}&#10;Pengeluaran: Rp {{ number_format($monthData['pengeluaran'], 0, ',', '.') }}">
                    <div style="display: flex; gap: 2px; align-items: end; height: 100%;">
                        <div class="chart-bar chart-bar-income" style="height: {{ $incomeHeight }}%;"></div>
                        <div class="chart-bar chart-bar-expense" style="height: {{ $expenseHeight }}%;"></div>
                    </div>
                    <span class="chart-label">{{ $monthData['short_month'] }}</span>
                </div>
            @endforeach
        </div>
    </x-filament::section>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mt-6">
        {{-- Top 5 Categories --}}
        <x-filament::section>
            <x-slot name="heading">
                <x-heroicon-o-folder class="w-5 h-5 mr-2 inline" />
                Top 5 Kategori Pemasukan
            </x-slot>

            @if(count($topCategories) > 0)
                @php
                    $maxCat = $topCategories[0]['raw_total'] ?? 1;
                @endphp
                <div class="space-y-3">
                    @foreach($topCategories as $cat)
                        @php
                            $pct = $maxCat > 0 ? ($cat['raw_total'] / $maxCat) * 100 : 0;
                        @endphp
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-medium">{{ $cat['name'] }}</span>
                                <span class="text-sm text-gray-500">Rp {{ $cat['total'] }}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                <div class="category-bar" style="width: {{ $pct }}%;"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada data pemasukan.</p>
            @endif
        </x-filament::section>

        {{-- Budget Status --}}
        <x-filament::section>
            <x-slot name="heading">
                <x-heroicon-o-flag class="w-5 h-5 mr-2 inline" />
                Status Anggaran
            </x-slot>

            @if($budgetStatus['total'] > 0)
                <div class="space-y-4">
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div>
                            <div class="text-2xl font-bold text-success-600 dark:text-success-400">{{ $budgetStatus['ok'] }}</div>
                            <div class="text-xs text-gray-500">Di Bawah Anggaran</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-warning-600 dark:text-warning-400">{{ $budgetStatus['warning'] }}</div>
                            <div class="text-xs text-gray-500">Hampir Habis (≥80%)</div>
                        </div>
                        <div>
                            <div class="text-2xl font-bold text-danger-600 dark:text-danger-400">{{ $budgetStatus['over'] }}</div>
                            <div class="text-xs text-gray-500">Melebihi Anggaran</div>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <span>Total {{ $budgetStatus['total'] }} kategori dengan anggaran aktif.</span>
                    </div>
                </div>
            @else
                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada data anggaran untuk periode ini.</p>
            @endif
        </x-filament::section>
    </div>

    {{-- Recent Transactions --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">
            <x-heroicon-o-clock class="w-5 h-5 mr-2 inline" />
            Transaksi Terakhir
        </x-slot>

        @if(count($recentTransactions) > 0)
            <div class="overflow-x-auto">
                <table class="txn-table">
                    <thead>
                        <tr>
                            <th>No. Transaksi</th>
                            <th>Tanggal</th>
                            <th>Jenis</th>
                            <th>Kategori</th>
                            <th>Unit</th>
                            <th class="text-right">Jumlah</th>
                            <th>Deskripsi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentTransactions as $txn)
                            <tr>
                                <td class="font-medium">{{ $txn['number'] }}</td>
                                <td>{{ $txn['date'] }}</td>
                                <td>
                                    @if($txn['type'] === 'income')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-700 dark:bg-success-900 dark:text-success-300">
                                            Pemasukan
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-danger-100 text-danger-700 dark:bg-danger-900 dark:text-danger-300">
                                            Pengeluaran
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $txn['category'] }}</td>
                                <td>{{ $txn['unit'] }}</td>
                                <td class="text-right font-medium {{ $txn['type'] === 'income' ? 'text-success-600' : 'text-danger-600' }}">
                                    {{ $txn['type'] === 'income' ? '+' : '-' }} Rp {{ $txn['amount'] }}
                                </td>
                                <td class="text-gray-500 max-w-[200px] truncate">{{ $txn['description'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-4">Belum ada transaksi.</p>
        @endif
    </x-filament::section>
</x-filament-panels::page>
