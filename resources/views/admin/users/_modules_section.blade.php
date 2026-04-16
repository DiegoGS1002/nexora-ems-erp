{{--
    Partial: Seleção de Módulos Contratados
    Variáveis esperadas:
        $selectedModules  → array de slugs já selecionados (pode ser [] para create)
--}}
@php
    $_allModules = [
        ['slug' => 'dashboard',   'name' => 'Dashboard',          'color' => '#3B82F6',
         'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>'],
        ['slug' => 'cadastro',    'name' => 'Cadastro',           'color' => '#8B5CF6',
         'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"/><rect x="8" y="2" width="8" height="4" rx="1" ry="1"/></svg>'],
        ['slug' => 'producao',    'name' => 'Produção',           'color' => '#F59E0B',
         'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22V8"/><path d="m20 12-8-4-8 4"/><path d="M20 17v-5"/><path d="M4 17v-5"/><path d="M20 22v-5"/><path d="M4 22v-5"/></svg>'],
        ['slug' => 'estoque',     'name' => 'Estoque',            'color' => '#10B981',
         'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>'],
        ['slug' => 'vendas',      'name' => 'Vendas',             'color' => '#06B6D4',
         'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/></svg>'],
        ['slug' => 'compras',     'name' => 'Compras',            'color' => '#F97316',
         'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2 3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>'],
        ['slug' => 'fiscal',      'name' => 'Fiscal',             'color' => '#EC4899',
         'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>'],
        ['slug' => 'financeiro',  'name' => 'Financeiro',         'color' => '#22C55E',
         'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>'],
        ['slug' => 'rh',          'name' => 'Recursos Humanos',   'color' => '#A78BFA',
         'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>'],
        ['slug' => 'transporte',  'name' => 'Transporte',         'color' => '#0EA5E9',
         'icon' => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/></svg>'],
    ];

    // Filtra os módulos disponíveis com base nos módulos do usuário logado (usuário principal).
    // Se o admin não tiver nenhum módulo definido, exibe todos (sem restrição).
    $_adminUser    = auth()->user();
    $_adminModules = $_adminUser->modules ?? [];
    $allModules    = ($_adminUser->is_admin && empty($_adminModules))
        ? $_allModules
        : array_values(array_filter($_allModules, fn($m) => in_array($m['slug'], $_adminModules)));

    // Prioriza old() para reexibição após erro de validação
    $selected = old('modules', $selectedModules ?? []);
@endphp

<div class="nx-form-section">
    <div class="nx-form-section-header">
        <div class="nx-form-section-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <rect x="2" y="3" width="6" height="6" rx="1"/><rect x="9" y="3" width="6" height="6" rx="1"/>
                <rect x="16" y="3" width="6" height="6" rx="1"/><rect x="2" y="10" width="6" height="6" rx="1"/>
                <rect x="9" y="10" width="6" height="6" rx="1"/><rect x="16" y="10" width="6" height="6" rx="1"/>
                <rect x="2" y="17" width="6" height="6" rx="1"/><rect x="9" y="17" width="6" height="6" rx="1"/>
                <rect x="16" y="17" width="6" height="6" rx="1"/>
            </svg>
        </div>
        <div style="flex:1;">
            <h3 class="nx-form-section-title">Módulos Contratados</h3>
        </div>
        {{-- Botões atalho: marcar / desmarcar todos --}}
        <div style="display:flex; gap:6px;">
            <button type="button" onclick="nxModulesSelectAll(true)"
                class="nx-btn nx-btn-outline nx-btn-sm" style="font-size:11px;">
                Marcar todos
            </button>
            <button type="button" onclick="nxModulesSelectAll(false)"
                class="nx-btn nx-btn-ghost nx-btn-sm" style="font-size:11px;">
                Desmarcar todos
            </button>
        </div>
    </div>

    <p style="font-size:12.5px; color:#64748B; margin-bottom:16px; line-height:1.5;">
        Selecione os módulos que este usuário tem acesso de acordo com o plano contratado.
        Administradores têm acesso irrestrito independente desta configuração.
        Apenas os módulos disponíveis no seu plano são exibidos abaixo.
    </p>

    <div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(160px,1fr)); gap:10px;" id="nx-modules-grid">
        @foreach($allModules as $mod)
            @php $isChecked = in_array($mod['slug'], (array) $selected); @endphp
            <label
                for="mod_{{ $mod['slug'] }}"
                class="nx-module-option {{ $isChecked ? 'nx-module-option--active' : '' }}"
                style="--mod-color: {{ $mod['color'] }};"
            >
                <input
                    type="checkbox"
                    id="mod_{{ $mod['slug'] }}"
                    name="modules[]"
                    value="{{ $mod['slug'] }}"
                    class="nx-module-checkbox"
                    {{ $isChecked ? 'checked' : '' }}
                >
                <span class="nx-module-icon">{!! $mod['icon'] !!}</span>
                <span class="nx-module-name">{{ $mod['name'] }}</span>
                <span class="nx-module-check">
                    <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="3.5">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </span>
            </label>
        @endforeach
    </div>

    @error('modules')
        <small style="color:#EF4444; display:block; margin-top:8px;">{{ $message }}</small>
    @enderror
</div>

<script>
function nxModulesSelectAll(check) {
    document.querySelectorAll('.nx-module-checkbox').forEach(function(cb) {
        cb.checked = check;
        cb.closest('label').classList.toggle('nx-module-option--active', check);
    });
}
document.querySelectorAll('.nx-module-checkbox').forEach(function(cb) {
    cb.addEventListener('change', function() {
        this.closest('label').classList.toggle('nx-module-option--active', this.checked);
    });
});
</script>

