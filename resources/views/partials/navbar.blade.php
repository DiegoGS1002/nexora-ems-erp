{{-- ============================================================
     SIDEBAR PRINCIPAL — Nexora ERP
     ============================================================ --}}
<aside class="nx-sidebar" id="nx-sidebar">
    {{-- ── LOGO ── --}}
    <div class="nx-sb-brand">
        <a href="{{ url('/') }}" class="nx-sb-brand-link">
            <img src="{{ app()->environment() === 'production' ? secure_asset('images/logo.png') : asset('images/logo.png') }}"
                 alt="Nexora ERP" class="nx-sb-logo">
            <span class="nx-sb-brand-text">
                Nexora <span class="nx-sb-brand-hl">ERP</span>
            </span>
        </a>
        <button class="nx-sb-toggle" id="nx-sb-toggle" onclick="nxSidebarToggle()" aria-label="Recolher menu" title="Recolher menu">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        </button>
    </div>
    <div class="nx-sb-sep"></div>
    {{-- ── NAVEGAÇÃO ── --}}
    @php
        $navUserModules = auth()->user()->modules ?? [];
        $navIsAdmin     = auth()->user()->is_admin;
        // Admins sem módulos definidos vêem tudo; não-admins vêem só os contratados
        $navShowAll = $navIsAdmin && empty($navUserModules);
    @endphp
    <nav class="nx-sb-nav">
        <ul class="nx-sb-list">
            <li class="nx-sb-item">
                <a href="{{ route('home') }}" class="nx-sb-link {{ request()->routeIs('home') ? 'nx-sb-active' : '' }}">
                    <span class="nx-sb-icon"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg></span>
                    <span class="nx-sb-label">Início</span>
                </a>
            </li>
            @if($navShowAll || in_array('dashboard', $navUserModules))
            <li class="nx-sb-item">
                <a href="{{ route('module.show', 'dashboard') }}" class="nx-sb-link {{ request()->is('modulo/dashboard') ? 'nx-sb-active' : '' }}" style="--sb-accent:#3B82F6">
                    <span class="nx-sb-icon" style="color:#3B82F6"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg></span>
                    <span class="nx-sb-label">Dashboard</span>
                </a>
            </li>
            @endif
            @if($navShowAll || in_array('cadastro', $navUserModules))
            <li class="nx-sb-item">
                <a href="{{ route('module.show', 'cadastro') }}" class="nx-sb-link {{ request()->is('modulo/cadastro') ? 'nx-sb-active' : '' }}" style="--sb-accent:#8B5CF6">
                    <span class="nx-sb-icon" style="color:#8B5CF6"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg></span>
                    <span class="nx-sb-label">Cadastro</span>
                </a>
            </li>
            @endif
            @if($navShowAll || in_array('producao', $navUserModules))
            <li class="nx-sb-item">
                <a href="{{ route('module.show', 'producao') }}" class="nx-sb-link {{ request()->is('modulo/producao') ? 'nx-sb-active' : '' }}" style="--sb-accent:#F59E0B">
                    <span class="nx-sb-icon" style="color:#F59E0B"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22V8"/><path d="m20 12-8-4-8 4"/><path d="M20 17v-5"/><path d="M4 17v-5"/><path d="M20 22v-5"/><path d="M4 22v-5"/></svg></span>
                    <span class="nx-sb-label">Produção</span>
                </a>
            </li>
            @endif
            @if($navShowAll || in_array('estoque', $navUserModules))
            <li class="nx-sb-item">
                <a href="{{ route('module.show', 'estoque') }}" class="nx-sb-link {{ request()->is('modulo/estoque') ? 'nx-sb-active' : '' }}" style="--sb-accent:#10B981">
                    <span class="nx-sb-icon" style="color:#10B981"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg></span>
                    <span class="nx-sb-label">Estoque</span>
                </a>
            </li>
            @endif
            @if($navShowAll || in_array('vendas', $navUserModules))
            <li class="nx-sb-item">
                <a href="{{ route('module.show', 'vendas') }}" class="nx-sb-link {{ request()->is('modulo/vendas') ? 'nx-sb-active' : '' }}" style="--sb-accent:#06B6D4">
                    <span class="nx-sb-icon" style="color:#06B6D4"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg></span>
                    <span class="nx-sb-label">Vendas</span>
                </a>
            </li>
            @endif
            @if($navShowAll || in_array('compras', $navUserModules))
            <li class="nx-sb-item">
                <a href="{{ route('module.show', 'compras') }}" class="nx-sb-link {{ request()->is('modulo/compras') ? 'nx-sb-active' : '' }}" style="--sb-accent:#F97316">
                    <span class="nx-sb-icon" style="color:#F97316"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg></span>
                    <span class="nx-sb-label">Compras</span>
                </a>
            </li>
            @endif
            @if($navShowAll || in_array('fiscal', $navUserModules))
            <li class="nx-sb-item">
                <a href="{{ route('module.show', 'fiscal') }}" class="nx-sb-link {{ request()->is('modulo/fiscal') ? 'nx-sb-active' : '' }}" style="--sb-accent:#EC4899">
                    <span class="nx-sb-icon" style="color:#EC4899"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></span>
                    <span class="nx-sb-label">Fiscal</span>
                </a>
            </li>
            @endif
            @if($navShowAll || in_array('financeiro', $navUserModules))
            <li class="nx-sb-item">
                <a href="{{ route('module.show', 'financeiro') }}" class="nx-sb-link {{ request()->is('modulo/financeiro') ? 'nx-sb-active' : '' }}" style="--sb-accent:#22C55E">
                    <span class="nx-sb-icon" style="color:#22C55E"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg></span>
                    <span class="nx-sb-label">Financeiro</span>
                </a>
            </li>
            @endif
            @if($navShowAll || in_array('rh', $navUserModules))
            <li class="nx-sb-item">
                <a href="{{ route('module.show', 'rh') }}" class="nx-sb-link {{ request()->is('modulo/rh') ? 'nx-sb-active' : '' }}" style="--sb-accent:#A78BFA">
                    <span class="nx-sb-icon" style="color:#A78BFA"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
                    <span class="nx-sb-label">RH</span>
                </a>
            </li>
            @endif
            @if($navShowAll || in_array('transporte', $navUserModules))
            <li class="nx-sb-item">
                <a href="{{ route('module.show', 'transporte') }}" class="nx-sb-link {{ request()->is('modulo/transporte') ? 'nx-sb-active' : '' }}" style="--sb-accent:#0EA5E9">
                    <span class="nx-sb-icon" style="color:#0EA5E9"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg></span>
                    <span class="nx-sb-label">Transporte</span>
                </a>
            </li>
            @endif
            @if(auth()->user()->is_admin)
            <li class="nx-sb-item">
                <a href="{{ route('module.show', 'administracao') }}" class="nx-sb-link {{ request()->is('modulo/administracao') || request()->is('empresas*') ? 'nx-sb-active' : '' }}" style="--sb-accent:#6366F1">
                    <span class="nx-sb-icon" style="color:#6366F1"><svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg></span>
                    <span class="nx-sb-label">Administração</span>
                </a>
            </li>
            @endif
        </ul>
    </nav>
    {{-- ── ZONA DO USUÁRIO ── --}}
    <div class="nx-sb-user">
        <div class="nx-sb-sep"></div>
        <div class="nx-sb-user-actions">
            @auth
                <livewire:notification-dropdown />
            @endauth
        </div>
        <div class="nx-sb-profile nx-has-dropdown" id="nx-sb-profile">
            <button class="nx-sb-profile-btn nx-dropdown-trigger" data-dropdown="usuario">
                @if(auth()->user()->avatar)
                    <img src="{{ Storage::url(auth()->user()->avatar) }}"
                         alt="{{ auth()->user()->name }}" class="nx-sb-avatar">
                @else
                    <span class="nx-sb-avatar" style="background:linear-gradient(135deg,#3B82F6,#6366F1);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;border:2px solid #3B82F6;">
                        {{ strtoupper(substr(auth()->user()->name, 0, 2)) }}
                    </span>
                @endif
                <span class="nx-sb-label nx-sb-profile-info">
                    <span class="nx-sb-profile-name">{{ auth()->user()->name }}</span>
                    <span class="nx-sb-profile-role">{{ auth()->user()->is_admin ? 'Administrador' : 'Usuário' }}</span>
                </span>
                <svg class="nx-chevron nx-sb-label" xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="6 9 12 15 18 9"/></svg>
            </button>
            <div class="nx-dropdown nx-sb-user-menu" id="dropdown-usuario">
                <div class="nx-dropdown-header">Minha Conta</div>
                <a href="{{ route('profile.index') }}" class="nx-dropdown-item {{ request()->routeIs('profile.*') ? 'nx-dropdown-item--active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    Meu Perfil
                </a>
                <a href="{{ route('notifications.index') }}" class="nx-dropdown-item {{ request()->routeIs('notifications.*') ? 'nx-dropdown-item--active' : '' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 0 1-3.46 0"/></svg>
                    Notificações
                </a>
                @if(auth()->user()->is_admin)
                    <a href="{{ route('users.index') }}" class="nx-dropdown-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        Usuários
                    </a>
                    <a href="{{ route('permissions.index') }}" class="nx-dropdown-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                        Permissões
                    </a>
                    <a href="{{ route('logs.index') }}" class="nx-dropdown-item">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        Logs do Sistema
                    </a>
                @endif
                <a href="{{ route('configuration.index') }}" class="nx-dropdown-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93A10 10 0 1 1 4.93 19.07 10 10 0 0 1 19.07 4.93z"/></svg>
                    Configurações
                </a>
                <div class="nx-dropdown-divider"></div>
                <a href="{{ route('suporte.chat') }}" class="nx-dropdown-item">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a2 2 0 0 1 2 2c0 .74-.4 1.39-1 1.73V7h1a7 7 0 0 1 7 7h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v1a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-1H2a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h1a7 7 0 0 1 7-7h1V5.73c-.6-.34-1-.99-1-1.73a2 2 0 0 1 2-2z"/><circle cx="9" cy="13" r="1"/><circle cx="15" cy="13" r="1"/></svg>
                    Suporte IA
                </a>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="nx-dropdown-item nx-dropdown-danger" style="width:100%;text-align:left;">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Sair
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
{{-- ── OVERLAY MOBILE ── --}}
<div class="nx-overlay" id="nx-overlay" onclick="nxSidebarClose()"></div>
{{-- ── TOPBAR MOBILE ── --}}
<header class="nx-topbar" id="nx-topbar">
    <button class="nx-topbar-hamburger" onclick="nxSidebarOpen()" aria-label="Abrir menu">
        <span></span><span></span><span></span>
    </button>
    <a href="{{ url('/') }}" class="nx-topbar-brand">
        <img src="{{ app()->environment() === 'production' ? secure_asset('images/logo.png') : asset('images/logo.png') }}"
             alt="Nexora" class="nx-topbar-logo">
        <span>Nexora <strong>ERP</strong></span>
    </a>
    @auth
        <livewire:notification-dropdown />
    @endauth
</header>
