# pwa_suite

# PWA Suite & Customizer for Nextcloud

Transforma cualquier instancia de Nextcloud 34 en una Progressive Web App (PWA) totalmente instalable y personalizable directamente desde la interfaz de administración.

## Características
* **Manifest Dinámico:** Modifica el nombre de la app, colores de interfaz y colores de fondo.
* **Service Worker Automatizado:** Registro transparente sin tocar archivos del core.
* **Integración Nativa:** Panel de ajustes integrado en la sección de Seguridad de Nextcloud.

## 🛠️ Mantenimiento y Contribuciones

Este es un proyecto personal mantenido de forma voluntaria. 

- **Ciclo de actualizaciones:** Suelo actualizar la app periódicamente coincidiendo con la actualización de mi propia instancia de Nextcloud (normalmente por estabilidad cuando se agota la vida de esa version).
- **Soporte de versiones:** Si sale una nueva versión de Nextcloud (ej. Nextcloud 35, 36...) y necesitas compatibilidad antes de que yo actualice mi servidor, **los Pull Requests son totalmente bienvenidos**. Si testeas la app y confirmas compatibilidad, estaré encantado de fusionar el cambio y publicar una nueva versión.
- Si verificas que las versiones anteriores a la 34 son compatibles indícalo y le bajo el min a esa versión (nace con la 34 dado que es la que uso actualmente y con la que he testado).

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
│   └── admin-style.css      
├── js/
│   ├── admin-script.js      
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
