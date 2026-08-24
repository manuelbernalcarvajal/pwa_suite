<?php

namespace OCA\PwaSuite\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCP\IConfig;
use OCP\IURLGenerator;
use OCP\Files\IAppData;
use OCP\AppFramework\Http\Attribute\PublicPage;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;

class PwaController extends Controller {

    private IConfig $config;
    private IAppData $appData;
    private IURLGenerator $urlGenerator;

    public function __construct(string $appName, IRequest $request, IConfig $config, IAppData $appData, IURLGenerator $urlGenerator) {
        parent::__construct($appName, $request);
        $this->config = $config;
        $this->appData = $appData;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @NoCSRFRequired
     * @PublicPage
     */
    #[PublicPage]
    #[NoCSRFRequired]
    public function getIcon(): void {
        $hasCustom = $this->config->getAppValue('pwa_suite', 'has_custom_icon', 'no');

        if ($hasCustom === 'yes') {
            try {
                $folder = $this->appData->getFolder('icons');
                $file = $folder->getFile('app-icon.png');
                $content = $file->getContent();

                header('Content-Type: image/png');
                header('Content-Length: ' . strlen($content));
                header('Cache-Control: public, max-age=604800');
                echo $content;
                exit;
            } catch (\Exception $e) {
                // Fallback automático si no se encuentra el archivo
            }
        }

        header('Location: /apps/theming/icon?v=0');
        exit;
    }

    /**
     * @NoCSRFRequired
     * @PublicPage
     */
    #[PublicPage]
    #[NoCSRFRequired]
    public function getManifest(): DataResponse {
        $advancedMode = $this->config->getAppValue('pwa_suite', 'advanced_mode', 'no');
        $customManifest = $this->config->getAppValue('pwa_suite', 'custom_manifest', '');

        // Si el usuario activa el modo experto, puede definir Window Controls Overlay u otros parámetros manualmente
        if ($advancedMode === 'yes' && !empty(trim($customManifest))) {
            $decoded = json_decode($customManifest, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $response = new DataResponse($decoded);
                $response->addHeader('Content-Type', 'application/manifest+json; charset=utf-8');
                return $response;
            }
        }

        $appName = $this->config->getAppValue('pwa_suite', 'app_name', 'Nextcloud PWA');
        $themeColor = $this->config->getAppValue('pwa_suite', 'theme_color', '#181818');
        $bgColor = $this->config->getAppValue('pwa_suite', 'bg_color', '#181818');
        $displayMode = $this->config->getAppValue('pwa_suite', 'display_mode', 'standalone');
        $iconVersion = $this->config->getAppValue('pwa_suite', 'icon_version', '1');

        $iconUrl = $this->urlGenerator->linkToRoute('pwa_suite.pwa.getIcon') . '?v=' . $iconVersion;

        // Estructura limpia con barra superior de ventana nativa estándar
        $manifest = [
            'id' => 'nextcloud-custom-pwa',
            'name' => $appName,
            'short_name' => $appName,
            'description' => $appName . ' - Entorno Unificado',
            'start_url' => '/',
            'scope' => '/',
            'display' => $displayMode,
            'orientation' => 'any',
            'background_color' => $bgColor,
            'theme_color' => $themeColor,
            'icons' => [
                [
                    'src' => $iconUrl,
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any maskable'
                ],
                [
                    'src' => $iconUrl,
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any'
                ]
            ],
            'shortcuts' => [
                [
                    'name' => 'Archivos',
                    'url' => '/apps/files/',
                    'icons' => [['src' => $iconUrl, 'sizes' => '512x512']]
                ]
            ]
        ];

        $response = new DataResponse($manifest);
        $response->addHeader('Content-Type', 'application/manifest+json; charset=utf-8');
        return $response;
    }

    /**
     * @NoCSRFRequired
     * @PublicPage
     */
    #[PublicPage]
    #[NoCSRFRequired]
    public function getServiceWorker(): DataResponse {
        $advancedMode = $this->config->getAppValue('pwa_suite', 'advanced_mode', 'no');
        $customSw = $this->config->getAppValue('pwa_suite', 'custom_sw', '');

        if ($advancedMode === 'yes' && !empty(trim($customSw))) {
            $response = new DataResponse($customSw);
            $response->addHeader('Content-Type', 'application/javascript; charset=utf-8');
            $response->addHeader('Service-Worker-Allowed', '/');
            return $response;
        }

        $appName = addslashes($this->config->getAppValue('pwa_suite', 'app_name', 'Nextcloud PWA'));
        $themeColor = addslashes($this->config->getAppValue('pwa_suite', 'theme_color', '#181818'));
        $bgColor = addslashes($this->config->getAppValue('pwa_suite', 'bg_color', '#181818'));

        $swCode = <<<JS
self.addEventListener('install', () => self.skipWaiting());
self.addEventListener('activate', (e) => e.waitUntil(clients.claim()));

self.addEventListener('fetch', (e) => {
    if (e.request.mode === 'navigate') {
        e.respondWith(
            fetch(e.request).catch(() => new Response(`
                <!DOCTYPE html>
                <html lang="es">
                <head>
                    <meta charset="UTF-8">
                    <title>{$appName} Offline</title>
                    <style>
                        body { background:{$bgColor}; color:#fff; font-family:system-ui; display:flex; height:100vh; align-items:center; justify-content:center; margin:0; text-align:center; }
                        .c { background:#222; padding:2rem; border-radius:12px; max-width:380px; }
                        h1 { color:{$themeColor}; margin-top:0; }
                        button { background:{$themeColor}; color:#fff; border:0; padding:.75rem 1.5rem; border-radius:6px; font-weight:700; cursor:pointer; width:100%; margin-top:1rem; }
                    </style>
                </head>
                <body>
                    <div class="c">
                        <h1>{$appName}</h1>
                        <p>Sin conexión con el servidor.</p>
                        <button onclick="location.reload()">Reintentar</button>
                    </div>
                </body>
                </html>
            `, { headers: { 'content-type': 'text/html;charset=UTF-8' } }))
        );
    }
});

self.addEventListener('push', (e) => {
    let title = '{$appName}';
    let options = {
        icon: '/apps/pwa_suite/icon',
        badge: '/apps/pwa_suite/icon',
        data: { url: '/' }
    };
    if (e.data) {
        try {
            const json = e.data.json();
            title = json.title || title;
            options.body = json.body || '';
            if (json.url) options.data.url = json.url;
        } catch (err) {
            const text = e.data.text();
            const lines = text.split('\\n');
            if (lines.length > 1) {
                title = lines[0];
                options.body = lines.slice(1).join('\\n');
            } else {
                options.body = text;
            }
        }
    }
    e.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', (e) => {
    e.notification.close();
    const targetUrl = e.notification.data?.url || '/';
    e.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((windowClients) => {
            for (let client of windowClients) {
                if (client.url.includes(self.location.origin) && 'focus' in client) {
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
JS;

        $response = new DataResponse($swCode);
        $response->addHeader('Content-Type', 'application/javascript; charset=utf-8');
        $response->addHeader('Service-Worker-Allowed', '/');
        return $response;
    }
}
