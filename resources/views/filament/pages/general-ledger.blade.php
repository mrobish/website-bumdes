<x-filament-panels::page>
    <form wire:submit.prevent="loadData">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Akun</label>
                <select wire:model="accountCode" class="w-full rounded-lg border-gray-300 shadow-sm">
                    <option value="">-- Pilih Akun --</option>
                    @foreach(\App\Models\Account::active()->orderBy('code')->get() as $acc)
                        <option value="{{ $acc->code }}">{{ $acc->code }} — {{ $acc->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Dari</label>
                <input type="date" wire:model="dateFrom" class="w-full rounded-lg border-gray-300 shadow-sm">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Sampai</label>
                <input type="date" wire:model="dateTo" class="w-full rounded-lg border-gray-300 shadow-sm">
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
        <div class="flex gap-2">
            <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">Tampilkan Buku Besar</x-filament::button>
            @if($loaded && $accountCode)

            @endif
        </div>
    </form>

    @if($loaded && $accountCode)
        <div class="mt-6">
            <x-filament::section>
                <x-slot name="heading">Buku Besar — {{ \App\Models\Account::where('code', $accountCode)->first()->name ?? '' }}</x-slot>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead><tr class="border-b bg-gray-50">
                            <th class="px-3 py-2 text-left">Tanggal</th>
                            <th class="px-3 py-2 text-left">No. Ref</th>
                            <th class="px-3 py-2 text-left">Keterangan</th>
                            <th class="px-3 py-2 text-right">Debet</th>
                            <th class="px-3 py-2 text-right">Kredit</th>
                            <th class="px-3 py-2 text-right">Saldo</th>
                        </tr></thead>
                        <tbody>
                            @php $rb = 0; @endphp
                            @foreach($entries as $e)
                                @php $rb += $e['debit'] - $e['credit']; @endphp
                                <tr class="border-b">
                                    <td class="px-3 py-2">{{ \Carbon\Carbon::parse($e['entry_date'])->format('d/m/Y') }}</td>
                                    <td class="px-3 py-2">{{ $e['transaction']['transaction_number'] ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $e['description'] }}</td>
                                    <td class="px-3 py-2 text-right">@if($e['debit'] > 0) Rp {{ number_format($e['debit'], 0, ',', '.') }} @endif</td>
                                    <td class="px-3 py-2 text-right">@if($e['credit'] > 0) Rp {{ number_format($e['credit'], 0, ',', '.') }} @endif</td>
                                    <td class="px-3 py-2 text-right font-bold">Rp {{ number_format($rb, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="border-t-2 font-bold bg-gray-100">
                                <td colspan="3" class="px-3 py-2">Total</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($totalDebit, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($totalCredit, 0, ',', '.') }}</td>
                                <td class="px-3 py-2 text-right">Rp {{ number_format($balance, 0, ',', '.') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </x-filament::section>
        </div>
    @endif
    {{-- ===== TOMBOL DOWNLOAD PDF BESAR ===== --}}
    @php $fy = \App\Models\FiscalYear::where('year', \Carbon\Carbon::parse($dateFrom)->year)->first(); @endphp
    @if($fy && $loaded && $accountCode)
    <div class="mt-4 p-4 bg-gradient-to-r from-red-500 to-red-600 rounded-xl shadow-lg flex items-center justify-between" style="background: linear-gradient(135deg, #dc2626, #b91c1c);">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center text-2xl">📄</div>
            <div>
                <h3 class="text-white font-bold text-lg">Download Laporan PDF</h3>
                <p class="text-red-100 text-sm">File PDF profesional dengan kop surat &amp; tanda tangan</p>
            </div>
        </div>
        <a href="{{ route('pdf.jurnal-umum', ['fiscal_year_id' => $fy->id]) }}" target="_blank"
           class="inline-flex items-center gap-2 px-6 py-3 bg-white text-red-600 font-bold rounded-lg hover:bg-red-50 transition-all shadow-md text-base">
            ⬇️ Download PDF
        </a>
    </div>
    @endif

</x-filament-panels::page>
