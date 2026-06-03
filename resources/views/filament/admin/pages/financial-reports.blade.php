<x-filament-panels::page>
    <form wire:submit="generateReport">
        <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
            <div class="flex items-end gap-4 flex-wrap">
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Periode</label>
                    <select wire:model="period" class="w-full border-gray-300 rounded-lg">
                        @foreach(range(1, 12) as $m)
                            <option value="{{ date('Y') }}-{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">
                                {{ date('Y') }}-{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 min-w-[200px]">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jenis Laporan</label>
                    <select wire:model="activeTab" class="w-full border-gray-300 rounded-lg">
                        <option value="neraca">Neraca (Posisi Keuangan)</option>
                        <option value="lr">Laba/Rugi Konsolidasi</option>
                        <option value="arus_kas">Arus Kas</option>
                        <option value="calk">CALK</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-lg">
                    Generate
                </button>
            </div>
        </div>
    </form>

    @if($period)
        <div class="bg-white rounded-xl shadow-sm border overflow-hidden">
            <div class="flex border-b">
                <button wire:click="$set('activeTab', 'neraca')" class="px-6 py-3 text-sm font-medium {{ $activeTab === 'neraca' ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50' : 'text-gray-500 hover:text-gray-700' }}">📊 Neraca</button>
                <button wire:click="$set('activeTab', 'lr')" class="px-6 py-3 text-sm font-medium {{ $activeTab === 'lr' ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50' : 'text-gray-500 hover:text-gray-700' }}">📈 Laba/Rugi</button>
                <button wire:click="$set('activeTab', 'arus_kas')" class="px-6 py-3 text-sm font-medium {{ $activeTab === 'arus_kas' ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50' : 'text-gray-500 hover:text-gray-700' }}">💵 Arus Kas</button>
                <button wire:click="$set('activeTab', 'calk')" class="px-6 py-3 text-sm font-medium {{ $activeTab === 'calk' ? 'text-blue-600 border-b-2 border-blue-600 bg-blue-50' : 'text-gray-500 hover:text-gray-700' }}">📝 CALK</button>
            </div>

            <div class="p-6">
                @if($activeTab === 'neraca')
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold">PT BUMDes Digital</h2>
                        <h3 class="text-lg font-semibold text-gray-600">Laporan Posisi Keuangan (Neraca)</h3>
                        <p class="text-sm text-gray-500">Per 31 {{ \Carbon\Carbon::parse($period . '-01')->locale('id')->isoFormat('MMMM YYYY') }}</p>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-bold text-lg mb-3 text-blue-800">ASET</h4>
                            <div class="mb-4">
                                <h5 class="font-semibold text-gray-700 mb-2">Aset Lancar</h5>
                                <div class="bg-gray-50 rounded-lg p-3 space-y-1">
                                    <div class="flex justify-between"><span>Kas dan Ekivalen Kas</span><span class="font-medium">Rp {{ number_format($assets['current'] * 0.6, 0, ',', '.') }}</span></div>
                                    <div class="flex justify-between"><span>Piutang Usaha</span><span class="font-medium">Rp {{ number_format($assets['current'] * 0.2, 0, ',', '.') }}</span></div>
                                    <div class="flex justify-between"><span>Persediaan</span><span class="font-medium">Rp {{ number_format($assets['current'] * 0.2, 0, ',', '.') }}</span></div>
                                    <div class="flex justify-between border-t font-bold"><span>Total Aset Lancar</span><span>Rp {{ number_format($assets['current'], 0, ',', '.') }}</span></div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <h5 class="font-semibold text-gray-700 mb-2">Aset Tidak Lancar</h5>
                                <div class="bg-gray-50 rounded-lg p-3 space-y-1">
                                    <div class="flex justify-between"><span>Tanah</span><span class="font-medium">Rp {{ number_format($assets['fixed'] * 0.4, 0, ',', '.') }}</span></div>
                                    <div class="flex justify-between"><span>Bangunan</span><span class="font-medium">Rp {{ number_format($assets['fixed'] * 0.3, 0, ',', '.') }}</span></div>
                                    <div class="flex justify-between"><span>Peralatan</span><span class="font-medium">Rp {{ number_format($assets['fixed'] * 0.2, 0, ',', '.') }}</span></div>
                                    <div class="flex justify-between"><span>Kendaraan</span><span class="font-medium">Rp {{ number_format($assets['fixed'] * 0.1, 0, ',', '.') }}</span></div>
                                    <div class="flex justify-between border-t font-bold"><span>Total Aset Tidak Lancar</span><span>Rp {{ number_format($assets['fixed'], 0, ',', '.') }}</span></div>
                                </div>
                            </div>
                            <div class="bg-blue-100 rounded-lg p-3 font-bold text-lg">
                                <div class="flex justify-between"><span>JUMLAH ASET</span><span class="text-blue-800">Rp {{ number_format($assets['total'], 0, ',', '.') }}</span></div>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg mb-3 text-red-800">KEWAJIBAN DAN EKUITAS</h4>
                            <div class="mb-4">
                                <h5 class="font-semibold text-gray-700 mb-2">Kewajiban Lancar</h5>
                                <div class="bg-gray-50 rounded-lg p-3 space-y-1">
                                    <div class="flex justify-between"><span>Hutang Usaha</span><span class="font-medium">Rp {{ number_format($liabilities['current'] * 0.6, 0, ',', '.') }}</span></div>
                                    <div class="flex justify-between"><span>Hutang Bank</span><span class="font-medium">Rp {{ number_format($liabilities['current'] * 0.4, 0, ',', '.') }}</span></div>
                                    <div class="flex justify-between border-t font-bold"><span>Total Kewajiban</span><span>Rp {{ number_format($liabilities['total'], 0, ',', '.') }}</span></div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <h5 class="font-semibold text-gray-700 mb-2">Ekuitas</h5>
                                <div class="bg-gray-50 rounded-lg p-3 space-y-1">
                                    <div class="flex justify-between"><span>Modal Setoran</span><span class="font-medium">Rp {{ number_format($equity['capital'], 0, ',', '.') }}</span></div>
                                    <div class="flex justify-between"><span>Laba Ditahan</span><span class="font-medium">Rp {{ number_format($equity['retained'], 0, ',', '.') }}</span></div>
                                    <div class="flex justify-between"><span>Laba Tahun Berjalan</span><span class="font-medium">Rp {{ number_format($equity['current_year'], 0, ',', '.') }}</span></div>
                                    <div class="flex justify-between border-t font-bold"><span>Total Ekuitas</span><span>Rp {{ number_format($equity['total'], 0, ',', '.') }}</span></div>
                                </div>
                            </div>
                            <div class="bg-red-100 rounded-lg p-3 font-bold text-lg">
                                <div class="flex justify-between"><span>JUMLAH KEWAJIBAN & EKUITAS</span><span class="text-red-800">Rp {{ number_format($liabilities['total'] + $equity['total'], 0, ',', '.') }}</span></div>
                            </div>
                        </div>
                    </div>

                @elseif($activeTab === 'lr')
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold">PT BUMDes Digital</h2>
                        <h3 class="text-lg font-semibold text-gray-600">Laporan Laba/Rugi Konsolidasi</h3>
                        <p class="text-sm text-gray-500">Periode {{ \Carbon\Carbon::parse($period . '-01')->locale('id')->isoFormat('MMMM YYYY') }}</p>
                    </div>
                    <div class="max-w-xl mx-auto space-y-4">
                        <div>
                            <h4 class="font-bold text-green-800 mb-2">PENDAPATAN</h4>
                            <div class="bg-gray-50 rounded-lg p-3 space-y-1">
                                <div class="flex justify-between"><span>Penjualan Produk</span><span>Rp {{ number_format($revenue['operating'] * 0.7, 0, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span>Pendapatan Jasa</span><span>Rp {{ number_format($revenue['operating'] * 0.3, 0, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span>Pendapatan Bunga</span><span>Rp {{ number_format($revenue['other'] * 0.5, 0, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span>Pendapatan Sewa</span><span>Rp {{ number_format($revenue['other'] * 0.5, 0, ',', '.') }}</span></div>
                                <div class="flex justify-between border-t font-bold pt-2"><span>Total Pendapatan</span><span class="text-green-600">Rp {{ number_format($revenue['total'], 0, ',', '.') }}</span></div>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-bold text-red-800 mb-2">BEBAN</h4>
                            <div class="bg-gray-50 rounded-lg p-3 space-y-1">
                                <div class="flex justify-between"><span>Beban Gaji</span><span>Rp {{ number_format($expenses['operating'] * 0.4, 0, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span>Beban Sewa</span><span>Rp {{ number_format($expenses['operating'] * 0.2, 0, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span>Beban Listrik</span><span>Rp {{ number_format($expenses['operating'] * 0.1, 0, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span>Beban ATK</span><span>Rp {{ number_format($expenses['operating'] * 0.1, 0, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span>Beban Admin Bank</span><span>Rp {{ number_format($expenses['other'] * 0.5, 0, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span>Beban Pajak</span><span>Rp {{ number_format($expenses['other'] * 0.5, 0, ',', '.') }}</span></div>
                                <div class="flex justify-between border-t font-bold pt-2"><span>Total Beban</span><span class="text-red-600">Rp {{ number_format($expenses['total'], 0, ',', '.') }}</span></div>
                            </div>
                        </div>
                        <div class="bg-{{ $netProfit >= 0 ? 'green' : 'red' }}-100 rounded-lg p-4 font-bold text-xl">
                            <div class="flex justify-between"><span>LABA BERSIH</span><span class="text-{{ $netProfit >= 0 ? 'green' : 'red' }}-800">Rp {{ number_format($netProfit, 0, ',', '.') }}</span></div>
                        </div>
                    </div>

                @elseif($activeTab === 'arus_kas')
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold">PT BUMDes Digital</h2>
                        <h3 class="text-lg font-semibold text-gray-600">Laporan Arus Kas</h3>
                        <p class="text-sm text-gray-500">Periode {{ \Carbon\Carbon::parse($period . '-01')->locale('id')->isoFormat('MMMM YYYY') }}</p>
                    </div>
                    <div class="max-w-xl mx-auto space-y-4">
                        <div>
                            <h4 class="font-bold mb-2">Aktivitas Operasional</h4>
                            <div class="bg-gray-50 rounded-lg p-3 space-y-1">
                                <div class="flex justify-between"><span>Penerimaan dari pelanggan</span><span class="text-green-600">Rp {{ number_format($revenue['total'], 0, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span>Pembayaran pemasok & gaji</span><span class="text-red-600">-Rp {{ number_format($expenses['operating'], 0, ',', '.') }}</span></div>
                                <div class="flex justify-between"><span>Pembayaran pajak</span><span class="text-red-600">-Rp {{ number_format($expenses['other'] * 0.5, 0, ',', '.') }}</span></div>
                                <div class="flex justify-between border-t font-bold pt-2"><span>Kas Bersih Operasional</span><span>Rp {{ number_format($cashFlow['operating'], 0, ',', '.') }}</span></div>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-bold mb-2">Aktivitas Investasi</h4>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="flex justify-between"><span>Pembelian aset tetap</span><span class="text-red-600">-Rp 0</span></div>
                                <div class="flex justify-between border-t font-bold pt-2"><span>Kas Bersih Investasi</span><span>Rp 0</span></div>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-bold mb-2">Aktivitas Pendanaan</h4>
                            <div class="bg-gray-50 rounded-lg p-3">
                                <div class="flex justify-between"><span>Penyertaan modal dari desa</span><span class="text-green-600">Rp 0</span></div>
                                <div class="flex justify-between border-t font-bold pt-2"><span>Kas Bersih Pendanaan</span><span>Rp 0</span></div>
                            </div>
                        </div>
                        <div class="bg-blue-100 rounded-lg p-4 font-bold">
                            <div class="flex justify-between"><span>Saldo Kas Awal</span><span>Rp 0</span></div>
                            <div class="flex justify-between"><span>Penambahan Kas Bersih</span><span>Rp {{ number_format($cashFlow['net'], 0, ',', '.') }}</span></div>
                            <div class="flex justify-between text-lg border-t pt-2"><span>SALDO KAS AKHIR</span><span class="text-blue-800">Rp {{ number_format($cashFlow['ending'], 0, ',', '.') }}</span></div>
                        </div>
                    </div>

                @elseif($activeTab === 'calk')
                    <div class="text-center mb-6">
                        <h2 class="text-xl font-bold">PT BUMDes Digital</h2>
                        <h3 class="text-lg font-semibold text-gray-600">Catatan Atas Laporan Keuangan (CALK)</h3>
                        <p class="text-sm text-gray-500">Periode {{ \Carbon\Carbon::parse($period . '-01')->locale('id')->isoFormat('MMMM YYYY') }}</p>
                    </div>
                    <div class="max-w-2xl mx-auto space-y-6 text-sm">
                        <div>
                            <h4 class="font-bold text-lg mb-2">1. Umum</h4>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                <p><strong>1.1 Bentuk Badan Hukum:</strong> BUMDes sesuai UU No. 6 Tahun 2014 tentang Desa.</p>
                                <p><strong>1.2 Bidang Usaha:</strong> Ketahanan Pangan, Pariwisata, Pengelolaan Sampah, Jaringan Internet.</p>
                                <p><strong>1.3 Periode:</strong> {{ \Carbon\Carbon::parse($period . '-01')->locale('id')->isoFormat('MMMM YYYY') }}</p>
                                <p><strong>1.4 Dasar:</strong> SAK EMKM.</p>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg mb-2">2. Ikhtisar Posisi Keuangan</h4>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                <p><strong>2.1 Kas:</strong> Kas di kas BUMDes dan rekening bank untuk operasional harian.</p>
                                <p><strong>2.2 Piutang:</strong> Piutang dari penjualan produk dan jasa, tertagih dalam 30 hari.</p>
                                <p><strong>2.3 Persediaan:</strong> Persediaan produk pertanian dan barang dagangan.</p>
                                <p><strong>2.4 Aset Tetap:</strong> Tanah, bangunan, peralatan, kendaraan. Penyusutan metode garis lurus.</p>
                                <p><strong>2.5 Kewajiban:</strong> Hutang usaha dan hutang bank untuk modal kerja.</p>
                                <p><strong>2.6 Ekuitas:</strong> Modal setoran desa, laba ditahan, laba tahun berjalan.</p>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg mb-2">3. Ikhtisar Hasil Usaha</h4>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                <p><strong>3.1 Pendapatan:</strong> Penjualan produk, jasa pariwisata, daur ulang sampah, jasa internet.</p>
                                <p><strong>3.2 Beban:</strong> Gaji, sewa, listrik, ATK, admin bank, pajak.</p>
                                <p><strong>3.3 Laba Bersih:</strong> Rp {{ number_format($netProfit, 0, ',', '.') }}. Dialokasikan untuk penyertaan modal dan laba ditahan.</p>
                            </div>
                        </div>
                        <div>
                            <h4 class="font-bold text-lg mb-2">4. Kebijakan Akuntansi</h4>
                            <div class="bg-gray-50 rounded-lg p-4 space-y-2">
                                <p><strong>4.1 Pendapatan:</strong> Diakui saat barang diserahkan atau jasa selesai.</p>
                                <p><strong>4.2 Penyusutan:</strong> Metode garis lurus selama umur ekonomis.</p>
                                <p><strong>4.3 Persediaan:</strong> Metode FIFO.</p>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</x-filament-panels::page>
