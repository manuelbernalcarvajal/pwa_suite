<?php
/** @var array $_ */
?>

<div class="section" id="pwa-suite-admin">
    <h2>PWA Suite &amp; Customizer</h2>
    <p class="settings-hint">Configura el comportamiento del Web App Manifest y el Service Worker.</p>

    <!-- MODO ESTÁNDAR -->
    <div id="pwa-basic-settings">
        <p>
            <label for="pwa-app-name">Nombre de la PWA:</label><br>
            <input type="text" id="pwa-app-name" value="<?php p($_['appName']); ?>" class="text">
        </p>
        <p>
            <label for="pwa-theme-color">Color del tema (Theme Color):</label><br>
            <input type="color" id="pwa-theme-color" value="<?php p($_['themeColor']); ?>">
        </p>
        <p>
            <label for="pwa-bg-color">Color de fondo (Splash Background):</label><br>
            <input type="color" id="pwa-bg-color" value="<?php p($_['bgColor']); ?>">
        </p>
        <p>
            <label for="pwa-display-mode">Modo de visualización:</label><br>
            <select id="pwa-display-mode">
                <option value="standalone" <?php if ($_['displayMode'] === 'standalone') p('selected'); ?>>Standalone (App Nativa)</option>
                <option value="fullscreen" <?php if ($_['displayMode'] === 'fullscreen') p('selected'); ?>>Fullscreen (Pantalla completa)</option>
                <option value="minimal-ui" <?php if ($_['displayMode'] === 'minimal-ui') p('selected'); ?>>Minimal UI</option>
                <option value="browser" <?php if ($_['displayMode'] === 'browser') p('selected'); ?>>Browser (Navegador estándar)</option>
            </select>
        </p>
    </div>

    <hr style="margin: 20px 0; border: 0; border-top: 1px solid var(--color-border);">

    <!-- INTERRUPTOR MODO AVANZADO -->
    <p>
        <input type="checkbox" id="pwa-advanced-toggle" class="checkbox" <?php if ($_['advancedMode'] === 'yes') p('checked'); ?>>
        <label for="pwa-advanced-toggle"><strong>Habilitar Modo Experto (Sobreescritura manual)</strong></label>
    </p>

    <!-- SECCIÓN AVANZADA (Oculta por defecto si está en OFF) -->
    <div id="pwa-advanced-section" style="<?php echo ($_['advancedMode'] === 'yes') ? '' : 'display:none;'; ?>">
        <div style="background: rgba(230, 80, 0, 0.15); border-left: 4px solid #e65000; padding: 10px 15px; margin: 15px 0; border-radius: 4px;">
            <strong>⚠️ Advertencia de seguridad y estabilidad:</strong> 
            El modo experto anula la configuración básica. Un JSON mal formado o un Service Worker con errores de script puede impedir que los usuarios carguen la nube o provocar bucles de caché en los navegadores móviles.
        </div>

        <p>
            <label for="pwa-custom-manifest"><strong>Manifest JSON personalizado:</strong></label><br>
            <textarea id="pwa-custom-manifest" rows="8" style="width: 100%; font-family: monospace;" placeholder='{"name": "Mi PWA", "short_name": "PWA", "start_url": "/", "display": "standalone"}'><?php p($_['customManifest']); ?></textarea>
        </p>

        <p>
            <label for="pwa-custom-sw"><strong>Service Worker JS personalizado:</strong></label><br>
            <textarea id="pwa-custom-sw" rows="8" style="width: 100%; font-family: monospace;" placeholder="// Tu código javascript personalizado para el SW"><?php p($_['customSw']); ?></textarea>
        </p>
    </div>

    <p style="margin-top: 20px;">
        <button id="pwa-save-btn" class="button primary">Guardar cambios</button>
        <span id="pwa-save-msg" style="margin-left: 10px; font-weight: bold;"></span>
    </p>
</div>
