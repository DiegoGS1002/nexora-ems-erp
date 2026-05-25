@extends('layouts.app')

@section('title', 'Controle de Usuários')

@section('content')
<div class="nx-list-page">

    {{-- ── HEADER ── --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <h1 class="nx-page-title">Controle de Usuários</h1>
            <p class="nx-page-subtitle">Gerencie os usuários e permissões de acesso ao sistema</p>
        </div>
        <div class="nx-page-actions">
            <a href="{{ route('users.create') }}" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="8.5" cy="7" r="4"/>
                    <line x1="20" y1="8" x2="20" y2="14"/>
                    <line x1="23" y1="11" x2="17" y2="11"/>
                </svg>
                Novo Usuário
            </a>
        </div>
    </div>

    {{-- ── KPI CARDS ── --}}
    <div class="nx-dashboard-kpis" style="grid-template-columns: repeat(4, minmax(0, 1fr));">
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Total de Usuários</p>
                    <p class="nx-kpi-card-value">{{ $stats['total'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background: rgba(59,130,246,0.1); color:#3B82F6;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Administradores</p>
                    <p class="nx-kpi-card-value">{{ $stats['admins'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background: rgba(99,102,241,0.1); color:#6366F1;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Usuários Inativos</p>
                    <p class="nx-kpi-card-value">{{ $stats['inativos'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background: rgba(239,68,68,0.08); color:#EF4444;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Com Licença Ativa</p>
                    <p class="nx-kpi-card-value">{{ $stats['licencas'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background: rgba(16,185,129,0.1); color:#10B981;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                        <polyline points="22 4 12 14.01 9 11.01"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ── FILTROS ── --}}
    <div class="nx-card" style="padding: 16px;">
        <form method="GET" action="{{ route('users.index') }}">
            <div class="nx-filters-bar">
                <div class="nx-search-wrap">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <input
                        type="text"
                        name="search"
                        class="nx-search"
                        placeholder="Buscar por nome ou e-mail..."
                        value="{{ request('search') }}"
                    >
                </div>
                <select name="perfil" class="nx-filter-select">
                    <option value="">Todos os perfis</option>
                    <option value="admin"   {{ request('perfil') === 'admin'   ? 'selected' : '' }}>Administrador</option>
                    <option value="padrao"  {{ request('perfil') === 'padrao'  ? 'selected' : '' }}>Padrão</option>
                </select>
                <select name="status" class="nx-filter-select">
                    <option value="">Todos os status</option>
                    <option value="ativo"   {{ request('status') === 'ativo'   ? 'selected' : '' }}>Ativo</option>
                    <option value="inativo" {{ request('status') === 'inativo' ? 'selected' : '' }}>Inativo</option>
                </select>
                <div class="nx-filter-actions">
                    <button type="submit" class="nx-btn nx-btn-outline nx-btn-sm">
                        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2.5"><line x1="4" y1="6" x2="20" y2="6"/><line x1="8" y1="12" x2="16" y2="12"/><line x1="11" y1="18" x2="13" y2="18"/></svg>
                        Filtrar
                    </button>
                    @if(request('search') || request('perfil') || request('status'))
                        <a href="{{ route('users.index') }}" class="nx-btn nx-btn-ghost nx-btn-sm">Limpar</a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- ── TABELA ── --}}
    <div class="nx-card">
        @if($users->isEmpty())
            <div class="nx-empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24"
                     fill="none" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                     style="margin-bottom:20px;">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                    <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                    <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <p class="nx-empty-title">Nenhum usuário encontrado</p>
                <p class="nx-empty-desc">Cadastre o primeiro usuário ou ajuste os filtros de busca.</p>
                <a href="{{ route('users.create') }}" class="nx-btn nx-btn-primary">Novo Usuário</a>
            </div>
        @else
            <div class="nx-table-wrap">
                <table class="nx-table">
                    <thead>
                        <tr>
                            <th>Usuário</th>
                            <th>E-mail</th>
                            <th>Perfil</th>
                            <th>Status</th>
                            <th>Licença</th>
                            <th>Último Acesso</th>
                            <th class="nx-th-actions">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                        <tr>
                            <td>
                                <div style="display:flex; align-items:center; gap:10px;">
                                    {{-- Avatar com iniciais --}}
                                    <div style="
                                        width: 34px; height: 34px; border-radius: 50%;
                                        background: {{ $user->is_admin ? 'rgba(99,102,241,0.12)' : 'rgba(59,130,246,0.1)' }};
                                        color: {{ $user->is_admin ? '#6366F1' : '#3B82F6' }};
                                        display: flex; align-items: center; justify-content: center;
                                        font-size: 12px; font-weight: 700; letter-spacing: 0.02em;
                                        flex-shrink: 0; border: 1.5px solid {{ $user->is_admin ? 'rgba(99,102,241,0.25)' : 'rgba(59,130,246,0.2)' }};
                                    ">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}{{ strtoupper(substr(strrchr($user->name, ' ') ?: ' ', 1, 1)) ?: strtoupper(substr($user->name, 1, 1)) }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600; color:#0F172A; font-size:13.5px;">{{ $user->name }}</div>
                                        @if($user->is_admin)
                                            <div style="font-size:11px; color:#6366F1; font-weight:500; margin-top:1px;">Acesso completo ao sistema</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td style="color:#475569; font-size:13px;">{{ $user->email }}</td>
                            <td>
                                @if($user->is_admin)
                                    <span class="nx-badge nx-badge-info">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2.5"
                                             style="margin-right:4px;">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                        </svg>
                                        Administrador
                                    </span>
                                @else
                                    <span class="nx-badge nx-badge-neutral">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2.5"
                                             style="margin-right:4px;">
                                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                                            <circle cx="12" cy="7" r="4"/>
                                        </svg>
                                        Padrão
                                    </span>
                                @endif
                            </td>

                            {{-- Status: Ativo / Inativo --}}
                            <td>
                                @if($user->is_active)
                                    <span class="nx-badge nx-badge-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="3"
                                             style="margin-right:4px;">
                                            <polyline points="20 6 9 17 4 12"/>
                                        </svg>
                                        Ativo
                                    </span>
                                @else
                                    <span class="nx-badge nx-badge-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="3"
                                             style="margin-right:4px;">
                                            <line x1="18" y1="6" x2="6" y2="18"/>
                                            <line x1="6" y1="6" x2="18" y2="18"/>
                                        </svg>
                                        Inativo
                                    </span>
                                @endif
                            </td>

                            {{-- Licença --}}
                            <td>
                                @if($user->has_license)
                                    <span class="nx-badge nx-badge-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2.5"
                                             style="margin-right:4px;">
                                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                                            <polyline points="22 4 12 14.01 9 11.01"/>
                                        </svg>
                                        Licença Paga
                                    </span>
                                @else
                                    <span class="nx-badge nx-badge-warning">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2.5"
                                             style="margin-right:4px;">
                                            <circle cx="12" cy="12" r="10"/>
                                            <line x1="12" y1="8" x2="12" y2="12"/>
                                            <line x1="12" y1="16" x2="12.01" y2="16"/>
                                        </svg>
                                        Sem Licença
                                    </span>
                                @endif
                            </td>
                            <td>
                                @if($user->last_login_at)
                                    <div style="font-size:13px; color:#374151;">
                                        {{ $user->last_login_at->timezone(config('app.timezone'))->format('d/m/Y') }}
                                    </div>
                                    <div style="font-size:11.5px; color:#94A3B8; margin-top:1px;">
                                        {{ $user->last_login_at->timezone(config('app.timezone'))->format('H:i') }}
                                        &bull;
                                        {{ $user->last_login_at->diffForHumans() }}
                                    </div>
                                @else
                                    <span style="color:#CBD5E1; font-size:13px;">Nunca acessou</span>
                                @endif
                            </td>
                            <td class="nx-td-actions">
                                {{-- Editar --}}
                                <a
                                    href="{{ route('users.edit', $user) }}"
                                    class="nx-action-btn nx-action-edit"
                                    title="Editar usuário"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                                         fill="none" stroke="currentColor" stroke-width="2"
                                         stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                </a>

                                {{-- Excluir (oculto para conta própria) --}}
                                @if($user->id !== auth()->id())
                                    <form
                                        action="{{ route('users.destroy', $user) }}"
                                        method="POST"
                                        style="display:inline;"
                                        onsubmit="return confirm('Tem certeza que deseja excluir o usuário {{ addslashes($user->name) }}? Esta ação não pode ser desfeita.');"
                                    >
                                        @csrf
                                        @method('DELETE')
                                        <button
                                            type="submit"
                                            class="nx-action-btn nx-action-delete"
                                            title="Excluir usuário"
                                        >
                                            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                                                 fill="none" stroke="currentColor" stroke-width="2"
                                                 stroke-linecap="round" stroke-linejoin="round">
                                                <polyline points="3 6 5 6 21 6"/>
                                                <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                                                <path d="M10 11v6"/><path d="M14 11v6"/>
                                                <path d="M9 6V4h6v2"/>
                                            </svg>
                                        </button>
                                    </form>
                                @else
                                    {{-- Ícone de escudo: conta atual protegida --}}
                                    <span
                                        class="nx-action-btn"
                                        title="Você não pode excluir a própria conta"
                                        style="cursor:not-allowed; opacity:0.35;"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                                             fill="none" stroke="currentColor" stroke-width="2"
                                             stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                        </svg>
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="nx-table-footer">
                <span class="nx-table-count">
                    {{ $users->total() }} {{ $users->total() === 1 ? 'usuário' : 'usuários' }} encontrado{{ $users->total() === 1 ? '' : 's' }}
                </span>
                @if($users->hasPages())
                    <div class="nx-pagination">{{ $users->links() }}</div>
                @endif
            </div>
        @endif
    </div>

</div>
@endsection

