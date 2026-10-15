<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Nexora ERP — Sistema de Gestão Empresarial">

    {{-- ═══════════════════════════════════════════
         FAVICON — aba do navegador (logo Nexora)
         ═══════════════════════════════════════════ --}}
    <link rel="icon" type="image/x-icon"  href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="16x16"  href="/pwa-icons/favicon-16x16.png">
    <link rel="icon" type="image/png" sizes="32x32"  href="/pwa-icons/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="192x192" href="/pwa-icons/icon-192x192.png">
    <link rel="shortcut icon" href="/favicon.ico">

    {{-- ═══════════════════════════════════════════
         PWA — instalação desktop & mobile
         ═══════════════════════════════════════════ --}}
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0B1220">
    <meta name="application-name" content="Nexora ERP">

    {{-- Android / Chrome --}}
    <meta name="mobile-web-app-capable" content="yes">

    {{-- iOS Safari — ícone home screen & splash --}}
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="Nexora ERP">
    <link rel="apple-touch-icon"              href="/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="apple-touch-icon" sizes="152x152" href="/pwa-icons/icon-152x152.png">
    <link rel="apple-touch-icon" sizes="144x144" href="/pwa-icons/icon-144x144.png">
    <link rel="apple-touch-icon" sizes="128x128" href="/pwa-icons/icon-128x128.png">

    {{-- Windows / Edge pinned tile --}}
    <meta name="msapplication-TileColor"    content="#0B1220">
    <meta name="msapplication-TileImage"    content="/pwa-icons/icon-144x144.png">
    <meta name="msapplication-config"       content="none">

    <title>{{ $title ?? trim($__env->yieldContent('title', 'Nexora ERP')) }} — Nexora</title>

    {{-- Google Fonts — Inter --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    {{-- Fonts & Styles --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @stack('styles')
</head>
<body>

    {{-- Wrapper global --}}
    <div class="nx-app-wrapper" id="nx-app-wrapper">

        {{-- Sidebar Principal --}}
        @include('partials.navbar')

        {{-- Área principal --}}
        <div class="nx-page" id="nx-page">

            {{-- Alertas de sessão --}}
            @session('success')
                <div class="nx-alert nx-alert--success">
                    ✓ {{ $value }}
                </div>
            @endsession

            @session('error')
                <div class="nx-alert nx-alert--error">
                    ✕ {{ $value }}
                </div>
            @endsession

            {{-- Conteúdo Principal --}}
            <main @class([
                'main-content',
                'main-content--catalog-list' => request()->routeIs(
                    'products.index',
                    'product-categories.index',
                    'unit-of-measures.index',
                ),
            ])>
                @yield('content')
                {{ $slot ?? '' }}
            </main>

        </div>{{-- /.nx-page --}}

    </div>{{-- /.nx-app-wrapper --}}

    {{-- AI Assistant Chat Bubble --}}
    @auth
        <livewire:ai-chat-bubble :module="request()->segment(1) ?? 'suporte'" />
    @endauth

    @livewireScripts
    @stack('scripts')

    {{-- ══════════════════════════════════════════
         AVISO DE LICENÇA — Modal central
         Exibido apenas para usuários: ativos, não-admin e sem licença paga
         ══════════════════════════════════════════ --}}
    @auth
        @if(! auth()->user()->is_admin && auth()->user()->is_active && ! auth()->user()->has_license)
        {{-- Modal styling is handled by the design system CSS (resources/css/_components.css). Removed inline critical styles to keep markup consistent with system design. --}}

            <div id="nx-license-overlay" class="nx-license-overlay" role="dialog" aria-modal="true" aria-labelledby="nx-license-title"
                 style="position:fixed;top:0;left:0;right:0;bottom:0;width:100vw;height:100vh;
                        display:flex;align-items:center;justify-content:center;
                        z-index:99999;background:rgba(15,23,42,0.70);
                        backdrop-filter:blur(5px);-webkit-backdrop-filter:blur(5px);
                        margin:0;padding:20px;box-sizing:border-box;
                        opacity:0;pointer-events:none;transition:opacity 0.28s ease;">
                <div class="nx-license-modal" style="position:relative;width:100%;max-width:520px;background:#fff;border-radius:16px;overflow:hidden;box-shadow:0 24px 64px rgba(2,6,23,0.36);border:1px solid rgba(245,158,11,0.18);">

                    {{-- Botão X — fechar --}}
                    <button onclick="nxLicenseClose()" class="nx-license-modal-x" aria-label="Fechar aviso">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                        </svg>
                    </button>

                    {{-- Ícone de alerta centralizado --}}
                    <div class="nx-license-modal-icon-wrap">
                        <div class="nx-license-modal-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/>
                                <line x1="12" y1="9" x2="12" y2="13"/>
                                <line x1="12" y1="17" x2="12.01" y2="17"/>
                            </svg>
                        </div>
                    </div>

                    {{-- Corpo --}}
                    <div class="nx-license-modal-body">
                        <p id="nx-license-title" class="nx-license-modal-title">Licença não regularizada</p>
                        <p class="nx-license-modal-subtitle">Nexora ERP &middot; Aviso do sistema</p>

                        <p class="nx-license-modal-msg">
                            Você está utilizando o <strong>Nexora ERP</strong> sem uma licença ativa.<br><br>
                            Para continuar usando o sistema de forma livre e sem interrupções,
                            regularize sua licença junto ao suporte.
                        </p>
                        <p class="nx-license-modal-note">
                            Enquanto a licença não for regularizada, este aviso continuará aparecendo periodicamente.
                        </p>
                    </div>

                    {{-- Rodapé --}}
                    <div class="nx-license-modal-footer">
                        <a href="https://wa.me/5532984502345" target="_blank" rel="noopener noreferrer"
                           class="nx-license-modal-btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 13a19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 3.6 2.18h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 9.91a16 16 0 0 0 6.18 6.18l1.44-.45a2 2 0 0 1 2.11.45c.907.339 1.85.573 2.81.7a2 2 0 0 1 1.72 2z"/>
                            </svg>
                            Falar com o Suporte
                        </a>
                        <button onclick="nxLicenseClose()" class="nx-license-modal-close">
                            Entendi, fechar
                        </button>
                        <div class="nx-license-modal-timer" id="nx-license-timer" style="display:none;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                            </svg>
                            Próximo aviso em <span id="nx-license-seconds">15</span>s
                        </div>
                    </div>

                    {{-- Barra de progresso --}}
                    <div class="nx-license-modal-progress">
                        <div class="nx-license-modal-progress-bar" id="nx-license-bar"></div>
                    </div>

                </div>
            </div>

            <script>
            (function () {
                var SHOW_MS   = 8000;   // visível 8 s, depois fecha sozinho
                var CYCLE_S   = 15;     // segundos até reaparecer após fechar

                var autoCloseTimer, countdownInterval, reopenTimer;

                function getEls() {
                    return {
                        overlay:   document.getElementById('nx-license-overlay'),
                        bar:       document.getElementById('nx-license-bar'),
                        timerEl:   document.getElementById('nx-license-timer'),
                        secondsEl: document.getElementById('nx-license-seconds'),
                    };
                }

                function showModal() {
                    var els = getEls();
                    var overlay = els.overlay;
                    var bar = els.bar;
                    var timerEl = els.timerEl;
                    if (!overlay) return;
                    clearTimeout(reopenTimer);
                    clearInterval(countdownInterval);
                    if (timerEl) timerEl.style.display = 'none';

                    // Garantir posicionamento correto via inline style (irrefutável)
                    overlay.style.cssText = 'position:fixed!important;top:0!important;left:0!important;right:0!important;bottom:0!important;' +
                        'width:100vw!important;height:100vh!important;z-index:99999!important;' +
                        'display:flex!important;align-items:center!important;justify-content:center!important;' +
                        'background:rgba(15,23,42,0.70);backdrop-filter:blur(5px);-webkit-backdrop-filter:blur(5px);' +
                        'margin:0!important;padding:20px;box-sizing:border-box;' +
                        'opacity:1;pointer-events:all;transition:opacity 0.28s ease;';

                    overlay.classList.add('nx-license-overlay--visible');

                    // Barra de progresso
                    if (bar) {
                        bar.classList.remove('nx-running');
                        bar.style.animationDuration = '';
                        void bar.offsetWidth;
                        bar.style.animationDuration = SHOW_MS + 'ms';
                        bar.classList.add('nx-running');
                    }

                    // Auto-fecha após SHOW_MS
                    clearTimeout(autoCloseTimer);
                    autoCloseTimer = setTimeout(function () {
                        closeModal();
                    }, SHOW_MS);
                }

                function closeModal() {
                    var els = getEls();
                    var overlay = els.overlay;
                    var bar = els.bar;
                    var timerEl = els.timerEl;
                    var secondsEl = els.secondsEl;
                    if (!overlay) return;
                    overlay.classList.remove('nx-license-overlay--visible');
                    overlay.style.opacity = '0';
                    overlay.style.pointerEvents = 'none';
                    if (bar) bar.classList.remove('nx-running');
                    clearTimeout(autoCloseTimer);

                    // Contagem regressiva visível
                    var remaining = CYCLE_S;
                    if (timerEl) timerEl.style.display = 'flex';
                    if (secondsEl) secondsEl.textContent = remaining;

                    clearInterval(countdownInterval);
                    countdownInterval = setInterval(function () {
                        remaining--;
                        if (remaining <= 0) {
                            clearInterval(countdownInterval);
                            var te = document.getElementById('nx-license-timer');
                            if (te) te.style.display = 'none';
                            return;
                        }
                        var se = document.getElementById('nx-license-seconds');
                        if (se) se.textContent = remaining;
                    }, 1000);

                    // Reabre após CYCLE_S segundos
                    clearTimeout(reopenTimer);
                    reopenTimer = setTimeout(function () {
                        clearInterval(countdownInterval);
                        var te = document.getElementById('nx-license-timer');
                        if (te) te.style.display = 'none';
                        showModal();
                    }, CYCLE_S * 1000);
                }

                // Expõe para o botão "Fechar"
                window.nxLicenseClose = closeModal;

                // Fecha ao clicar no overlay (fora do modal)
                function bindOverlayClick() {
                    var ov = document.getElementById('nx-license-overlay');
                    if (ov && !ov._nxBound) {
                        ov._nxBound = true;
                        ov.addEventListener('click', function (e) {
                            if (e.target === ov) closeModal();
                        });
                    }
                }
                bindOverlayClick();

                // Primeira exibição: 1,5 s após carregar
                setTimeout(showModal, 1500);

                // Re-inicializar após wire:navigate (Livewire SPA)
                document.addEventListener('livewire:navigated', function () {
                    clearTimeout(autoCloseTimer);
                    clearTimeout(reopenTimer);
                    clearInterval(countdownInterval);
                    bindOverlayClick();
                    setTimeout(showModal, 1500);
                });
            })();
            </script>
        @endif
    @endauth
</body>
</html>
