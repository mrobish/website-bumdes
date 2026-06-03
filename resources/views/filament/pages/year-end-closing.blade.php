<x-filament-panels::page>
    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- STEP INDICATOR — Mobile: numbers only, Desktop: full labels --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}
    @php
        $steps = [
            1 => 'Pilih Tahun',
            2 => 'Neraca Saldo',
            3 => 'Laba/Rugi',
            4 => 'Konfirmasi',
            5 => 'Selesai',
        ];
    @endphp

    <div class="mb-6 sm:mb-8 px-1">
        {{-- Mobile: simple compact stepper --}}
        <div class="sm:hidden">
            <div class="flex items-center justify-between relative">
                {{-- background line --}}
                <div class="absolute top-4 left-6 right-6 h-0.5 bg-gray-200 z-0"></div>
                <div class="absolute top-4 left-6 h-0.5 bg-primary-500 z-0 transition-all duration-300"
                     style="width: {{ (($currentStep - 1) / 4) * 100 }}%; max-width: calc(100% - 48px);"></div>

                @foreach($steps as $stepNum => $label)
                    <div class="relative z-10 flex flex-col items-center" style="width: {{ 100 / 5 }}%">
                        <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold transition-all duration-300
                            {{ $currentStep >= $stepNum
                                ? 'bg-primary-600 text-white shadow-md'
                                : 'bg-gray-100 text-gray-400 border-2 border-gray-200' }}">
                            @if($currentStep > $stepNum)
                                <x-heroicon-s-check class="w-4 h-4" />
                            @else
                                {{ $stepNum }}
                            @endif
                        </div>
                        {{-- Label: only show for current step on mobile --}}
                        <span class="text-[9px] mt-1 text-center leading-tight max-w-[50px] truncate
                            {{ $currentStep === $stepNum ? 'text-primary-600 font-semibold' : 'text-gray-400' }}">
                            {{ $label }}
                        </span>
                    </div>
                @endforeach
            </div>
            {{-- Current step description --}}
            <div class="text-center mt-2 text-sm font-medium text-gray-700">
                Langkah {{ $currentStep }}: {{ $steps[$currentStep] }}
            </div>
        </div>

        {{-- Desktop: full step indicator --}}
        <div class="hidden sm:flex items-center max-w-2xl mx-auto">
            @foreach($steps as $stepNum => $label)
                <div class="flex items-center {{ $stepNum < 5 ? 'flex-1' : '' }}">
                    <div class="flex flex-col items-center shrink-0">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold transition-all duration-300
                            {{ $currentStep >= $stepNum
                                ? 'bg-primary-600 text-white shadow-md'
                                : 'bg-gray-100 text-gray-400 border-2 border-gray-200' }}">
                            @if($currentStep > $stepNum)
                                <x-heroicon-s-check class="w-5 h-5" />
                            @else
                                {{ $stepNum }}
                            @endif
                        </div>
                        <span class="text-xs mt-1.5 text-center whitespace-nowrap
                            {{ $currentStep >= $stepNum ? 'text-primary-600 font-medium' : 'text-gray-400' }}">
                            {{ $label }}
                        </span>
                    </div>
                    @if($stepNum < 5)
                        <div class="flex-1 h-0.5 mx-3 transition-all duration-300
                            {{ $currentStep > $stepNum ? 'bg-primary-500' : 'bg-gray-200' }}"></div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════ --}}
    {{-- STEP CONTENT --}}
    {{-- ═══════════════════════════════════════════════════════════ --}}

    {{-- Step 1: Select Fiscal Year --}}
    @if($currentStep === 1)
        <x-filament::section heading="Pilih Tahun Fiskal">
            @if(count($openFiscalYears) > 0)
                <p class="text-gray-600 dark:text-gray-400 mb-4 text-sm">
                    Pilih tahun fiskal yang ingin ditutup. Hanya tahun dengan status <x-filament::badge color="success">Open</x-filament::badge> yang dapat ditutup.
                </p>

                <div class="space-y-3">
                    @foreach($openFiscalYears as $fy)
                        <label wire:click="selectedFiscalYearId = {{ $fy['id'] }}"
                               class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all duration-200
                                      {{ ($selectedFiscalYearId ?? 0) == $fy['id']
                                          ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 shadow-sm'
                                          : 'border-gray-200 dark:border-gray-700 hover:border-gray-300 dark:hover:border-gray-600' }}">
                            <div class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mr-4
                                {{ ($selectedFiscalYearId ?? 0) == $fy['id']
                                    ? 'border-primary-600 bg-primary-600'
                                    : 'border-gray-300 dark:border-gray-600' }}">
                                @if(($selectedFiscalYearId ?? 0) == $fy['id'])
                                    <div class="w-2 h-2 rounded-full bg-white"></div>
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-gray-900 dark:text-gray-100">Tahun Fiskal {{ $fy['year'] }}</div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Status: Open</div>
                            </div>
                            <x-heroicon-o-chevron-right class="w-5 h-5 text-gray-400 shrink-0" />
                        </label>
                    @endforeach
                </div>

                <div class="flex justify-end mt-6">
                    <x-filament::button wire:click="goToStep2" icon="heroicon-o-arrow-right"
                                        :disabled="!$selectedFiscalYearId">
                        Selanjutnya
                    </x-filament::button>
                </div>
            @else
                <div class="text-center py-12">
                    <x-heroicon-o-check-circle class="w-16 h-16 mx-auto mb-4 text-green-400" />
                    <p class="text-lg font-semibold text-gray-700 dark:text-gray-300">Semua tahun fiskal sudah ditutup</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tidak ada tahun fiskal yang tersedia untuk ditutup.</p>
                </div>
            @endif
        </x-filament::section>
    @endif

    {{-- Step 2: Trial Balance --}}
    @if($currentStep === 2)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 w-full">
                    <span>Neraca Saldo — Tahun {{ $this->getSelectedYear() }}</span>
                    @if($tbBalanced)
                        <x-filament::badge color="success">SEIMBANG ✓</x-filament::badge>
                    @else
                        <x-filament::badge color="danger">TIDAK SEIMBANG ✗</x-filament::badge>
                    @endif
                </div>
            </x-slot>

            {{-- Status alert --}}
            <div class="rounded-lg p-3 sm:p-4 mb-4 {{ $canClose ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800' }}">
                <div class="flex items-start gap-2 {{ $canClose ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300' }}">
                    <x-heroicon-o-{{ $canClose ? 'check-circle' : 'exclamation-triangle' }} class="w-5 h-5 shrink-0 mt-0.5" />
                    <span class="text-sm font-medium">{{ $canCloseReason }}</span>
                </div>
            </div>

            {{-- Table --}}
            @if(count($trialBalance) > 0)
                <div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800 border-b">
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Kode</th>
                                <th class="px-3 py-2.5 text-left text-xs font-semibold text-gray-600 dark:text-gray-400">Nama Akun</th>
                                <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-600 dark:text-gray-400">Debet</th>
                                <th class="px-3 py-2.5 text-right text-xs font-semibold text-gray-600 dark:text-gray-400">Kredit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trialBalance as $a)
                                <tr class="border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                    <td class="px-3 py-2 font-mono text-xs text-gray-600 dark:text-gray-400">{{ $a['code'] }}</td>
                                    <td class="px-3 py-2 text-gray-900 dark:text-gray-100">{{ $a['name'] }}</td>
                                    <td class="px-3 py-2 text-right font-mono">
                                        @if($a['debit'] > 0)
                                            <span class="text-green-700 dark:text-green-400">{{ number_format($a['debit'], 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2 text-right font-mono">
                                        @if($a['credit'] > 0)
                                            <span class="text-red-600 dark:text-red-400">{{ number_format($a['credit'], 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-100 dark:bg-gray-700 font-bold border-t-2">
                                <td colspan="2" class="px-3 py-2.5 text-gray-900 dark:text-gray-100">TOTAL</td>
                                <td class="px-3 py-2.5 text-right font-mono text-green-700 dark:text-green-400">{{ number_format($tbTotalDebit, 0, ',', '.') }}</td>
                                <td class="px-3 py-2.5 text-right font-mono text-red-600 dark:text-red-400">{{ number_format($tbTotalCredit, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <div class="text-center py-8 text-gray-500">Tidak ada data neraca saldo.</div>
            @endif

            <div class="flex justify-between mt-6">
                <x-filament::button wire:click="goBack" color="gray" icon="heroicon-o-arrow-left">Kembali</x-filament::button>
                <x-filament::button wire:click="goToStep3" icon="heroicon-o-arrow-right">Selanjutnya</x-filament::button>
            </div>
        </x-filament::section>
    @endif

    {{-- Step 3: Income Statement --}}
    @if($currentStep === 3)
        <x-filament::section heading="Laba/Rugi — Tahun {{ $this->getSelectedYear() }}">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6">
                {{-- Revenue --}}
                <div class="border border-green-200 dark:border-green-800 rounded-lg overflow-hidden">
                    <div class="bg-green-50 dark:bg-green-900/30 px-4 py-2.5 border-b border-green-200 dark:border-green-800">
                        <h4 class="font-bold text-green-800 dark:text-green-300 text-sm">📈 Pendapatan</h4>
                    </div>
                    @if(count($incomeRevenues) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-green-100 dark:border-green-900">
                                        <th class="px-3 py-2 text-left text-xs text-gray-600">Kode</th>
                                        <th class="px-3 py-2 text-left text-xs text-gray-600">Nama</th>
                                        <th class="px-3 py-2 text-right text-xs text-gray-600">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($incomeRevenues as $r)
                                        <tr class="border-b border-green-50 dark:border-green-900/50">
                                            <td class="px-3 py-2 font-mono text-xs">{{ $r['code'] }}</td>
                                            <td class="px-3 py-2">{{ $r['name'] }}</td>
                                            <td class="px-3 py-2 text-right font-mono text-green-700">{{ number_format($r['amount'], 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-green-50 dark:bg-green-900/20 font-bold border-t">
                                        <td colspan="2" class="px-3 py-2.5">Total Pendapatan</td>
                                        <td class="px-3 py-2.5 text-right font-mono">{{ number_format($incomeTotalRevenue, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-400 text-sm italic p-4 text-center">Tidak ada pendapatan</p>
                    @endif
                </div>

                {{-- Expense --}}
                <div class="border border-red-200 dark:border-red-800 rounded-lg overflow-hidden">
                    <div class="bg-red-50 dark:bg-red-900/30 px-4 py-2.5 border-b border-red-200 dark:border-red-800">
                        <h4 class="font-bold text-red-800 dark:text-red-300 text-sm">📉 Beban</h4>
                    </div>
                    @if(count($incomeExpenses) > 0)
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="border-b border-red-100 dark:border-red-900">
                                        <th class="px-3 py-2 text-left text-xs text-gray-600">Kode</th>
                                        <th class="px-3 py-2 text-left text-xs text-gray-600">Nama</th>
                                        <th class="px-3 py-2 text-right text-xs text-gray-600">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($incomeExpenses as $e)
                                        <tr class="border-b border-red-50 dark:border-red-900/50">
                                            <td class="px-3 py-2 font-mono text-xs">{{ $e['code'] }}</td>
                                            <td class="px-3 py-2">{{ $e['name'] }}</td>
                                            <td class="px-3 py-2 text-right font-mono text-red-600">{{ number_format($e['amount'], 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-red-50 dark:bg-red-900/20 font-bold border-t">
                                        <td colspan="2" class="px-3 py-2.5">Total Beban</td>
                                        <td class="px-3 py-2.5 text-right font-mono">{{ number_format($incomeTotalExpense, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-400 text-sm italic p-4 text-center">Tidak ada beban</p>
                    @endif
                </div>
            </div>

            {{-- Net Income --}}
            <div class="mt-6 p-4 rounded-xl border-2 {{ $incomeNetIncome >= 0 ? 'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700' : 'bg-red-50 dark:bg-red-900/20 border-red-300 dark:border-red-700' }}">
                <div class="flex items-center justify-between flex-wrap gap-2">
                    <span class="font-bold text-base sm:text-lg {{ $incomeNetIncome >= 0 ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300' }}">
                        {{ $incomeNetIncome >= 0 ? '💰 Laba Bersih' : '📉 Rugi Bersih' }}
                    </span>
                    <span class="font-bold text-xl sm:text-2xl {{ $incomeNetIncome >= 0 ? 'text-green-700 dark:text-green-400' : 'text-red-700 dark:text-red-400' }}">
                        Rp {{ number_format(abs($incomeNetIncome), 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <div class="flex justify-between mt-6">
                <x-filament::button wire:click="goBack" color="gray" icon="heroicon-o-arrow-left">Kembali</x-filament::button>
                <x-filament::button wire:click="goToStep4" icon="heroicon-o-arrow-right">Selanjutnya</x-filament::button>
            </div>
        </x-filament::section>
    @endif

    {{-- Step 4: Confirmation --}}
    @if($currentStep === 4)
        <x-filament::section heading="Konfirmasi Tutup Buku">
            {{-- Warning --}}
            <div class="bg-amber-50 dark:bg-amber-900/20 border-2 border-amber-300 dark:border-amber-700 rounded-xl p-4 mb-6">
                <div class="flex items-start gap-3">
                    <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" />
                    <div>
                        <p class="font-bold text-amber-800 dark:text-amber-300">Tindakan ini tidak dapat dibatalkan!</p>
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
                    <div class="text-xs text-green-700 dark:text-green-400 mb-1">Total Pendapatan</div>
                    <div class="text-lg font-bold text-green-700 dark:text-green-400">Rp {{ number_format($incomeTotalRevenue, 0, ',', '.') }}</div>
                </div>
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-4 text-center">
                    <div class="text-xs text-red-700 dark:text-red-400 mb-1">Total Beban</div>
                    <div class="text-lg font-bold text-red-700 dark:text-red-400">Rp {{ number_format($incomeTotalExpense, 0, ',', '.') }}</div>
                </div>
            </div>

            {{-- Checklist --}}
            <div class="bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl p-4 sm:p-5 mb-6">
                <h4 class="font-bold text-sm mb-3 text-gray-700 dark:text-gray-300">Yang akan dilakukan:</h4>
                <ul class="space-y-2.5">
                    @foreach([
                        'Jurnal penutup untuk semua akun pendapatan',
                        'Jurnal penutup untuk semua akun beban',
                        'Transfer laba bersih ke Laba Ditahan (3201)',
                        'Status tahun fiskal → "closed"',
                        "Tahun fiskal baru " . ($this->getSelectedYear() + 1) . " dibuat otomatis",
                    ] as $item)
                        <li class="flex items-center gap-2.5 text-sm text-gray-700 dark:text-gray-300">
                            <div class="w-5 h-5 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                                <x-heroicon-s-check class="w-3 h-3 text-green-600 dark:text-green-400" />
                            </div>
                            {{ $item }}
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="flex flex-col sm:flex-row justify-between gap-3">
                <x-filament::button wire:click="goBack" color="gray" icon="heroicon-o-arrow-left" class="order-2 sm:order-1">
                    Kembali
                </x-filament::button>
                <x-filament::button wire:click="executeClosing" icon="heroicon-o-check" color="danger"
                                    :loading="$processing" :disabled="$processing || !$canClose" class="order-1 sm:order-2">
                    ⚡ Tutup Buku Sekarang
                </x-filament::button>
            </div>
        </x-filament::section>
    @endif

    {{-- Step 5: Success --}}
    @if($currentStep === 5)
        <x-filament::section>
            <div class="text-center py-8 sm:py-12">
                <div class="w-20 h-20 bg-green-100 dark:bg-green-900/30 rounded-full flex items-center justify-center mx-auto mb-5 shadow-lg">
                    <x-heroicon-s-check-circle class="w-12 h-12 text-green-600 dark:text-green-400" />
                </div>

                <h3 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">Tutup Buku Berhasil! 🎉</h3>
                <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base mb-8">{{ $closingResult['message'] ?? 'Tahun fiskal berhasil ditutup.' }}</p>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4 max-w-2xl mx-auto mb-8">
                    <div class="bg-gray-50 dark:bg-gray-800 border rounded-xl p-4">
                        <div class="text-xs text-gray-500 mb-1">Tahun Ditutup</div>
                        <div class="text-xl font-bold text-gray-900 dark:text-gray-100">{{ $closingResult['year'] ?? '-' }}</div>
                    </div>
                    <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl p-4">
                        <div class="text-xs text-green-700 mb-1">Laba Bersih</div>
                        <div class="text-xl font-bold {{ ($closingResult['netIncome'] ?? 0) >= 0 ? 'text-green-700' : 'text-red-700' }}">
                            Rp {{ number_format(abs($closingResult['netIncome'] ?? 0), 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-xl p-4">
                        <div class="text-xs text-primary-700 mb-1">Tahun Baru</div>
                        <div class="text-xl font-bold text-primary-700">{{ $closingResult['nextYear'] ?? '-' }}</div>
                    </div>
                </div>

                <x-filament::button wire:click="resetWizard" icon="heroicon-o-arrow-path" size="lg">
                    Tutup Tahun Lain
                </x-filament::button>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
