<x-filament-panels::page>
    <div class="max-w-4xl mx-auto">
        <div class="text-center mb-8">
            <div class="w-24 h-24 mx-auto mb-4 bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">BUMDes Management System</h1>
            <p class="text-lg text-gray-600 dark:text-gray-400 mt-2">v2.0 - Sistem Keuangan BUMDes Berbasis SAK ETAP</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-400 to-blue-500 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Developer</h3>
                        <p class="text-gray-600 dark:text-gray-400">Pengembang Aplikasi</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="text-gray-500">Nama:</span>
                        <span class="font-medium text-gray-900 dark:text-white">mrobis</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-gray-500">GitHub:</span>
                        <a href="https://github.com/mrobis" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">github.com/mrobis</a>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-gray-500">Repo:</span>
                        <a href="https://github.com/mrobis/website-bumdes" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">website-bumdes</a>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700">
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-400 to-pink-500 rounded-full flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Lisensi</h3>
                        <p class="text-gray-600 dark:text-gray-400">MIT License</p>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex items-center gap-3">
                        <span class="text-gray-500">Tipe:</span>
                        <span class="text-gray-700 dark:text-gray-300">MIT License</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-gray-500">Copyright:</span>
                        <span class="text-gray-700 dark:text-gray-300">{{ date('Y') }} mrobis</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <span class="text-gray-500">Status:</span>
                        <span class="text-gray-700 dark:text-gray-300">Bebas digunakan dan dimodifikasi</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md p-6 border border-gray-200 dark:border-gray-700 mb-8">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tech Stack</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <span class="text-red-500">Laravel 10</span>
                </div>
                <div class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <span class="text-yellow-500">Filament 3</span>
                </div>
                <div class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <span class="text-blue-500">PHP 8.1</span>
                </div>
                <div class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <span class="text-green-500">MariaDB</span>
                </div>
                <div class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <span class="text-cyan-500">Tailwind CSS</span>
                </div>
                <div class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <span class="text-purple-500">DomPDF</span>
                </div>
                <div class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <span class="text-orange-500">PhpSpreadsheet</span>
                </div>
                <div class="flex items-center gap-2 p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <span class="text-indigo-500">Livewire</span>
                </div>
            </div>
        </div>

        <div class="text-center py-6 border-t border-gray-200 dark:border-gray-700">
            <p class="text-gray-600 dark:text-gray-400">
                {{ date('Y') }} mrobis - 
                <a href="https://github.com/mrobis/website-bumdes" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400">GitHub Repository</a>
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-500 mt-2">
                Dibuat dengan ❤️ untuk Kemajuan Desa
            </p>
        </div>
    </div>
</x-filament-panels::page>
