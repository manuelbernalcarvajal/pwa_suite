(function () {
    'use strict';

    const MANIFEST_URL = OC.generateUrl('/apps/pwa_suite/manifest.json');
    const SW_URL = OC.generateUrl('/apps/pwa_suite/sw.js');

    // =========================================================================
    // 1. SECUESTRADOR ACTIVO DE MANIFEST (MutationObserver)
    // =========================================================================
    function enforceOurManifest() {
        const manifests = document.querySelectorAll('link[rel="manifest"]');
        let ourManifestExists = false;

        manifests.forEach(link => {
            if (link.getAttribute('href') === MANIFEST_URL) {
                ourManifestExists = true;
            } else {
                // Eliminar cualquier manifest intruso (Nextcloud core, Dashboard, etc.)
                link.remove();
            }
        });

        // Si nuestro manifest no está presente, lo creamos e insertamos
        if (!ourManifestExists) {
            const link = document.createElement('link');
            link.rel = 'manifest';
            link.href = MANIFEST_URL;
            document.head.appendChild(link);
        }
    }

    // Ejecución inmediata
    enforceOurManifest();

    // Vigilar el <head> en tiempo real: si Nextcloud inyecta otro manifest luego, se destruye al vuelo
    const observer = new MutationObserver(() => {
        enforceOurManifest();
    });

    observer.observe(document.head, {
        childList: true,
        subtree: true
    });

    // =========================================================================
    // 2. PURGA Y CONTROL ABSOLUTO DE SERVICE WORKERS
    // =========================================================================
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.getRegistrations().then(registrations => {
                for (let registration of registrations) {
                    // Si hay un Service Worker viejo que no sea el nuestro, lo aniquilamos
                    if (!registration.active || !registration.active.scriptURL.includes('pwa_suite')) {
                        registration.unregister().then(unregistered => {
                            if (unregistered) {
                                console.log('[PWA Sentinel] Service Worker intruso eliminado:', registration.scope);
                            }
                        });
                    }
                }

                // Registrar nuestro Service Worker maestro con alcance en la raíz
                navigator.serviceWorker.register(SW_URL, { scope: '/' })
                    .then(reg => {
                        console.log('[PWA Sentinel] SW Maestro activo con alcance global:', reg.scope);
                    })
                    .catch(err => {
                        console.error('[PWA Sentinel] Error al registrar SW:', err);
                    });
            });
        });
    }
})();
