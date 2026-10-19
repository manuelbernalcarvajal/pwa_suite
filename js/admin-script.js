(function () {
    // Modo Experto Toggle
    document.addEventListener('change', function (e) {
        if (e.target && e.target.id === 'pwa-advanced-toggle') {
            const section = document.getElementById('pwa-advanced-section');
            if (section) section.style.display = e.target.checked ? 'block' : 'none';
        }
    });

    // Preview inmediato al seleccionar icono
    document.addEventListener('change', function (e) {
        if (e.target && e.target.id === 'pwa-icon-file') {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    const img = document.getElementById('pwa-icon-preview');
                    if (img) img.src = evt.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    });

    // Guardar cambios
    document.addEventListener('click', function (e) {
        if (e.target && e.target.id === 'pwa-save-btn') {
            e.preventDefault();
            const msg = document.getElementById('pwa-save-msg');
            const toggle = document.getElementById('pwa-advanced-toggle');
            const fileInput = document.getElementById('pwa-icon-file');

            if (msg) {
                msg.textContent = 'Guardando...';
                msg.style.color = 'var(--color-text-maxcontrast, #fff)';
            }

            const payload = {
                appName: document.getElementById('pwa-app-name')?.value || '',
                themeColor: document.getElementById('pwa-theme-color')?.value || '#181818',
                bgColor: document.getElementById('pwa-bg-color')?.value || '#181818',
                displayMode: document.getElementById('pwa-display-mode')?.value || 'standalone',
                advancedMode: toggle && toggle.checked ? 'yes' : 'no',
                customManifest: document.getElementById('pwa-custom-manifest')?.value || '',
                customSw: document.getElementById('pwa-custom-sw')?.value || ''
            };

            const uploadPromises = [];

            // 1. Si se eligió un archivo nuevo de icono, se sube primero
            if (fileInput && fileInput.files.length > 0) {
                const formData = new FormData();
                formData.append('pwa_icon', fileInput.files[0]);

                const iconUpload = fetch(OC.generateUrl('/apps/pwa_suite/api/v1/admin/icon'), {
                    method: 'POST',
                    headers: { 'requesttoken': OC.requestToken },
                    body: formData
                }).then(res => {
                    if (!res.ok) throw new Error('Error al subir el icono');
                    return res.json();
                });

                uploadPromises.push(iconUpload);
            }

            // 2. Guardar el resto de configuración de texto/colores
            const configSave = fetch(OC.generateUrl('/apps/pwa_suite/api/v1/admin/config'), {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'requesttoken': OC.requestToken
                },
                body: JSON.stringify(payload)
            }).then(res => {
                if (!res.ok) throw new Error('Error al guardar configuración');
                return res.json();
            });

            uploadPromises.push(configSave);

            Promise.all(uploadPromises)
                .then(() => {
                    if (msg) {
                        msg.textContent = '¡Ajustes e icono guardados correctamente!';
                        msg.style.color = '#46ba61';
                        setTimeout(() => { msg.textContent = ''; }, 3500);
                    }
                })
                .catch(err => {
                    console.error('[PWA Suite Error]', err);
                    if (msg) {
                        msg.textContent = 'Error: ' + err.message;
                        msg.style.color = '#e9322d';
                    }
                });
        }
    });
})();
