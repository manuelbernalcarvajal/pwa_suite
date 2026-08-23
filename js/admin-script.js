(function () {
    // Escuchar el cambio en el interruptor de modo experto
    document.addEventListener('change', function (e) {
        if (e.target && e.target.id === 'pwa-advanced-toggle') {
            const section = document.getElementById('pwa-advanced-section');
            if (section) {
                section.style.display = e.target.checked ? 'block' : 'none';
            }
        }
    });

    // Escuchar el clic del botón guardar
    document.addEventListener('click', function (e) {
        if (e.target && e.target.id === 'pwa-save-btn') {
            e.preventDefault();
            const msg = document.getElementById('pwa-save-msg');
            const toggle = document.getElementById('pwa-advanced-toggle');

            if (msg) {
                msg.textContent = 'Guardando...';
                msg.style.color = 'var(--color-text-maxcontrast, #fff)';
            }

            const payload = {
                appName: document.getElementById('pwa-app-name')?.value || '',
                themeColor: document.getElementById('pwa-theme-color')?.value || '#0082c9',
                bgColor: document.getElementById('pwa-bg-color')?.value || '#181818',
                displayMode: document.getElementById('pwa-display-mode')?.value || 'standalone',
                advancedMode: toggle && toggle.checked ? 'yes' : 'no',
                customManifest: document.getElementById('pwa-custom-manifest')?.value || '',
                customSw: document.getElementById('pwa-custom-sw')?.value || ''
            };

            fetch(OC.generateUrl('/apps/pwa_suite/api/v1/admin/config'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'requesttoken': OC.requestToken
                },
                body: JSON.stringify(payload)
            })
            .then(res => {
                if (!res.ok) throw new Error('HTTP ' + res.status);
                return res.json();
            })
            .then(() => {
                if (msg) {
                    msg.textContent = '¡Ajustes guardados con éxito!';
                    msg.style.color = '#46ba61';
                    setTimeout(() => { msg.textContent = ''; }, 3500);
                }
            })
            .catch(err => {
                console.error('[PWA Suite]', err);
                if (msg) {
                    msg.textContent = 'Error al guardar: ' + err.message;
                    msg.style.color = '#e9322d';
                }
            });
        }
    });
})();
