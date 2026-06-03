<x-filament-panels::page>
    <form wire:submit.prevent="loadData">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Tahun</label>
                <select wire:model="year" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    @for($y = now()->year + 1; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Bulan</label>
                <select wire:model="month" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    @foreach([1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'] as $m => $n)
                        <option value="{{ $m }}">{{ $n }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Unit</label>
                <select wire:model="unitId" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                    <option value="">Semua Unit</option>
                    @foreach(\App\Models\BusinessUnit::pluck('name', 'id') as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">
            Tampilkan Arus Kas
        </x-filament::button>
    </form>

    @if($loaded)
        <div class="mt-6 space-y-4">
            <x-filament::section>
                <x-slot name="heading">Kas Masuk</x-slot>
                <table class="w-full text-sm">
                    <thead><tr class="border-b bg-green-50"><th class="px-3 py-2 text-left">Kategori</th><th class="px-3 py-2 text-right">Jumlah</th></tr></thead>
                    <tbody>
                        @forelse($incomeByCategory as $cat => $amt)
                            <tr class="border-b"><td class="px-3 py-2">{{ $cat }}</td><td class="px-3 py-2 text-right text-green-600">Rp {{ number_format($amt, 0, ',', '.') }}</td></tr>
                        @empty <tr><td colspan="2" class="px-3 py-4 text-center text-gray-500">Belum ada pendapatan</td></tr>@endforelse
                    </tbody>
                    <tfoot><tr class="border-t-2 font-bold bg-green-100"><td class="px-3 py-2">Total</td><td class="px-3 py-2 text-right">Rp {{ number_format($totalIncome, 0, ',', '.') }}</td></tr></tfoot>
                </table>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">Kas Keluar</x-slot>
                <table class="w-full text-sm">
                    <thead><tr class="border-b bg-red-50"><th class="px-3 py-2 text-left">Kategori</th><th class="px-3 py-2 text-right">Jumlah</th></tr></thead>
                    <tbody>
                        @forelse($expenseByCategory as $cat => $amt)
                            <tr class="border-b"><td class="px-3 py-2">{{ $cat }}</td><td class="px-3 py-2 text-right text-red-600">Rp {{ number_format($amt, 0, ',', '.') }}</td></tr>
                        @empty <tr><td colspan="2" class="px-3 py-4 text-center text-gray-500">Belum ada pengeluaran</td></tr>@endforelse
                    </tbody>
                    <tfoot><tr class="border-t-2 font-bold bg-red-100"><td class="px-3 py-2">Total</td><td class="px-3 py-2 text-right">Rp {{ number_format($totalExpense, 0, ',', '.') }}</td></tr></tfoot>
                </table>
            </x-filament::section>

            <div class="text-center py-4 bg-gray-50 rounded-lg">
                <div class="text-sm text-gray-500">Arus Kas Bersih</div>
                <div class="text-2xl font-bold {{ $netCashFlow >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    Rp {{ number_format(abs($netCashFlow), 0, ',', '.') }} ({{ $netCashFlow >= 0 ? 'POSITIF' : 'NEGATIF' }})
                </div>
            </div>
        </div>
    @endif
</x-filament-panels::page>
