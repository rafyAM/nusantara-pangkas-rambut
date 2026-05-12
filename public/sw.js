const CACHE_NAME = 'nusantara-pwa-cache-v1';
const OFFLINE_URL = '/';

const urlsToCache = [
    OFFLINE_URL,
    '/build/assets/app.css', // Asumsi nama dari vite, walau dinamis ini fallback dasar
    '/build/assets/app.js',
    '/images/hero-bg.png',
    '/favicon.ico',
];

// Event: Install (Simpan Cache Awal)
self.addEventListener('install', function(event) {
    event.waitUntil(
        caches.open(CACHE_NAME)
        .then(function(cache) {
            return Promise.allSettled(
                urlsToCache.map(url => cache.add(url).catch(err => console.log('Gagal cache:', url)))
            );
        })
        .then(() => self.skipWaiting())
    );
});

// Event: Activate (Bersihkan Cache Lama)
self.addEventListener('activate', function(event) {
    event.waitUntil(
        caches.keys().then(function(cacheNames) {
            return Promise.all(
                cacheNames.map(function(cacheName) {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
    self.clients.claim();
});

// Event: Fetch (Ambil dari Jaringan dulu, kalau gagal ambil dari cache)
self.addEventListener('fetch', function(event) {
    if (event.request.method !== 'GET') return;

    event.respondWith(
        fetch(event.request).catch(function() {
            return caches.match(event.request).then(function(response) {
                if (response) {
                    return response;
                }
                // Fallback terakhir: jika buka halaman web dan offline, kembalikan ke Beranda Offline Cache
                if (event.request.mode === 'navigate') {
                    return caches.match(OFFLINE_URL);
                }
            });
        })
    );
});

// EVENT PUSH NOTIFICATION (LOGIKA LAMA TETAP DIPERTAHANKAN)
self.addEventListener('push', function (e) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    if (e.data) {
        var msg = e.data.json();
        e.waitUntil(self.registration.showNotification(msg.title, {
            body: msg.body,
            icon: msg.icon || '/favicon.ico',
            actions: msg.actions || [],
            data: msg.data
        }));
    }
});

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    if (event.notification.data && event.notification.data.url) {
        event.waitUntil(
            clients.openWindow(event.notification.data.url)
        );
    }
});
