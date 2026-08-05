// Service Worker para ZapBy PWA
const CACHE_VERSION = 'zapby-v6-video-range-fix';
const CACHE_NAME = `zapby-cache-${CACHE_VERSION}`;
const urlsToCache = [
  '/',
  '/manifest.json',
  '/zapby-192.png',
  '/zapby-512.png',
  '/zapby-192-maskable.png',
  '/zapby-512-maskable.png'
];

// Instalação do Service Worker
self.addEventListener('install', event => {
  console.log('[SW] Instalando Service Worker...');
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('[SW] Cache aberto');
        return cache.addAll(urlsToCache);
      })
      .then(() => self.skipWaiting())
  );
});

// Ativação do Service Worker
self.addEventListener('activate', event => {
  console.log('[SW] Ativando Service Worker...');
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            console.log('[SW] Deletando cache antigo:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    }).then(() => self.clients.claim())
  );
});

// Estratégia Network First com fallback para cache
self.addEventListener('fetch', event => {
  // Apenas interceptar requisições GET
  if (event.request.method !== 'GET') {
    return;
  }

  const requestUrl = new URL(event.request.url);
  const isMediaRequest = event.request.destination === 'video'
    || event.request.destination === 'audio'
    || requestUrl.pathname.startsWith('/videos/');
  const isRangeRequest = event.request.headers.has('range');

  // O navegador precisa controlar diretamente as requisições parciais de
  // áudio e vídeo. Interceptá-las pode substituir um trecho 206 por uma
  // resposta de fallback text/plain e interromper a reprodução.
  if (isMediaRequest || isRangeRequest) {
    return;
  }

  event.respondWith(
    fetch(event.request)
      .then(response => {
        // Se a resposta é válida, clonar e adicionar ao cache
        if (response && response.status === 200) {
          const responseToCache = response.clone();
          caches.open(CACHE_NAME).then(cache => {
            cache.put(event.request, responseToCache);
          });
        }
        return response;
      })
      .catch(() => {
        // Se falhar, tentar buscar do cache
        return caches.match(event.request)
          .then(cachedResponse => {
            if (cachedResponse) {
              return cachedResponse;
            }
            // Retornar uma resposta básica se não houver cache
            return new Response('Offline - Conteúdo não disponível', {
              status: 503,
              statusText: 'Service Unavailable',
              headers: new Headers({
                'Content-Type': 'text/plain'
              })
            });
          });
      })
  );
});
