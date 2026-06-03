<x-filament-panels::page>
    <form wire:submit.prevent="loadData">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium mb-1">Tahun</label>
                <select wire:model="year" class="w-full rounded-lg border-gray-300 shadow-sm">
                    @for($y = now()->year + 1; $y >= now()->year - 3; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Unit Usaha</label>
                <select wire:model="unitId" class="w-full rounded-lg border-gray-300 shadow-sm">
                    <option value="">Semua Unit</option>
                    @foreach(\App\Models\BusinessUnit::where('is_active', true)->orderBy('name')->get() as $unit)
                        <option value="{{ $unit->id }}">{{ $unit->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Jenis</label>
                <select wire:model="typeFilter" class="w-full rounded-lg border-gray-300 shadow-sm">
                    <option value="">Semua Jenis</option>
                    <option value="income">Pendapatan</option>
                    <option value="expense">Beban/Pengeluaran</option>
                </select>
            </div>
        </div>
        <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">
            Tampilkan Anggaran
        </x-filament::button>
    </form>

    @if($loaded)
        <div class="mt-6">
            {{-- Legend --}}
            <div class="flex gap-4 mb-4 text-sm">
                <span>🟢 &lt;80% (Under budget)</span>
                <span>🟡 80-100% (On track)</span>
                <span>🔴 &gt;100% (Over budget)</span>
            </div>

            @forelse($budgetData as $item)
                <x-filament::section class="mb-4">
                    <x-slot name="heading">
                        <div class="flex items-center gap-2">
                            <span class="font-bold">{{ $item['category']->name }}</span>
                            <span class="text-xs px-2 py-0.5 rounded-full {{ $item['category']->type === 'income' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ $item['category']->type === 'income' ? 'Pendapatan' : 'Pengeluaran' }}
                            </span>
                        </div>
                    </x-slot>

                    {{-- Monthly table --}}
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm border-collapse">
                            <thead>
                                <tr class="bg-gray-50 border-b">
                                    <th class="px-2 py-2 text-left font-semibold">Bulan</th>
                                    <th class="px-2 py-2 text-right font-semibold">Anggaran</th>
                                    <th class="px-2 py-2 text-right font-semibold">Realisasi</th>
                                    <th class="px-2 py-2 text-right font-semibold">Selisih</th>
                                    <th class="px-2 py-2 text-right font-semibold">Persentase</th>
                                    <th class="px-2 py-2 text-center font-semibold">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($item['monthly'] as $month => $data)
                                    <tr class="border-b hover:bg-gray-50 {{ $data['has_budget'] ? '' : 'text-gray-400' }}">
                                        <td class="px-2 py-2 font-medium">{{ $monthNames[$month] }}</td>
                                        <td class="px-2 py-2 text-right">
                                            @if($data['budget'] > 0)
                                                {{ formatCurrency($data['budget']) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-2 py-2 text-right {{ $data['actual'] > 0 ? 'font-medium' : '' }}">
                                            @if($data['actual'] > 0)
                                                {{ formatCurrency($data['actual']) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-2 py-2 text-right {{ $data['selisih'] > 0 ? 'text-red-600' : ($data['selisih'] < 0 ? 'text-green-600' : '') }}">
                                            @if($data['has_budget'])
                                                {{ $data['selisih'] >= 0 ? '+' : '' }}{{ formatCurrency($data['selisih']) }}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-2 py-2 text-right {{ getStatusClass($data['persentase']) }}">
                                            @if($data['has_budget'])
                                                {{ number_format($data['persentase'], 1) }}%
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td class="px-2 py-2 text-center">
                                            @if($data['has_budget'])
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ getStatusBadgeClass($data['persentase']) }}">
                                                    {{ $data['status_icon'] }} {{ number_format($data['persentase'], 0) }}%
                                                </span>
                                            @else
                                                <span class="text-gray-300">-</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-t-2 font-bold bg-gray-100">
                                    <td class="px-2 py-2">TOTAL TAHUNAN</td>
                                    <td class="px-2 py-2 text-right">{{ formatCurrency($item['total_budget']) }}</td>
                                    <td class="px-2 py-2 text-right">{{ formatCurrency($item['total_actual']) }}</td>
                                    <td class="px-2 py-2 text-right {{ $item['total_selisih'] > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ $item['total_selisih'] >= 0 ? '+' : '' }}{{ formatCurrency($item['total_selisih']) }}
                                    </td>
                                    <td class="px-2 py-2 text-right {{ getStatusClass($item['total_persentase']) }}">
                                        {{ $item['total_budget'] > 0 ? number_format($item['total_persentase'], 1) . '%' : '-' }}
                                    </td>
                                    <td class="px-2 py-2 text-center">
                                        @if($item['total_budget'] > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium {{ getStatusBadgeClass($item['total_persentase']) }}">
                                                {{ getStatusIcon($item['total_persentase']) }} {{ number_format($item['total_persentase'], 0) }}%
                                            </span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </x-filament::section>
            @empty
                <div class="text-center py-12 bg-white rounded-lg shadow">
                    <div class="text-gray-500">Tidak ada data anggaran untuk periode ini.</div>
                    <div class="text-sm text-gray-400 mt-1">Buat anggaran terlebih dahulu melalui menu Master Data > Anggaran.</div>
                </div>
            @endforelse

            {{-- Grand Total Summary --}}
            @if(count($budgetData) > 0)
                <x-filament::section class="mt-4">
                    <x-slot name="heading">Ringkasan Grand Total</x-slot>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div class="bg-blue-50 rounded-lg p-4 text-center">
                            <div class="text-sm text-blue-600 font-medium">Total Anggaran</div>
                            <div class="text-xl font-bold text-blue-800">{{ formatCurrency($grandTotal['budget']) }}</div>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-4 text-center">
                            <div class="text-sm text-purple-600 font-medium">Total Realisasi</div>
                            <div class="text-xl font-bold text-purple-800">{{ formatCurrency($grandTotal['actual']) }}</div>
                        </div>
                        <div class="rounded-lg p-4 text-center {{ $grandTotal['diff'] <= 0 ? 'bg-green-50' : 'bg-red-50' }}">
                            <div class="text-sm font-medium {{ $grandTotal['diff'] <= 0 ? 'text-green-600' : 'text-red-600' }}">Selisih</div>
                            <div class="text-xl font-bold {{ $grandTotal['diff'] <= 0 ? 'text-green-800' : 'text-red-800' }}">
                                {{ $grandTotal['diff'] >= 0 ? '+' : '' }}{{ formatCurrency($grandTotal['diff']) }}
                            </div>
                        </div>
                        <div class="rounded-lg p-4 text-center {{ getStatusBadgeClass($grandTotal['pct']) }}">
                            <div class="text-sm font-medium">Persentase</div>
                            <div class="text-xl font-bold">
                                {{ $grandTotal['budget'] > 0 ? number_format($grandTotal['pct'], 1) . '%' : '-' }}
                            </div>
                        </div>
                    </div>
                </x-filament::section>
            @endif
        </div>
    @endif
</x-filament-panels::page>
