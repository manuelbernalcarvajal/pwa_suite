<?php

namespace OCA\PwaSuite\AppInfo;

use OCP\AppFramework\App;
use OCP\Util;

class Application extends App {

    public function __construct(array $urlParams = []) {
        parent::__construct('pwa_suite', $urlParams);

        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (str_starts_with($uri, '/remote.php') || str_starts_with($uri, '/ocs/')) {
            return;
        }

        // Intercepta el flujo HTML antes de enviarlo al navegador
        ob_start(function (?string $buffer) {
            if ($buffer === null || $buffer === '' || !str_contains($buffer, '<html')) {
                return $buffer;
            }

            $manifestUrl = Util::linkToRoute('pwa_suite.pwa.getManifest');
            $swUrl = Util::linkToRoute('pwa_suite.pwa.getServiceWorker');

            // 1. Eliminar cualquier manifest previo de Theming o Dashboard
            $cleaned = preg_replace('/<link\s+[^>]*rel=["\']manifest["\'][^>]*>/i', '', $buffer);

            // 2. Inyección prioritaria inline en el <head>
            $headInject = <<<HTML
    <link rel="manifest" href="{$manifestUrl}">
    <script>
    (function() {
        if ('serviceWorker' in navigator) {
            const targetSW = '{$swUrl}';
            const nativeRegister = navigator.serviceWorker.register.bind(navigator.serviceWorker);

            // Redirigir cualquier intento de registro (ej. Notifications) hacia nuestro SW unificado
            navigator.serviceWorker.register = function(url, options) {
                return nativeRegister(targetSW, { scope: '/' });
            };

            window.addEventListener('load', function() {
                nativeRegister(targetSW, { scope: '/' }).catch(function(err) {
                    console.error('[PWA Suite] Error registrando SW:', err);
                });
            });
        }
    })();
    </script>
HTML;

            return preg_replace(
                '/(<head[^>]*>)/i',
                '$1' . "\n" . $headInject,
                $cleaned,
                1
            );
        });
    }
}
