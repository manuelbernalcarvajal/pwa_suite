<?php

namespace OCA\PwaSuite\AppInfo;

use OCP\AppFramework\App;
use OCP\IConfig;
use OCP\Util;
use OCA\PwaSuite\Controller\PwaController;

class Application extends App {

    public function __construct(array $urlParams = []) {
        parent::__construct('pwa_suite', $urlParams);

        $uri = $_SERVER['REQUEST_URI'] ?? '';

        // 1. SECUESTRO DE CUALQUIER SERVICE WORKER (Notifications, Talk, Core, etc.)
        if (preg_match('/service-worker\.js/i', $uri) || str_ends_with($uri, '/sw.js')) {
            /** @var PwaController $controller */
            $controller = $this->getContainer()->get(PwaController::class);
            $response = $controller->getServiceWorker();

            header('Content-Type: application/javascript; charset=utf-8');
            header('Service-Worker-Allowed: /');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            
            echo $response->getData();
            exit; // Corta la ejecución: Nextcloud nunca llega a cargar el worker por defecto
        }

        // 2. SECUESTRO DE CUALQUIER MANIFEST (Theming, Files, Dashboard, etc.)
        if (
            str_contains($uri, 'theming/manifest') ||
            str_contains($uri, '/manifest.json') ||
            preg_match('/\/manifest(\/|\?|$)/i', $uri)
        ) {
            /** @var PwaController $controller */
            $controller = $this->getContainer()->get(PwaController::class);
            $response = $controller->getManifest();

            header('Content-Type: application/manifest+json; charset=utf-8');
            header('Cache-Control: no-cache, no-store, must-revalidate');

            echo json_encode($response->getData(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            exit; // Corta la ejecución: devuelve nuestro JSON para cualquier app
        }

        // 3. Inyección limpia en el HTML para navegaciones estándar
        if (!str_starts_with($uri, '/remote.php') && !str_starts_with($uri, '/ocs/')) {
            ob_start(function (?string $buffer) {
                if ($buffer === null || $buffer === '' || !str_contains($buffer, '<html')) {
                    return $buffer;
                }

                $manifestUrl = Util::linkToRoute('pwa_suite.pwa.getManifest');

                // Reemplaza cualquier manifest previo por el nuestro
                $cleaned = preg_replace('/<link\s+[^>]*rel=["\']manifest["\'][^>]*>/i', '', $buffer);
                return preg_replace(
                    '/(<head[^>]*>)/i',
                    '$1' . "\n" . '    <link rel="manifest" href="' . $manifestUrl . '">',
                    $cleaned,
                    1
                );
            });
        }
    }
}
