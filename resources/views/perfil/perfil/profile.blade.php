@extends('layouts.app')

@section('title', 'Meu Perfil')

@section('content')

{{-- ══════════════════════════════════════════════════════════════
     PÁGINA DE PERFIL DO USUÁRIO — Nexora ERP
     ══════════════════════════════════════════════════════════════ --}}

<div class="nx-list-page" id="nx-profile-page">

    {{-- ── PAGE HEADER ─────────────────────────────────────────── --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <h1 class="nx-page-title">Meu Perfil</h1>
            <p class="nx-page-subtitle">Gerencie suas informações pessoais, segurança e preferências da conta.</p>
        </div>
        <div class="nx-page-actions">
            <a href="{{ route('home') }}" class="nx-btn nx-btn-outline">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="15 18 9 12 15 6"/>
                </svg>
                Voltar ao Início
            </a>
        </div>
    </div>

    {{-- ── LAYOUT PRINCIPAL: Avatar Sidebar + Conteúdo ─────────── --}}
    <div class="nx-profile-layout">

        {{-- ════════════════════════════════
             COLUNA ESQUERDA — Identidade
             ════════════════════════════════ --}}
        <aside class="nx-profile-sidebar">

            {{-- Card de Avatar --}}
            <div class="nx-card nx-profile-avatar-card">

                {{-- Avatar --}}
                <div class="nx-profile-avatar-wrap">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}"
                             alt="{{ $user->name }}"
                             class="nx-profile-avatar-img"
                             id="nx-avatar-preview">
                    @else
                        <div class="nx-profile-avatar-initials" id="nx-avatar-preview">
                            {{ strtoupper(substr($user->name, 0, 2)) }}
                        </div>
                    @endif
                    <label for="avatarInput" class="nx-profile-avatar-overlay" title="Alterar foto">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                            <circle cx="12" cy="13" r="4"/>
                        </svg>
                    </label>
                </div>

                {{-- Upload de avatar (oculto) --}}
                <form id="avatarForm" action="{{ route('profile.uploadAvatar') }}" method="POST"
                      enctype="multipart/form-data" style="display:none;">
                    @csrf
                    <input type="file" id="avatarInput" name="avatar" accept="image/*"
                           onchange="nxAvatarPreview(this)">
                </form>

                {{-- Nome e cargo --}}
                <div class="nx-profile-avatar-info">
                    <h2 class="nx-profile-avatar-name">{{ $user->name }}</h2>
                    @if($user->job_title)
                        <p class="nx-profile-avatar-role">{{ $user->job_title }}</p>
                    @elseif($user->is_admin)
                        <p class="nx-profile-avatar-role nx-profile-avatar-role--admin">Administrador</p>
                    @else
                        <p class="nx-profile-avatar-role">Usuário</p>
                    @endif
                    @if($user->department)
                        <p class="nx-profile-avatar-dept">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                                <polyline points="9 22 9 12 15 12 15 22"/>
                            </svg>
                            {{ $user->department }}
                        </p>
                    @endif
                </div>

                {{-- Status badges --}}
                <div class="nx-profile-avatar-badges">
                    @if($user->is_active)
                        <span class="nx-badge nx-badge-success">
                            <span style="width:6px;height:6px;border-radius:50%;background:currentColor;display:inline-block;margin-right:3px;"></span>
                            Ativo
                        </span>
                    @else
                        <span class="nx-badge nx-badge-danger">Inativo</span>
                    @endif
                    @if($user->has_license)
                        <span class="nx-badge nx-badge--blue">Licenciado</span>
                    @endif
                    @if($user->is_admin)
                        <span class="nx-badge nx-badge--purple">Admin</span>
                    @endif
                </div>

                {{-- Último acesso --}}
                @if($user->last_login_at)
                    <div class="nx-profile-last-login">
                        <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <polyline points="12 6 12 12 16 14"/>
                        </svg>
                        Último acesso: {{ $user->last_login_at->diffForHumans() }}
                    </div>
                @endif

                {{-- Remover foto --}}
                @if($user->avatar)
                    <form action="{{ route('profile.removeAvatar') }}" method="POST" style="margin-top:8px;width:100%;text-align:center;">
                        @csrf @method('DELETE')
                        <button type="submit" class="nx-profile-remove-avatar-btn"
                                onclick="return confirm('Remover foto de perfil?')">
                            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/>
                                <path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/>
                            </svg>
                            Remover foto
                        </button>
                    </form>
                @endif

            </div>{{-- /.nx-profile-avatar-card --}}

            {{-- Navegação lateral (tabs) --}}
            <nav class="nx-settings-nav" id="nx-profile-nav">
                <button class="nx-settings-nav-item active" onclick="nxProfileTab('dados', this)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    Dados Pessoais
                </button>
                <button class="nx-settings-nav-item" onclick="nxProfileTab('seguranca', this)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                    </svg>
                    Segurança
                </button>
                <div class="nx-settings-nav-divider"></div>
                <button class="nx-settings-nav-item" onclick="nxProfileTab('atividade', this)">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                    </svg>
                    Atividade
                </button>
            </nav>

        </aside>{{-- /.nx-profile-sidebar --}}

        {{-- ════════════════════════════════
             COLUNA DIREITA — Conteúdo
             ════════════════════════════════ --}}
        <div class="nx-profile-content">

            {{-- ── TAB: DADOS PESSOAIS ──────────────────────────── --}}
            <div class="nx-settings-content active" id="nx-tab-dados">
                <form action="{{ route('profile.updateInfo') }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="nx-form-card">

                        <div class="nx-settings-section-header">
                            <div class="nx-settings-section-icon"
                                 style="background:rgba(59,130,246,0.1);border:1px solid rgba(59,130,246,0.2);color:#3B82F6;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                            </div>
                            <div class="nx-settings-section-text">
                                <p class="nx-settings-section-title">Dados Pessoais</p>
                                <p class="nx-settings-section-desc">Atualize seu nome, cargo e informações de contato.</p>
                            </div>
                        </div>

                        <div class="nx-settings-body">

                            <div class="nx-settings-row">
                                <div class="nx-field">
                                    <label for="name">Nome Completo <span style="color:#EF4444">*</span></label>
                                    <input type="text" id="name" name="name"
                                           value="{{ old('name', $user->name) }}"
                                           placeholder="Seu nome completo" required>
                                    @error('name')<span class="nx-field-error">{{ $message }}</span>@enderror
                                </div>
                                <div class="nx-field">
                                    <label for="phone">Telefone / WhatsApp</label>
                                    <input type="tel" id="phone" name="phone"
                                           value="{{ old('phone', $user->phone) }}"
                                           placeholder="(00) 00000-0000">
                                    @error('phone')<span class="nx-field-error">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <div class="nx-settings-row nx-settings-row-full">
                                <div class="nx-field">
                                    <label for="email_ro">E-mail</label>
                                    <input type="email" id="email_ro" value="{{ $user->email }}"
                                           disabled style="opacity:.55;cursor:not-allowed;background:#F8FAFC;">
                                    <small>O e-mail não pode ser alterado diretamente. Contate o suporte.</small>
                                </div>
                            </div>

                            <div class="nx-settings-row">
                                <div class="nx-field">
                                    <label for="job_title">Cargo</label>
                                    <input type="text" id="job_title" name="job_title"
                                           value="{{ old('job_title', $user->job_title) }}"
                                           placeholder="Ex.: Analista Financeiro">
                                    @error('job_title')<span class="nx-field-error">{{ $message }}</span>@enderror
                                </div>
                                <div class="nx-field">
                                    <label for="department">Departamento / Unidade</label>
                                    <input type="text" id="department" name="department"
                                           value="{{ old('department', $user->department) }}"
                                           placeholder="Ex.: Financeiro, TI, RH">
                                    @error('department')<span class="nx-field-error">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <div class="nx-settings-row nx-settings-row-full">
                                <div class="nx-field">
                                    <label for="bio">Sobre mim</label>
                                    <textarea id="bio" name="bio" rows="3"
                                              placeholder="Uma breve descrição sobre você..."
                                              style="resize:vertical;">{{ old('bio', $user->bio) }}</textarea>
                                    <small>Máximo de 500 caracteres.</small>
                                    @error('bio')<span class="nx-field-error">{{ $message }}</span>@enderror
                                </div>
                            </div>

                        </div>

                        <div class="nx-settings-footer">
                            <button type="reset" class="nx-btn nx-btn-outline nx-btn-sm">Descartar</button>
                            <button type="submit" class="nx-btn nx-btn-primary nx-btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                Salvar Alterações
                            </button>
                        </div>

                    </div>
                </form>
            </div>{{-- /#nx-tab-dados --}}

            {{-- ── TAB: SEGURANÇA ───────────────────────────────── --}}
            <div class="nx-settings-content" id="nx-tab-seguranca">
                <form action="{{ route('profile.updatePassword') }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="nx-form-card">

                        <div class="nx-settings-section-header">
                            <div class="nx-settings-section-icon"
                                 style="background:rgba(245,158,11,0.1);border:1px solid rgba(245,158,11,0.2);color:#D97706;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </div>
                            <div class="nx-settings-section-text">
                                <p class="nx-settings-section-title">Alterar Senha</p>
                                <p class="nx-settings-section-desc">Use uma senha forte com no mínimo 8 caracteres, letras e números.</p>
                            </div>
                        </div>

                        <div class="nx-settings-body">

                            <div class="nx-profile-security-tip">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="12" cy="12" r="10"/>
                                    <line x1="12" y1="8" x2="12" y2="12"/>
                                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                                </svg>
                                Nunca compartilhe sua senha. Nossa equipe jamais solicitará sua senha por e-mail ou telefone.
                            </div>

                            <div class="nx-settings-row nx-settings-row-full">
                                <div class="nx-field">
                                    <label for="current_password">Senha Atual <span style="color:#EF4444">*</span></label>
                                    <div class="nx-password-wrap">
                                        <input type="password" id="current_password" name="current_password"
                                               placeholder="••••••••" autocomplete="current-password">
                                        <button type="button" class="nx-password-toggle"
                                                onclick="nxTogglePassword('current_password')" tabindex="-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                                 fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </button>
                                    </div>
                                    @error('current_password')<span class="nx-field-error">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <div class="nx-settings-row">
                                <div class="nx-field">
                                    <label for="password">Nova Senha <span style="color:#EF4444">*</span></label>
                                    <div class="nx-password-wrap">
                                        <input type="password" id="password" name="password"
                                               placeholder="••••••••" autocomplete="new-password"
                                               oninput="nxPasswordStrength(this.value)">
                                        <button type="button" class="nx-password-toggle"
                                                onclick="nxTogglePassword('password')" tabindex="-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                                 fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div class="nx-password-strength" id="nx-pwd-strength" style="display:none;">
                                        <div class="nx-password-strength-bar">
                                            <div class="nx-password-strength-fill" id="nx-pwd-fill"></div>
                                        </div>
                                        <span class="nx-password-strength-label" id="nx-pwd-label"></span>
                                    </div>
                                    @error('password')<span class="nx-field-error">{{ $message }}</span>@enderror
                                </div>
                                <div class="nx-field">
                                    <label for="password_confirmation">Confirmar Nova Senha <span style="color:#EF4444">*</span></label>
                                    <div class="nx-password-wrap">
                                        <input type="password" id="password_confirmation" name="password_confirmation"
                                               placeholder="••••••••" autocomplete="new-password">
                                        <button type="button" class="nx-password-toggle"
                                                onclick="nxTogglePassword('password_confirmation')" tabindex="-1">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                                 fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="nx-settings-footer">
                            <button type="reset" class="nx-btn nx-btn-outline nx-btn-sm">Limpar</button>
                            <button type="submit" class="nx-btn nx-btn-primary nx-btn-sm">
                                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                Alterar Senha
                            </button>
                        </div>

                    </div>
                </form>
            </div>{{-- /#nx-tab-seguranca --}}

            {{-- ── TAB: ATIVIDADE ───────────────────────────────── --}}
            <div class="nx-settings-content" id="nx-tab-atividade">
                <div class="nx-form-card">

                    <div class="nx-settings-section-header">
                        <div class="nx-settings-section-icon"
                             style="background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);color:#059669;">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>
                            </svg>
                        </div>
                        <div class="nx-settings-section-text">
                            <p class="nx-settings-section-title">Histórico de Atividade</p>
                            <p class="nx-settings-section-desc">Seus acessos e informações de sessão.</p>
                        </div>
                    </div>

                    <div class="nx-settings-body">

                        {{-- KPI cards --}}
                        <div class="nx-profile-activity-kpis">
                            <div class="nx-profile-activity-kpi">
                                <div class="nx-profile-activity-kpi-icon" style="background:#EFF6FF;color:#3B82F6;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="12" cy="12" r="10"/>
                                        <polyline points="12 6 12 12 16 14"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="nx-profile-activity-kpi-label">Último Acesso</p>
                                    <p class="nx-profile-activity-kpi-value">
                                        {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y H:i') : 'Nunca' }}
                                    </p>
                                </div>
                            </div>
                            <div class="nx-profile-activity-kpi">
                                <div class="nx-profile-activity-kpi-icon" style="background:#F0FDF4;color:#059669;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="nx-profile-activity-kpi-label">Status</p>
                                    <p class="nx-profile-activity-kpi-value"
                                       style="{{ $user->is_active ? 'color:#059669' : 'color:#EF4444' }}">
                                        {{ $user->is_active ? 'Conta Ativa' : 'Conta Inativa' }}
                                    </p>
                                </div>
                            </div>
                            <div class="nx-profile-activity-kpi">
                                <div class="nx-profile-activity-kpi-icon" style="background:#F5F3FF;color:#7C3AED;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="2" y="3" width="20" height="14" rx="2"/>
                                        <line x1="8" y1="21" x2="16" y2="21"/>
                                        <line x1="12" y1="17" x2="12" y2="21"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="nx-profile-activity-kpi-label">Membro desde</p>
                                    <p class="nx-profile-activity-kpi-value">{{ $user->created_at->format('d/m/Y') }}</p>
                                </div>
                            </div>
                            <div class="nx-profile-activity-kpi">
                                <div class="nx-profile-activity-kpi-icon" style="background:#FFFBEB;color:#D97706;">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="nx-profile-activity-kpi-label">Licença</p>
                                    <p class="nx-profile-activity-kpi-value"
                                       style="{{ $user->has_license ? 'color:#059669' : 'color:#D97706' }}">
                                        {{ $user->has_license ? 'Regularizada' : 'Pendente' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Sessão atual --}}
                        <div class="nx-profile-session-card">
                            <div class="nx-profile-session-header">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" style="color:#3B82F6;">
                                    <rect x="2" y="3" width="20" height="14" rx="2"/>
                                    <line x1="8" y1="21" x2="16" y2="21"/>
                                    <line x1="12" y1="17" x2="12" y2="21"/>
                                </svg>
                                Sessão Atual
                                <span class="nx-profile-session-badge">Ativa</span>
                            </div>
                            <div class="nx-profile-session-body">
                                <div class="nx-profile-session-row">
                                    <span class="nx-profile-session-label">Navegador</span>
                                    <span class="nx-profile-session-value" id="nx-user-agent">—</span>
                                </div>
                                <div class="nx-profile-session-row">
                                    <span class="nx-profile-session-label">Plataforma</span>
                                    <span class="nx-profile-session-value" id="nx-platform">—</span>
                                </div>
                                <div class="nx-profile-session-row">
                                    <span class="nx-profile-session-label">Login registrado</span>
                                    <span class="nx-profile-session-value">
                                        {{ $user->last_login_at ? $user->last_login_at->format('d/m/Y \à\s H:i') : '—' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Nota --}}
                        <div class="nx-profile-info-note">
                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/>
                                <line x1="12" y1="8" x2="12" y2="12"/>
                                <line x1="12" y1="16" x2="12.01" y2="16"/>
                            </svg>
                            Para encerrar sua sessão com segurança, utilize o botão <strong>Sair</strong> no menu lateral.
                        </div>

                    </div>{{-- /.nx-settings-body --}}
                </div>{{-- /.nx-form-card --}}
            </div>{{-- /#nx-tab-atividade --}}

        </div>{{-- /.nx-profile-content --}}
    </div>{{-- /.nx-profile-layout --}}

</div>{{-- /.nx-list-page --}}

@endsection

@push('scripts')
<script>
/* ── Troca de Tabs ───────────────────────────────────── */
function nxProfileTab(tab, btn) {
    document.querySelectorAll('.nx-settings-content').forEach(function (el) { el.classList.remove('active'); });
    document.querySelectorAll('#nx-profile-nav .nx-settings-nav-item').forEach(function (el) { el.classList.remove('active'); });
    document.getElementById('nx-tab-' + tab).classList.add('active');
    btn.classList.add('active');
}

/* ── Preview + Envio do Avatar ──────────────────────── */
function nxAvatarPreview(input) {
    if (!input.files || !input.files[0]) return;
    var file = input.files[0];
    if (file.size > 2 * 1024 * 1024) {
        alert('A imagem deve ter no máximo 2 MB.');
        input.value = '';
        return;
    }
    var reader = new FileReader();
    reader.onload = function (e) {
        var wrap    = document.querySelector('.nx-profile-avatar-wrap');
        var preview = document.getElementById('nx-avatar-preview');
        if (preview.tagName === 'IMG') {
            preview.src = e.target.result;
        } else {
            var img = document.createElement('img');
            img.src = e.target.result;
            img.id  = 'nx-avatar-preview';
            img.className = 'nx-profile-avatar-img';
            img.alt = 'Preview';
            wrap.replaceChild(img, preview);
        }
        document.getElementById('avatarForm').submit();
    };
    reader.readAsDataURL(file);
}

/* ── Toggle senha ────────────────────────────────────── */
function nxTogglePassword(id) {
    var el = document.getElementById(id);
    if (el) el.type = el.type === 'password' ? 'text' : 'password';
}

/* ── Força da senha ──────────────────────────────────── */
function nxPasswordStrength(value) {
    var wrap  = document.getElementById('nx-pwd-strength');
    var fill  = document.getElementById('nx-pwd-fill');
    var label = document.getElementById('nx-pwd-label');
    if (!value) { wrap.style.display = 'none'; return; }
    wrap.style.display = 'flex';
    var score = 0;
    if (value.length >= 8)             score++;
    if (/[A-Z]/.test(value))           score++;
    if (/[a-z]/.test(value))           score++;
    if (/\d/.test(value))              score++;
    if (/[^A-Za-z0-9]/.test(value))    score++;
    var levels = [
        { pct:'20%', color:'#EF4444', text:'Muito fraca'  },
        { pct:'40%', color:'#F97316', text:'Fraca'         },
        { pct:'60%', color:'#EAB308', text:'Razoável'      },
        { pct:'80%', color:'#22C55E', text:'Forte'         },
        { pct:'100%',color:'#10B981', text:'Muito forte'   },
    ];
    var lvl = levels[Math.max(0, score - 1)];
    fill.style.width      = lvl.pct;
    fill.style.background = lvl.color;
    label.textContent     = lvl.text;
    label.style.color     = lvl.color;
}

/* ── Informações do navegador ────────────────────────── */
(function () {
    var ua  = navigator.userAgent;
    var browser = 'Desconhecido';
    if (/Edg\//.test(ua))          browser = 'Microsoft Edge';
    else if (/OPR\//.test(ua))     browser = 'Opera';
    else if (/Chrome\//.test(ua))  browser = 'Google Chrome';
    else if (/Firefox\//.test(ua)) browser = 'Mozilla Firefox';
    else if (/Safari\//.test(ua))  browser = 'Safari';
    var uaEl   = document.getElementById('nx-user-agent');
    var platEl = document.getElementById('nx-platform');
    if (uaEl)   uaEl.textContent   = browser;
    if (platEl) platEl.textContent = navigator.platform || '—';
})();

/* ── Auto-ocultar alertas de sessão ──────────────────── */
setTimeout(function () {
    document.querySelectorAll('.alert-success, .alert-error').forEach(function (el) {
        el.style.transition = 'opacity 0.4s ease';
        el.style.opacity    = '0';
        setTimeout(function () { el.remove(); }, 400);
    });
}, 4000);
</script>
@endpush

@push('styles')
<style>
/* ══════════════════════════════════════════════════════════
   PERFIL — Estilos inline da página
   ══════════════════════════════════════════════════════════ */

.nx-profile-layout {
    display: grid;
    grid-template-columns: 260px 1fr;
    gap: 24px;
    align-items: flex-start;
}

/* ── Sidebar ─────────────────────────────────────────── */
.nx-profile-sidebar {
    display: flex;
    flex-direction: column;
    gap: 16px;
    position: sticky;
    top: 24px;
}

.nx-profile-avatar-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 28px 20px 20px;
    text-align: center;
}

.nx-profile-avatar-wrap {
    position: relative;
    width: 96px;
    height: 96px;
    margin-bottom: 16px;
}

.nx-profile-avatar-img {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #3B82F6;
    box-shadow: 0 4px 16px rgba(59,130,246,0.3);
    display: block;
}

.nx-profile-avatar-initials {
    width: 96px;
    height: 96px;
    border-radius: 50%;
    background: linear-gradient(135deg, #3B82F6 0%, #6366F1 100%);
    color: #fff;
    font-size: 28px;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 4px 16px rgba(59,130,246,0.35);
    letter-spacing: -0.5px;
    border: 3px solid rgba(255,255,255,0.9);
}

.nx-profile-avatar-overlay {
    position: absolute;
    inset: 0;
    border-radius: 50%;
    background: rgba(15,23,42,0.55);
    display: flex;
    align-items: center;
    justify-content: center;
    color: #fff;
    opacity: 0;
    cursor: pointer;
    transition: opacity 0.2s;
}
.nx-profile-avatar-wrap:hover .nx-profile-avatar-overlay { opacity: 1; }

.nx-profile-avatar-info {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    margin-bottom: 12px;
    width: 100%;
}
.nx-profile-avatar-name {
    font-size: 17px; font-weight: 700; color: #0F172A;
    letter-spacing: -0.3px; margin: 0;
}
.nx-profile-avatar-role {
    font-size: 12px; color: #64748B; font-weight: 500; margin: 0;
}
.nx-profile-avatar-role--admin { color: #7C3AED; font-weight: 600; }
.nx-profile-avatar-dept {
    display: flex; align-items: center; gap: 4px;
    font-size: 11.5px; color: #94A3B8; margin: 0;
}

.nx-profile-avatar-badges {
    display: flex; flex-wrap: wrap; gap: 5px;
    justify-content: center; margin-bottom: 12px;
}

.nx-profile-last-login {
    display: flex; align-items: center; gap: 5px;
    font-size: 11.5px; color: #94A3B8;
    border-top: 1px solid #F1F5F9; padding-top: 12px;
    width: 100%; justify-content: center;
}

.nx-profile-remove-avatar-btn {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; font-weight: 500; color: #94A3B8;
    background: transparent; border: none; cursor: pointer;
    padding: 4px 8px; border-radius: 6px;
    transition: color 0.15s, background 0.15s; font-family: inherit;
    margin-top: 8px;
}
.nx-profile-remove-avatar-btn:hover { color: #EF4444; background: #FEF2F2; }

/* ── Conteúdo ────────────────────────────────────────── */
.nx-profile-content { min-width: 0; }

/* Dica de segurança */
.nx-profile-security-tip {
    display: flex; align-items: flex-start; gap: 10px;
    background: #EFF6FF; border: 1px solid #BFDBFE;
    border-radius: 10px; padding: 12px 16px;
    font-size: 13px; color: #1D4ED8; line-height: 1.55;
}
.nx-profile-security-tip svg { flex-shrink: 0; margin-top: 1px; }

/* Campo de senha com toggle */
.nx-password-wrap { position: relative; display: flex; align-items: center; }
.nx-password-wrap input { width: 100%; padding-right: 40px; }
.nx-password-toggle {
    position: absolute; right: 10px; background: transparent;
    border: none; color: #94A3B8; cursor: pointer; padding: 4px;
    display: flex; align-items: center; border-radius: 4px; transition: color 0.15s;
}
.nx-password-toggle:hover { color: #64748B; }

/* Barra de força */
.nx-password-strength { display: flex; align-items: center; gap: 10px; margin-top: 6px; }
.nx-password-strength-bar {
    flex: 1; height: 5px; background: #F1F5F9;
    border-radius: 99px; overflow: hidden;
}
.nx-password-strength-fill {
    height: 100%; border-radius: 99px;
    transition: width 0.3s ease, background 0.3s ease; width: 0%;
}
.nx-password-strength-label { font-size: 11.5px; font-weight: 600; white-space: nowrap; min-width: 80px; }

/* KPIs de atividade */
.nx-profile-activity-kpis {
    display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px;
}
.nx-profile-activity-kpi {
    display: flex; align-items: center; gap: 12px;
    background: #F8FAFC; border: 1px solid #E8EEF5;
    border-radius: 12px; padding: 14px 16px;
}
.nx-profile-activity-kpi-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.nx-profile-activity-kpi-label {
    font-size: 11.5px; font-weight: 600; color: #94A3B8;
    text-transform: uppercase; letter-spacing: 0.05em; margin: 0 0 3px;
}
.nx-profile-activity-kpi-value { font-size: 14px; font-weight: 700; color: #0F172A; margin: 0; }

/* Card de sessão */
.nx-profile-session-card {
    background: #F8FAFC; border: 1px solid #E8EEF5;
    border-radius: 12px; overflow: hidden;
}
.nx-profile-session-header {
    display: flex; align-items: center; gap: 8px;
    padding: 12px 16px; background: #fff;
    border-bottom: 1px solid #F1F5F9;
    font-size: 13.5px; font-weight: 600; color: #1E293B;
}
.nx-profile-session-badge {
    margin-left: auto; background: #DCFCE7; color: #166534;
    border: 1px solid #BBF7D0; font-size: 11px; font-weight: 700;
    padding: 2px 9px; border-radius: 999px;
}
.nx-profile-session-body { padding: 4px 0; }
.nx-profile-session-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 16px; border-bottom: 1px solid #F1F5F9; gap: 12px;
}
.nx-profile-session-row:last-child { border-bottom: none; }
.nx-profile-session-label { font-size: 12.5px; font-weight: 600; color: #64748B; flex-shrink: 0; }
.nx-profile-session-value { font-size: 13px; color: #1E293B; text-align: right; }

/* Nota informativa */
.nx-profile-info-note {
    display: flex; align-items: center; gap: 8px;
    padding: 12px 14px; background: #FFFBEB;
    border: 1px solid #FDE68A; border-radius: 10px;
    font-size: 12.5px; color: #92400E; line-height: 1.5;
}
.nx-profile-info-note svg { flex-shrink: 0; color: #D97706; }

/* ── Responsivo ──────────────────────────────────────── */
@media (max-width: 960px) {
    .nx-profile-layout { grid-template-columns: 1fr; }
    .nx-profile-sidebar { position: static; }
    .nx-profile-avatar-card { flex-direction: row; align-items: flex-start; text-align: left; gap: 20px; }
    .nx-profile-avatar-info { align-items: flex-start; }
    .nx-profile-avatar-badges { justify-content: flex-start; }
    .nx-profile-last-login { justify-content: flex-start; }
    .nx-settings-nav { display: flex; overflow-x: auto; padding: 6px; gap: 4px; }
    .nx-settings-nav-item { white-space: nowrap; flex-shrink: 0; }
    .nx-settings-nav-divider { display: none; }
}
@media (max-width: 640px) {
    .nx-profile-activity-kpis { grid-template-columns: 1fr; }
    .nx-settings-row { grid-template-columns: 1fr !important; }
    .nx-profile-avatar-card { flex-direction: column; align-items: center; text-align: center; }
}
</style>
@endpush

