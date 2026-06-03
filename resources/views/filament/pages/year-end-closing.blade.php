<x-filament-panels::page>
    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- STEP INDICATOR --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @php
        $steps = [
            1 => 'Pilih Tahun',
            2 => 'Neraca Saldo',
            3 => 'Laba/Rugi',
            4 => 'Konfirmasi',
            5 => 'Selesai',
        ];
        $progressPercent = $currentStep > 1 ? (($currentStep - 1) / 4) * 100 : 0;
    @endphp

    <div class="mb-6 sm:mb-8">
        {{-- Mobile: ultra compact --}}
        <div class="sm:hidden px-2">
            <div class="relative flex items-center justify-between">
                {{-- Track --}}
                <div class="absolute left-[16px] right-[16px] top-[15px] h-[3px] bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                <div class="absolute left-[16px] top-[15px] h-[3px] bg-primary-500 rounded-full transition-all duration-500"
                     style="width: calc({{ $progressPercent }}% - {{ $progressPercent > 0 ? '32px' : '0px' }}); max-width: calc(100% - 32px);"></div>

                @foreach($steps as $stepNum => $stepLabel)
                    <div class="relative z-10 flex flex-col items-center" style="width: 20%">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold
                            {{ $currentStep >= $stepNum ? 'bg-primary-600 text-white shadow' : 'bg-gray-100 dark:bg-gray-700 text-gray-400 border-2 border-gray-200 dark:border-gray-600' }}">
                            @if($currentStep > $stepNum)
                                <x-heroicon-s-check class="w-4 h-4" />
                            @else
                                {{ $stepNum }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
            <p class="text-center text-xs font-semibold text-primary-600 mt-2">
                Langkah {{ $currentStep }} dari 5 — {{ $steps[$currentStep] }}
            </p>
        </div>

        {{-- Desktop: full labels --}}
        <div class="hidden sm:block max-w-2xl mx-auto px-4">
            <div class="relative flex items-start justify-between">
                {{-- Track --}}
                <div class="absolute top-5 left-[40px] right-[40px] h-[3px] bg-gray-200 dark:bg-gray-700 rounded-full"></div>
                <div class="absolute top-5 left-[40px] h-[3px] bg-primary-500 rounded-full transition-all duration-500"
                     style="width: calc({{ $progressPercent }}% - {{ $progressPercent > 0 ? '80px' : '0px' }}); max-width: calc(100% - 80px);"></div>

                @foreach($steps as $stepNum => $stepLabel)
                    <div class="relative z-10 flex flex-col items-center" style="width: 20%">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold
                            {{ $currentStep >= $stepNum ? 'bg-primary-600 text-white shadow-lg' : 'bg-gray-100 dark:bg-gray-700 text-gray-400 border-2 border-gray-200 dark:border-gray-600' }}">
                            @if($currentStep > $stepNum)
                                <x-heroicon-s-check class="w-5 h-5" />
                            @else
                                {{ $stepNum }}
                            @endif
                        </div>
                        <span class="text-xs mt-2 text-center whitespace-nowrap
                            {{ $currentStep >= $stepNum ? 'text-primary-600 dark:text-primary-400 font-semibold' : 'text-gray-400 dark:text-gray-500' }}">
                            {{ $stepLabel }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- STEP 1: Pilih Tahun Fiskal --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if($currentStep === 1)
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 sm:p-6">
            <div class="flex items-center gap-2 mb-4">
                <x-heroicon-o-calendar class="w-5 h-5 text-primary-600" />
                <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100">Pilih Tahun Fiskal</h2>
            </div>

            @if(count($openFiscalYears) > 0)
                <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm">
                    Pilih tahun fiskal yang ingin ditutup. Hanya tahun dengan status <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">Open</span> yang dapat ditutup.
                </p>

                <div class="space-y-3">
                    @foreach($openFiscalYears as $fy)
                        @php $isSelected = ($selectedFiscalYearId ?? null) == $fy['id']; @endphp
                        <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all duration-200
                                      {{ $isSelected
                                          ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 shadow-sm'
                                          : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-800/50' }}">
                            <input type="radio" wire:model.live="selectedFiscalYearId" value="{{ $fy['id'] }}"
                                   class="w-4 h-4 text-primary-600 focus:ring-primary-500 border-gray-300 shrink-0">
                            <div class="ml-3 flex-1 min-w-0">
                                <div class="font-semibold text-gray-900 dark:text-gray-100">Tahun Fiskal {{ $fy['year'] }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Status: Open &middot; Dapat ditutup</div>
                            </div>
                            <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-400 shrink-0" />
                        </label>
                    @endforeach
                </div>

                <div class="flex justify-end mt-6">
                    <button wire:click="goToStep2"
                            class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 text-white rounded-lg font-medium text-sm hover:bg-primary-700 transition disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ !$selectedFiscalYearId ? 'disabled' : '' }}>
                        Selanjutnya
                        <x-heroicon-o-arrow-right class="w-4 h-4" />
                    </button>
                </div>
            @else
                <div class="text-center py-12">
                    <x-heroicon-o-check-circle class="w-16 h-16 mx-auto mb-4 text-green-400" />
                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">Semua tahun fiskal sudah ditutup</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tidak ada tahun fiskal yang tersedia.</p>
                </div>
            @endif
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- STEP 2: Neraca Saldo --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if($currentStep === 2)
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-scale class="w-5 h-5 text-primary-600" />
                    <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100">Neraca Saldo — {{ $this->getSelectedYear() }}</h2>
                </div>
                @if($tbBalanced)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-green-100 text-green-800">✓ SEIMBANG</span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800">✗ TIDAK SEIMBANG</span>
                @endif
            </div>

            @if(!$canClose)
                <div class="rounded-lg p-3 mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                    <div class="flex items-start gap-2 text-red-800 dark:text-red-300">
                        <x-heroicon-o-exclamation-triangle class="w-5 h-5 shrink-0 mt-0.5" />
                        <span class="text-sm font-medium">{{ $canCloseReason }}</span>
                    </div>
                </div>
            @else
                <div class="rounded-lg p-3 mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
                    <div class="flex items-start gap-2 text-green-800 dark:text-green-300">
                        <x-heroicon-o-check-circle class="w-5 h-5 shrink-0 mt-0.5" />
                        <span class="text-sm font-medium">{{ $canCloseReason }}</span>
                    </div>
                </div>
            @endif

            @if(count($trialBalance) > 0)
                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg -mx-4 sm:mx-0">
                    <table class="w-full text-sm min-w-[420px]">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Kode</th>
                                <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nama Akun</th>
                                <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Debet</th>
                                <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Kredit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($trialBalance as $a)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="px-4 py-2.5 font-mono text-xs text-gray-600 dark:text-gray-400">{{ $a['code'] }}</td>
                                    <td class="px-4 py-2.5 text-gray-900 dark:text-gray-100">{{ $a['name'] }}</td>
                                    <td class="px-4 py-2.5 text-right font-mono text-xs">
                                        @if($a['debit'] > 0)<span class="text-green-700 dark:text-green-400">{{ number_format($a['debit'], 0, ',', '.') }}</span>@endif
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-mono text-xs">
                                        @if($a['credit'] > 0)<span class="text-red-600 dark:text-red-400">{{ number_format($a['credit'], 0, ',', '.') }}</span>@endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-100 dark:bg-gray-700 font-bold border-t-2 border-gray-300 dark:border-gray-600">
                                <td colspan="2" class="px-4 py-2.5 text-gray-900 dark:text-gray-100">TOTAL</td>
                                <td class="px-4 py-2.5 text-right font-mono text-xs text-green-700 dark:text-green-400">{{ number_format($tbTotalDebit, 0, ',', '.') }}</td>
                                <td class="px-4 py-2.5 text-right font-mono text-xs text-red-600 dark:text-red-400">{{ number_format($tbTotalCredit, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-8">Tidak ada data neraca saldo.</p>
            @endif

            <div class="flex justify-between mt-6">
                <button wire:click="goBack" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                    Kembali
                </button>
                <button wire:click="goToStep3" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                    Selanjutnya
                    <x-heroicon-o-arrow-right class="w-4 h-4" />
                </button>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- STEP 3: Laba/Rugi --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if($currentStep === 3)
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 sm:p-6">
            <div class="flex items-center gap-2 mb-4">
                <x-heroicon-o-calculator class="w-5 h-5 text-primary-600" />
                <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100">Laba/Rugi — {{ $this->getSelectedYear() }}</h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                {{-- Revenue --}}
                <div class="border border-green-200 dark:border-green-800 rounded-xl overflow-hidden">
                    <div class="bg-green-50 dark:bg-green-900/30 px-4 py-3 border-b border-green-200 dark:border-green-800">
                        <h4 class="font-bold text-green-800 dark:text-green-300 text-sm">📈 Pendapatan</h4>
                    </div>
                    @if(count($incomeRevenues) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-green-100 dark:border-green-900">
                                        <th class="px-3 py-2 text-left text-xs text-gray-500">Kode</th>
                                        <th class="px-3 py-2 text-left text-xs text-gray-500">Nama</th>
                                        <th class="px-3 py-2 text-right text-xs text-gray-500">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-green-50 dark:divide-green-900/30">
                                    @foreach($incomeRevenues as $r)
                                        <tr>
                                            <td class="px-3 py-2 font-mono text-xs">{{ $r['code'] }}</td>
                                            <td class="px-3 py-2">{{ $r['name'] }}</td>
                                            <td class="px-3 py-2 text-right font-mono text-green-700 dark:text-green-400">{{ number_format($r['amount'], 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-green-50 dark:bg-green-900/20 font-bold border-t border-green-200 dark:border-green-800">
                                        <td colspan="2" class="px-3 py-2.5">Total Pendapatan</td>
                                        <td class="px-3 py-2.5 text-right font-mono">{{ number_format($incomeTotalRevenue, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-400 text-sm italic p-6 text-center">Tidak ada data pendapatan</p>
                    @endif
                </div>

                {{-- Expense --}}
                <div class="border border-red-200 dark:border-red-800 rounded-xl overflow-hidden">
                    <div class="bg-red-50 dark:bg-red-900/30 px-4 py-3 border-b border-red-200 dark:border-red-800">
                        <h4 class="font-bold text-red-800 dark:text-red-300 text-sm">📉 Beban</h4>
                    </div>
                    @if(count($incomeExpenses) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-red-100 dark:border-red-900">
                                        <th class="px-3 py-2 text-left text-xs text-gray-500">Kode</th>
                                        <th class="px-3 py-2 text-left text-xs text-gray-500">Nama</th>
                                        <th class="px-3 py-2 text-right text-xs text-gray-500">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-red-50 dark:divide-red-900/30">
                                    @foreach($incomeExpenses as $e)
                                        <tr>
                                            <td class="px-3 py-2 font-mono text-xs">{{ $e['code'] }}</td>
                                            <td class="px-3 py-2">{{ $e['name'] }}</td>
                                            <td class="px-3 py-2 text-right font-mono text-red-600 dark:text-red-400">{{ number_format($e['amount'], 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-red-50 dark:bg-red-900/20 font-bold border-t border-red-200 dark:border-red-800">
                                        <td colspan="2" class="px-3 py-2.5">Total Beban</td>
                                        <td class="px-3 py-2.5 text-right font-mono">{{ number_format($incomeTotalExpense, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-400 text-sm italic p-6 text-center">Tidak ada data beban</p>
                    @endif
                </div>
            </div>

            {{-- Net Income --}}
            <div class="mt-6 p-4 sm:p-5 rounded-xl border-2
                {{ $incomeNetIncome >= 0
                    ? 'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700'
                    : 'bg-red-50 dark:bg-red-900/20 border-red-300 dark:border-red-700' }}">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <span class="font-bold {{ $incomeNetIncome >= 0 ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300' }}">
                        {{ $incomeNetIncome >= 0 ? '💰 Laba Bersih' : '📉 Rugi Bersih' }}
                    </span>
                    <span class="font-bold text-xl sm:text-2xl {{ $incomeNetIncome >= 0 ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
                        Rp {{ number_format(abs($incomeNetIncome), 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <div class="flex justify-between mt-6">
                <button wire:click="goBack" class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                    Kembali
                </button>
                <button wire:click="goToStep4" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 text-white rounded-lg text-sm font-medium hover:bg-primary-700 transition">
                    Selanjutnya
                    <x-heroicon-o-arrow-right class="w-4 h-4" />
                </button>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- STEP 4: Konfirmasi --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if($currentStep === 4)
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-4 sm:p-6">
            <div class="flex items-center gap-2 mb-4">
                <x-heroicon-o-check-circle class="w-5 h-5 text-primary-600" />
                <h2 class="text-base sm:text-lg font-bold text-gray-900 dark:text-gray-100">Konfirmasi Tutup Buku</h2>
            </div>

            {{-- Warning --}}
            <div class="bg-amber-50 dark:bg-amber-900/20 border-2 border-amber-300 dark:border-amber-700 rounded-xl p-4 mb-6">
                <div class="flex items-start gap-3">
                    <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" />
                    <div>
                        <p class="font-bold text-amber-800 dark:text-amber-300">⚠️ Tindakan ini tidak dapat dibatalkan!</p>
                        <p class="text-sm text-amber-700 dark:text-amber-400 mt-1">Semua jurnal penutup akan dibuat dan tahun fiskal akan ditandai sebagai "closed".</p>
                    </div>
                </div>
            </div>

            {{-- Summary cards --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 mb-6">
                <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 text-center">
                    <div class="text-xs text-gray-500 dark:text-gray-400 mb-1">Tahun Fiskal</div>
                    <div class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $this->getSelectedYear() }}</div>
                </div>
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4 text-center">
                    <div class="text-xs text-green-600 dark:text-green-400 mb-1">Total Pendapatan</div>
                    <div class="text-base sm:text-lg font-bold text-green-700 dark:text-green-400 break-all">Rp {{ number_format($incomeTotalRevenue, 0, ',', '.') }}</div>
                </div>
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-center">
                    <div class="text-xs text-red-600 dark:text-red-400 mb-1">Total Beban</div>
                    <div class="text-base sm:text-lg font-bold text-red-700 dark:text-red-400 break-all">Rp {{ number_format($incomeTotalExpense, 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- Checklist --}}
            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 sm:p-5 mb-6">
                <h4 class="font-bold text-sm mb-3 text-gray-700 dark:text-gray-300">Yang akan dilakukan:</h4>
                <ul class="space-y-3">
                    @foreach([
                        'Jurnal penutup untuk semua akun pendapatan',
                        'Jurnal penutup untuk semua akun beban',
                        'Transfer laba bersih ke Laba Ditahan (3201)',
                        'Status tahun fiskal → "closed"',
                        "Tahun fiskal baru " . ($this->getSelectedYear() + 1) . " dibuat otomatis",
                    ] as $item)
                        <li class="flex items-center gap-3 text-sm text-gray-700 dark:text-gray-300">
                            <div class="w-5 h-5 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                                <x-heroicon-s-check class="w-3 h-3 text-green-600 dark:text-green-400" />
                            </div>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
            </div>

            {{-- Actions --}}
            <div class="flex flex-col-reverse sm:flex-row justify-between gap-3">
                <button wire:click="goBack"
                        class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 rounded-lg text-sm font-medium hover:bg-gray-200 dark:hover:bg-gray-700 transition">
                    <x-heroicon-o-arrow-left class="w-4 h-4" />
                    Kembali
                </button>
                <button wire:click="executeClosing"
                        class="inline-flex items-center justify-center gap-2 px-6 py-2.5 bg-red-600 text-white rounded-lg text-sm font-bold hover:bg-red-700 transition disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-red-600/20"
                        @if($processing) disabled @endif>
                    @if($processing)
                        <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        Memproses...
                    @else
                        ⚡ Tutup Buku Sekarang
                    @endif
                </button>
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- STEP 5: Selesai --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @if($currentStep === 5)
        <div class="bg-white dark:bg-gray-900 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm p-6 sm:p-12">
            <div class="text-center">
                <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-5 shadow-lg shadow-green-200 dark:shadow-green-900/30">
                    <x-heroicon-s-check-circle class="w-12 h-12 text-green-600 dark:text-green-400" />
                </div>

                <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">Tutup Buku Berhasil! 🎉</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base mb-8">{{ $closingResult['message'] ?? 'Tahun fiskal berhasil ditutup.' }}</p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 max-w-2xl mx-auto mb-8">
                    <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4">
                        <div class="text-xs text-gray-500 mb-1">Tahun Ditutup</div>
                        <div class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $closingResult['year'] ?? '-' }}</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
                        <div class="text-xs text-green-600 mb-1">Laba Bersih</div>
                        <div class="text-xl font-bold {{ ($closingResult['netIncome'] ?? 0) >= 0 ? 'text-green-700' : 'text-red-700' }}">
                            Rp {{ number_format(abs($closingResult['netIncome'] ?? 0), 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-xl p-4">
                        <div class="text-xs text-primary-700 mb-1">Tahun Baru</div>
                        <div class="text-xl font-bold text-primary-700">{{ $closingResult['nextYear'] ?? '-' }}</div>
                    </div>
                </div>

                <button wire:click="resetWizard"
                        class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 text-white rounded-lg font-medium text-sm hover:bg-primary-700 transition shadow-lg shadow-primary-600/20">
                    <x-heroicon-o-arrow-path class="w-5 h-5" />
                    Tutup Tahun Lain
                </button>
            </div>
        </div>
    @endif
</x-filament-panels::page>
