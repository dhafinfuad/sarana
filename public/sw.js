/**
 * Service Worker — Sarana PWA
 * Strategi: Network-First untuk konten dinamis, Cache-First untuk aset statis.
 *
 * Cache Names — gunakan versi untuk memudahkan invalidasi saat deploy baru.
 */
const CACHE_VERSION    = 'v1';
const STATIC_CACHE     = `sarana-static-${CACHE_VERSION}`;
const DYNAMIC_CACHE    = `sarana-dynamic-${CACHE_VERSION}`;
const OFFLINE_PAGE_URL = '/offline';

/**
 * Aset statis yang di-precache saat Service Worker pertama kali install.
 * Sesuaikan path build assets dengan output Vite.
 */
const STATIC_ASSETS = [
    '/',
    '/offline',
    '/manifest.json',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png',
];

// ---------------------------------------------------------------------------
// INSTALL — Precache aset statis
// ---------------------------------------------------------------------------
self.addEventListener('install', (event) => {
    console.log('[SW] Installing Service Worker...');

    event.waitUntil(
        caches.open(STATIC_CACHE).then((cache) => {
            console.log('[SW] Precaching static assets...');
            // Gunakan individual try-catch agar satu aset gagal tidak membatalkan semua
            return Promise.allSettled(
                STATIC_ASSETS.map((url) =>
                    cache.add(url).catch((err) => {
                        console.warn(`[SW] Failed to cache: ${url}`, err);
                    })
                )
            );
        })
    );

    // Paksa SW baru aktif segera tanpa menunggu tab lama tertutup
    self.skipWaiting();
});

// ---------------------------------------------------------------------------
// ACTIVATE — Bersihkan cache lama
// ---------------------------------------------------------------------------
self.addEventListener('activate', (event) => {
    console.log('[SW] Activating Service Worker...');

    event.waitUntil(
        caches.keys().then((cacheNames) => {
            return Promise.all(
                cacheNames
                    .filter((name) => {
                        // Hapus cache yang bukan milik versi saat ini
                        return (
                            name.startsWith('sarana-') &&
                            name !== STATIC_CACHE &&
                            name !== DYNAMIC_CACHE
                        );
                    })
                    .map((name) => {
                        console.log(`[SW] Deleting old cache: ${name}`);
                        return caches.delete(name);
                    })
            );
        })
    );

    // Ambil alih semua tab yang sedang terbuka segera
    self.clients.claim();
});

// ---------------------------------------------------------------------------
// FETCH — Strategi caching per jenis request
// ---------------------------------------------------------------------------
self.addEventListener('fetch', (event) => {
    const { request } = event;
    const url = new URL(request.url);

    // Hanya tangani request GET dari origin yang sama
    if (request.method !== 'GET' || url.origin !== self.location.origin) {
        return;
    }

    // Abaikan request HMR Vite saat development
    if (url.pathname.startsWith('/@') || url.pathname.startsWith('/node_modules')) {
        return;
    }

    // --- Strategi 1: Cache-First untuk aset statis (JS, CSS, gambar, font) ---
    if (isStaticAsset(url.pathname)) {
        event.respondWith(cacheFirstStrategy(request));
        return;
    }

    // --- Strategi 2: Network-First untuk halaman HTML (konten dinamis) ---
    event.respondWith(networkFirstStrategy(request));
});

// ---------------------------------------------------------------------------
// Fungsi Strategi
// ---------------------------------------------------------------------------

/**
 * Cache-First: Coba ambil dari cache, fallback ke network, lalu simpan ke cache.
 * Ideal untuk aset build Vite (build/assets/*) yang sudah di-hash.
 */
async function cacheFirstStrategy(request) {
    const cachedResponse = await caches.match(request);
    if (cachedResponse) {
        return cachedResponse;
    }

    try {
        const networkResponse = await fetch(request);
        if (networkResponse && networkResponse.status === 200) {
            const cache = await caches.open(STATIC_CACHE);
            cache.put(request, networkResponse.clone());
        }
        return networkResponse;
    } catch {
        console.warn('[SW] Cache-First: network failed for', request.url);
        return new Response('Asset tidak tersedia offline.', { status: 503 });
    }
}

/**
 * Network-First: Coba ambil dari network, simpan ke dynamic cache.
 * Jika network gagal (offline), tampilkan dari cache atau halaman offline.
 */
async function networkFirstStrategy(request) {
    try {
        const networkResponse = await fetch(request);

        if (networkResponse && networkResponse.status === 200) {
            const cache = await caches.open(DYNAMIC_CACHE);
            cache.put(request, networkResponse.clone());
        }

        return networkResponse;
    } catch {
        // Network gagal — coba dari dynamic cache
        const cachedResponse = await caches.match(request);
        if (cachedResponse) {
            console.log('[SW] Serving from dynamic cache:', request.url);
            return cachedResponse;
        }

        // Fallback ke halaman offline untuk navigasi HTML
        if (request.headers.get('accept')?.includes('text/html')) {
            const offlinePage = await caches.match(OFFLINE_PAGE_URL);
            if (offlinePage) {
                return offlinePage;
            }
        }

        return new Response('Tidak ada koneksi internet.', {
            status: 503,
            headers: { 'Content-Type': 'text/plain; charset=utf-8' },
        });
    }
}

// ---------------------------------------------------------------------------
// Push Notification — Notifikasi Web untuk perubahan status booking (PRD §4.A)
// ---------------------------------------------------------------------------
self.addEventListener('push', (event) => {
    if (!event.data) return;

    let payload;
    try {
        payload = event.data.json();
    } catch {
        payload = { title: 'Sarana', body: event.data.text() };
    }

    const options = {
        body:    payload.body    ?? 'Ada pembaruan pada pengajuan Anda.',
        icon:    payload.icon    ?? '/icons/icon-192x192.png',
        badge:   payload.badge   ?? '/icons/icon-96x96.png',
        tag:     payload.tag     ?? 'sarana-notif',
        data:    payload.data    ?? { url: '/' },
        actions: payload.actions ?? [],
        vibrate: [200, 100, 200],
        requireInteraction: false,
    };

    event.waitUntil(
        self.registration.showNotification(payload.title ?? 'Sarana', options)
    );
});

/**
 * Klik notifikasi: buka/fokus tab aplikasi dan navigasi ke URL yang relevan.
 */
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const targetUrl = event.notification.data?.url ?? '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            // Cek apakah ada tab yang sudah terbuka
            for (const client of clientList) {
                if (new URL(client.url).origin === self.location.origin) {
                    client.focus();
                    client.navigate(targetUrl);
                    return;
                }
            }
            // Buka tab baru jika belum ada
            return clients.openWindow(targetUrl);
        })
    );
});

// ---------------------------------------------------------------------------
// Helper
// ---------------------------------------------------------------------------

/**
 * Periksa apakah pathname merupakan aset statis.
 * Aset build Vite selalu berada di /build/assets/ dan sudah di-hash.
 */
function isStaticAsset(pathname) {
    return (
        pathname.startsWith('/build/') ||
        pathname.startsWith('/icons/') ||
        pathname.startsWith('/images/') ||
        pathname.endsWith('.png')  ||
        pathname.endsWith('.jpg')  ||
        pathname.endsWith('.jpeg') ||
        pathname.endsWith('.webp') ||
        pathname.endsWith('.svg')  ||
        pathname.endsWith('.ico')  ||
        pathname.endsWith('.woff') ||
        pathname.endsWith('.woff2')
    );
}
