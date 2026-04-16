@extends('layouts.app')

@section('title', 'Editar Usuário')

@section('content')
<div class="nx-form-page">

    {{-- ── HEADER ── --}}
    <div class="nx-form-header">
        <a href="{{ route('users.index') }}" class="nx-back-link">
            <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="15 18 9 12 15 6"/>
            </svg>
            Voltar para Usuários
        </a>
        <h1 class="nx-form-title">Editar Usuário</h1>
        <p class="nx-form-subtitle">Atualize os dados da conta de acesso de <strong>{{ $user->name }}</strong></p>
    </div>

    {{-- ── ERROS ── --}}
    @if ($errors->any())
        <div class="alert-error" style="margin-bottom:16px;">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('users.update', $user) }}" method="POST">
        @csrf
        @method('PUT')

        {{-- ── SEÇÃO: IDENTIFICAÇÃO ── --}}
        <div class="nx-form-card">
            <div class="nx-form-section">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <h3 class="nx-form-section-title">Identificação</h3>
                </div>
                <div class="grid grid-2">
                    <div class="nx-field">
                        <label for="name">Nome Completo</label>
                        <input
                            id="name"
                            name="name"
                            type="text"
                            value="{{ old('name', $user->name) }}"
                            placeholder="Nome do usuário"
                            required
                            autocomplete="name"
                        >
                        @error('name')
                            <small style="color:#EF4444;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="nx-field">
                        <label for="email">E-mail Corporativo</label>
                        <input
                            id="email"
                            name="email"
                            type="email"
                            value="{{ old('email', $user->email) }}"
                            placeholder="usuario@nexora.com"
                            required
                            autocomplete="email"
                        >
                        @error('email')
                            <small style="color:#EF4444;">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>

            {{-- ── SEÇÃO: NOVA SENHA (opcional) ── --}}
            <div class="nx-form-section">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                            <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                        </svg>
                    </div>
                    <h3 class="nx-form-section-title">Redefinir Senha</h3>
                </div>

                {{-- Banner informativo --}}
                <div style="
                    display:flex; align-items:center; gap:10px;
                    background:#F0F9FF; border:1px solid #BAE6FD;
                    border-radius:9px; padding:12px 16px; margin-bottom:18px;
                ">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="#0EA5E9" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         style="flex-shrink:0;">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span style="font-size:13px; color:#0369A1;">
                        Deixe os campos em branco para <strong>manter a senha atual</strong>. Preencha apenas se quiser alterá-la.
                    </span>
                </div>

                <div class="grid grid-2">
                    <div class="nx-field">
                        <label for="password">Nova Senha</label>
                        <input
                            id="password"
                            name="password"
                            type="password"
                            placeholder="Mínimo 8 caracteres"
                            autocomplete="new-password"
                        >
                        @error('password')
                            <small style="color:#EF4444;">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="nx-field">
                        <label for="password_confirmation">Confirmar Nova Senha</label>
                        <input
                            id="password_confirmation"
                            name="password_confirmation"
                            type="password"
                            placeholder="Repita a nova senha"
                            autocomplete="new-password"
                        >
                    </div>
                </div>
            </div>

            {{-- ── SEÇÃO: PERMISSÕES ── --}}
            <div class="nx-form-section">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                    </div>
                    <h3 class="nx-form-section-title">Perfil e Permissões</h3>
                </div>

                <div style="display:flex; flex-direction:column; gap:10px;">
                    <label for="role_padrao" style="
                        display:flex; align-items:flex-start; gap:14px; padding:16px 18px;
                        border:1.5px solid #E2E8F0; border-radius:10px; cursor:pointer;
                        transition:border-color 0.18s, background 0.18s;
                    " onmouseover="this.style.borderColor='#3B82F6'" onmouseout="this.style.borderColor=document.getElementById('role_padrao').checked?'#3B82F6':'#E2E8F0'">
                        <input
                            type="radio"
                            id="role_padrao"
                            name="is_admin"
                            value="0"
                            style="width:auto; margin-top:2px; accent-color:#3B82F6;"
                            {{ old('is_admin', $user->is_admin ? '1' : '0') == '0' ? 'checked' : '' }}
                        >
                        <div>
                            <div style="font-size:13.5px; font-weight:600; color:#0F172A; margin-bottom:3px;">Usuário Padrão</div>
                            <div style="font-size:12.5px; color:#64748B; line-height:1.5;">
                                Acesso limitado aos módulos permitidos. Não pode gerenciar outros usuários nem acessar configurações do sistema.
                            </div>
                        </div>
                    </label>

                    <label for="role_admin" style="
                        display:flex; align-items:flex-start; gap:14px; padding:16px 18px;
                        border:1.5px solid #E2E8F0; border-radius:10px; cursor:pointer;
                        transition:border-color 0.18s, background 0.18s;
                    " onmouseover="this.style.borderColor='#6366F1'" onmouseout="this.style.borderColor=document.getElementById('role_admin').checked?'#6366F1':'#E2E8F0'">
                        <input
                            type="radio"
                            id="role_admin"
                            name="is_admin"
                            value="1"
                            style="width:auto; margin-top:2px; accent-color:#6366F1;"
                            {{ old('is_admin', $user->is_admin ? '1' : '0') == '1' ? 'checked' : '' }}
                        >
                        <div>
                            <div style="display:flex; align-items:center; gap:7px; margin-bottom:3px;">
                                <span style="font-size:13.5px; font-weight:600; color:#0F172A;">Administrador</span>
                                <span class="nx-badge nx-badge-info" style="font-size:10px; padding:2px 8px;">Acesso Total</span>
                            </div>
                            <div style="font-size:12.5px; color:#64748B; line-height:1.5;">
                                Controle total do sistema: gerenciar usuários, acessar todos os módulos, configurar permissões e visualizar logs.
                            </div>
                        </div>
                    </label>
                </div>

                @error('is_admin')
                    <small style="color:#EF4444; display:block; margin-top:8px;">{{ $message }}</small>
                @enderror
            </div>

            {{-- ── SEÇÃO: EMPRESA VINCULADA ── --}}
            <div class="nx-form-section">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                        </svg>
                    </div>
                    <h3 class="nx-form-section-title">Empresa Vinculada</h3>
                </div>

                <div class="nx-field">
                    <label for="company_id">Empresa</label>
                    <select id="company_id" name="company_id">
                        <option value="">— Sem empresa vinculada (acesso global) —</option>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}"
                                {{ old('company_id', $user->company_id) == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}{{ $company->cnpj ? ' — ' . $company->cnpj : '' }}
                            </option>
                        @endforeach
                    </select>
                    @error('company_id')
                        <small style="color:#EF4444;">{{ $message }}</small>
                    @enderror
                    <small>Usuários sem empresa vinculada possuem acesso global ao sistema.</small>
                </div>
            </div>

            {{-- ── SEÇÃO: STATUS E LICENÇA ── --}}
            <div class="nx-form-section">
                <div class="nx-form-section-header">
                    <div class="nx-form-section-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="1" y="4" width="22" height="16" rx="2" ry="2"/>
                            <line x1="1" y1="10" x2="23" y2="10"/>
                        </svg>
                    </div>
                    <h3 class="nx-form-section-title">Status e Licença</h3>
                </div>

                <div class="grid grid-2">
                    {{-- Status --}}
                    <div class="nx-field">
                        <label>Status da Conta</label>
                        <div style="display:flex; flex-direction:column; gap:8px; margin-top:4px;">
                            <label for="status_ativo" style="
                                display:flex; align-items:center; gap:12px; padding:12px 16px;
                                border:1.5px solid #E2E8F0; border-radius:10px; cursor:pointer;
                                transition:border-color 0.18s; background:#F8FAFC;
                            ">
                                <input
                                    type="radio"
                                    id="status_ativo"
                                    name="is_active"
                                    value="1"
                                    style="width:auto; accent-color:#10B981;"
                                    {{ old('is_active', $user->is_active ? '1' : '0') == '1' ? 'checked' : '' }}
                                >
                                <div>
                                    <div style="font-size:13px; font-weight:600; color:#059669;">● Ativo</div>
                                    <div style="font-size:12px; color:#64748B;">Usuário pode acessar o sistema normalmente</div>
                                </div>
                            </label>
                            <label for="status_inativo" style="
                                display:flex; align-items:center; gap:12px; padding:12px 16px;
                                border:1.5px solid #E2E8F0; border-radius:10px; cursor:pointer;
                                transition:border-color 0.18s;
                            ">
                                <input
                                    type="radio"
                                    id="status_inativo"
                                    name="is_active"
                                    value="0"
                                    style="width:auto; accent-color:#EF4444;"
                                    {{ old('is_active', $user->is_active ? '1' : '0') == '0' ? 'checked' : '' }}
                                >
                                <div>
                                    <div style="font-size:13px; font-weight:600; color:#EF4444;">● Inativo</div>
                                    <div style="font-size:12px; color:#64748B;">Conta suspensa, acesso bloqueado</div>
                                </div>
                            </label>
                        </div>
                        @error('is_active')
                            <small style="color:#EF4444;">{{ $message }}</small>
                        @enderror
                    </div>

                    {{-- Licença --}}
                    <div class="nx-field">
                        <label>Licença de Uso</label>
                        <div style="display:flex; flex-direction:column; gap:8px; margin-top:4px;">
                            <label for="licenca_nao" style="
                                display:flex; align-items:center; gap:12px; padding:12px 16px;
                                border:1.5px solid #E2E8F0; border-radius:10px; cursor:pointer;
                                transition:border-color 0.18s; background:#F8FAFC;
                            ">
                                <input
                                    type="radio"
                                    id="licenca_nao"
                                    name="has_license"
                                    value="0"
                                    style="width:auto; accent-color:#F59E0B;"
                                    {{ old('has_license', $user->has_license ? '1' : '0') == '0' ? 'checked' : '' }}
                                >
                                <div>
                                    <div style="font-size:13px; font-weight:600; color:#B45309;">⚠ Sem Licença</div>
                                    <div style="font-size:12px; color:#64748B;">Licença não adquirida ou pendente</div>
                                </div>
                            </label>
                            <label for="licenca_sim" style="
                                display:flex; align-items:center; gap:12px; padding:12px 16px;
                                border:1.5px solid #E2E8F0; border-radius:10px; cursor:pointer;
                                transition:border-color 0.18s;
                            ">
                                <input
                                    type="radio"
                                    id="licenca_sim"
                                    name="has_license"
                                    value="1"
                                    style="width:auto; accent-color:#10B981;"
                                    {{ old('has_license', $user->has_license ? '1' : '0') == '1' ? 'checked' : '' }}
                                >
                                <div>
                                    <div style="font-size:13px; font-weight:600; color:#059669;">✓ Licença Paga</div>
                                    <div style="font-size:12px; color:#64748B;">Licença ativa e regularizada</div>
                                </div>
                            </label>
                        </div>
                        @error('has_license')
                            <small style="color:#EF4444;">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- ── SEÇÃO: MÓDULOS CONTRATADOS ── --}}
        <div class="nx-form-card">
            @include('admin.users._modules_section', ['selectedModules' => $user->modules ?? []])
        </div>

        {{-- ── FOOTER ── --}}
        <div class="nx-form-footer">
            <a href="{{ route('users.index') }}" class="nx-btn nx-btn-ghost">Cancelar</a>
            <button type="submit" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/>
                    <polyline points="17 21 17 13 7 13 7 21"/>
                    <polyline points="7 3 7 8 15 8"/>
                </svg>
                Salvar Alterações
            </button>
        </div>

    </form>
</div>
@endsection

