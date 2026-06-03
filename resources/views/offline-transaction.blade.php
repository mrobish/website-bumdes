<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Input Transaksi - BUMDes Digital</title>
    
    <!-- PWA Meta Tags -->
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#f59e0b">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
    <!-- Offline Banner -->
    <div id="offline-banner" class="bg-yellow-500 text-white text-center py-2 text-sm font-medium" style="display: none;">
        <span class="inline-flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636a9 9 0 010 12.728m0 0l-2.829-2.829m2.829 2.829L21 21M15.536 8.464a5 5 0 010 7.072m0 0l-2.829-2.829m-4.242 2.829a4.978 4.978 0 01-1.414-2.83m-1.414 5.658a9 9 0 01-2.167-9.238m7.824 2.167a1 1 0 111.414 1.414m-1.414-1.414L3 3"></path>
            </svg>
            Mode Offline - Data akan disinkronkan saat online
        </span>
    </div>

    <!-- Header -->
    <header class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <a href="/admin" class="flex items-center text-gray-600 hover:text-gray-900">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Kembali
                </a>
                
                <div class="flex items-center space-x-4">
                    <span id="connection-status" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                        <span class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></span>
                        Online
                    </span>
                    
                    <button id="sync-btn" onclick="syncNow()" 
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Sinkronkan
                    </button>
                    <span id="pending-count" class="inline-flex items-center px-2 py-1 text-xs font-bold text-white bg-red-500 rounded-full" style="display: none;">0</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Form -->
    <main class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Input Transaksi Baru</h2>
            
            <form id="transaction-form" onsubmit="return handleSubmit(event)">
                @csrf
                
                <!-- Unit Usaha -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit Usaha *</label>
                    <select name="business_unit_id" id="business_unit_id" required
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Unit</option>
                        <option value="1">Induk (Pusat)</option>
                        <option value="2">Ketahanan Pangan</option>
                        <option value="3">Pariwisata</option>
                        <option value="4">Pengelolaan Sampah</option>
                        <option value="5">Jaringan Internet</option>
                    </select>
                </div>

                <!-- Tanggal -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal *</label>
                    <input type="date" name="transaction_date" id="transaction_date" required
                           value="{{ date('Y-m-d') }}"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Tipe -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tipe Transaksi *</label>
                    <select name="type" id="type" required onchange="updateAccountOptions()"
                            class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Pilih Tipe</option>
                        <option value="pemasukan">Pemasukan</option>
                        <option value="pengeluaran">Pengeluaran</option>
                    </select>
                </div>

                <!-- Jumlah -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Jumlah (Rp) *</label>
                    <input type="number" name="amount" id="amount" required min="1"
                           placeholder="0"
                           class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Keterangan -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Keterangan *</label>
                    <textarea name="description" id="description" required rows="3"
                              placeholder="Keterangan transaksi..."
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <!-- Catatan -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="notes" id="notes" rows="2"
                              placeholder="Catatan tambahan (opsional)..."
                              class="w-full border-gray-300 rounded-lg shadow-sm focus:ring-blue-500 focus:border-blue-500"></textarea>
                </div>

                <!-- Submit Buttons -->
                <div class="flex space-x-4">
                    <button type="submit" id="submit-btn"
                            class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-4 rounded-lg transition">
                        Simpan Transaksi
                    </button>
                </div>
            </form>

            <!-- Offline Queue -->
            <div id="offline-queue" class="mt-8 hidden">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Antrian Offline</h3>
                <div id="queue-list" class="space-y-3">
                    <!-- Queue items will be added here -->
                </div>
            </div>
        </div>
    </main>

    <!-- Scripts -->
    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        let isOnline = navigator.onLine;
        let db = null;

        // Initialize IndexedDB
        function openDB() {
            return new Promise((resolve, reject) => {
                const request = indexedDB.open('BUMDesOfflineDB', 1);
                request.onerror = () => reject(request.error);
                request.onsuccess = () => {
                    db = request.result;
                    resolve(db);
                };
                request.onupgradeneeded = (event) => {
                    const db = event.target.result;
                    if (!db.objectStoreNames.contains('offlineQueue')) {
                        const store = db.createObjectStore('offlineQueue', {
                            keyPath: 'id',
                            autoIncrement: true
                        });
                        store.createIndex('type', 'type');
                        store.createIndex('status', 'status');
                    }
                };
            });
        }

        // Update online status UI
        function updateOnlineStatus() {
            const statusEl = document.getElementById('connection-status');
            const bannerEl = document.getElementById('offline-banner');
            
            if (isOnline) {
                statusEl.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
                statusEl.innerHTML = '<span class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></span>Online';
                bannerEl.style.display = 'none';
            } else {
                statusEl.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800';
                statusEl.innerHTML = '<span class="w-2 h-2 bg-red-500 rounded-full mr-1"></span>Offline';
                bannerEl.style.display = 'block';
            }
        }

        // Listen for online/offline events
        window.addEventListener('online', () => {
            isOnline = true;
            updateOnlineStatus();
            syncNow();
        });
        window.addEventListener('offline', () => {
            isOnline = false;
            updateOnlineStatus();
        });

        // Handle form submit
        async function handleSubmit(event) {
            event.preventDefault();
            
            const formData = {
                business_unit_id: document.getElementById('business_unit_id').value,
                transaction_date: document.getElementById('transaction_date').value,
                type: document.getElementById('type').value,
                amount: document.getElementById('amount').value,
                description: document.getElementById('description').value,
                notes: document.getElementById('notes').value
            };

            if (isOnline) {
                // Try to save online first
                try {
                    const response = await fetch('/admin/financial-transactions', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify(formData)
                    });

                    if (response.ok) {
                        alert('Transaksi berhasil disimpan!');
                        document.getElementById('transaction-form').reset();
                        return false;
                    }
                } catch (error) {
                    console.log('Online save failed, saving offline...');
                }
            }

            // Save offline
            await saveOffline(formData);
            return false;
        }

        // Save to offline queue
        async function saveOffline(data) {
            await openDB();
            
            const tx = db.transaction('offlineQueue', 'readwrite');
            const store = tx.objectStore('offlineQueue');
            
            const item = {
                type: 'transaction',
                data: data,
                status: 'pending',
                createdAt: new Date().toISOString(),
                csrfToken: csrfToken
            };

            store.add(item);

            tx.oncomplete = () => {
                alert('Data tersimpan offline!\nAkan disinkronkan saat online.');
                document.getElementById('transaction-form').reset();
                updatePendingCount();
                showOfflineQueue();
            };

            tx.onerror = () => {
                alert('Gagal menyimpan data!');
            };
        }

        // Update pending count
        async function updatePendingCount() {
            await openDB();
            const tx = db.transaction('offlineQueue', 'readonly');
            const store = tx.objectStore('offlineQueue');
            const request = store.count();
            
            request.onsuccess = () => {
                const count = request.result;
                const countEl = document.getElementById('pending-count');
                const syncBtn = document.getElementById('sync-btn');
                
                if (count > 0) {
                    countEl.textContent = count;
                    countEl.style.display = 'inline';
                    syncBtn.disabled = !isOnline;
                } else {
                    countEl.style.display = 'none';
                    syncBtn.disabled = true;
                }
            };
        }

        // Show offline queue
        async function showOfflineQueue() {
            await openDB();
            const tx = db.transaction('offlineQueue', 'readonly');
            const store = tx.objectStore('offlineQueue');
            const request = store.getAll();
            
            request.onsuccess = () => {
                const items = request.result;
                const queueEl = document.getElementById('offline-queue');
                const listEl = document.getElementById('queue-list');
                
                if (items.length > 0) {
                    queueEl.classList.remove('hidden');
                    listEl.innerHTML = items.map(item => `
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-medium text-gray-900">${item.data.description || 'Tanpa keterangan'}</p>
                                    <p class="text-sm text-gray-600">Rp ${Number(item.data.amount).toLocaleString('id-ID')}</p>
                                    <p class="text-xs text-gray-500">${new Date(item.createdAt).toLocaleString('id-ID')}</p>
                                </div>
                                <span class="px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-800 rounded">Offline</span>
                            </div>
                        </div>
                    `).join('');
                } else {
                    queueEl.classList.add('hidden');
                }
            };
        }

        // Sync now
        async function syncNow() {
            if (!isOnline) {
                alert('Tidak ada koneksi internet!');
                return;
            }

            await openDB();
            const tx = db.transaction('offlineQueue', 'readonly');
            const store = tx.objectStore('offlineQueue');
            const request = store.getAll();
            
            request.onsuccess = async () => {
                const items = request.result;
                
                if (items.length === 0) {
                    alert('Tidak ada data yang perlu disinkronkan.');
                    return;
                }

                let synced = 0;
                for (const item of items) {
                    try {
                        const response = await fetch('/api/sync', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({
                                type: item.type,
                                data: item.data,
                                offline_id: item.id
                            })
                        });

                        if (response.ok) {
                            // Delete from queue
                            const deleteTx = db.transaction('offlineQueue', 'readwrite');
                            const deleteStore = deleteTx.objectStore('offlineQueue');
                            deleteStore.delete(item.id);
                            synced++;
                        }
                    } catch (error) {
                        console.error('Sync failed:', error);
                    }
                }

                alert(`${synced} data berhasil disinkronkan!`);
                updatePendingCount();
                showOfflineQueue();
            };
        }

        // Initialize
        document.addEventListener('DOMContentLoaded', () => {
            updateOnlineStatus();
            updatePendingCount();
            showOfflineQueue();
        });
    </script>
</body>
</html>
