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
                @php $pdfUrl = route('pdf.buku-besar', ['account_code' => $accountCode, 'date_from' => $dateFrom, 'date_to' => $dateTo]); @endphp
                <a href="{{ $pdfUrl }}" target="_blank" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-lg hover:from-red-700 hover:to-red-800 text-sm font-medium shadow-sm">
                    📄 Download PDF
                </a>
            @endif
        </div>
    </form>

    @if($loaded && $accountCode)
        <div class="mt-6">
            <x-filament::section>
                <x-slot name="heading">Buku Besar — {{ \App\Models\Account::where('code', $accountCode)->first()->name ?? '' }}</x-slot>
                <div class="overflow-x-auto">
                    Tabel Buku Besar...
                </div>
            </x-filament::section>
        </div>
    @endif
</x-filament-panels::page>
