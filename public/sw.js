/**
 * Nexora EMS ERP — Service Worker
 * Estratégias:
 *  - Cache First  → assets estáticos (CSS, JS, fonts, imagens)
 *  - Network First → requisições de dados / páginas dinâmicas
 *  - Stale While Revalidate → dashboards / páginas secundárias
 */

const CACHE_VERSION = 'nexora-v1';
const STATIC_CACHE  = `${CACHE_VERSION}-static`;
const DYNAMIC_CACHE = `${CACHE_VERSION}-dynamic`;
const OFFLINE_URL   = '/offline';

// Assets para pré-cache (Cache First)
const STATIC_ASSETS = [
    '/',
    '/offline',
    '/manifest.json',
    '/favicon.ico',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
];

// ──────────────────────────────────────────────
// INSTALL — pré-cacheamento dos assets estáticos
// ──────────────────────────────────────────────
self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => {
            console.log('[SW] Pré-cacheando assets estáticos');
            return cache.addAll(STATIC_ASSETS);
        }).then(() => self.skipWaiting())
    );
});

// ──────────────────────────────────────────────
// ACTIVATE — limpar caches antigos
// ──────────────────────────────────────────────
self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys
                    .filter((key) => key.startsWith('nexora-') && key !== STATIC_CACHE && key !== DYNAMIC_CACHE)
                    .map((key) => {
                        console.log('[SW] Removendo cache antigo:', key);
                        return caches.delete(key);
                    })
            )
        ).then(() => self.clients.claim())
    );
});

// ──────────────────────────────────────────────
// FETCH — estratégias por tipo de recurso
// ──────────────────────────────────────────────
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Ignorar requests não-GET e de outras origens
    if (request.method !== 'GET') return;
    if (url.origin !== location.origin && !isExternalFont(url)) return;

    // API / dados dinâmicos → Network First
    if (url.pathname.startsWith('/api/') || url.pathname.startsWith('/livewire/')) {
        event.respondWith(networkFirst(request));
        return;
    }

    // Assets estáticos (build Vite, fontes, imagens) → Cache First
    if (isStaticAsset(url)) {
        event.respondWith(cacheFirst(request));
        return;
    }

    // Navegação → Network First com fallback offline
    if (request.mode === 'navigate') {
        event.respondWith(navigationHandler(request));
        return;
    }

    // Demais → Stale While Revalidate
    event.respondWith(staleWhileRevalidate(request));
});

// ──────────────────────────────────────────────
// Push Notifications
// ──────────────────────────────────────────────
self.addEventListener('push', (event) => {
    if (!event.data) return;

    let data = {};
    try { data = event.data.json(); } catch (e) { data = { title: 'Nexora ERP', body: event.data.text() }; }

    const options = {
        body: data.body || '',
        icon: '/icons/icon-192x192.png',
        badge: '/icons/icon-96x96.png',
        vibrate: [200, 100, 200],
        data: { url: data.url || '/' },
        actions: data.actions || [],
        tag: data.tag || 'nexora-notification',
        renotify: true,
    };

    event.waitUntil(
        self.registration.showNotification(data.title || 'Nexora ERP', options)
    );
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
    const url = event.notification.data?.url || '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if (client.url === url && 'focus' in client) return client.focus();
            }
            if (clients.openWindow) return clients.openWindow(url);
        })
    );
});

// ──────────────────────────────────────────────
// Background Sync
// ──────────────────────────────────────────────
self.addEventListener('sync', (event) => {
    if (event.tag === 'nexora-sync-pending') {
        event.waitUntil(syncPendingRequests());
    }
});

async function syncPendingRequests() {
    try {
        const db = await openIDB();
        const pending = await getAllPending(db);
        for (const item of pending) {
            try {
                await fetch(item.url, {
                    method: item.method,
                    headers: item.headers,
                    body: item.body,
                });
                await deletePending(db, item.id);
            } catch (e) {
                console.warn('[SW] Sync falhou para:', item.url);
            }
        }
    } catch (e) {
        console.error('[SW] Background sync error:', e);
    }
}

// ──────────────────────────────────────────────
// Helpers de estratégia
// ──────────────────────────────────────────────
async function cacheFirst(request) {
    const cached = await caches.match(request);
    if (cached) return cached;
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(STATIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch (e) {
        return caches.match(OFFLINE_URL);
    }
}

async function networkFirst(request) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch (e) {
        const cached = await caches.match(request);
        return cached || new Response(JSON.stringify({ error: 'offline', message: 'Sem conexão com a internet.' }), {
            status: 503,
            headers: { 'Content-Type': 'application/json' },
        });
    }
}

async function staleWhileRevalidate(request) {
    const cache = await caches.open(DYNAMIC_CACHE);
    const cached = await cache.match(request);

    const fetchPromise = fetch(request).then((response) => {
        if (response.ok) cache.put(request, response.clone());
        return response;
    }).catch(() => null);

    return cached || fetchPromise;
}

async function navigationHandler(request) {
    try {
        const response = await fetch(request);
        if (response.ok) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, response.clone());
        }
        return response;
    } catch (e) {
        const cached = await caches.match(request);
        if (cached) return cached;
        return caches.match(OFFLINE_URL);
    }
}

// ──────────────────────────────────────────────
// Helpers de detecção de tipo de arquivo
// ──────────────────────────────────────────────
function isStaticAsset(url) {
    return (
        url.pathname.startsWith('/build/') ||
        url.pathname.startsWith('/css/') ||
        url.pathname.startsWith('/js/') ||
        url.pathname.startsWith('/fonts/') ||
        url.pathname.startsWith('/images/') ||
        url.pathname.startsWith('/icons/') ||
        /\.(woff2?|ttf|eot|otf|svg|png|jpe?g|gif|webp|ico|css|js)$/.test(url.pathname)
    );
}

function isExternalFont(url) {
    return url.hostname.includes('fonts.googleapis.com') || url.hostname.includes('fonts.gstatic.com');
}

// ──────────────────────────────────────────────
// IndexedDB helpers (para offline sync)
// ──────────────────────────────────────────────
function openIDB() {
    return new Promise((resolve, reject) => {
        const req = indexedDB.open('nexora-offline', 1);
        req.onupgradeneeded = (e) => {
            e.target.result.createObjectStore('pending', { keyPath: 'id', autoIncrement: true });
        };
        req.onsuccess = (e) => resolve(e.target.result);
        req.onerror   = (e) => reject(e.target.error);
    });
}

function getAllPending(db) {
    return new Promise((resolve, reject) => {
        const tx  = db.transaction('pending', 'readonly');
        const req = tx.objectStore('pending').getAll();
        req.onsuccess = (e) => resolve(e.target.result);
        req.onerror   = (e) => reject(e.target.error);
    });
}

function deletePending(db, id) {
    return new Promise((resolve, reject) => {
        const tx  = db.transaction('pending', 'readwrite');
        const req = tx.objectStore('pending').delete(id);
        req.onsuccess = () => resolve();
        req.onerror   = (e) => reject(e.target.error);
    });
}

