(function () {
    'use strict';

    const MANIFEST_URL = OC.generateUrl('/apps/pwa_suite/manifest.json');
    const SW_URL = OC.generateUrl('/apps/pwa_suite/sw.js');

    // =========================================================================
    // 1. BLOQUEO DE REGISTRO DE SERVICE WORKERS AJENOS (Dashboard / Notifications)
    // =========================================================================
    if ('serviceWorker' in navigator) {
        const nativeRegister = navigator.serviceWorker.register.bind(navigator.serviceWorker);

        // Interceptamos cualquier llamada de Nextcloud a register()
        navigator.serviceWorker.register = function (scriptURL, options) {
            const urlStr = scriptURL.toString();
            // Si la llamada no viene de pwa_suite, la bloqueamos silenciosamente
            if (!urlStr.includes('pwa_suite')) {
                console.warn('[PWA Suite] Intento de registro ajeno bloqueado:', urlStr);
                return Promise.resolve(null);
            }
            return nativeRegister(scriptURL, options);
        };

        // Purga de Service Workers activos que no sean el nuestro
        navigator.serviceWorker.getRegistrations().then(registrations => {
            for (let reg of registrations) {
                if (!reg.active || !reg.active.scriptURL.includes('pwa_suite')) {
                    reg.unregister().then(() => {
                        console.log('[PWA Suite] Service Worker antiguo purgado:', reg.scope);
                    });
                }
            }
            // Registramos el nuestro con alcance global
            nativeRegister(SW_URL, { scope: '/' }).catch(err => {
                console.error('[PWA Suite] Error al registrar SW:', err);
            });
        });
    }

    // =========================================================================
    // 2. FIJACIÓN Y BLINDAJE DEL MANIFEST CONTRA EL DASHBOARD
    // =========================================================================
    function lockManifest() {
        let manifest = document.querySelector('link#pwa-suite-manifest');

        // Eliminar cualquier manifest que intente inyectar el Dashboard o el Theming
        document.querySelectorAll('link[rel="manifest"]').forEach(el => {
            if (el.id !== 'pwa-suite-manifest') {
                el.remove();
            }
        });

        // Crear o forzar nuestro manifest oficial
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

    lockManifest();

    // Vigilar modificaciones dinámicas que haga Nextcloud al cambiar de pestaña
    const observer = new MutationObserver(() => {
        lockManifest();
    });

    observer.observe(document.head, {
        childList: true,
        subtree: true,
        attributes: true,
        attributeFilter: ['href', 'rel']
    });

    // Re-evaluar ante cualquier evento de navegación SPA interna
    window.addEventListener('popstate', lockManifest);
})();
