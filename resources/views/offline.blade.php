<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0B1220">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">
    <link rel="icon" type="image/png" sizes="32x32" href="/icons/favicon-32x32.png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <title>Sem conexão — Nexora ERP</title>
    <link rel="icon" href="/favicon.ico">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #0B1220;
            color: #E2E8F0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            text-align: center;
        }

        .offline-icon {
            width: 80px;
            height: 80px;
            background: rgba(37, 99, 235, 0.15);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            border: 2px solid rgba(37, 99, 235, 0.3);
        }

        .offline-icon svg {
            width: 40px;
            height: 40px;
            color: #2563EB;
            stroke: #2563EB;
        }

        .logo {
            width: 64px;
            height: 64px;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            opacity: 0.9;
        }

        h1 {
            font-size: 1.75rem;
            font-weight: 700;
            color: #F1F5F9;
            margin-bottom: 0.75rem;
        }

        p {
            font-size: 1rem;
            color: #94A3B8;
            max-width: 400px;
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: center;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-size: 0.9rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            transition: all 0.2s ease;
            border: none;
        }

        .btn-primary {
            background: #2563EB;
            color: #fff;
        }

        .btn-primary:hover { background: #1D4ED8; }

        .btn-secondary {
            background: rgba(255,255,255,0.07);
            color: #CBD5E1;
            border: 1px solid rgba(255,255,255,0.1);
        }

        .btn-secondary:hover { background: rgba(255,255,255,0.12); }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            background: rgba(239, 68, 68, 0.15);
            color: #FCA5A5;
            border: 1px solid rgba(239, 68, 68, 0.25);
            border-radius: 999px;
            padding: 0.35rem 0.85rem;
            font-size: 0.8rem;
            font-weight: 500;
            margin-bottom: 2rem;
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #EF4444;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        .cached-info {
            margin-top: 2.5rem;
            background: rgba(37, 99, 235, 0.08);
            border: 1px solid rgba(37, 99, 235, 0.2);
            border-radius: 12px;
            padding: 1.25rem 1.5rem;
            max-width: 400px;
            font-size: 0.85rem;
            color: #93C5FD;
            line-height: 1.6;
        }

        .cached-info strong { color: #BFDBFE; }
    </style>
</head>
<body>

    <img src="/icons/icon-128x128.png" alt="Nexora" class="logo">

    <div class="status-badge">
        <span class="status-dot"></span>
        Sem conexão com a internet
    </div>

    <div class="offline-icon">
        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
            <line x1="1" y1="1" x2="23" y2="23"/>
            <path d="M16.72 11.06A10.94 10.94 0 0 1 19 12.55"/>
            <path d="M5 12.55a10.94 10.94 0 0 1 5.17-2.39"/>
            <path d="M10.71 5.05A16 16 0 0 1 22.56 9"/>
            <path d="M1.42 9a15.91 15.91 0 0 1 4.7-2.88"/>
            <path d="M8.53 16.11a6 6 0 0 1 6.95 0"/>
            <line x1="12" y1="20" x2="12.01" y2="20"/>
        </svg>
    </div>

    <h1>Você está offline</h1>
    <p>
        Não foi possível conectar ao Nexora ERP.<br>
        Verifique sua conexão com a internet e tente novamente.
    </p>

    <div class="actions">
        <button class="btn btn-primary" onclick="tryReload()">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="23 4 23 10 17 10"/>
                <path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>
            </svg>
            Tentar novamente
        </button>
        <a href="/" class="btn btn-secondary">
            Ir para o início
        </a>
    </div>

    <div class="cached-info">
        <strong>💡 Modo offline parcial disponível</strong><br>
        Algumas páginas visitadas recentemente podem estar disponíveis no cache local.
        Quando a conexão voltar, as alterações serão sincronizadas automaticamente.
    </div>

    <script>
        function tryReload() {
            if (navigator.onLine) {
                window.location.reload();
            } else {
                const btn = document.querySelector('.btn-primary');
                const orig = btn.innerHTML;
                btn.innerHTML = '⚠ Ainda sem conexão';
                btn.style.background = '#92400E';
                setTimeout(() => {
                    btn.innerHTML = orig;
                    btn.style.background = '';
                }, 2500);
            }
        }

        // Recarrega automaticamente quando a conexão voltar
        window.addEventListener('online', () => {
            window.location.reload();
        });
    </script>

</body>
</html>

