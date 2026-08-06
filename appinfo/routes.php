<?php

return [
    'routes' => [
        // Rutas públicas de la PWA
        ['name' => 'pwa#getManifest', 'url' => '/manifest.json', 'verb' => 'GET'],
        ['name' => 'pwa#getServiceWorker', 'url' => '/sw.js', 'verb' => 'GET'],
        
        // Ruta de administración
        ['name' => 'admin#saveConfig', 'url' => '/api/v1/admin/config', 'verb' => 'POST'],
    ]
];
