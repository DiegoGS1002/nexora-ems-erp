{{--
    _tree-item.blade.php — Recursive tree node
    Variables: $accounts (Collection), $depth (int)
--}}
@foreach($accounts as $account)
    @php
        $hasChildren    = $account->children->isNotEmpty();
        $paddingLeft    = ($depth * 24) + 16;
        $typeClass      = $account->type_css_class;
        $isGroup        = $hasChildren;
    @endphp

    <div x-data="{ open: {{ $depth < 2 ? 'true' : 'false' }} }" wire:key="account-{{ $account->id }}">

        {{-- ── ROW ──────────────────────────────────────── --}}
        <div class="nx-tree-row group {{ $isGroup ? 'nx-tree-row--group' : '' }}"
             style="padding-left: {{ $paddingLeft }}px">

            {{-- Expand/Collapse toggle --}}
            @if($hasChildren)
                <button type="button" @click="open = !open" class="nx-tree-toggle" aria-label="Expandir/Recolher">
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5"
                         :class="open ? 'nx-tree-chevron--open' : ''"
                         class="nx-tree-chevron">
                        <polyline points="9 18 15 12 9 6"/>
                    </svg>
                </button>
            @else
                <span class="nx-tree-toggle-placeholder"></span>
            @endif

            {{-- Icon: folder (group) or file (leaf) --}}
            <span class="nx-tree-icon">
                @if($isGroup)
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="nx-tree-icon--folder">
                        <path d="M22 19a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h5l2 3h9a2 2 0 0 1 2 2z"/>
                    </svg>
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                         class="nx-tree-icon--file">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                        <polyline points="14 2 14 8 20 8"/>
                    </svg>
                @endif
            </span>

            {{-- Code --}}
            <span class="nx-tree-code">{{ $account->code }}</span>

            {{-- Name --}}
            <span class="nx-tree-name {{ $isGroup ? 'nx-tree-name--group' : '' }}">
                {{ $account->name }}
                @if(!$account->is_active)
                    <span class="nx-tree-inactive-hint">(inativo)</span>
                @endif
            </span>

            {{-- Type badge --}}
            <span class="nx-tree-type-badge {{ $typeClass }}">
                {{ $account->type_label }}
            </span>

            {{-- Selectable badge --}}
            @if(!$isGroup)
                <span class="nx-tree-selectable-badge {{ $account->is_selectable ? 'nx-tree-selectable--yes' : 'nx-tree-selectable--no' }}">
                    {{ $account->is_selectable ? 'Analítica' : 'Bloqueada' }}
                </span>
            @else
                <span class="nx-tree-selectable-badge nx-tree-selectable--group">Sintética</span>
            @endif

            {{-- Status badge --}}
            <span class="nx-tree-status {{ $account->is_active ? 'nx-tree-status--active' : 'nx-tree-status--inactive' }}">
                {{ $account->is_active ? 'Ativo' : 'Inativo' }}
            </span>

            {{-- Actions (visible on hover) --}}
            <div class="nx-tree-actions">
                {{-- Add Sub-account --}}
                <button type="button"
                    wire:click="openCreate({{ $account->id }})"
                    class="nx-tree-action-btn nx-tree-action-btn--add"
                    title="Adicionar Subconta">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2.5"
                         stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                </button>
                {{-- Edit --}}
                <button type="button"
                    wire:click="openEdit({{ $account->id }})"
                    class="nx-tree-action-btn nx-tree-action-btn--edit"
                    title="Editar conta">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                </button>
                {{-- Delete --}}
                <button type="button"
                    wire:click="confirmDelete({{ $account->id }})"
                    wire:confirm="Deseja excluir a conta '{{ $account->name }}'? Esta ação não pode ser desfeita."
                    class="nx-tree-action-btn nx-tree-action-btn--delete"
                    title="Excluir conta">
                    <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="3 6 5 6 21 6"/>
                        <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                        <path d="M10 11v6"/><path d="M14 11v6"/>
                        <path d="M9 6V4h6v2"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ── CHILDREN (collapsible) ───────────────────── --}}
        @if($hasChildren)
            <div x-show="open"
                 x-transition:enter="nx-tree-transition-enter"
                 x-transition:enter-start="nx-tree-transition-enter-start"
                 x-transition:enter-end="nx-tree-transition-enter-end"
                 x-transition:leave="nx-tree-transition-leave"
                 x-transition:leave-start="nx-tree-transition-leave-start"
                 x-transition:leave-end="nx-tree-transition-leave-end">
                @include('livewire.financeiro.plano-contas._tree-item', [
                    'accounts' => $account->children,
                    'depth'    => $depth + 1,
                ])
            </div>
        @endif

    </div>
@endforeach

