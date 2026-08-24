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

    #[PublicPage]
    #[NoCSRFRequired]
    public function getManifest(): DataResponse {
        $advancedMode = $this->config->getAppValue('pwa_suite', 'advanced_mode', 'no');
        $customManifest = $this->config->getAppValue('pwa_suite', 'custom_manifest', '');

        // Si el modo experto está activo y el JSON es válido, se sirve directamente
        if ($advancedMode === 'yes' && !empty(trim($customManifest))) {
            $decoded = json_decode($customManifest, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $response = new DataResponse($decoded);
                $response->addHeader('Content-Type', 'application/manifest+json; charset=utf-8');
                return $response;
            }
        }

        // Configuración estándar
        $appName = $this->config->getAppValue('pwa_suite', 'app_name', 'Bcloud WorkSuite');
        $themeColor = $this->config->getAppValue('pwa_suite', 'theme_color', '#181818');
        $bgColor = $this->config->getAppValue('pwa_suite', 'bg_color', '#181818');
        $displayMode = $this->config->getAppValue('pwa_suite', 'display_mode', 'standalone');

        $manifest = [
            'id' => 'bcloud-worksuite-pwa',
            'name' => $appName,
            'short_name' => $appName,
            'description' => 'Entorno corporativo unificado Nextcloud',
            'start_url' => '/',
            'scope' => '/',
            'display' => $displayMode,
            'display_override' => ['window-controls-overlay', 'minimal-ui', 'standalone'],
            'orientation' => 'any',
            'background_color' => $bgColor,
            'theme_color' => $themeColor,
            'categories' => ['productivity', 'business'],
            'prefer_related_applications' => false,
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
                    'name' => 'Mis Archivos',
                    'url' => '/apps/files/',
                    'icons' => [['src' => '/apps/theming/icon?v=0', 'sizes' => '512x512']]
                ],
                [
                    'name' => 'Calendario',
                    'url' => '/apps/calendar/',
                    'icons' => [['src' => '/apps/theming/icon?v=0', 'sizes' => '512x512']]
                ]
            ]
        ];

        $response = new DataResponse($manifest);
        $response->addHeader('Content-Type', 'application/manifest+json; charset=utf-8');
        return $response;
    }

    #[PublicPage]
    #[NoCSRFRequired]
    public function getServiceWorker(): DataResponse {
        $advancedMode = $this->config->getAppValue('pwa_suite', 'advanced_mode', 'no');
        $customSw = $this->config->getAppValue('pwa_suite', 'custom_sw', '');

        // Si el modo experto está activo y tiene código JavaScript
        if ($advancedMode === 'yes' && !empty(trim($customSw))) {
            $response = new DataResponse($customSw);
            $response->addHeader('Content-Type', 'application/javascript; charset=utf-8');
            $response->addHeader('Service-Worker-Allowed', '/');
            return $response;
        }

        // Service Worker robusto por defecto (offline fallback + claim inmediato)
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
                    <title>Bcloud Offline</title>
                    <style>
                        body { background:#181818; color:#fff; font-family:system-ui; display:flex; height:100vh; align-items:center; justify-content:center; margin:0; text-align:center; }
                        .c { background:#222; padding:2rem; border-radius:12px; max-width:380px; }
                        h1 { color:#0082c9; margin-top:0; }
                        button { background:#0082c9; color:#fff; border:0; padding:.75rem 1.5rem; border-radius:6px; font-weight:700; cursor:pointer; width:100%; margin-top:1rem; }
                    </style>
                </head>
                <body>
                    <div class="c">
                        <h1>Bcloud WorkSuite</h1>
                        <p>Sin conexión con el servidor.</p>
                        <button onclick="location.reload()">Reintentar</button>
                    </div>
                </body>
                </html>
            `, { headers: { 'content-type': 'text/html;charset=UTF-8' } }))
        );
    }
});
JS;

        $response = new DataResponse($swCode);
        $response->addHeader('Content-Type', 'application/javascript; charset=utf-8');
        $response->addHeader('Service-Worker-Allowed', '/');
        return $response;
    }
}
