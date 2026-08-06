<?php
/** @var array $_ */
// Cargamos los estilos nativos de la interfaz de Nextcloud
script('pwa_suite', 'admin-script');
style('pwa_suite', 'admin-style');
?>

<div class="section" id="pwa-suite-admin">
    <h2>Configuración de PWA Suite</h2>
    <p class="settings-hint">Personaliza los parámetros de la Progressive Web App para los usuarios de este servidor.</p>

    <form id="pwa-suite-settings-form">
        <p>
            <label for="pwa_app_name">Nombre de la App PWA</label><br>
            <input type="text" id="pwa_app_name" name="app_name" value="<?php p($_['appName']); ?>" class="pwa-input" />
        </p>

        <p>
            <label for="pwa_theme_color">Color del Tema (Barra de estado / Ventana)</label><br>
            <input type="color" id="pwa_theme_color" name="theme_color" value="<?php p($_['themeColor']); ?>" />
        </p>

        <p>
            <label for="pwa_bg_color">Color de Fondo de Pantalla de Carga</label><br>
            <input type="color" id="pwa_bg_color" name="bg_color" value="<?php p($_['bgColor']); ?>" />
        </p>

        <p>
            <label for="pwa_display_mode">Modo de Visualización</label><br>
            <select id="pwa_display_mode" name="display_mode">
                <option value="standalone" <?php echo $_['displayMode'] === 'standalone' ? 'selected' : ''; ?>>Standalone (Ventana independiente)</option>
                <option value="minimal-ui" <?php echo $_['displayMode'] === 'minimal-ui' ? 'selected' : ''; ?>>Minimal UI (Controles básicos de navegación)</option>
                <option value="fullscreen" <?php echo $_['displayMode'] === 'fullscreen' ? 'selected' : ''; ?>>Fullscreen (Pantalla completa)</option>
            </select>
        </p>

        <p>
            <button type="submit" class="button primary">Guardar Configuración PWA</button>
            <span id="pwa-status-message"></span>
        </p>
    </form>
</div>
