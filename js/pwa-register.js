(function() {
    // 1. Reemplazar el manifest de Nextcloud
    const oldManifest = document.querySelector('link[rel="manifest"]');
    if (oldManifest) {
        oldManifest.remove();
    }
    const link = document.createElement('link');
    link.rel = 'manifest';
    link.href = OC.generateUrl('/apps/pwa_suite/manifest.json');
    document.head.appendChild(link);

    // 2. Registrar el Service Worker
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            const swUrl = OC.generateUrl('/apps/pwa_suite/sw.js');
            navigator.serviceWorker.register(swUrl, { scope: '/' })
                .then(reg => console.log('PWA Suite SW registrado con alcance global:', reg.scope))
                .catch(err => console.error('Error al registrar SW en PWA Suite:', err));
        });
    }
})();
