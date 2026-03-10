const CACHE_NAME = 'portalje-static-v2';
const urlsToCache = [
  '/manifest.json',
  '/icons/icon-192.png',
  '/icons/icon-512.png'
];

// Instala o SW e armazena o cache
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
  );
});

// Intercepta requisições e responde do cache quando possível
self.addEventListener('fetch', event => {
  const request = event.request;

  // Never proxy non-GET requests (e.g. login POST, forms).
  if (request.method !== 'GET') {
    return;
  }

  const url = new URL(request.url);
  const accept = request.headers.get('accept') || '';
  const isNavigation = request.mode === 'navigate' || accept.includes('text/html');

  // Dynamic pages must always go to network to avoid stale CSRF tokens.
  if (
    url.origin === self.location.origin &&
    (
      isNavigation ||
      url.pathname.startsWith('/login') ||
      url.pathname.startsWith('/user') ||
      url.pathname.startsWith('/administrador')
    )
  ) {
    return;
  }

  event.respondWith(
    caches.match(request).then(response => response || fetch(request))
  );
});

// Atualiza cache ao ativar nova versão
self.addEventListener('activate', event => {
  const cacheWhitelist = [CACHE_NAME];
  event.waitUntil(
    caches.keys().then(keyList =>
      Promise.all(keyList.map(key => {
        if (!cacheWhitelist.includes(key)) {
          return caches.delete(key);
        }
      }))
    ).then(() => self.clients.claim())
  );
});
