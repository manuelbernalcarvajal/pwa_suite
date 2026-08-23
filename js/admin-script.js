document.addEventListener('DOMContentLoaded', () => {
    const toggle = document.getElementById('pwa-advanced-toggle');
    const advancedSection = document.getElementById('pwa-advanced-section');
    const saveBtn = document.getElementById('pwa-save-btn');
    const msg = document.getElementById('pwa-save-msg');

    // Manejar visibilidad del panel experto
    if (toggle && advancedSection) {
        toggle.addEventListener('change', () => {
            advancedSection.style.display = toggle.checked ? 'block' : 'none';
        });
    }

    if (saveBtn) {
        saveBtn.addEventListener('click', () => {
            msg.textContent = 'Guardando...';
            msg.style.color = 'var(--color-text-maxcontrast)';

            const payload = {
                appName: document.getElementById('pwa-app-name').value,
                themeColor: document.getElementById('pwa-theme-color').value,
                bgColor: document.getElementById('pwa-bg-color').value,
                displayMode: document.getElementById('pwa-display-mode').value,
                advancedMode: toggle && toggle.checked ? 'yes' : 'no',
                customManifest: document.getElementById('pwa-custom-manifest').value,
                customSw: document.getElementById('pwa-custom-sw').value
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
                if (!res.ok) throw new Error('Error al guardar');
                return res.json();
            })
            .then(() => {
                msg.textContent = '¡Ajustes guardados con éxito!';
                msg.style.color = '#46ba61';
                setTimeout(() => { msg.textContent = ''; }, 3500);
            })
            .catch(err => {
                console.error(err);
                msg.textContent = 'Error al guardar los ajustes.';
                msg.style.color = '#e9322d';
            });
        });
    }
});
