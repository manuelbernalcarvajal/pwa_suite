(function () {
    'use strict';

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            const swUrl = OC.generateUrl('/apps/pwa_suite/sw.js');

            navigator.serviceWorker.getRegistrations().then(registrations => {
                for (let reg of registrations) {
                    if (!reg.active || !reg.active.scriptURL.includes('pwa_suite')) {
                        reg.unregister();
                    }
                }

                navigator.serviceWorker.register(swUrl, { scope: '/' })
                    .then(reg => {
                        console.log('[PWA Suite] Service Worker maestro activo en:', reg.scope);
                    })
                    .catch(err => {
                        console.error('[PWA Suite] Error registrando SW:', err);
                    });
            });
        });
    }
})();
