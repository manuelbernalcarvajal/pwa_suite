(function () {
    function initPwaAdmin() {
        const toggle = document.getElementById('pwa-advanced-toggle');
        const advancedSection = document.getElementById('pwa-advanced-section');
        const saveBtn = document.getElementById('pwa-save-btn');
        const msg = document.getElementById('pwa-save-msg');

        if (!saveBtn) return;

        if (toggle && advancedSection) {
            toggle.addEventListener('change', () => {
                advancedSection.style.display = toggle.checked ? 'block' : 'none';
            });
        }

        saveBtn.addEventListener('click', (e) => {
            e.preventDefault();
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
                if (!res.ok) throw new Error('Error HTTP ' + res.status);
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
                console.error('[PWA Suite Error]', err);
                if (msg) {
                    msg.textContent = 'Error al guardar: ' + err.message;
                    msg.style.color = '#e9322d';
                }
            });
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initPwaAdmin);
    } else {
        initPwaAdmin();
    }
})();
