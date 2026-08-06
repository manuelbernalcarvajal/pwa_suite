# pwa_suite

# PWA Suite & Customizer for Nextcloud

Transforma cualquier instancia de Nextcloud 34 en una Progressive Web App (PWA) totalmente instalable y personalizable directamente desde la interfaz de administración.

## Características
* **Manifest Dinámico:** Modifica el nombre de la app, colores de interfaz y colores de fondo.
* **Service Worker Automatizado:** Registro transparente sin tocar archivos del core.
* **Integración Nativa:** Panel de ajustes integrado en la sección de Seguridad de Nextcloud.

## Licencia
AGPL-3.0

```
pwa_suite/
├── LICENSE
├── README.md
├── appinfo/
│   ├── info.xml
│   └── routes.php
├── css/
│   └── admin-style.css       <-- [NUEVO] Estilos de la UI de administración
├── js/
│   ├── admin-script.js       <-- [NUEVO] Peticiones AJAX del panel
│   └── pwa-register.js
├── lib/
│   ├── AppInfo/
│   │   └── Application.php
│   ├── Controller/
│   │   ├── AdminController.php
│   │   └── PwaController.php
│   └── Settings/
│       └── AdminSettings.php
└── templates/
    └── admin.php

```
