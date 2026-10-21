# PWA Suite & Customizer for Nextcloud

Turns any Nextcloud 34/35 instance into a unified, fully installable and customizable Progressive Web App (PWA), configured straight from the admin settings.

By default, it unifies your entire Nextcloud instance (Files, Talk, Calendar, Mail, etc.) under a single cohesive application window.

## Features
- **Unified PWA by default:** installs your entire Nextcloud instance as a single, central PWA (`start_url: /`) with global scope.
- **Dynamic manifest:** customize app name, theme color, background splash color, and display mode directly from the admin panel.
- **Optional Per-App PWAs:** enable the dedicated toggle in settings if you want individual Nextcloud apps (Calendar, Mail, Talk, etc.) to be installed as separate, independent PWAs. This allows targeted rollouts via Chrome/Edge enterprise policies (`WebAppInstallForceList`).
- **Per-app overrides (Expert Mode):** when per-app PWAs are enabled, an optional `"apps"` object in the custom manifest JSON lets you override names and icons per app: `"apps": { "spreed": { "name": "Talk", "icons": [...] } }`. Everything except `id` can be overridden; the `apps` key itself is stripped before serving to the browser.
- **Automated Service Worker:** registers transparently with offline fallback support, without modifying core files.
- **Native integration:** settings panel seamlessly built into Nextcloud's Theming admin section.
- **Translatable:** English source strings, complete Spanish translation included (`l10n/`).

## 🛠️ Maintenance and contributions

This is a personal project maintained on a voluntary basis.

- **Update cycle:** I update the app periodically, in step with updating my own Nextcloud instance (usually for stability, when that version reaches end of life).
- **Version support:** if a new Nextcloud version comes out (e.g. Nextcloud 36, 37…) and you need compatibility before I update my server, **pull requests are very welcome**. If you test the app and confirm compatibility, I'll gladly merge the change and publish a new release.
- If you verify that versions older than 34 are compatible, let me know and I'll lower the minimum (it started at 34 because that's the version I use and tested with).

## License
AGPL-3.0

```text
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
├── l10n/
│   ├── es.js
│   └── es.json
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
