<x-filament-panels::page>
    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500">Total Baris</div>
            <div class="text-2xl font-bold">{{ number_format($totalLines) }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500">Errors</div>
            <div class="text-2xl font-bold text-red-500">{{ $errorCount }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500">Warnings</div>
            <div class="text-2xl font-bold text-yellow-500">{{ $warningCount }}</div>
        </div>
        <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
            <div class="text-sm text-gray-500">File Size</div>
            <div class="text-2xl font-bold">{{ $selectedFile ? \Illuminate\Support\Facades\File::size($selectedFile) / 1024 . ' KB' : '-' }}</div>
        </div>
    </div>

    {{-- Form --}}
    <form wire:submit="refreshLog">
        {{ $this->form }}
    </form>

    {{-- Actions --}}
    <div class="flex gap-2 mb-4">
        <x-filament::button wire:click="refreshLog" size="sm" icon="heroicon-o-arrow-path">
            Refresh
        </x-filament::button>
        <x-filament::button wire:click="clearLog" size="sm" color="danger" icon="heroicon-o-trash" wire:confirm="Yakin ingin membersihkan log ini?">
            Bersihkan Log
        </x-filament::button>
    </div>

    {{-- Log Content --}}
    <div class="bg-gray-900 rounded-xl p-4 overflow-auto max-h-[600px] font-mono text-sm text-green-400 border border-gray-700">
        @if($logContent)
            <pre class="whitespace-pre-wrap break-all">{{ $logContent }}</pre>
        @else
            <div class="text-gray-500 text-center py-8">
                Tidak ada log yang tersedia
            </div>
        @endif
    </div>
</x-filament-panels::page>
