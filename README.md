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
├── LICENSE                    (AGPL-3.0-or-later)
├── README.md
├── appinfo/
│   ├── info.xml               (Metadatos de la app para Nextcloud)
│   └── routes.php             (Rutas para manifest.json, sw.js y API de admin)
├── js/
│   └── pwa-register.js        (Script inyectado que registra el Service Worker)
├── lib/
│   ├── AppInfo/
│   │   └── Application.php    (Inyecta las etiquetas <link rel="manifest"> en el <head>)
│   ├── Controller/
│   │   ├── PwaController.php  (Genera el manifest.json y sw.js dinámicos)
│   │   └── AdminController.php(Guarda la configuración del admin)
│   └── Settings/
│       └── AdminSettings.php  (Registra la pestaña en Configuración > Administración)
└── templates/
    └── admin.php              (Interfaz HTML/Vue con los selectores y campos)
```
