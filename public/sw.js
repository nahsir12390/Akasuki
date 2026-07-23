const CACHE_VERSION = 'akatsuki-devs-v1';
const STATIC_CACHE = `${CACHE_VERSION}-static`;
const OFFLINE_URL = '/offline.html';

const PRECACHE_URLS = [
    '/',
    OFFLINE_URL,
    '/manifest.webmanifest',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
    '/icons/maskable-512.png'
];

self.addEventListener('install', (event) => {
    event.waitUntil(
        caches.open(STATIC_CACHE)
            .then((cache) => cache.addAll(PRECACHE_URLS))
            .then(() => self.skipWaiting())
    );
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys()
            .then((keys) => Promise.all(
                keys
                    .filter((key) => key.startsWith('akatsuki-devs-') && key !== STATIC_CACHE)
                    .map((key) => caches.delete(key))
            ))
            .then(() => self.clients.claim())
    );
});

self.addEventListener('fetch', (event) => {
    const { request } = event;

    if (request.method !== 'GET') {
        return;
    }

    const url = new URL(request.url);

    if (url.origin !== self.location.origin) {
        return;
    }

    if (request.mode === 'navigate') {
        event.respondWith(
            fetch(request)
                .then((response) => {
                    const copy = response.clone();
                    caches.open(STATIC_CACHE).then((cache) => cache.put(request, copy));
                    return response;
                })
                .catch(() => caches.match(request).then((cached) => cached || caches.match(OFFLINE_URL)))
        );
        return;
    }

    if (url.pathname.startsWith('/build/') || url.pathname.startsWith('/icons/') || url.pathname === '/manifest.webmanifest') {
        event.respondWith(
            caches.match(request).then((cached) => cached || fetch(request).then((response) => {
                const copy = response.clone();
                caches.open(STATIC_CACHE).then((cache) => cache.put(request, copy));
                return response;
            }))
        );
    }
});

self.addEventListener('push', (event) => {
    const fallbackPayload = {
        title: 'Akatsuki Devs',
        body: 'You have a new village notification.',
        icon: '/icons/icon-192.png',
        badge: '/icons/icon-192.png',
        data: { url: '/notifications' },
    };

    const payload = event.data ? event.data.json() : fallbackPayload;
    const title = payload.title || fallbackPayload.title;
    const options = {
        body: payload.body || fallbackPayload.body,
        icon: payload.icon || fallbackPayload.icon,
        badge: payload.badge || fallbackPayload.badge,
        image: payload.image,
        tag: payload.tag,
        renotify: payload.renotify,
        requireInteraction: payload.requireInteraction,
        vibrate: payload.vibrate || [120, 60, 120],
        data: payload.data || fallbackPayload.data,
        actions: payload.actions || [],
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    const targetUrl = new URL(event.notification.data?.url || '/notifications', self.location.origin).href;

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            const appClient = clientList.find((client) => client.url.startsWith(self.location.origin));

            if (appClient) {
                appClient.focus();
                return appClient.navigate(targetUrl);
            }

            return clients.openWindow(targetUrl);
        })
    );
});
