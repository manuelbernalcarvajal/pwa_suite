(function () {
    'use strict';
    if (!('serviceWorker' in navigator)) return;

    window.addEventListener('load', function () {
        const swUrl = OC.generateUrl('/apps/pwa_suite/sw.js');

        navigator.serviceWorker.getRegistrations().then(function (registrations) {
            let pwaSuiteExists = false;
            const alienRegistrations = [];

            for (const reg of registrations) {
                const currentUrl = reg.active?.scriptURL || reg.installing?.scriptURL || reg.waiting?.scriptURL || '';
                
                if (currentUrl.includes('pwa_suite')) {
                    pwaSuiteExists = true;
                } else {
                    // Detectado Service Worker de otra app: marcar para eliminar
                    alienRegistrations.push(reg.unregister());
                }
            }

            // 1. Si hay Service Workers ajenos, los eliminamos en segundo plano
            Promise.all(alienRegistrations).then(function () {
                // 2. Si PWA Suite aún no estaba registrado o controlando la instancia, se registra
                if (!pwaSuiteExists) {
                    navigator.serviceWorker.register(swUrl, { scope: '/' })
                        .then(function (reg) {
                            console.log('[PWA Suite] Service Worker maestro registrado con éxito en scope:', reg.scope);
                        })
                        .catch(function (err) {
                            console.warn('[PWA Suite] Error registrando SW maestro:', err);
                        });
                }
            });
        });
    });
})();
