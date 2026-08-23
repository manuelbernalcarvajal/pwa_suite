(function () {
    const manifestUrl = OC.generateUrl('/apps/pwa_suite/manifest.json');

    // 1. Sobreescribir cualquier manifest previo que Nextcloud haya inyectado
    let manifestLink = document.querySelector('link[rel="manifest"]');
    if (manifestLink) {
        manifestLink.setAttribute('href', manifestUrl);
    } else {
        manifestLink = document.createElement('link');
        manifestLink.rel = 'manifest';
        manifestLink.href = manifestUrl;
        document.head.appendChild(manifestLink);
    }

    // 2. Registrar el Service Worker propio con alcance global
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            const swUrl = OC.generateUrl('/apps/pwa_suite/sw.js');
            navigator.serviceWorker.register(swUrl, { scope: '/' })
                .then(reg => {
                    console.log('[PWA Suite] Service Worker registrado con éxito en scope:', reg.scope);
                })
                .catch(err => {
                    console.error('[PWA Suite] Error al registrar Service Worker:', err);
                });
        });
    }
})();
