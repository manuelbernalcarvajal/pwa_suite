<?php

namespace OCA\PwaSuite\AppInfo;

use OCP\AppFramework\App;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\AppTemplate\BeforeTemplateRenderedEvent;
use OCP\Util;
use OCA\PwaSuite\Settings\AdminSettings;
use OCP\Settings\IManager;

class Application extends App {

    public function __construct() {
        parent::__construct('pwa_suite');
        
        $container = $this->getContainer();
        
        /** @var IEventDispatcher $dispatcher */
        $dispatcher = $container->get(IEventDispatcher::class);
        
        $dispatcher->addListener(BeforeTemplateRenderedEvent::class, function () {
            Util::addHeader('link', [
                'rel' => 'manifest',
                'href' => Util::linkToRoute('pwa_suite.pwa.getManifest')
            ]);
            
            Util::addScript('pwa_suite', 'pwa-register');
        });

        // REGISTRO DEL PANEL DE ADMINISTRACIÓN
        /** @var IManager $settingsManager */
        $settingsManager = $container->getServer()->getSettingsManager();
        
        // Registra el formulario de configuración en el panel global
        $settingsManager->registerSetting('admin', AdminSettings::class);
    }
}
