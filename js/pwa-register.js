(function () {
    const manifestUrl = OC.generateUrl('/apps/pwa_suite/manifest.json');

    // 1. Eliminar cualquier manifest duplicado o inyectado por el núcleo de Nextcloud
    document.querySelectorAll('link[rel="manifest"]').forEach(el => el.remove());

    // 2. Inyectar exclusivamente nuestro manifest
    const manifestLink = document.createElement('link');
    manifestLink.rel = 'manifest';
    manifestLink.href = manifestUrl;
    document.head.appendChild(manifestLink);

    // 3. Registrar el Service Worker global
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            const swUrl = OC.generateUrl('/apps/pwa_suite/sw.js');
            navigator.serviceWorker.register(swUrl, { scope: '/' })
                .then(reg => {
                    console.log('[PWA Suite] Service Worker activo en:', reg.scope);
                })
                .catch(err => {
                    console.error('[PWA Suite] Error al registrar SW:', err);
                });
        });
    }
})();
