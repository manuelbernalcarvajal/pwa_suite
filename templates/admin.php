<?php
/** @var array $_ */
?>
<div class="section pwa-admin-section">
    <h2>PWA Suite &amp; Customizer</h2>
    <p class="settings-hint">Configura la identidad, icono, colores y comportamiento de tu PWA corporativa.</p>

    <div class="pwa-field-group">
        <label for="pwa-app-name">Nombre de la PWA:</label>
        <input type="text" id="pwa-app-name" value="<?php p($_['appName']); ?>" placeholder="Nextcloud PWA" />
    </div>

    <div class="pwa-field-group" style="margin-top: 15px;">
        <label for="pwa-icon-file">Icono de la PWA (PNG recomendado 512x512):</label>
        <div style="display: flex; align-items: center; gap: 15px; margin-top: 5px;">
            <img id="pwa-icon-preview" src="<?php p($_['iconUrl']); ?>" alt="Icon Preview" style="width: 48px; height: 48px; border-radius: 8px; border: 1px solid var(--color-border);" />
            <input type="file" id="pwa-icon-file" accept="image/png,image/jpeg,image/webp" />
        </div>
    </div>

    <div class="pwa-field-group" style="margin-top: 15px;">
        <label for="pwa-theme-color">Color del tema (Theme Color):</label>
        <input type="color" id="pwa-theme-color" value="<?php p($_['themeColor']); ?>" />
    </div>

    <div class="pwa-field-group">
        <label for="pwa-bg-color">Color de fondo (Splash Background):</label>
        <input type="color" id="pwa-bg-color" value="<?php p($_['bgColor']); ?>" />
    </div>

    <div class="pwa-field-group">
        <label for="pwa-display-mode">Modo de visualización:</label>
        <select id="pwa-display-mode">
            <option value="standalone" <?php if ($_['displayMode'] === 'standalone') p('selected'); ?>>Standalone (App Nativa)</option>
            <option value="minimal-ui" <?php if ($_['displayMode'] === 'minimal-ui') p('selected'); ?>>Minimal UI</option>
            <option value="fullscreen" <?php if ($_['displayMode'] === 'fullscreen') p('selected'); ?>>Fullscreen (Pantalla completa)</option>
            <option value="browser" <?php if ($_['displayMode'] === 'browser') p('selected'); ?>>Browser (Pestaña normal)</option>
        </select>
    </div>

    <div class="pwa-field-group" style="margin-top: 20px;">
        <input type="checkbox" id="pwa-advanced-toggle" <?php if ($_['advancedMode'] === 'yes') p('checked'); ?> />
        <label for="pwa-advanced-toggle">Habilitar Modo Experto (Sobreescritura manual)</label>
    </div>

    <div id="pwa-advanced-section" style="<?php echo $_['advancedMode'] === 'yes' ? 'display: block;' : 'display: none;'; ?> margin-top: 15px;">
        <p class="settings-hint" style="color: #e9322d;">Atención: Los campos siguientes anularán la configuración visual previa si contienen código.</p>
        
        <div class="pwa-field-group">
            <label for="pwa-custom-manifest">Manifest JSON Personalizado:</label>
            <textarea id="pwa-custom-manifest" rows="8" style="width: 100%; font-family: monospace;" placeholder='{ "name": "Mi App" }'><?php p($_['customManifest']); ?></textarea>
        </div>

        <div class="pwa-field-group">
            <label for="pwa-custom-sw">Service Worker JS Personalizado:</label>
            <textarea id="pwa-custom-sw" rows="8" style="width: 100%; font-family: monospace;" placeholder="self.addEventListener('fetch', ...);"><?php p($_['customSw']); ?></textarea>
        </div>
    </div>

    <div style="margin-top: 20px;">
        <button id="pwa-save-btn" class="button primary">Guardar cambios</button>
        <span id="pwa-save-msg" style="margin-left: 10px; font-weight: bold;"></span>
    </div>
</div>
