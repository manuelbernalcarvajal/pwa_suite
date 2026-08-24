(function () {
    'use strict';

    const MANIFEST_URL = OC.generateUrl('/apps/pwa_suite/manifest.json');
    const SW_URL = OC.generateUrl('/apps/pwa_suite/sw.js');

    // 1. Asegurar que nuestro manifest sea el único activo en el <head>
    function ensureManifest() {
        let manifest = document.querySelector('link#pwa-suite-manifest');

        document.querySelectorAll('link[rel="manifest"]').forEach(el => {
            if (el.id !== 'pwa-suite-manifest') {
                el.remove();
            }
        });

        if (!manifest) {
            manifest = document.createElement('link');
            manifest.id = 'pwa-suite-manifest';
            manifest.rel = 'manifest';
            manifest.href = MANIFEST_URL;
            document.head.appendChild(manifest);
        } else if (manifest.getAttribute('href') !== MANIFEST_URL) {
            manifest.setAttribute('href', MANIFEST_URL);
        }
    }

    ensureManifest();

    // 2. Registrar el Service Worker con alcance global
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.getRegistrations().then(registrations => {
                for (let reg of registrations) {
                    if (!reg.active || !reg.active.scriptURL.includes('pwa_suite')) {
                        reg.unregister();
                    }
                }

                navigator.serviceWorker.register(SW_URL, { scope: '/' })
                    .then(reg => {
                        console.log('[PWA Suite] Service Worker registrado en scope:', reg.scope);
                    })
                    .catch(err => {
                        console.error('[PWA Suite] Error al registrar Service Worker:', err);
                    });
            });
        });
    }
})();
