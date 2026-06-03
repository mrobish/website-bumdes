const CACHE_NAME = 'bumdes-v1';
const STATIC_CACHE = 'bumdes-static-v1';
const DYNAMIC_CACHE = 'bumdes-dynamic-v1';

// Files to cache for offline use
const STATIC_FILES = [
    '/',
    '/manifest.json',
    '/css/filament/filament/app.css',
    '/css/filament/forms/forms.css',
    '/css/filament/support/support.css',
    '/js/filament/filament/app.js',
    '/js/filament/support/support.js',
    '/offline',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png'
];

// Install event - cache static files
self.addEventListener('install', event => {
    console.log('[SW] Installing...');
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then(cache => {
                console.log('[SW] Caching static files');
                return cache.addAll(STATIC_FILES);
            })
            .then(() => self.skipWaiting())
    );
});

// Activate event - clean old caches
self.addEventListener('activate', event => {
    console.log('[SW] Activating...');
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames
                    .filter(name => name !== STATIC_CACHE && name !== DYNAMIC_CACHE)
                    .map(name => caches.delete(name))
            );
        }).then(() => self.clients.claim())
    );
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', event => {
    const { request } = event;
    const url = new URL(request.url);

    // Skip non-GET requests
    if (request.method !== 'GET') return;

    // Skip admin panel pages (always need fresh data)
    if (url.pathname.startsWith('/admin')) {
        event.respondWith(
            fetch(request)
                .catch(() => caches.match('/offline'))
        );
        return;
    }

    // Network first, cache fallback for dynamic content
    event.respondWith(
        fetch(request)
            .then(response => {
                // Clone the response
                const responseClone = response.clone();
                caches.open(DYNAMIC_CACHE)
                    .then(cache => cache.put(request, responseClone));
                return response;
            })
            .catch(() => caches.match(request))
    );
});

// Background Sync - sync offline data when online
self.addEventListener('sync', event => {
    console.log('[SW] Sync event:', event.tag);
    if (event.tag === 'sync-offline-data') {
        event.waitUntil(syncOfflineData());
    }
});

// Sync offline data from IndexedDB to server
async function syncOfflineData() {
    try {
        // Get all clients
        const clients = await self.clients.matchAll();
        
        // Open IndexedDB
        const db = await openDB();
        const tx = db.transaction('offlineQueue', 'readwrite');
        const store = tx.objectStore('offlineQueue');
        
        // Get all pending items
        const items = await getAllFromStore(store);
        
        for (const item of items) {
            try {
                const response = await fetch('/api/sync', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': item.csrfToken,
                    },
                    body: JSON.stringify({
                        type: item.type,
                        data: item.data,
                        offline_id: item.id
                    })
                });

                if (response.ok) {
                    // Remove from queue
                    store.delete(item.id);
                    console.log('[SW] Synced item:', item.id);
                    
                    // Notify client
                    clients.forEach(client => {
                        client.postMessage({
                            type: 'SYNC_COMPLETE',
                            offlineId: item.id
                        });
                    });
                }
            } catch (error) {
                console.error('[SW] Sync failed for item:', item.id, error);
            }
        }
        
        return true;
    } catch (error) {
        console.error('[SW] Sync error:', error);
        return false;
    }
}

// IndexedDB helper
function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('BUMDesOfflineDB', 1);
        
        request.onerror = () => reject(request.error);
        request.onsuccess = () => resolve(request.result);
        
        request.onupgradeneeded = (event) => {
            const db = event.target.result;
            if (!db.objectStoreNames.contains('offlineQueue')) {
                const store = db.createObjectStore('offlineQueue', {
                    keyPath: 'id',
                    autoIncrement: true
                });
                store.createIndex('type', 'type', { unique: false });
                store.createIndex('status', 'status', { unique: false });
            }
        };
    });
}

function getAllFromStore(store) {
    return new Promise((resolve, reject) => {
        const request = store.getAll();
        request.onsuccess = () => resolve(request.result);
        request.onerror = () => reject(request.error);
    });
}

// Listen for messages from client
self.addEventListener('message', event => {
    if (event.data && event.data.type === 'SKIP_WAITING') {
        self.skipWaiting();
    }
});
