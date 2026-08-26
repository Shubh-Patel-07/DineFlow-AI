// DineFlow AI Customer Menu Service Worker v1.0.0
const CACHE_NAME = 'dineflow-menu-static-v1';
const STATIC_ASSETS = [
  '/frontend/customer/menu.html',
  '/frontend/customer/menu.css',
  '/frontend/customer/menu.js',
  '/frontend/customer/data.js',
  '/frontend/customer/cart-storage.js',
  '/frontend/customer/order-tracker-api.js'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => {
      return cache.addAll(STATIC_ASSETS).catch(err => {
        console.log('PWA Service Worker partial cache install:', err);
      });
    })
  );
  self.skipWaiting();
});

self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(keys => {
      return Promise.all(
        keys.filter(key => key !== CACHE_NAME).map(key => caches.delete(key))
      );
    })
  );
  self.clients.claim();
});

self.addEventListener('fetch', event => {
  const url = new URL(event.request.url);

  // NEVER cache API requests or non-GET requests (live orders, payments, tracking)
  if (url.pathname.startsWith('/api/') || event.request.method !== 'GET') {
    return; // Let standard network request proceed untouched
  }

  // Network-First for static assets with cache fallback
  event.respondWith(
    fetch(event.request)
      .then(response => {
        if (response.status === 200 && event.request.method === 'GET') {
          const responseClone = response.clone();
          caches.open(CACHE_NAME).then(cache => {
            cache.put(event.request, responseClone);
          });
        }
        return response;
      })
      .catch(() => caches.match(event.request))
  );
});