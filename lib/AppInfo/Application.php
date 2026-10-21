<?php

namespace OCA\PwaSuite\AppInfo;

use OCP\AppFramework\App;
use OCP\IURLGenerator;
use OCP\Util;
use OCA\PwaSuite\Controller\PwaController;

class Application extends App {

    public function __construct(array $urlParams = []) {
        parent::__construct('pwa_suite', $urlParams);

        $uri = $_SERVER['REQUEST_URI'] ?? '';

        // 1. Intercept the Service Worker
        if (preg_match('/service-worker\.js/i', $uri) || str_ends_with($uri, '/sw.js')) {
            /** @var PwaController $controller */
            $controller = $this->getContainer()->get(PwaController::class);
            $response = $controller->getServiceWorker();

            header('Content-Type: application/javascript; charset=utf-8');
            header('Service-Worker-Allowed: /');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            
            echo $response->getData();
            exit;
        }

        // 2. Intercepción de Manifest
        if (
            str_contains($uri, 'theming/manifest') ||
            str_contains($uri, '/manifest.json') ||
            str_ends_with($uri, '/manifest.webmanifest')
        ) {
            $container = $this->getContainer();
            /** @var \OCP\IAppConfig $appConfig */
            $appConfig = $container->get(\OCP\IAppConfig::class);
            $perAppEnabled = $appConfig->getValueString('pwa_suite', 'per_app_pwa', 'no') === 'yes';

            /** @var PwaController $controller */
            $controller = $container->get(PwaController::class);
            
            $manifestApp = ($perAppEnabled && preg_match('#/theming/manifest/([a-z0-9_]+)#i', $uri, $m)) ? $m[1] : '';
            $response = $controller->getManifest($manifestApp);

            header('Content-Type: application/manifest+json; charset=utf-8');
            header('Cache-Control: no-cache, no-store, must-revalidate');
            echo $response->getData();
            exit;
        }

        // 3. Inyección en cabeceras HTML y carga del Service Worker
        if (!str_starts_with($uri, '/remote.php') && !str_starts_with($uri, '/ocs/')) {
            $container = $this->getContainer();
            /** @var \OCP\IAppConfig $appConfig */
            $appConfig = $container->get(\OCP\IAppConfig::class);
            $perAppEnabled = $appConfig->getValueString('pwa_suite', 'per_app_pwa', 'no') === 'yes';
            
            Util::addScript('pwa_suite', 'pwa-register');

            $pageApp = ($perAppEnabled && preg_match('#/apps/([a-z0-9_]+)#i', urldecode($uri), $m)) ? strtolower($m[1]) : '';

            ob_start(function (?string $buffer) use ($container, $pageApp) {
                if ($buffer === null || $buffer === '' || !str_contains($buffer, '<html')) {
                    return $buffer;
                }

                /** @var IURLGenerator $urlGenerator */
                $urlGenerator = $container->get(IURLGenerator::class);
                $manifestUrl = $urlGenerator->linkToRoute('pwa_suite.pwa.getManifest');
                if ($pageApp !== '') {
                    $manifestUrl .= '?app=' . $pageApp;
                }

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
