<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Backup Section --}}
        <x-filament::section>
            <x-slot name="heading">
                <x-heroicon-o-arrow-down-tray class="w-5 h-5 mr-2 inline" />
                Buat Backup
            </x-slot>

            <x-slot name="description">
                Download database backup dalam format .sql
            </x-slot>

            <div class="space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Klik tombol di bawah untuk membuat backup database <strong>bumdes</strong> dan mengunduhnya sebagai file .sql.
                </p>

                <form wire:submit="downloadBackup">
                    <x-filament::button
                        tag="button"
                        type="submit"
                        icon="heroicon-o-arrow-down-tray"
                        color="success"
                        size="lg"
                        class="w-full"
                        wire:loading.attr="disabled"
                    >
                        <span wire:loading.remove wire:target="downloadBackup">Download Backup</span>
                        <span wire:loading wire:target="downloadBackup" class="flex items-center gap-2">
                            <x-heroicon-o-arrow-path class="w-5 h-5 animate-spin" />
                            Sedang membuat backup...
                        </span>
                    </x-filament::button>
                </form>

                <div class="p-3 bg-warning-50 dark:bg-warning-900/20 rounded-lg border border-warning-200 dark:border-warning-800">
                    <p class="text-sm text-warning-700 dark:text-warning-300">
                        <x-heroicon-o-information-circle class="w-4 h-4 inline mr-1" />
                        Backup akan mengunduh file langsung ke komputer Anda. Pastikan Anda menyimpannya di tempat yang aman.
                    </p>
                </div>
            </div>
        </x-filament::section>

        {{-- Restore Section --}}
        <x-filament::section>
            <x-slot name="heading">
                <x-heroicon-o-arrow-up-tray class="w-5 h-5 mr-2 inline" />
                Restore Database
            </x-slot>

            <x-slot name="description">
                Upload file .sql untuk merestore database
            </x-slot>

            <div class="space-y-4">
                <p class="text-sm text-gray-600 dark:text-gray-400">
                    Pilih file backup .sql untuk merestore database. <strong class="text-danger-600">Perhatian:</strong> Proses ini akan menimpa data yang ada.
                </p>

                {{ $this->form }}

                <div class="flex gap-3">
                    <form wire:submit="restoreBackup" class="flex-1">
                        <x-filament::button
                            tag="button"
                            type="submit"
                            icon="heroicon-o-arrow-up-tray"
                            color="warning"
                            size="lg"
                            class="w-full"
                            wire:loading.attr="disabled"
                            x-data
                            x-on:click="if(!confirm('Apakah Anda yakin ingin merestore database? Semua data saat ini akan ditimpa!')) return false;"
                        >
                            <span wire:loading.remove wire:target="restoreBackup">Upload & Restore</span>
                            <span wire:loading wire:target="restoreBackup" class="flex items-center gap-2">
                                <x-heroicon-o-arrow-path class="w-5 h-5 animate-spin" />
                                Sedang merestore...
                            </span>
                        </x-filament::button>
                    </form>
                </div>

                <div class="p-3 bg-danger-50 dark:bg-danger-900/20 rounded-lg border border-danger-200 dark:border-danger-800">
                    <p class="text-sm text-danger-700 dark:text-danger-300">
                        <x-heroicon-o-exclamation-triangle class="w-4 h-4 inline mr-1" />
                        Proses restore akan menghapus semua data saat ini dan menggantinya dengan data dari file backup. Pastikan Anda sudah membuat backup terbaru sebelum melakukan restore!
                    </p>
                </div>
            </div>
        </x-filament::section>
    </div>

    {{-- Backup History --}}
    <x-filament::section class="mt-6">
        <x-slot name="heading">
            <x-heroicon-o-clock class="w-5 h-5 mr-2 inline" />
            Riwayat Backup
        </x-slot>

        <x-slot name="description">
            Daftar backup yang telah dibuat sebelumnya
        </x-slot>

        @if(count($backups) > 0)
            <div class="overflow-x-auto">
                <table class="w-full border-collapse">
                    <thead>
                        <tr>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">No</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">Nama File</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">Tanggal</th>
                            <th class="text-left py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">Ukuran</th>
                            <th class="text-center py-3 px-4 text-sm font-semibold text-gray-600 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($backups as $index => $backup)
                            <tr wire:key="backup-{{ $index }}" class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                                <td class="py-3 px-4 text-sm border-b border-gray-100 dark:border-gray-800">{{ $index + 1 }}</td>
                                <td class="py-3 px-4 text-sm font-medium border-b border-gray-100 dark:border-gray-800">
                                    <x-heroicon-o-document-text class="w-4 h-4 inline mr-1 text-gray-400" />
                                    {{ $backup['name'] }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400 border-b border-gray-100 dark:border-gray-800">
                                    {{ $backup['date'] }}
                                </td>
                                <td class="py-3 px-4 text-sm text-gray-600 dark:text-gray-400 border-b border-gray-100 dark:border-gray-800">
                                    <x-heroicon-o-server-stack class="w-4 h-4 inline mr-1 text-gray-400" />
                                    {{ $backup['size'] }}
                                </td>
                                <td class="py-3 px-4 text-center border-b border-gray-100 dark:border-gray-800">
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ route('filament.admin.pages.backup-restore.download', ['file' => basename($backup['path'])]) }}"
                                           class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-primary-700 bg-primary-100 rounded-lg hover:bg-primary-200 dark:bg-primary-900 dark:text-primary-300 dark:hover:bg-primary-800 transition-colors">
                                            <x-heroicon-o-arrow-down-tray class="w-3.5 h-3.5 mr-1" />
                                            Download
                                        </a>
                                        <button wire:click="deleteBackup('{{ addslashes($backup['path']) }}')"
                                                wire:confirm="Apakah Anda yakin ingin menghapus backup ini?"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium text-danger-700 bg-danger-100 rounded-lg hover:bg-danger-200 dark:bg-danger-900 dark:text-danger-300 dark:hover:bg-danger-800 transition-colors">
                                            <x-heroicon-o-trash class="w-3.5 h-3.5 mr-1" />
                                            Hapus
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-8">
                <x-heroicon-o-inbox class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" />
                <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada file backup.</p>
                <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">Klik tombol "Download Backup" untuk membuat backup pertama.</p>
            </div>
        @endif
    </x-filament::section>
</x-filament-panels::page>
