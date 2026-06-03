<x-filament-panels::page>
    {{-- Filter Form --}}
    <form wire:submit.prevent="loadData">
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center gap-2">
                    <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                    <span>Filter Audit Log</span>
                </div>
            </x-slot>

            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                {{-- Date From --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Dari Tanggal</label>
                    <input type="date" wire:model="dateFrom"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>

                {{-- Date To --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Sampai Tanggal</label>
                    <input type="date" wire:model="dateTo"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>

                {{-- User --}}
                <div>
                    <label class="block text-sm font-medium mb-1">User</label>
                    <select wire:model="filterUserId"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">Semua User</option>
                        @foreach($this->getUsers() as $userId => $userName)
                            <option value="{{ $userId }}">{{ $userName }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Action --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Aksi</label>
                    <select wire:model="filterAction"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                        <option value="">Semua Aksi</option>
                        <option value="create">Buat</option>
                        <option value="update">Ubah</option>
                        <option value="delete">Hapus</option>
                        <option value="void">Void</option>
                        <option value="approve">Setuju</option>
                        <option value="login">Login</option>
                    </select>
                </div>

                {{-- Table --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Tipe Entity</label>
                    <input type="text" wire:model="filterTable" placeholder="Cari tabel..."
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>

                {{-- Search --}}
                <div>
                    <label class="block text-sm font-medium mb-1">Pencarian</label>
                    <input type="text" wire:model="search" placeholder="Cari..."
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500">
                </div>
            </div>

            <div class="flex gap-2 mt-4">
                <x-filament::button type="submit" icon="heroicon-o-magnifying-glass">
                    Cari
                </x-filament::button>
                <x-filament::button type="button" wire:click="resetFilters" color="gray" icon="heroicon-o-x-mark">
                    Reset
                </x-filament::button>
            </div>
        </x-filament::section>
    </form>

    {{-- Results --}}
    <div class="mt-6">
        <x-filament::section>
            <x-slot name="heading">
                <div class="flex items-center justify-between w-full">
                    <span>Hasil: {{ number_format($totalRecords) }} catatan</span>
                </div>
            </x-slot>

            @php $logs = $this->getLogs(); @endphp

            @if($loaded && count($logs) > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b bg-gray-50">
                                <th class="px-3 py-2 text-left">Waktu</th>
                                <th class="px-3 py-2 text-left">User</th>
                                <th class="px-3 py-2 text-left">Aksi</th>
                                <th class="px-3 py-2 text-left">Tipe</th>
                                <th class="px-3 py-2 text-left">Entity</th>
                                <th class="px-3 py-2 text-left">IP</th>
                                <th class="px-3 py-2 text-center">Detail</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                @php $logId = $log->id ?? $log['id']; @endphp
                                {{-- Main Row --}}
                                <tr class="border-b hover:bg-gray-50 {{ $expandedLogId === $logId ? 'bg-blue-50' : '' }}">
                                    <td class="px-3 py-2 whitespace-nowrap text-gray-600">
                                        {{ \Carbon\Carbon::parse($log->created_at ?? $log['created_at'])->format('d/m/Y H:i:s') }}
                                    </td>
                                    <td class="px-3 py-2">
                                        @if($log->user ?? $log['user'] ?? null)
                                            <span class="font-medium">{{ $log->user->name ?? $log['user']['name'] ?? '-' }}</span>
                                        @else
                                            <span class="text-gray-400 italic">Sistem</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-2">
                                        <x-filament::badge :color="$this->getActionColor($log->action ?? $log['action'])">
                                            {{ $this->getActionLabel($log->action ?? $log['action']) }}
                                        </x-filament::badge>
                                    </td>
                                    <td class="px-3 py-2 text-gray-600">{{ $log->table_name ?? $log['table_name'] ?? '-' }}</td>
                                    <td class="px-3 py-2 font-mono text-gray-600">
                                        {{ $log->record_id ?? $log['record_id'] ?? '-' }}
                                    </td>
                                    <td class="px-3 py-2 text-gray-500 text-xs">{{ $log->ip_address ?? $log['ip_address'] ?? '-' }}</td>
                                    <td class="px-3 py-2 text-center">
                                        @if(($log->old_values ?? $log['old_values'] ?? null) || ($log->new_values ?? $log['new_values'] ?? null))
                                            <button wire:click="toggleExpand({{ $logId }})"
                                                    class="text-primary-600 hover:text-primary-800">
                                                @if($expandedLogId === $logId)
                                                    <x-heroicon-s-chevron-up class="w-5 h-5 inline" />
                                                @else
                                                    <x-heroicon-s-chevron-down class="w-5 h-5 inline" />
                                                @endif
                                            </button>
                                        @else
                                            <span class="text-gray-300">-</span>
                                        @endif
                                    </td>
                                </tr>

                                {{-- Expanded Detail Row --}}
                                @if($expandedLogId === $logId)
                                    @php
                                        $oldValues = $log->old_values ?? $log['old_values'] ?? null;
                                        $newValues = $log->new_values ?? $log['new_values'] ?? null;
                                    @endphp
                                    <tr class="bg-blue-50/50">
                                        <td colspan="7" class="px-6 py-4">
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                                {{-- Old Values --}}
                                                @if($oldValues)
                                                    <div>
                                                        <h4 class="text-sm font-semibold text-gray-700 mb-2">
                                                            Nilai Lama (Old Values)
                                                        </h4>
                                                        <div class="bg-white rounded-lg border p-3 text-xs font-mono">
                                                            @if(is_array($oldValues))
                                                                @foreach($oldValues as $key => $value)
                                                                    <div class="flex justify-between py-1 border-b last:border-0">
                                                                        <span class="text-gray-600">{{ $key }}</span>
                                                                        <span class="text-gray-900 font-medium">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <pre class="whitespace-pre-wrap">{{ $oldValues }}</pre>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif

                                                {{-- New Values --}}
                                                @if($newValues)
                                                    <div>
                                                        <h4 class="text-sm font-semibold text-gray-700 mb-2">
                                                            Nilai Baru (New Values)
                                                        </h4>
                                                        <div class="bg-white rounded-lg border p-3 text-xs font-mono">
                                                            @if(is_array($newValues))
                                                                @foreach($newValues as $key => $value)
                                                                    <div class="flex justify-between py-1 border-b last:border-0">
                                                                        <span class="text-gray-600">{{ $key }}</span>
                                                                        <span class="text-gray-900 font-medium">{{ is_array($value) ? json_encode($value) : $value }}</span>
                                                                    </div>
                                                                @endforeach
                                                            @else
                                                                <pre class="whitespace-pre-wrap">{{ $newValues }}</pre>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @php $totalPages = $this->getTotalPages(); @endphp
                @if($totalPages > 1)
                    <div class="flex items-center justify-between mt-4">
                        <span class="text-sm text-gray-500">
                            Halaman {{ $currentPage }} dari {{ $totalPages }}
                        </span>
                        <div class="flex gap-1">
                            @if($currentPage > 1)
                                <x-filament::button size="sm" wire:click="goToPage(1)" color="gray">
                                    &laquo;
                                </x-filament::button>
                                <x-filament::button size="sm" wire:click="goToPage({{ $currentPage - 1 }})" color="gray">
                                    &lsaquo;
                                </x-filament::button>
                            @endif

                            @for($p = max(1, $currentPage - 2); $p <= min($totalPages, $currentPage + 2); $p++)
                                <x-filament::button size="sm"
                                    wire:click="goToPage({{ $p }})"
                                    :color="$p === $currentPage ? 'primary' : 'gray'">
                                    {{ $p }}
                                </x-filament::button>
                            @endfor

                            @if($currentPage < $totalPages)
                                <x-filament::button size="sm" wire:click="goToPage({{ $currentPage + 1 }})" color="gray">
                                    &rsaquo;
                                </x-filament::button>
                                <x-filament::button size="sm" wire:click="goToPage({{ $totalPages }})" color="gray">
                                    &raquo;
                                </x-filament::button>
                            @endif
                        </div>
                    </div>
                @endif
            @elseif($loaded)
                <div class="text-center py-12 text-gray-500">
                    <x-heroicon-o-magnifying-glass class="w-12 h-12 mx-auto mb-3 text-gray-300" />
                    <p class="text-lg font-medium">Tidak ada data audit log ditemukan</p>
                    <p class="text-sm">Coba ubah filter pencarian Anda</p>
                </div>
            @endif
        </x-filament::section>
    </div>
</x-filament-panels::page>
