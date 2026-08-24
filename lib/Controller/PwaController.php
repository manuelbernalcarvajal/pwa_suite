<?php

namespace OCA\PwaSuite\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCP\IConfig;
use OCP\AppFramework\Http\Attribute\PublicPage;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;

class PwaController extends Controller {

    private IConfig $config;

    public function __construct(string $appName, IRequest $request, IConfig $config) {
        parent::__construct($appName, $request);
        $this->config = $config;
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

        if ($advancedMode === 'yes' && !empty(trim($customManifest))) {
            $decoded = json_decode($customManifest, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $response = new DataResponse($decoded);
                $response->addHeader('Content-Type', 'application/manifest+json; charset=utf-8');
                return $response;
            }
        }

        $appName = $this->config->getAppValue('pwa_suite', 'app_name', 'Bcloud WorkSuite');
        $themeColor = $this->config->getAppValue('pwa_suite', 'theme_color', '#181818');
        $bgColor = $this->config->getAppValue('pwa_suite', 'bg_color', '#181818');
        $displayMode = $this->config->getAppValue('pwa_suite', 'display_mode', 'standalone');

        $manifest = [
            'id' => 'nextcloud-custom-pwa',
            'name' => $appName,
            'short_name' => $appName,
            'description' => $appName . ' - Entorno Unificado',
            'start_url' => '/',
            'scope' => '/',
            'display' => $displayMode,
            'display_override' => ['window-controls-overlay', 'minimal-ui', 'standalone'],
            'orientation' => 'any',
            'background_color' => $bgColor,
            'theme_color' => $themeColor,
            'icons' => [
                [
                    'src' => '/apps/theming/icon?v=0',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any maskable'
                ],
                [
                    'src' => '/apps/theming/favicon?v=0',
                    'sizes' => '192x192',
                    'type' => 'image/png',
                    'purpose' => 'any'
                ],
                [
                    'src' => '/core/img/favicon.png',
                    'sizes' => '64x64',
                    'type' => 'image/png',
                    'purpose' => 'any'
                ]
            ],
            'shortcuts' => [
                [
                    'name' => 'Archivos',
                    'url' => '/apps/files/',
                    'icons' => [['src' => '/apps/theming/icon?v=0', 'sizes' => '512x512']]
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

        $appName = addslashes($this->config->getAppValue('pwa_suite', 'app_name', 'Bcloud WorkSuite'));
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

// Soporte unificado de Notificaciones Push de Nextcloud
self.addEventListener('push', (e) => {
    let d = { title: '{$appName}', body: 'Nueva notificación' };
    if (e.data) {
        try { d = e.data.json(); } catch(err) { d.body = e.data.text(); }
    }
    e.waitUntil(
        self.registration.showNotification(d.title || '{$appName}', {
            body: d.body || '',
            icon: '/apps/theming/icon?v=0',
            badge: '/apps/theming/icon?v=0',
            data: { url: d.url || '/' }
        })
    );
});

self.addEventListener('notificationclick', (e) => {
    e.notification.close();
    e.waitUntil(clients.openWindow(e.notification.data?.url || '/'));
});
JS;

        $response = new DataResponse($swCode);
        $response->addHeader('Content-Type', 'application/javascript; charset=utf-8');
        $response->addHeader('Service-Worker-Allowed', '/');
        return $response;
    }
}
