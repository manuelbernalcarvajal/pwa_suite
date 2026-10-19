<?php

namespace OCA\PwaSuite\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\Settings\ISettings;
use OCP\IConfig;
use OCP\IURLGenerator;
use OCP\Util;

class AdminSettings implements ISettings {

    private IConfig $config;
    private IURLGenerator $urlGenerator;

    public function __construct(IConfig $config, IURLGenerator $urlGenerator) {
        $this->config = $config;
        $this->urlGenerator = $urlGenerator;
    }

    public function getForm(): TemplateResponse {
        Util::addScript('pwa_suite', 'admin-script');

        $iconVersion = $this->config->getAppValue('pwa_suite', 'icon_version', '1');
        $iconUrl = $this->urlGenerator->linkToRoute('pwa_suite.pwa.getIcon') . '?v=' . $iconVersion;

        $parameters = [
            'appName' => $this->config->getAppValue('pwa_suite', 'app_name', 'Nextcloud PWA'),
            'themeColor' => $this->config->getAppValue('pwa_suite', 'theme_color', '#181818'),
            'bgColor' => $this->config->getAppValue('pwa_suite', 'bg_color', '#181818'),
            'displayMode' => $this->config->getAppValue('pwa_suite', 'display_mode', 'standalone'),
            'advancedMode' => $this->config->getAppValue('pwa_suite', 'advanced_mode', 'no'),
            'customManifest' => $this->config->getAppValue('pwa_suite', 'custom_manifest', ''),
            'customSw' => $this->config->getAppValue('pwa_suite', 'custom_sw', ''),
            'iconUrl' => $iconUrl,
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
