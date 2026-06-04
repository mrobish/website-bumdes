<x-filament-panels::page>
    <form wire:submit.prevent="loadData">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Tahun</label>
                <select wire:model="year" class="w-full rounded-lg border-gray-300 shadow-sm">
                    @for($y = now()->year + 1; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
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
            <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">Tampilkan Laba/Rugi</x-filament::button>
            @if($loaded)
                @php $fy = \App\Models\FiscalYear::where('year', $year)->first(); @endphp
                @if($fy)
                <a href="{{ route('pdf.laba-rugi', ['fiscal_year_id' => $fy->id]) }}" target="_blank" class="inline-flex items-center gap-1 px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium">📄 Download PDF</a>
                @endif
            @endif
        </div>
    </form>

    @if($loaded)
        <div class="mt-6 space-y-4">
            <x-filament::section>
                <x-slot name="heading">Pendapatan</x-slot>
                <table class="w-full text-sm">
                    <thead><tr class="border-b bg-green-50"><th class="px-3 py-2 text-left">Kode</th><th class="px-3 py-2 text-left">Nama</th><th class="px-3 py-2 text-right">Jumlah</th></tr></thead>
                    <tbody>
                        @forelse($revenues as $r)
                            <tr class="border-b"><td class="px-3 py-2 font-mono">{{ $r['code'] }}</td><td class="px-3 py-2">{{ $r['name'] }}</td><td class="px-3 py-2 text-right text-green-600">Rp {{ number_format($r['amount'], 0, ',', '.') }}</td></tr>
                        @empty <tr><td colspan="3" class="px-3 py-4 text-center text-gray-500">Belum ada</td></tr>@endforelse
                    </tbody>
                    <tfoot><tr class="border-t-2 font-bold bg-green-100"><td colspan="2" class="px-3 py-2">Total</td><td class="px-3 py-2 text-right">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td></tr></tfoot>
                </table>
            </x-filament::section>
            <x-filament::section>
                <x-slot name="heading">Beban</x-slot>
                <table class="w-full text-sm">
                    <thead><tr class="border-b bg-red-50"><th class="px-3 py-2 text-left">Kode</th><th class="px-3 py-2 text-left">Nama</th><th class="px-3 py-2 text-right">Jumlah</th></tr></thead>
                    <tbody>
                        @forelse($expenses as $e)
                            <tr class="border-b"><td class="px-3 py-2 font-mono">{{ $e['code'] }}</td><td class="px-3 py-2">{{ $e['name'] }}</td><td class="px-3 py-2 text-right text-red-600">Rp {{ number_format($e['amount'], 0, ',', '.') }}</td></tr>
                        @empty <tr><td colspan="3" class="px-3 py-4 text-center text-gray-500">Belum ada</td></tr>@endforelse
                    </tbody>
                    <tfoot><tr class="border-t-2 font-bold bg-red-100"><td colspan="2" class="px-3 py-2">Total</td><td class="px-3 py-2 text-right">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td></tr></tfoot>
                </table>
            </x-filament::section>
            <div class="text-center py-4 bg-gray-50 rounded-lg">
                <div class="text-sm text-gray-500">Laba / Rugi Bersih</div>
                <div class="text-2xl font-bold {{ $netIncome >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $netIncome >= 0 ? 'LABA' : 'RUGI' }} Rp {{ number_format(abs($netIncome), 0, ',', '.') }}
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
