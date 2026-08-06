<?php

namespace OCA\PwaSuite\Settings;

use OCP\Settings\ISettings;
use OCP\IConfig;
use OCP\AppFramework\Http\TemplateResponse;

class AdminSettings implements ISettings {

    private IConfig $config;

    public function __construct(IConfig $config) {
        $this->config = $config;
    }

    public function getForm(): TemplateResponse {
        $parameters = [
            'appName' => $this->config->getAppValue('pwa_suite', 'app_name', 'Nextcloud PWA'),
            'themeColor' => $this->config->getAppValue('pwa_suite', 'theme_color', '#181818'),
            'bgColor' => $this->config->getAppValue('pwa_suite', 'bg_color', '#181818'),
            'displayMode' => $this->config->getAppValue('pwa_suite', 'display_mode', 'standalone'),
        ];

        return new TemplateResponse('pwa_suite', 'admin', $parameters, '');
    }

    public function getSection(): string {
        return 'security'; // Pestaña de Seguridad en el panel de admin
    }

    public function getPriority(): int {
        return 50;
    }
}
