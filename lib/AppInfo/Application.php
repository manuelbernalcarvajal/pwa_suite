<?php

namespace OCA\PwaSuite\AppInfo;

use OCP\AppFramework\App;
use OCP\Util;

class Application extends App {

    public function __construct(array $urlParams = []) {
        parent::__construct('pwa_suite', $urlParams);

        // Omitir peticiones de sincronización DAV o API interna
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if (str_starts_with($uri, '/remote.php') || str_starts_with($uri, '/ocs/')) {
            return;
        }

        // Búfer de salida idéntico al HTMLRewriter de Cloudflare
        ob_start(function (?string $buffer) {
            if ($buffer === null || $buffer === '' || !str_contains($buffer, '<html')) {
                return $buffer;
            }

            $manifestUrl = Util::linkToRoute('pwa_suite.pwa.getManifest');

            // 1. Eliminar cualquier manifest nativo inyectado por Theming / Dashboard
            $cleaned = preg_replace('/<link\s+[^>]*rel=["\']manifest["\'][^>]*>/i', '', $buffer);

            // 2. Inyectar nuestro manifest en la cabecera antes de que el navegador procese el HTML
            return preg_replace(
                '/(<head[^>]*>)/i',
                '$1' . "\n" . '    <link rel="manifest" href="' . $manifestUrl . '">',
                $cleaned,
                1
            );
        });

        Util::addScript('pwa_suite', 'pwa-register');
    }
}
