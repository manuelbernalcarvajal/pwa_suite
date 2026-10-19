<?php

namespace OCA\PwaSuite\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;
use OCP\IConfig;
use OCP\Files\IAppData;

class AdminController extends Controller {

    private IConfig $config;
    private IAppData $appData;

    public function __construct(string $appName, IRequest $request, IConfig $config, IAppData $appData) {
        parent::__construct($appName, $request);
        $this->config = $config;
        $this->appData = $appData;
    }

    public function saveConfig(): JSONResponse {
        $params = $this->request->getParams();

        if (isset($params['appName'])) $this->config->setAppValue('pwa_suite', 'app_name', $params['appName']);
        if (isset($params['themeColor'])) $this->config->setAppValue('pwa_suite', 'theme_color', $params['themeColor']);
        if (isset($params['bgColor'])) $this->config->setAppValue('pwa_suite', 'bg_color', $params['bgColor']);
        if (isset($params['displayMode'])) $this->config->setAppValue('pwa_suite', 'display_mode', $params['displayMode']);
        if (isset($params['advancedMode'])) $this->config->setAppValue('pwa_suite', 'advanced_mode', $params['advancedMode']);
        if (isset($params['customManifest'])) $this->config->setAppValue('pwa_suite', 'custom_manifest', $params['customManifest']);
        if (isset($params['customSw'])) $this->config->setAppValue('pwa_suite', 'custom_sw', $params['customSw']);

        return new JSONResponse(['status' => 'success']);
    }

    public function uploadIcon(): JSONResponse {
        $uploaded = $this->request->getUploadedFile('pwa_icon');

        if (!$uploaded || $uploaded['error'] !== UPLOAD_ERR_OK) {
            return new JSONResponse(['status' => 'error', 'message' => 'Error al subir imagen'], 400);
        }

        $content = file_get_contents($uploaded['tmp_name']);
        if (!$content) {
            return new JSONResponse(['status' => 'error', 'message' => 'Archivo vacío'], 400);
        }

        try {
            $folder = $this->appData->getFolder('icons');
        } catch (\Exception $e) {
            $folder = $this->appData->newFolder('icons');
        }

        try {
            $file = $folder->getFile('app-icon.png');
            $file->putContent($content);
        } catch (\Exception $e) {
            $file = $folder->newFile('app-icon.png');
            $file->putContent($content);
        }

        $version = time();
        $this->config->setAppValue('pwa_suite', 'has_custom_icon', 'yes');
        $this->config->setAppValue('pwa_suite', 'icon_version', (string)$version);

        return new JSONResponse(['status' => 'success', 'version' => $version]);
    }
}
