<?php

namespace OCA\PwaSuite\AppInfo;

use OCP\AppFramework\App;
use OCP\EventDispatcher\IEventDispatcher;
use OCP\AppTemplate\BeforeTemplateRenderedEvent;
use OCP\Util;

class Application extends App {

    public function __construct(array $urlParams = []) {
        parent::__construct('pwa_suite', $urlParams);
        
        /** @var IEventDispatcher $dispatcher */
        $dispatcher = $this->getContainer()->get(IEventDispatcher::class);
        
        $dispatcher->addListener(BeforeTemplateRenderedEvent::class, function () {
            Util::addHeader('link', [
                'rel' => 'manifest',
                'href' => Util::linkToRoute('pwa_suite.pwa.getManifest')
            ]);
            
            Util::addScript('pwa_suite', 'pwa-register');
        });
    }
}
