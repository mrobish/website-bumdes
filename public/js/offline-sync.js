/**
 * BUMDes Offline Sync Manager
 * Mengelola sinkronisasi data offline ke server
 */

class OfflineSyncManager {
    constructor() {
        this.isOnline = navigator.onLine;
        this.syncInProgress = false;
        this.pendingCount = 0;
        
        // Listen for online/offline events
        window.addEventListener('online', () => this.handleOnline());
        window.addEventListener('offline', () => this.handleOffline());
        
        // Register service worker
        this.registerServiceWorker();
        
        // Initialize
        this.init();
    }

    // Register service worker
    async registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/sw.js');
                console.log('[Sync] Service Worker registered:', registration.scope);
                
                // Listen for sync messages
                navigator.serviceWorker.addEventListener('message', (event) => {
                    if (event.data.type === 'SYNC_COMPLETE') {
                        this.onSyncComplete(event.data.offlineId);
                    }
                });
            } catch (error) {
                console.error('[Sync] Service Worker registration failed:', error);
            }
        }
    }

    // Initialize
    async init() {
        await this.updateUI();
        this.checkPendingItems();
    }

    // Handle online event
    async handleOnline() {
        console.log('[Sync] Back online!');
        this.isOnline = true;
        this.updateOnlineStatus(true);
        
        // Start sync
        await this.syncNow();
    }

    // Handle offline event
    handleOffline() {
        console.log('[Sync] Gone offline');
        this.isOnline = false;
        this.updateOnlineStatus(false);
    }

    // Update online status UI
    updateOnlineStatus(isOnline) {
        const statusEl = document.getElementById('connection-status');
        if (statusEl) {
            if (isOnline) {
                statusEl.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800';
                statusEl.innerHTML = '<span class="w-2 h-2 bg-green-500 rounded-full mr-1 animate-pulse"></span>Online';
            } else {
                statusEl.className = 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800';
                statusEl.innerHTML = '<span class="w-2 h-2 bg-red-500 rounded-full mr-1"></span>Offline';
            }
        }
    }

    // Update pending count UI
    async updatePendingUI() {
        const count = await window.bumdesDB.getPendingCount();
        this.pendingCount = count;
        
        const pendingEl = document.getElementById('pending-count');
        if (pendingEl) {
            pendingEl.textContent = count;
            pendingEl.style.display = count > 0 ? 'inline' : 'none';
        }
        
        const syncBtn = document.getElementById('sync-btn');
        if (syncBtn) {
            syncBtn.disabled = count === 0 || !this.isOnline || this.syncInProgress;
        }
    }

    // Check pending items
    async checkPendingItems() {
        await this.updatePendingUI();
    }

    // Simpan transaksi offline
    async saveOfflineTransaction(data) {
        try {
            const id = await window.bumdesDB.saveTransaction(data);
            console.log('[Sync] Transaction saved offline:', id);
            
            await this.updatePendingUI();
            
            // Show notification
            this.showNotification('Data tersimpan offline', 'Data akan disinkronkan saat online.', 'warning');
            
            return id;
        } catch (error) {
            console.error('[Sync] Save offline failed:', error);
            throw error;
        }
    }

    // Sync now
    async syncNow() {
        if (this.syncInProgress || !this.isOnline) {
            console.log('[Sync] Sync skipped:', this.syncInProgress ? 'in progress' : 'offline');
            return;
        }

        this.syncInProgress = true;
        this.updateSyncButton(true);

        try {
            const pendingItems = await window.bumdesDB.getPendingItems();
            
            if (pendingItems.length === 0) {
                console.log('[Sync] No pending items');
                this.syncInProgress = false;
                this.updateSyncButton(false);
                return;
            }

            console.log(`[Sync] Syncing ${pendingItems.length} items...`);
            
            for (const item of pendingItems) {
                try {
                    const response = await fetch('/api/sync', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': item.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({
                            type: item.type,
                            data: item.data,
                            offline_id: item.id
                        })
                    });

                    if (response.ok) {
                        await window.bumdesDB.markAsSynced(item.id);
                        console.log('[Sync] Synced item:', item.id);
                    } else {
                        console.error('[Sync] Sync failed for item:', item.id, response.status);
                    }
                } catch (error) {
                    console.error('[Sync] Sync error for item:', item.id, error);
                }
            }

            // Clean synced items
            await window.bumdesDB.deleteSynced();
            
            // Update UI
            await this.updatePendingUI();
            
            // Show success notification
            this.showNotification('Sinkronisasi Berhasil', `${pendingItems.length} data berhasil disinkronkan.`, 'success');
            
        } catch (error) {
            console.error('[Sync] Sync error:', error);
            this.showNotification('Gagal Sinkronisasi', 'Terjadi kesalahan saat sinkronisasi.', 'error');
        } finally {
            this.syncInProgress = false;
            this.updateSyncButton(false);
        }
    }

    // Update sync button state
    updateSyncButton(isSyncing) {
        const btn = document.getElementById('sync-btn');
        if (btn) {
            if (isSyncing) {
                btn.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Menyinkronkan...';
                btn.disabled = true;
            } else {
                btn.innerHTML = '<svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Sinkronkan';
                btn.disabled = this.pendingCount === 0 || !this.isOnline;
            }
        }
    }

    // Show notification
    showNotification(title, message, type = 'info') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 max-w-sm p-4 rounded-lg shadow-lg transition-all transform translate-x-full`;
        
        const bgColor = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            warning: 'bg-yellow-500',
            info: 'bg-blue-500'
        }[type];
        
        notification.className += ` ${bgColor} text-white`;
        notification.innerHTML = `
            <div class="flex items-start">
                <div class="flex-1">
                    <p class="font-bold">${title}</p>
                    <p class="text-sm opacity-90">${message}</p>
                </div>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-4 opacity-75 hover:opacity-100">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Animate in
        setTimeout(() => notification.classList.remove('translate-x-full'), 100);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            notification.classList.add('translate-x-full');
            setTimeout(() => notification.remove(), 300);
        }, 5000);
    }

    // Callback when sync completes
    onSyncComplete(offlineId) {
        console.log('[Sync] Sync completed for:', offlineId);
        this.updatePendingUI();
    }
}

// Initialize sync manager
window.offlineSync = new OfflineSyncManager();
