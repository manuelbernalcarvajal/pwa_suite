<?php

namespace OCA\PwaSuite\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Http\ContentSecurityPolicy;
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
        // Lee valores configurados por el admin o carga los defaults
        $appName = $this->config->getAppValue('pwa_suite', 'app_name', 'Nextcloud PWA');
        $themeColor = $this->config->getAppValue('pwa_suite', 'theme_color', '#0082c9');
        $bgColor = $this->config->getAppValue('pwa_suite', 'bg_color', '#181818');
        $displayMode = $this->config->getAppValue('pwa_suite', 'display_mode', 'standalone');

        $manifest = [
            'name' => $appName,
            'short_name' => $appName,
            'start_url' => '/',
            'scope' => '/',
            'display' => $displayMode,
            'background_color' => $bgColor,
            'theme_color' => $themeColor,
            'icons' => [
                [
                    'src' => '/apps/theming/icon?v=0',
                    'sizes' => '512x512',
                    'type' => 'image/png',
                    'purpose' => 'any maskable'
                ]
            ],
            'shortcuts' => [
                [
                    'name' => 'Mis Archivos',
                    'url' => '/apps/files/'
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
        $swCode = <<<JS
            self.addEventListener('install', () => self.skipWaiting());
            self.addEventListener('activate', (e) => e.waitUntil(clients.claim()));
            self.addEventListener('fetch', (e) => {
                // Lógica de cache/offline básica
            });
        JS;

        $response = new DataResponse($swCode);
        $response->addHeader('Content-Type', 'application/javascript; charset=utf-8');
        // Cabecera que autoriza al SW a controlar todo el dominio desde la raíz:
        $response->addHeader('Service-Worker-Allowed', '/');
        
        return $response;
    }
}
