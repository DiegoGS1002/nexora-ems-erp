<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Nexora ERP — Sistema de Gestão Empresarial">

    <title>{{ $title ?? trim($__env->yieldContent('title', 'Nexora ERP')) }} | Nexora</title>

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
                <div class="alert-success">
                    ✓ {{ $value }}
                </div>
            @endsession

            @session('error')
                <div class="alert-error">
                    ✕ {{ $value }}
                </div>
            @endsession

            {{-- Conteúdo Principal --}}
            <main class="main-content">
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

        <div id="nx-license-overlay" class="nx-license-overlay" role="dialog" aria-modal="true" aria-labelledby="nx-license-title">
            <div class="nx-license-modal">

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
                    <a href="https://wa.me/5500000000000" target="_blank" rel="noopener noreferrer"
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

            var overlay   = document.getElementById('nx-license-overlay');
            var bar       = document.getElementById('nx-license-bar');
            var timerEl   = document.getElementById('nx-license-timer');
            var secondsEl = document.getElementById('nx-license-seconds');

            var autoCloseTimer, countdownInterval, reopenTimer;

            function showModal() {
                if (!overlay) return;
                clearTimeout(reopenTimer);
                clearInterval(countdownInterval);
                timerEl.style.display = 'none';

                overlay.classList.add('nx-license-overlay--visible');

                // Barra de progresso
                bar.classList.remove('nx-running');
                bar.style.animationDuration = '';
                void bar.offsetWidth;
                bar.style.animationDuration = SHOW_MS + 'ms';
                bar.classList.add('nx-running');

                // Auto-fecha após SHOW_MS
                clearTimeout(autoCloseTimer);
                autoCloseTimer = setTimeout(function () {
                    closeModal();
                }, SHOW_MS);
            }

            function closeModal() {
                if (!overlay) return;
                overlay.classList.remove('nx-license-overlay--visible');
                bar.classList.remove('nx-running');
                clearTimeout(autoCloseTimer);

                // Contagem regressiva visível
                var remaining = CYCLE_S;
                timerEl.style.display = 'flex';
                secondsEl.textContent = remaining;

                clearInterval(countdownInterval);
                countdownInterval = setInterval(function () {
                    remaining--;
                    if (remaining <= 0) {
                        clearInterval(countdownInterval);
                        timerEl.style.display = 'none';
                        return;
                    }
                    secondsEl.textContent = remaining;
                }, 1000);

                // Reabre após CYCLE_S segundos
                clearTimeout(reopenTimer);
                reopenTimer = setTimeout(function () {
                    clearInterval(countdownInterval);
                    timerEl.style.display = 'none';
                    showModal();
                }, CYCLE_S * 1000);
            }

            // Expõe para o botão "Fechar"
            window.nxLicenseClose = closeModal;

            // Fecha ao clicar no overlay (fora do modal)
            overlay.addEventListener('click', function (e) {
                if (e.target === overlay) closeModal();
            });

            // Primeira exibição: 1,5 s após carregar
            setTimeout(showModal, 1500);
        })();
        </script>
        @endif
    @endauth
</body>
</html>
