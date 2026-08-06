if ('serviceWorker' in navigator) {
    window.addEventListener('load', () => {
        navigator.serviceWorker.register('/apps/pwa_suite/sw.js')
            .then(reg => console.log('PWA Suite SW registrado con éxito:', reg.scope))
            .catch(err => console.error('Error al registrar SW en PWA Suite:', err));
    });
}
