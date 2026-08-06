document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('pwa-suite-settings-form');
    const statusMsg = document.getElementById('pwa-status-message');

    if (!form) return;

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        statusMsg.textContent = 'Guardando...';
        statusMsg.style.color = 'var(--color-primary-element, #0082c9)';

        const appName = document.getElementById('pwa_app_name').value;
        const themeColor = document.getElementById('pwa_theme_color').value;
        const bgColor = document.getElementById('pwa_bg_color').value;
        const displayMode = document.getElementById('pwa_display_mode').value;

        // Genera la URL del endpoint definido en routes.php
        const url = OC.generateUrl('/apps/pwa_suite/api/v1/admin/config');

        try {
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'requesttoken': OC.requestToken // Protección CSRF nativa de Nextcloud
                },
                body: JSON.stringify({
                    appName: appName,
                    themeColor: themeColor,
                    bgColor: bgColor,
                    displayMode: displayMode
                })
            });

            if (response.ok) {
                statusMsg.textContent = '¡Configuración guardada con éxito!';
                statusMsg.style.color = 'var(--color-success, #46ba6f)';
            } else {
                statusMsg.textContent = 'Error al guardar la configuración.';
                statusMsg.style.color = 'var(--color-error, #d9534f)';
            }
        } catch (err) {
            console.error('PWA Suite Admin Error:', err);
            statusMsg.textContent = 'Error de conexión con el servidor.';
            statusMsg.style.color = 'var(--color-error, #d9534f)';
        }
    });
});
