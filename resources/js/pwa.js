/**
 * Nexora EMS ERP — PWA Manager
 * Registro do Service Worker, prompt de instalação e notificações push
 */

const NexoraPWA = (() => {
    let deferredPrompt = null;

    // ── Registro do Service Worker ──────────────────────
    function registerSW() {
        if (!('serviceWorker' in navigator)) return;

        window.addEventListener('load', async () => {
            try {
                const reg = await navigator.serviceWorker.register('/sw.js', { scope: '/' });
                console.log('[PWA] Service Worker registrado:', reg.scope);

                // Detectar atualização disponível
                reg.addEventListener('updatefound', () => {
                    const newWorker = reg.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            showUpdateBanner();
                        }
                    });
                });

            } catch (err) {
                console.error('[PWA] Erro ao registrar Service Worker:', err);
            }
        });
    }

    // ── Prompt de Instalação (A2HS) ─────────────────────
    function initInstallPrompt() {
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            showInstallBanner();
        });

        window.addEventListener('appinstalled', () => {
            deferredPrompt = null;
            hideInstallBanner();
            console.log('[PWA] App instalado com sucesso!');
        });
    }

    async function promptInstall() {
        if (!deferredPrompt) return;
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        console.log('[PWA] Escolha do usuário:', outcome);
        deferredPrompt = null;
        hideInstallBanner();
    }

    // ── Banner de Instalação ────────────────────────────
    function showInstallBanner() {
        if (document.getElementById('nx-pwa-install-banner')) return;

        const banner = document.createElement('div');
        banner.id = 'nx-pwa-install-banner';
        banner.innerHTML = `
            <div class="nx-pwa-banner-content">
                <img src="/icons/icon-72x72.png" alt="Nexora" class="nx-pwa-banner-icon">
                <div class="nx-pwa-banner-text">
                    <strong>Instalar Nexora ERP</strong>
                    <span>Acesso rápido sem abrir o navegador</span>
                </div>
                <button id="nx-pwa-install-btn" class="nx-pwa-banner-install">Instalar</button>
                <button id="nx-pwa-dismiss-btn" class="nx-pwa-banner-dismiss" aria-label="Fechar">✕</button>
            </div>
        `;
        banner.className = 'nx-pwa-install-banner';
        document.body.appendChild(banner);

        document.getElementById('nx-pwa-install-btn').addEventListener('click', promptInstall);
        document.getElementById('nx-pwa-dismiss-btn').addEventListener('click', () => {
            hideInstallBanner();
            // Não mostrar novamente por 7 dias
            localStorage.setItem('nx-pwa-dismissed', Date.now() + 7 * 24 * 60 * 60 * 1000);
        });

        // Não mostrar se descartado recentemente
        const dismissed = localStorage.getItem('nx-pwa-dismissed');
        if (dismissed && Date.now() < Number(dismissed)) {
            hideInstallBanner();
        }
    }

    function hideInstallBanner() {
        const banner = document.getElementById('nx-pwa-install-banner');
        if (banner) banner.remove();
    }

    // ── Banner de Atualização ───────────────────────────
    function showUpdateBanner() {
        if (document.getElementById('nx-pwa-update-banner')) return;

        const banner = document.createElement('div');
        banner.id = 'nx-pwa-update-banner';
        banner.innerHTML = `
            <div class="nx-pwa-banner-content">
                <span class="nx-pwa-update-icon">🔄</span>
                <div class="nx-pwa-banner-text">
                    <strong>Atualização disponível</strong>
                    <span>Uma nova versão do Nexora ERP está pronta</span>
                </div>
                <button id="nx-pwa-reload-btn" class="nx-pwa-banner-install">Atualizar</button>
                <button id="nx-pwa-update-dismiss" class="nx-pwa-banner-dismiss" aria-label="Fechar">✕</button>
            </div>
        `;
        banner.className = 'nx-pwa-install-banner nx-pwa-update-banner-style';
        document.body.appendChild(banner);

        document.getElementById('nx-pwa-reload-btn').addEventListener('click', () => {
            window.location.reload();
        });
        document.getElementById('nx-pwa-update-dismiss').addEventListener('click', () => {
            banner.remove();
        });
    }

    // ── Status de Conectividade ─────────────────────────
    function initConnectivityMonitor() {
        function updateStatus(online) {
            let bar = document.getElementById('nx-offline-bar');
            if (!online) {
                if (!bar) {
                    bar = document.createElement('div');
                    bar.id = 'nx-offline-bar';
                    bar.textContent = '⚠ Você está offline. As alterações serão sincronizadas quando a conexão voltar.';
                    bar.className = 'nx-offline-bar';
                    document.body.prepend(bar);
                }
            } else {
                if (bar) {
                    bar.textContent = '✓ Conexão restabelecida! Sincronizando...';
                    bar.classList.add('nx-offline-bar--online');
                    setTimeout(() => bar.remove(), 3000);
                    // Trigger background sync se disponível
                    if ('serviceWorker' in navigator && 'SyncManager' in window) {
                        navigator.serviceWorker.ready.then(reg => {
                            reg.sync.register('nexora-sync-pending').catch(() => {});
                        });
                    }
                }
            }
        }

        window.addEventListener('online',  () => updateStatus(true));
        window.addEventListener('offline', () => updateStatus(false));

        if (!navigator.onLine) updateStatus(false);
    }

    // ── Push Notifications ──────────────────────────────
    async function requestNotificationPermission() {
        if (!('Notification' in window)) return null;
        if (Notification.permission === 'granted') return 'granted';
        if (Notification.permission === 'denied') return 'denied';

        const permission = await Notification.requestPermission();
        return permission;
    }

    async function subscribePush(vapidPublicKey) {
        if (!('serviceWorker' in navigator) || !('PushManager' in window)) return null;
        try {
            const reg = await navigator.serviceWorker.ready;
            const subscription = await reg.pushManager.subscribe({
                userVisibleOnly: true,
                applicationServerKey: urlBase64ToUint8Array(vapidPublicKey),
            });
            return subscription;
        } catch (err) {
            console.error('[PWA] Erro ao subscrever push:', err);
            return null;
        }
    }

    function urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding).replace(/-/g, '+').replace(/_/g, '/');
        const raw = atob(base64);
        return Uint8Array.from([...raw].map(c => c.charCodeAt(0)));
    }

    // ── Init ────────────────────────────────────────────
    function init() {
        registerSW();
        initInstallPrompt();
        initConnectivityMonitor();
    }

    return { init, promptInstall, requestNotificationPermission, subscribePush };
})();

// Inicializa automaticamente
NexoraPWA.init();

export default NexoraPWA;

