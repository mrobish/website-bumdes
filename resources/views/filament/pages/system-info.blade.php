<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Server Info --}}
        <x-filament::section>
            <x-slot name="heading">
                <x-heroicon-o-cpu-chip class="w-5 h-5 mr-2 inline" />
                Server
            </x-slot>
            <div class="space-y-2">
                @foreach($serverInfo as $key => $value)
                    <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $key }}</span>
                        <span class="text-sm font-medium">{{ $value }}</span>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        {{-- Database Info --}}
        <x-filament::section>
            <x-slot name="heading">
                <x-heroicon-o-server-stack class="w-5 h-5 mr-2 inline" />
                Database
            </x-slot>
            <div class="space-y-2">
                @foreach($dbInfo as $key => $value)
                    <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $key }}</span>
                        <span class="text-sm font-medium">{{ $value }}</span>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        {{-- Disk Info --}}
        <x-filament::section>
            <x-slot name="heading">
                <x-heroicon-o-server-stack class="w-5 h-5 mr-2 inline" />
                Disk Space
            </x-slot>
            <div class="space-y-2">
                @foreach($diskInfo as $key => $value)
                    <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $key }}</span>
                        <span class="text-sm font-medium">{{ $value }}</span>
                    </div>
                @endforeach
            </div>
        </x-filament::section>

        {{-- App Info --}}
        <x-filament::section>
            <x-slot name="heading">
                <x-heroicon-o-chart-bar class="w-5 h-5 mr-2 inline" />
                Statistik Aplikasi
            </x-slot>
            <div class="space-y-2">
                @foreach($appInfo as $key => $value)
                    <div class="flex justify-between py-1 border-b border-gray-100 dark:border-gray-700">
                        <span class="text-sm text-gray-600 dark:text-gray-400">{{ $key }}</span>
                        <span class="text-sm font-medium">{{ $value }}</span>
                    </div>
                @endforeach
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
