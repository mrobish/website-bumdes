<x-filament-panels::page>
    <form wire:submit="loadData">
        {{ $this->form }}
    </form>

    @php
        $labaRugi = $this->getLabaRugi();
        $neraca = $this->getNeraca();
        $arusKas = $this->getArusKas();
        $bukuBesar = $this->getBukuBesar();
        $perubahanModal = $this->getPerubahanModal();
        $realisasi = $this->getRealisasiAnggaran();
        $cat = $this->getCAT();
    @endphp

    <div class="space-y-6">
        {{-- Ringkasan --}}
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-sm text-gray-500 dark:text-gray-400">📝 Draft (Belum Publish)</div>
                <div class="text-xl font-bold text-yellow-600">{{ number_format($draftCount) }} transaksi</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-sm text-gray-500 dark:text-gray-400">✅ Published</div>
                <div class="text-xl font-bold text-green-600">{{ number_format($publishedCount) }} transaksi</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Debet</div>
                <div class="text-xl font-bold text-blue-600">Rp {{ number_format($totalDebet) }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-sm text-gray-500 dark:text-gray-400">Total Kredit</div>
                <div class="text-xl font-bold text-purple-600">Rp {{ number_format($totalKredit) }}</div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="text-sm text-gray-500 dark:text-gray-400">Laba Bersih</div>
                @php $laba = $labaRugi['laba_bersih'] ?? 0; @endphp
                <div class="text-xl font-bold {{ $laba >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    Rp {{ number_format($laba) }}
                </div>
            </div>
        </div>

        {{-- Tab Navigation --}}
        <div x-data="{ activeTab: 'laba-rugi' }">
            <div class="flex flex-wrap gap-2 border-b border-gray-200 dark:border-gray-700 pb-2">
                <button @click="activeTab = 'laba-rugi'" :class="activeTab === 'laba-rugi' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'" class="px-4 py-2 rounded-lg text-sm font-medium transition">
                    Laba Rugi
                </button>
                <button @click="activeTab = 'neraca'" :class="activeTab === 'neraca' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'" class="px-4 py-2 rounded-lg text-sm font-medium transition">
                    Neraca
                </button>
                <button @click="activeTab = 'arus-kas'" :class="activeTab === 'arus-kas' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'" class="px-4 py-2 rounded-lg text-sm font-medium transition">
                    Arus Kas
                </button>
                <button @click="activeTab = 'buku-besar'" :class="activeTab === 'buku-besar' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'" class="px-4 py-2 rounded-lg text-sm font-medium transition">
                    Buku Besar
                </button>
                <button @click="activeTab = 'modal'" :class="activeTab === 'modal' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'" class="px-4 py-2 rounded-lg text-sm font-medium transition">
                    Perubahan Modal
                </button>
                <button @click="activeTab = 'realisasi'" :class="activeTab === 'realisasi' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'" class="px-4 py-2 rounded-lg text-sm font-medium transition">
                    Realisasi Anggaran
                </button>
                <button @click="activeTab = 'cat'" :class="activeTab === 'cat' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'" class="px-4 py-2 rounded-lg text-sm font-medium transition">
                    CAT
                </button>
            </div>

            {{-- Laba Rugi --}}
            <div x-show="activeTab === 'laba-rugi'" class="mt-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold mb-4">Laporan Laba Rugi - {{ $labaRugi['bulan'] ?? '-' }}</h3>
                    @if(!empty($labaRugi['pendapatan']))
                        <div class="mb-4">
                            <h4 class="font-semibold text-green-600 mb-2">PENDAPATAN</h4>
                            <table class="w-full text-sm">
                                @foreach($labaRugi['pendapatan'] as $item)
                                    <tr class="border-b">
                                        <td class="py-2">{{ $item['kode'] }} - {{ $item['nama'] }}</td>
                                        <td class="py-2 text-right">Rp {{ number_format($item['jumlah']) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="border-b font-bold bg-green-50 dark:bg-green-900/20">
                                    <td class="py-2">Total Pendapatan</td>
                                    <td class="py-2 text-right">Rp {{ number_format($labaRugi['total_pendapatan']) }}</td>
                                </tr>
                            </table>
                        </div>
                    @endif

                    @if(!empty($labaRugi['beban']))
                        <div class="mb-4">
                            <h4 class="font-semibold text-red-600 mb-2">BEBAN</h4>
                            <table class="w-full text-sm">
                                @foreach($labaRugi['beban'] as $item)
                                    <tr class="border-b">
                                        <td class="py-2">{{ $item['kode'] }} - {{ $item['nama'] }}</td>
                                        <td class="py-2 text-right">Rp {{ number_format($item['jumlah']) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="border-b font-bold bg-red-50 dark:bg-red-900/20">
                                    <td class="py-2">Total Beban</td>
                                    <td class="py-2 text-right">Rp {{ number_format($labaRugi['total_beban']) }}</td>
                                </tr>
                            </table>
                        </div>
                    @endif

                    <div class="mt-4 p-4 rounded-lg {{ ($labaRugi['laba_bersih'] ?? 0) >= 0 ? 'bg-green-100 dark:bg-green-900/30' : 'bg-red-100 dark:bg-red-900/30' }}">
                        <div class="font-bold text-lg">
                            LABA BERSIH: Rp {{ number_format($labaRugi['laba_bersih'] ?? 0) }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Neraca --}}
            <div x-show="activeTab === 'neraca'" class="mt-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold mb-4">Neraca (Posisi Keuangan) - {{ $neraca['bulan'] ?? '-' }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-semibold text-blue-600 mb-2">ASET</h4>
                            <table class="w-full text-sm">
                                @foreach($neraca['aset'] ?? [] as $item)
                                    <tr class="border-b">
                                        <td class="py-2">{{ $item['kode'] }} - {{ $item['nama'] }}</td>
                                        <td class="py-2 text-right">Rp {{ number_format($item['jumlah']) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="border-b font-bold bg-blue-50 dark:bg-blue-900/20">
                                    <td class="py-2">Total Aset</td>
                                    <td class="py-2 text-right">Rp {{ number_format($neraca['total_aset'] ?? 0) }}</td>
                                </tr>
                            </table>
                        </div>

                        <div>
                            <h4 class="font-semibold text-purple-600 mb-2">KEWAJIBAN + EKUITAS</h4>
                            <table class="w-full text-sm">
                                @foreach($neraca['kewajiban'] ?? [] as $item)
                                    <tr class="border-b">
                                        <td class="py-2">{{ $item['kode'] }} - {{ $item['nama'] }}</td>
                                        <td class="py-2 text-right">Rp {{ number_format($item['jumlah']) }}</td>
                                    </tr>
                                @endforeach
                                @foreach($neraca['ekuitas'] ?? [] as $item)
                                    <tr class="border-b">
                                        <td class="py-2">{{ $item['kode'] }} - {{ $item['nama'] }}</td>
                                        <td class="py-2 text-right">Rp {{ number_format($item['jumlah']) }}</td>
                                    </tr>
                                @endforeach
                                <tr class="border-b font-bold bg-purple-50 dark:bg-purple-900/20">
                                    <td class="py-2">Total Kewajiban + Ekuitas</td>
                                    <td class="py-2 text-right">Rp {{ number_format(($neraca['total_kewajiban'] ?? 0) + ($neraca['total_ekuitas'] ?? 0)) }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if(($neraca['selisih'] ?? 0) != 0)
                        <div class="mt-4 p-4 rounded-lg bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200">
                            ⚠️ Selisih: Rp {{ number_format($neraca['selisih'] ?? 0) }} (seharusnya = 0)
                        </div>
                    @endif
                </div>
            </div>

            {{-- Buku Besar --}}
            <div x-show="activeTab === 'buku-besar'" class="mt-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold mb-4">Buku Besar - {{ $bukuBesar['bulan'] ?? '-' }}</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <th class="py-2 px-3 text-left">Kode</th>
                                    <th class="py-2 px-3 text-left">Nama Akun</th>
                                    <th class="py-2 px-3 text-right">Saldo Awal</th>
                                    <th class="py-2 px-3 text-right">Debet</th>
                                    <th class="py-2 px-3 text-right">Kredit</th>
                                    <th class="py-2 px-3 text-right">Saldo Akhir</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($bukuBesar['data'] ?? [] as $item)
                                    <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                        <td class="py-2 px-3 font-mono">{{ $item['kode'] }}</td>
                                        <td class="py-2 px-3">{{ $item['nama'] }}</td>
                                        <td class="py-2 px-3 text-right">Rp {{ number_format($item['saldo_awal']) }}</td>
                                        <td class="py-2 px-3 text-right text-green-600">Rp {{ number_format($item['total_debet']) }}</td>
                                        <td class="py-2 px-3 text-right text-red-600">Rp {{ number_format($item['total_kredit']) }}</td>
                                        <td class="py-2 px-3 text-right font-bold">Rp {{ number_format($item['saldo_akhir']) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-100 dark:bg-gray-700 font-bold">
                                    <td colspan="2" class="py-2 px-3">TOTAL</td>
                                    <td class="py-2 px-3 text-right">Rp {{ number_format($bukuBesar['total_debet'] ?? 0) }}</td>
                                    <td class="py-2 px-3 text-right">Rp {{ number_format($bukuBesar['total_debet'] ?? 0) }}</td>
                                    <td class="py-2 px-3 text-right">Rp {{ number_format($bukuBesar['total_kredit'] ?? 0) }}</td>
                                    <td class="py-2 px-3 text-right">-</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Perubahan Modal --}}
            <div x-show="activeTab === 'modal'" class="mt-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold mb-4">Perubahan Modal - {{ $perubahanModal['bulan'] ?? '-' }}</h3>
                    <table class="w-full text-sm">
                        @foreach($perubahanModal['modal'] ?? [] as $item)
                            <tr class="border-b">
                                <td class="py-2">{{ $item['keterangan'] }}</td>
                                <td class="py-2 text-right">Rp {{ number_format($item['jumlah']) }}</td>
                            </tr>
                        @endforeach
                        <tr class="border-b font-bold bg-green-50 dark:bg-green-900/20">
                            <td class="py-2">Total Perubahan Modal</td>
                            <td class="py-2 text-right">Rp {{ number_format($perubahanModal['total_modal'] ?? 0) }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Realisasi Anggaran --}}
            <div x-show="activeTab === 'realisasi'" class="mt-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold mb-4">Realisasi Anggaran - {{ $realisasi['bulan'] ?? '-' }}</h3>
                    @if(!empty($realisasi['data']))
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-gray-100 dark:bg-gray-700">
                                        <th class="py-2 px-3 text-left">Kode</th>
                                        <th class="py-2 px-3 text-left">Uraian</th>
                                        <th class="py-2 px-3 text-right">Anggaran</th>
                                        <th class="py-2 px-3 text-right">Realisasi</th>
                                        <th class="py-2 px-3 text-right">%</th>
                                        <th class="py-2 px-3 text-right">Selisih</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($realisasi['data'] ?? [] as $item)
                                        <tr class="border-b hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                            <td class="py-2 px-3 font-mono">{{ $item['kode_akun'] ?? '' }}</td>
                                            <td class="py-2 px-3">{{ $item['uraian'] ?? '' }}</td>
                                            <td class="py-2 px-3 text-right">Rp {{ number_format($item['anggaran'] ?? 0) }}</td>
                                            <td class="py-2 px-3 text-right">Rp {{ number_format($item['realisasi'] ?? 0) }}</td>
                                            <td class="py-2 px-3 text-right">{{ number_format($item['persentase'] ?? 0, 1) }}%</td>
                                            <td class="py-2 px-3 text-right">Rp {{ number_format($item['selisih'] ?? 0) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr class="bg-gray-100 dark:bg-gray-700 font-bold">
                                        <td colspan="2" class="py-2 px-3">TOTAL</td>
                                        <td class="py-2 px-3 text-right">Rp {{ number_format($realisasi['total_anggaran'] ?? 0) }}</td>
                                        <td class="py-2 px-3 text-right">Rp {{ number_format($realisasi['total_realisasi'] ?? 0) }}</td>
                                        <td class="py-2 px-3 text-right">{{ number_format($realisasi['persentase'] ?? 0, 1) }}%</td>
                                        <td class="py-2 px-3 text-right">Rp {{ number_format(($realisasi['total_anggaran'] ?? 0) - ($realisasi['total_realisasi'] ?? 0)) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">Belum ada data realisasi anggaran untuk bulan ini.</p>
                    @endif
                </div>
            </div>

            {{-- CAT --}}
            <div x-show="activeTab === 'cat'" class="mt-4">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-6 shadow-sm border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-bold mb-4">Catatan Atas Laporan Keuangan - {{ $cat['bulan'] ?? '-' }}</h3>
                    @if(!empty($cat['data']))
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <th class="py-2 px-3 text-left">No.</th>
                                    <th class="py-2 px-3 text-left">Uraian</th>
                                    <th class="py-2 px-3 text-right">Nilai</th>
                                    <th class="py-2 px-3 text-left">Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cat['data'] ?? [] as $item)
                                    <tr class="border-b">
                                        <td class="py-2 px-3">{{ $item['no'] ?? '' }}</td>
                                        <td class="py-2 px-3">{{ $item['uraian'] ?? '' }}</td>
                                        <td class="py-2 px-3 text-right">
                                            @if(is_numeric($item['nilai'] ?? ''))
                                                Rp {{ number_format($item['nilai']) }}
                                            @else
                                                {{ $item['nilai'] ?? '-' }}
                                            @endif
                                        </td>
                                        <td class="py-2 px-3">{{ $item['keterangan'] ?? '' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <p class="text-gray-500 dark:text-gray-400">Belum ada data CAT untuk bulan ini.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
