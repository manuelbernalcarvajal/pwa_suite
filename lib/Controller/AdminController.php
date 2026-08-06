<?php

namespace OCA\PwaSuite\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;
use OCP\IConfig;

class AdminController extends Controller {

    private IConfig $config;

    public function __construct(string $appName, IRequest $request, IConfig $config) {
        parent::__construct($appName, $request);
        $this->config = $config;
    }

    /**
     * @NoCSRFRequired (Nextcloud maneja el token internamente si se pasa por cabecera)
     */
    public function saveConfig(string $appName, string $themeColor, string $bgColor, string $displayMode): DataResponse {
        $this->config->setAppValue('pwa_suite', 'app_name', $appName);
        $this->config->setAppValue('pwa_suite', 'theme_color', $themeColor);
        $this->config->setAppValue('pwa_suite', 'bg_color', $bgColor);
        $this->config->setAppValue('pwa_suite', 'display_mode', $displayMode);

        return new DataResponse(['status' => 'success', 'message' => 'Configuración PWA guardada']);
    }
}
