<div class="nx-list-page">

    {{-- ── HEADER ── --}}
    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <h1 class="nx-page-title">Gerenciamento de Empresas</h1>
            <p class="nx-page-subtitle">Cadastre e gerencie as empresas do sistema</p>
        </div>
        <div class="nx-page-actions">
            <a href="{{ route('companies.create') }}" class="nx-btn nx-btn-primary" wire:navigate>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Nova Empresa
            </a>
        </div>
    </div>

    {{-- ── KPI CARDS ── --}}
    <div class="nx-dashboard-kpis" style="grid-template-columns: repeat(4, minmax(0, 1fr));">
        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Total de Empresas</p>
                    <p class="nx-kpi-card-value">{{ $this->stats['total'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(59,130,246,0.1);color:#3B82F6;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="20" height="14" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                </div>
            </div>
        </div>

        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Empresas Ativas</p>
                    <p class="nx-kpi-card-value">{{ $this->stats['ativas'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(16,185,129,0.1);color:#10B981;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
            </div>
        </div>

        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Empresas Inativas</p>
                    <p class="nx-kpi-card-value">{{ $this->stats['inativas'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(239,68,68,0.08);color:#EF4444;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                </div>
            </div>
        </div>

        <div class="nx-kpi-card">
            <div class="nx-kpi-card-inner">
                <div class="nx-kpi-card-left">
                    <p class="nx-kpi-card-title">Usuários Vinculados</p>
                    <p class="nx-kpi-card-value">{{ $this->stats['usuarios'] }}</p>
                </div>
                <div class="nx-kpi-card-icon" style="background:rgba(99,102,241,0.1);color:#6366F1;">
                    <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </div>
            </div>
        </div>
    </div>

    {{-- ── FILTROS ── --}}
    <div class="nx-card" style="padding:16px;">
        <div class="nx-filters-bar">
            <div class="nx-search-wrap">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none"
                     stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
                <input type="text" wire:model.live.debounce.300ms="search"
                       class="nx-search" placeholder="Buscar por nome, CNPJ, cidade ou e-mail...">
            </div>

            <select wire:model.live="statusFilter" class="nx-filter-select">
                <option value="">Todos os status</option>
                <option value="ativo">Ativas</option>
                <option value="inativo">Inativas</option>
            </select>

            @if($search !== '' || $statusFilter !== '')
                <button wire:click="$set('search', ''); $set('statusFilter', '')"
                        class="nx-btn nx-btn-ghost nx-btn-sm">Limpar</button>
            @endif
        </div>
    </div>

    {{-- ── ALERTAS ── --}}
    @session('success')
        <div class="alert-success" style="position:relative;top:auto;right:auto;margin-bottom:0;">{{ $value }}</div>
    @endsession
    @session('error')
        <div class="alert-error" style="position:relative;top:auto;right:auto;margin-bottom:0;">{{ $value }}</div>
    @endsession

    {{-- ── TABELA ── --}}
    <div class="nx-card">
        @if($this->companies->isEmpty())
            <div class="nx-empty-state">
                <svg xmlns="http://www.w3.org/2000/svg" width="56" height="56" viewBox="0 0 24 24"
                     fill="none" stroke="#CBD5E1" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"
                     style="margin-bottom:20px;">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                </svg>
                <p class="nx-empty-title">Nenhuma empresa encontrada</p>
                <p class="nx-empty-desc">Cadastre a primeira empresa ou ajuste os filtros de busca.</p>
                <a href="{{ route('companies.create') }}" class="nx-btn nx-btn-primary" wire:navigate>Nova Empresa</a>
            </div>
        @else
            <div class="nx-table-wrap">
                <table class="nx-table">
                    <thead>
                        <tr>
                            <th>Empresa</th>
                            <th>CNPJ</th>
                            <th>Localização</th>
                            <th>Contato</th>
                            <th>Usuários</th>
                            <th>Status</th>
                            <th class="nx-th-actions">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($this->companies as $company)
                        <tr wire:key="company-{{ $company->id }}">
                            {{-- Empresa --}}
                            <td>
                                <div style="display:flex;align-items:center;gap:12px;">
                                    <div style="
                                        width:38px;height:38px;border-radius:10px;flex-shrink:0;
                                        background:rgba(59,130,246,0.1);color:#3B82F6;
                                        display:flex;align-items:center;justify-content:center;
                                        font-size:13px;font-weight:700;letter-spacing:0.02em;
                                        border:1.5px solid rgba(59,130,246,0.2);">
                                        {{ $company->initials }}
                                    </div>
                                    <div>
                                        <div style="font-weight:600;color:#0F172A;font-size:13.5px;">{{ $company->name }}</div>
                                        @if($company->social_name)
                                            <div style="font-size:11.5px;color:#94A3B8;margin-top:1px;">{{ $company->social_name }}</div>
                                        @endif
                                        @if($company->segment)
                                            <div style="font-size:11px;color:#6366F1;font-weight:500;margin-top:2px;">{{ $company->segment }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>

                            {{-- CNPJ --}}
                            <td>
                                @if($company->cnpj)
                                    <span class="nx-mono" style="font-size:12.5px;">{{ $company->cnpj }}</span>
                                @else
                                    <span style="color:#CBD5E1;">—</span>
                                @endif
                            </td>

                            {{-- Localização --}}
                            <td>
                                @if($company->address_city)
                                    <div style="font-size:13px;color:#374151;">{{ $company->address_city }}</div>
                                    @if($company->address_state)
                                        <div style="font-size:11.5px;color:#94A3B8;margin-top:1px;">{{ $company->address_state }}</div>
                                    @endif
                                @else
                                    <span style="color:#CBD5E1;">—</span>
                                @endif
                            </td>

                            {{-- Contato --}}
                            <td>
                                @if($company->email)
                                    <div style="font-size:13px;color:#374151;">{{ $company->email }}</div>
                                @endif
                                @if($company->phone)
                                    <div style="font-size:11.5px;color:#94A3B8;margin-top:1px;">{{ $company->phone }}</div>
                                @endif
                                @if(!$company->email && !$company->phone)
                                    <span style="color:#CBD5E1;">—</span>
                                @endif
                            </td>

                            {{-- Usuários --}}
                            <td>
                                <div style="display:flex;align-items:center;gap:6px;">
                                    <div style="
                                        display:inline-flex;align-items:center;gap:5px;
                                        background:rgba(99,102,241,0.08);color:#6366F1;
                                        padding:4px 10px;border-radius:20px;font-size:12px;font-weight:600;">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                                        {{ $company->users_count }}
                                    </div>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td>
                                @if($company->is_active)
                                    <span class="nx-badge nx-badge-success">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-right:4px;"><polyline points="20 6 9 17 4 12"/></svg>
                                        Ativa
                                    </span>
                                @else
                                    <span class="nx-badge nx-badge-danger">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" style="margin-right:4px;"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                        Inativa
                                    </span>
                                @endif
                            </td>

                            {{-- Ações --}}
                            <td class="nx-td-actions">
                                {{-- Toggle Status --}}
                                <button wire:click="toggleStatus({{ $company->id }})"
                                        class="nx-action-btn"
                                        title="{{ $company->is_active ? 'Desativar empresa' : 'Ativar empresa' }}"
                                        style="color:{{ $company->is_active ? '#F59E0B' : '#10B981' }};">
                                    @if($company->is_active)
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="4.93" y1="4.93" x2="19.07" y2="19.07"/></svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                                    @endif
                                </button>

                                {{-- Editar --}}
                                <a href="{{ route('companies.edit', $company) }}"
                                   class="nx-action-btn nx-action-edit"
                                   title="Editar empresa" wire:navigate>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>

                                {{-- Excluir --}}
                                <button wire:click="deleteCompany({{ $company->id }})"
                                        wire:confirm="Tem certeza que deseja excluir a empresa '{{ addslashes($company->name) }}'? Esta ação não pode ser desfeita."
                                        class="nx-action-btn nx-action-delete"
                                        title="Excluir empresa">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="nx-table-footer">
                <span class="nx-table-count">
                    {{ $this->companies->count() }} {{ $this->companies->count() === 1 ? 'empresa' : 'empresas' }} encontrada{{ $this->companies->count() === 1 ? '' : 's' }}
                </span>
            </div>
        @endif
    </div>

</div>

