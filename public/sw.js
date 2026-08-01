// DineFlow AI Service Worker for PWA Mobile App
const CACHE_NAME = 'dineflow-ai-v1';
const ASSETS_TO_CACHE = [
  '/pos',
  '/kitchen',
  '/dashboard',
  '/frontend/admin/admin.css',
  '/frontend/admin/utils.js',
  '/images/dineflow_logo.jpg'
];

self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME).then(cache => cache.addAll(ASSETS_TO_CACHE))
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
  if (event.request.method !== 'GET') return;
  event.respondWith(
    fetch(event.request).catch(() => caches.match(event.request))
  );
});
