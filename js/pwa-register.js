(function () {
    'use strict';

    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            const swUrl = OC.generateUrl('/apps/pwa_suite/sw.js');

            navigator.serviceWorker.getRegistrations().then(function (registrations) {
                // 1. Desregistrar cualquier Service Worker que no sea el de pwa_suite
                const unregisterPromises = [];
                for (let reg of registrations) {
                    if (!reg.active || !reg.active.scriptURL.includes('pwa_suite')) {
                        unregisterPromises.push(reg.unregister());
                    }
                }

                // 2. Tras limpiar los antiguos, registrar el Service Worker maestro
                Promise.all(unregisterPromises).then(function () {
                    navigator.serviceWorker.register(swUrl, { scope: '/' })
                        .then(function (reg) {
                            console.log('[PWA Suite] Service Worker maestro activo en scope:', reg.scope);
                        })
                        .catch(function (err) {
                            console.error('[PWA Suite] Error registrando SW:', err);
                        });
                });
            });
        });
    }
})();
