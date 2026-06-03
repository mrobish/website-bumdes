/**
 * BUMDes Offline Database Manager
 * Menggunakan IndexedDB untuk menyimpan data saat offline
 */

class BUMDesOfflineDB {
    constructor() {
        this.dbName = 'BUMDesOfflineDB';
        this.dbVersion = 1;
        this.db = null;
    }

    // Buka database
    async open() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.dbName, this.dbVersion);
            
            request.onerror = () => reject(request.error);
            request.onsuccess = () => {
                this.db = request.result;
                resolve(this.db);
            };
            
            request.onupgradeneeded = (event) => {
                const db = event.target.result;
                
                // Object store untuk antrian offline
                if (!db.objectStoreNames.contains('offlineQueue')) {
                    const store = db.createObjectStore('offlineQueue', {
                        keyPath: 'id',
                        autoIncrement: true
                    });
                    store.createIndex('type', 'type', { unique: false });
                    store.createIndex('status', 'status', { unique: false });
                    store.createIndex('createdAt', 'createdAt', { unique: false });
                }
                
                // Object store untuk transaksi offline
                if (!db.objectStoreNames.contains('transactions')) {
                    const store = db.createObjectStore('transactions', {
                        keyPath: 'id',
                        autoIncrement: true
                    });
                    store.createIndex('offlineId', 'offlineId', { unique: true });
                    store.createIndex('status', 'status', { unique: false });
                }
            };
        });
    }

    // Simpan transaksi offline
    async saveTransaction(transactionData) {
        await this.open();
        
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(['offlineQueue', 'transactions'], 'readwrite');
            
            // Simpan ke antrian
            const queueStore = tx.objectStore('offlineQueue');
            const queueItem = {
                type: 'transaction',
                data: transactionData,
                status: 'pending',
                createdAt: new Date().toISOString(),
                csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || ''
            };
            const queueRequest = queueStore.add(queueItem);
            
            // Simpan ke tabel transaksi
            const transStore = tx.objectStore('transactions');
            const transItem = {
                ...transactionData,
                offlineId: queueRequest.result,
                status: 'offline',
                createdAt: new Date().toISOString()
            };
            transStore.add(transItem);
            
            tx.oncomplete = () => {
                console.log('[OfflineDB] Transaction saved:', queueRequest.result);
                resolve(queueRequest.result);
            };
            tx.onerror = () => reject(tx.error);
        });
    }

    // Dapatkan semua transaksi offline
    async getOfflineTransactions() {
        await this.open();
        
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction('transactions', 'readonly');
            const store = tx.objectStore('transactions');
            const request = store.getAll();
            
            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    // Dapatkan item antrian yang belum di-sync
    async getPendingItems() {
        await this.open();
        
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction('offlineQueue', 'readonly');
            const store = tx.objectStore('offlineQueue');
            const index = store.index('status');
            const request = index.getAll('pending');
            
            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
        });
    }

    // Tandai item sudah di-sync
    async markAsSynced(offlineId) {
        await this.open();
        
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(['offlineQueue', 'transactions'], 'readwrite');
            
            // Update queue
            const queueStore = tx.objectStore('offlineQueue');
            const queueRequest = queueStore.get(offlineId);
            queueRequest.onsuccess = () => {
                const item = queueRequest.result;
                if (item) {
                    item.status = 'synced';
                    queueStore.put(item);
                }
            };
            
            // Update transaction
            const transStore = tx.objectStore('transactions');
            const index = transStore.index('offlineId');
            const transRequest = index.get(offlineId);
            transRequest.onsuccess = () => {
                const trans = transRequest.result;
                if (trans) {
                    trans.status = 'synced';
                    transStore.put(trans);
                }
            };
            
            tx.oncomplete = () => {
                console.log('[OfflineDB] Marked as synced:', offlineId);
                resolve(true);
            };
            tx.onerror = () => reject(tx.error);
        });
    }

    // Hapus item yang sudah di-sync
    async deleteSynced() {
        await this.open();
        
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction('offlineQueue', 'readwrite');
            const store = tx.objectStore('offlineQueue');
            const index = store.index('status');
            const request = index.openCursor('synced');
            
            request.onsuccess = (event) => {
                const cursor = event.target.result;
                if (cursor) {
                    cursor.delete();
                    cursor.continue();
                }
            };
            
            tx.oncomplete = () => {
                console.log('[OfflineDB] Cleaned synced items');
                resolve(true);
            };
            tx.onerror = () => reject(tx.error);
        });
    }

    // Bersihkan semua data offline
    async clearAll() {
        await this.open();
        
        return new Promise((resolve, reject) => {
            const tx = this.db.transaction(['offlineQueue', 'transactions'], 'readwrite');
            tx.objectStore('offlineQueue').clear();
            tx.objectStore('transactions').clear();
            
            tx.oncomplete = () => {
                console.log('[OfflineDB] All data cleared');
                resolve(true);
            };
            tx.onerror = () => reject(tx.error);
        });
    }

    // Hitung jumlah item pending
    async getPendingCount() {
        const items = await this.getPendingItems();
        return items.length;
    }
}

// Export instance global
window.bumdesDB = new BUMDesOfflineDB();
