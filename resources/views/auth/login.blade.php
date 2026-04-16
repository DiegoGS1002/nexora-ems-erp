<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Nexora ERP</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
        }

        body {
            background-color: #0d1b2e;
            background-image:
                radial-gradient(ellipse 80% 60% at 50% 0%,   rgba(30,90,160,0.30) 0%, transparent 70%),
                radial-gradient(ellipse 60% 50% at 50% 100%, rgba(14,100,140,0.18) 0%, transparent 65%);
            font-family: 'Inter', system-ui, sans-serif;
        }

        /* ── cenário de anéis – FIXO e realmente centralizado ── */
        .nx-stage {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            z-index: 0;
        }

        .nx-ring {
            position: absolute;
            border-radius: 50%;
            border: 1px solid transparent;
        }
        .nx-ring-1 {
            width: 700px; height: 700px;
            border-color: rgba(56,149,220,0.22);
            animation: ripple 10s ease-in-out infinite;
        }
        .nx-ring-2 {
            width: 520px; height: 520px;
            border-color: rgba(30,180,210,0.28);
            animation: ripple 7s ease-in-out infinite reverse;
        }
        .nx-ring-3 {
            width: 350px; height: 350px;
            border-color: rgba(20,190,200,0.20);
            animation: ripple 5s ease-in-out infinite;
        }
        /* brilho central suave */
        .nx-glow-center {
            position: absolute;
            width: 420px; height: 420px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(30,100,180,0.14) 0%, transparent 70%);
        }

        @keyframes ripple {
            0%,100% { transform: scale(1);   opacity: 0.7; }
            50%      { transform: scale(1.04); opacity: 1;   }
        }

        /* ── card ── */
        .nx-card {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 460px;
            margin: auto;
            border-radius: 20px;
            background: rgba(15, 30, 55, 0.72);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(80,140,210,0.18);
            box-shadow:
                0 0 0 1px rgba(255,255,255,0.04) inset,
                0 2px 60px rgba(10,40,100,0.45),
                0 0 120px rgba(14,90,155,0.12);
            padding: 52px 44px 44px;
        }

        /* linha de luz no topo do card */
        .nx-card::before {
            content: '';
            position: absolute;
            top: 0; left: 12%; right: 12%; height: 1px;
            background: linear-gradient(90deg, transparent, rgba(56,180,220,0.55), transparent);
            border-radius: 9999px;
        }

        /* ── inputs ── */
        .nx-input-wrap { position: relative; margin-top: 8px; }
        .nx-input-icon {
            position: absolute;
            left: 14px; top: 50%; transform: translateY(-50%);
            color: #4a9eca;
            display: flex;
        }
        .nx-input {
            width: 100%;
            padding: 13px 16px 13px 44px;
            border-radius: 12px;
            border: 1px solid rgba(60,110,170,0.35);
            background: rgba(8, 20, 42, 0.60);
            color: #e2eaf4;
            font-size: 14px;
            outline: none;
            transition: border-color .2s, box-shadow .2s;
        }
        .nx-input::placeholder { color: #3a5878; }
        .nx-input:focus {
            border-color: rgba(56,160,220,0.65);
            box-shadow: 0 0 0 3px rgba(40,140,210,0.14);
        }

        /* ── label ── */
        .nx-label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.12em;
            color: #6a93b8;
            margin-bottom: 6px;
        }

        /* ── botão ── */
        .nx-btn {
            display: block;
            width: 100%;
            padding: 15px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 0.14em;
            text-transform: uppercase;
            color: #fff;
            background: linear-gradient(90deg, #1a65c8 0%, #13bdd6 100%);
            box-shadow: 0 4px 24px rgba(20,160,210,0.28);
            transition: transform .15s, box-shadow .15s;
        }
        .nx-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 8px 36px rgba(20,180,220,0.38);
        }
        .nx-btn:active { transform: scale(0.98); }

        /* ── alerts ── */
        .nx-alert {
            display: flex; align-items: center; gap: 10px;
            border-radius: 10px;
            border: 1px solid rgba(239,68,68,0.30);
            background: rgba(239,68,68,0.08);
            color: #fca5a5;
            padding: 11px 14px;
            font-size: 13px;
            margin-bottom: 24px;
        }
    </style>
</head>
<body>

    {{-- ── ANÉIS DECORATIVOS – centrados com position:fixed ── --}}
    <div class="nx-stage" aria-hidden="true">
        <div class="nx-glow-center"></div>
        <div class="nx-ring nx-ring-1"></div>
        <div class="nx-ring nx-ring-2"></div>
        <div class="nx-ring nx-ring-3"></div>
    </div>

    {{-- ── LAYOUT CENTRALIZADO ── --}}
    <div style="min-height:100vh; display:flex; align-items:center; justify-content:center; padding:24px; position:relative; z-index:1;">

        <div class="nx-card">

            {{-- BRANDING --}}
            <div style="text-align:center; margin-bottom:40px;">
                <p style="font-size:11px;font-weight:600;letter-spacing:.22em;text-transform:uppercase;color:#3e7aaa;margin-bottom:14px;">
                    Sistema de Gestão Empresarial
                </p>
                <h1 style="font-size:42px;font-weight:800;letter-spacing:-0.03em;color:#e8f2fc;margin:0;line-height:1;">
                    NE<span style="color:#1cc8df;">X</span>ORA
                </h1>
                <p style="margin-top:10px;font-size:12px;color:#4a7498;letter-spacing:.05em;">
                    Enterprise Management System
                </p>
            </div>

            {{-- ALERTAS --}}
            @session('error')
                <div class="nx-alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $value }}
                </div>
            @endsession

            @if($errors->any())
                <div class="nx-alert">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $errors->first('email') ?: 'E-mail ou senha inválidos. Verifique e tente novamente.' }}
                </div>
            @endif

            {{-- FORMULÁRIO --}}
            <form action="{{ route('login.store') }}" method="POST">
                @csrf

                {{-- Campo e-mail --}}
                <div style="margin-bottom:24px;">
                    <label for="email" class="nx-label">Usuário / E-mail</label>
                    <div class="nx-input-wrap">
                        <span class="nx-input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </span>
                        <input
                            id="email" name="email" type="email"
                            value="{{ old('email') }}"
                            placeholder="seu@email.com"
                            required autofocus
                            class="nx-input">
                    </div>
                </div>

                {{-- Campo senha --}}
                <div style="margin-bottom:32px;">
                    <label for="password" class="nx-label">Senha</label>
                    <div class="nx-input-wrap">
                        <span class="nx-input-icon">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        </span>
                        <input
                            id="password" name="password" type="password"
                            placeholder="••••••••"
                            required
                            class="nx-input">
                    </div>
                </div>

                {{-- Botão --}}
                <button type="submit" class="nx-btn">
                    Entrar no Sistema
                </button>
            </form>

            {{-- RODAPÉ --}}
            <div style="margin-top:36px;padding-top:20px;border-top:1px solid rgba(255,255,255,0.06);text-align:center;">
                <p style="font-size:10px;text-transform:uppercase;letter-spacing:.18em;color:#243d58;">
                    © {{ date('Y') }} Nexora Systems · Ubá-MG
                </p>
            </div>

        </div>
    </div>

    {{-- Botão de suporte fixo no canto inferior direito --}}
    <a
        href="https://wa.me/5532984502345?text=Olá,%20preciso%20de%20suporte%20no%20Nexora%20ERP"
        target="_blank"
        rel="noopener noreferrer"
        title="Falar com o Suporte"
        style="
            position: fixed;
            bottom: 24px;
            right: 24px;
            z-index: 100;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            background: linear-gradient(135deg, #0EA5E9, #0284C7);
            box-shadow: 0 4px 20px rgba(14,165,233,0.45), 0 2px 8px rgba(0,0,0,0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            text-decoration: none;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        "
        onmouseover="this.style.transform='scale(1.1)';this.style.boxShadow='0 6px 28px rgba(14,165,233,0.6), 0 2px 8px rgba(0,0,0,0.3)'"
        onmouseout="this.style.transform='scale(1)';this.style.boxShadow='0 4px 20px rgba(14,165,233,0.45), 0 2px 8px rgba(0,0,0,0.3)'"
    >
        <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
        </svg>
    </a>

    {{-- Tooltip "Suporte" --}}
    <div style="
        position:fixed; bottom:82px; right:18px; z-index:101;
        background:rgba(15,23,42,0.85); color:#fff;
        font-size:11px; font-weight:600; letter-spacing:.04em;
        padding:5px 10px; border-radius:6px;
        pointer-events:none; white-space:nowrap;
        opacity:0; transition:opacity 0.2s;
        font-family:'Inter',sans-serif;
    " id="nx-support-tooltip">Suporte</div>

    <script>
    (function(){
        var btn = document.querySelector('a[title="Falar com o Suporte"]');
        var tip = document.getElementById('nx-support-tooltip');
        if(btn && tip){
            btn.addEventListener('mouseenter', function(){ tip.style.opacity='1'; });
            btn.addEventListener('mouseleave', function(){ tip.style.opacity='0'; });
        }
    })();
    </script>

</body>
</html>

