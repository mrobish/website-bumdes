<x-filament-panels::page>
    {{-- Step Indicator --}}
    <div class="mb-8">
        <div class="flex items-center justify-between max-w-3xl mx-auto">
            @php
                $steps = [
                    1 => ['label' => 'Pilih Tahun', 'icon' => 'heroicon-o-calendar'],
                    2 => ['label' => 'Neraca Saldo', 'icon' => 'heroicon-o-scale'],
                    3 => ['label' => 'Laba/Rugi', 'icon' => 'heroicon-o-calculator'],
                    4 => ['label' => 'Konfirmasi', 'icon' => 'heroicon-o-check-circle'],
                    5 => ['label' => 'Selesai', 'icon' => 'heroicon-o-flag'],
                ];
            @endphp

            @foreach($steps as $stepNum => $step)
                <div class="flex items-center {{ $stepNum < 5 ? 'flex-1' : '' }}">
                    <div class="flex flex-col items-center">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center text-sm font-bold
                            {{ $currentStep >= $stepNum ? 'bg-primary-600 text-white' : 'bg-gray-200 text-gray-500' }}">
                            @if($currentStep > $stepNum)
                                <x-heroicon-s-check class="w-5 h-5" />
                            @else
                                {{ $stepNum }}
                            @endif
                        </div>
                        <span class="text-xs mt-1 {{ $currentStep >= $stepNum ? 'text-primary-600 font-medium' : 'text-gray-400' }}">
                            {{ $step['label'] }}
                        </span>
                    </div>
                    @if($stepNum < 5)
                        <div class="flex-1 h-0.5 mx-2 {{ $currentStep > $stepNum ? 'bg-primary-600' : 'bg-gray-200' }}"></div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>

    {{-- Step 1: Select Fiscal Year --}}
    @if($currentStep === 1)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-calendar class="w-5 h-5" />
                    <span>Langkah 1: Pilih Tahun Fiskal yang Akan Ditutup</span>
                </div>
            </x-slot>

            @if(count($openFiscalYears) > 0)
                <p class="text-gray-600 mb-4">
                    Pilih tahun fiskal yang ingin ditutup. Hanya tahun dengan status "Open" yang dapat ditutup.
                </p>

                <div class="space-y-3">
                    @foreach($openFiscalYears as $fy)
                        <label class="flex items-center p-4 border rounded-lg cursor-pointer transition
                                      {{ $selectedFiscalYearId === $fy['id'] ? 'border-primary-500 bg-primary-50' : 'border-gray-200 hover:bg-gray-50' }}">
                            <input type="radio" wire:model="selectedFiscalYearId" value="{{ $fy['id'] }}"
                                   class="text-primary-600 focus:ring-primary-500">
                            <div class="ml-3">
                                <div class="font-medium text-gray-900">Tahun Fiskal {{ $fy['year'] }}</div>
                                <div class="text-sm text-gray-500">
                                    Status: <x-filament::badge color="success">Open</x-filament::badge>
                                    @if($fy['closed_at'])
                                        &middot; Ditutup: {{ $fy['closed_at'] }}
                                    @endif
                                </div>
                            </div>
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
                <div class="text-center py-8 text-gray-500">
                    <x-heroicon-o-check-circle class="w-12 h-12 mx-auto mb-3 text-green-400" />
                    <p class="text-lg font-medium">Semua tahun fiskal sudah ditutup</p>
                    <p class="text-sm">Tidak ada tahun fiskal yang tersedia untuk ditutup.</p>
                </div>
            @endif
        </x-filament::section>
    @endif

    {{-- Step 2: Trial Balance Preview --}}
    @if($currentStep === 2)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center justify-between w-full">
                    <div class="flex items-center gap-2">
                        <x-heroicon-o-scale class="w-5 h-5" />
                        <span>Langkah 2: Preview Neraca Saldo Tahun {{ $this->getSelectedYear() }}</span>
                    </div>
                    @if($tbBalanced)
                        <x-filament::badge color="success">SEIMBANG ✓</x-filament::badge>
                    @else
                        <x-filament::badge color="danger">TIDAK SEIMBANG ✗</x-filament::badge>
                    @endif
                </div>
            </x-slot>

            {{-- Pre-check status --}}
            @if(!$canClose)
                <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center gap-2 text-red-800">
                        <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                        <span class="font-medium">{{ $canCloseReason }}</span>
                    </div>
                </div>
            @else
                <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-4">
                    <div class="flex items-center gap-2 text-green-800">
                        <x-heroicon-o-check-circle class="w-5 h-5" />
                        <span class="font-medium">{{ $canCloseReason }}</span>
                    </div>
                </div>
            @endif

            @if(count($trialBalance) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="px-3 py-2 text-left">Kode</th>
                                <th class="px-3 py-2 text-left">Nama Akun</th>
                                <th class="px-3 py-2 text-left">Tipe</th>
                                <th class="px-3 py-2 text-right">Debet</th>
                                <th class="px-3 py-2 text-right">Kredit</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trialBalance as $a)
                                <tr class="border-b hover:bg-gray-50">
                                    <td class="px-3 py-2 font-mono">{{ $a['code'] }}</td>
                                    <td class="px-3 py-2">{{ $a['name'] }}</td>
                                    <td class="px-3 py-2">
                                        <x-filament::badge size="sm">{{ $a['type'] }}</x-filament::badge>
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        @if($a['debit'] > 0) Rp {{ number_format($a['debit'], 0, ',', '.') }} @endif
                                    </td>
                                    <td class="px-3 py-2 text-right">
                                        @if($a['credit'] > 0) Rp {{ number_format($a['credit'], 0, ',', '.') }} @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 font-bold bg-gray-100">
                                <td colspan="3" class="px-3 py-2">TOTAL</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($tbTotalDebit, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($tbTotalCredit, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @else
                <p class="text-gray-500 text-center py-4">Tidak ada data neraca saldo.</p>
            @endif

            <div class="flex justify-between mt-6">
                <x-filament::button wire:click="goBack" color="gray" icon="heroicon-o-arrow-left">
                    Kembali
                </x-filament::button>
                <x-filament::button wire:click="goToStep3" icon="heroicon-o-arrow-right">
                    Selanjutnya
                </x-filament::button>
            </div>
        </x-filament::section>
    @endif

    {{-- Step 3: Income Statement Preview --}}
    @if($currentStep === 3)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-calculator class="w-5 h-5" />
                    <span>Langkah 3: Preview Laba/Rugi Tahun {{ $this->getSelectedYear() }}</span>
                </div>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Revenue --}}
                <div>
                    <h4 class="font-semibold text-green-700 mb-2">📈 Pendapatan</h4>
                    @if(count($incomeRevenues) > 0)
                        <div class="border rounded-lg overflow-hidden">
                            <table class="w-full text-sm">
                                <thead><tr class="bg-green-50 border-b">
                                    <th class="px-3 py-2 text-left">Kode</th>
                                    <th class="px-3 py-2 text-left">Nama</th>
                                    <th class="px-3 py-2 text-right">Jumlah</th>
                                </tr></thead>
                                <tbody>
                                    @foreach($incomeRevenues as $r)
                                        <tr class="border-b hover:bg-green-50/50">
                                            <td class="px-3 py-2 font-mono">{{ $r['code'] }}</td>
                                            <td class="px-3 py-2">{{ $r['name'] }}</td>
                                            <td class="px-3 py-2 text-right">Rp {{ number_format($r['amount'], 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="font-bold bg-green-100 border-t">
                                        <td colspan="2" class="px-3 py-2">Total Pendapatan</td>
                                        <td class="px-3 py-2 text-right">Rp {{ number_format($incomeTotalRevenue, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-400 text-sm italic">Tidak ada pendapatan</p>
                    @endif
                </div>

                {{-- Expense --}}
                <div>
                    <h4 class="font-semibold text-red-700 mb-2">📉 Beban</h4>
                    @if(count($incomeExpenses) > 0)
                        <div class="border rounded-lg overflow-hidden">
                            <table class="w-full text-sm">
                                <thead><tr class="bg-red-50 border-b">
                                    <th class="px-3 py-2 text-left">Kode</th>
                                    <th class="px-3 py-2 text-left">Nama</th>
                                    <th class="px-3 py-2 text-right">Jumlah</th>
                                </tr></thead>
                                <tbody>
                                    @foreach($incomeExpenses as $e)
                                        <tr class="border-b hover:bg-red-50/50">
                                            <td class="px-3 py-2 font-mono">{{ $e['code'] }}</td>
                                            <td class="px-3 py-2">{{ $e['name'] }}</td>
                                            <td class="px-3 py-2 text-right">Rp {{ number_format($e['amount'], 0, ',', '.') }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="font-bold bg-red-100 border-t">
                                        <td colspan="2" class="px-3 py-2">Total Beban</td>
                                        <td class="px-3 py-2 text-right">Rp {{ number_format($incomeTotalExpense, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-400 text-sm italic">Tidak ada beban</p>
                    @endif
                </div>
            </div>

            {{-- Net Income Summary --}}
            <div class="mt-6 p-4 rounded-lg {{ $incomeNetIncome >= 0 ? 'bg-green-50 border border-green-200' : 'bg-red-50 border border-red-200' }}">
                <div class="flex items-center justify-between">
                    <span class="font-semibold text-lg">
                        {{ $incomeNetIncome >= 0 ? '💰 Laba Bersih' : '📉 Rugi Bersih' }}
                    </span>
                    <span class="font-bold text-xl {{ $incomeNetIncome >= 0 ? 'text-green-700' : 'text-red-700' }}">
                        Rp {{ number_format(abs($incomeNetIncome), 0, ',', '.') }}
                    </span>
                </div>
                @if($incomeNetIncome >= 0)
                    <p class="text-sm text-green-600 mt-1">
                        Laba bersih akan ditransfer ke akun "Laba Ditahan" (3201)
                    </p>
                @else
                    <p class="text-sm text-red-600 mt-1">
                        Rugi bersih akan mengurangi akun "Laba Ditahan" (3201)
                    </p>
                @endif
            </div>

            <div class="flex justify-between mt-6">
                <x-filament::button wire:click="goBack" color="gray" icon="heroicon-o-arrow-left">
                    Kembali
                </x-filament::button>
                <x-filament::button wire:click="goToStep4" icon="heroicon-o-arrow-right">
                    Selanjutnya
                </x-filament::button>
            </div>
        </x-filament::section>
    @endif

    {{-- Step 4: Confirmation --}}
    @if($currentStep === 4)
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-check-circle class="w-5 h-5" />
                    <span>Langkah 4: Konfirmasi Tutup Buku</span>
                </div>
            </x-slot>

            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-2 text-amber-800">
                    <x-heroicon-o-exclamation-triangle class="w-5 h-5" />
                    <span class="font-medium">Peringatan: Tindakan ini tidak dapat dibatalkan!</span>
                </div>
                <p class="text-amber-700 text-sm mt-2">
                    Semua jurnal penutup akan dibuat dan tahun fiskal akan ditandai sebagai "closed".
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div class="bg-white border rounded-lg p-4 text-center">
                    <div class="text-sm text-gray-500">Tahun Fiskal</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $this->getSelectedYear() }}</div>
                </div>
                <div class="bg-white border rounded-lg p-4 text-center">
                    <div class="text-sm text-gray-500">Total Pendapatan</div>
                    <div class="text-xl font-bold text-green-600">
                        Rp {{ number_format($incomeTotalRevenue, 0, ',', '.') }}
                    </div>
                </div>
                <div class="bg-white border rounded-lg p-4 text-center">
                    <div class="text-sm text-gray-500">Total Beban</div>
                    <div class="text-xl font-bold text-red-600">
                        Rp {{ number_format($incomeTotalExpense, 0, ',', '.') }}
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 border rounded-lg p-4 mb-6">
                <h4 class="font-semibold mb-2">Yang akan dilakukan:</h4>
                <ul class="space-y-2 text-sm text-gray-700">
                    <li class="flex items-center gap-2">
                        <x-heroicon-s-check class="w-4 h-4 text-green-500" />
                        Jurnal penutup untuk semua akun pendapatan
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-s-check class="w-4 h-4 text-green-500" />
                        Jurnal penutup untuk semua akun beban
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-s-check class="w-4 h-4 text-green-500" />
                        Transfer laba bersih ke Laba Ditahan (3201)
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-s-check class="w-4 h-4 text-green-500" />
                        Status tahun fiskal diubah ke "closed"
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-s-check class="w-4 h-4 text-green-500" />
                        Tahun fiskal baru {{ $this->getSelectedYear() + 1 }} akan dibuat otomatis
                    </li>
                    <li class="flex items-center gap-2">
                        <x-heroicon-s-check class="w-4 h-4 text-green-500" />
                        Catatan akan ditambahkan ke audit log
                    </li>
                </ul>
            </div>

            <div class="flex justify-between mt-6">
                <x-filament::button wire:click="goBack" color="gray" icon="heroicon-o-arrow-left">
                    Kembali
                </x-filament::button>
                <x-filament::button wire:click="executeClosing"
                                    icon="heroicon-o-check"
                                    color="danger"
                                    :loading="$processing"
                                    :disabled="$processing || !$canClose">
                    Tutup Buku Sekarang
                </x-filament::button>
            </div>
        </x-filament::section>
    @endif

    {{-- Step 5: Success --}}
    @if($currentStep === 5)
        <x-filament::section>
            <div class="text-center py-8">
                <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-heroicon-s-check-circle class="w-12 h-12 text-green-600" />
                </div>

                <h3 class="text-2xl font-bold text-gray-900 mb-2">Tutup Buku Berhasil! 🎉</h3>
                <p class="text-gray-600 mb-6">{{ $closingResult['message'] ?? 'Tahun fiskal berhasil ditutup.' }}</p>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 max-w-2xl mx-auto mb-6">
                    <div class="bg-white border rounded-lg p-4">
                        <div class="text-sm text-gray-500">Tahun Ditutup</div>
                        <div class="text-xl font-bold">{{ $closingResult['year'] ?? '-' }}</div>
                    </div>
                    <div class="bg-white border rounded-lg p-4">
                        <div class="text-sm text-gray-500">Laba Bersih</div>
                        <div class="text-xl font-bold {{ ($closingResult['netIncome'] ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            Rp {{ number_format(abs($closingResult['netIncome'] ?? 0), 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="bg-white border rounded-lg p-4">
                        <div class="text-sm text-gray-500">Tahun Baru</div>
                        <div class="text-xl font-bold text-primary-600">{{ $closingResult['nextYear'] ?? '-' }}</div>
                    </div>
                </div>

                <x-filament::button wire:click="resetWizard" icon="heroicon-o-arrow-path">
                    Tutup Tahun Lain
                </x-filament::button>
            </div>
        </x-filament::section>
    @endif
</x-filament-panels::page>
