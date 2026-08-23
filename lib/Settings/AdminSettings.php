<?php

namespace OCA\PwaSuite\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;
use OCP\IConfig;

class AdminSettings implements ISettings {

    private IConfig $config;

    public function __construct(IConfig $config) {
        $this->config = $config;
    }

    public function getForm(): TemplateResponse {
        $parameters = [
            'appName' => $this->config->getAppValue('pwa_suite', 'app_name', 'Nextcloud PWA'),
            'themeColor' => $this->config->getAppValue('pwa_suite', 'theme_color', '#0082c9'),
            'bgColor' => $this->config->getAppValue('pwa_suite', 'bg_color', '#181818'),
            'displayMode' => $this->config->getAppValue('pwa_suite', 'display_mode', 'standalone'),
            'advancedMode' => $this->config->getAppValue('pwa_suite', 'advanced_mode', 'no'),
            'customManifest' => $this->config->getAppValue('pwa_suite', 'custom_manifest', ''),
            'customSw' => $this->config->getAppValue('pwa_suite', 'custom_sw', ''),
        ];

        return new TemplateResponse('pwa_suite', 'admin', $parameters, '');
    }

    public function getSection(): string {
        return 'theming';
    }

    public function getPriority(): int {
        return 50;
    }
}
