
// ========== تنظیمات کش ==========
const CACHE_NAME = 'keyvan-v1';

// ========== لیست فایل های قابل کش ==========
const urlsToCache = [
  '/',
  '/index.php',
  '/ai.php',
  '/style.css',
  '/dashboard.php',
  '/courses.php',
  '/contact-us.php',
  '/about-us.php',
  '/sign-in.php',
  '/sign-up.php'
];

// ========== نصب Service Worker ==========
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => cache.addAll(urlsToCache))
  );
});

// ========== پاسخ به درخواست‌ها ==========
self.addEventListener('fetch', event => {
  event.respondWith(
    caches.match(event.request)
      .then(response => response || fetch(event.request))
  );
});

// ========== فعال‌سازی و پاک‌سازی کش قدیمی ==========
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cache => {
          if (cache !== CACHE_NAME) {
            return caches.delete(cache);
          }
        })
      );
    })
  );
});