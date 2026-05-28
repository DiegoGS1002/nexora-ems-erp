{{--
    ╔══════════════════════════════════════════════════════════════════╗
    ║   NexPagination — Livewire Edition                              ║
    ║   resources/views/vendor/livewire/tailwind.blade.php            ║
    ║                                                                  ║
    ║   Usado por: componentes com `use WithPagination`               ║
    ║   Navegação: wire:click (AJAX, sem reload)                      ║
    ║   Per-page:  $wire.set('perPage', value) via Alpine             ║
    ╚══════════════════════════════════════════════════════════════════╝
--}}
@php
    use App\Support\PerPage;

    $pageName       = $paginator->getPageName();
    $totalItems     = $paginator->total();
    $firstItem      = $paginator->firstItem() ?? 0;
    $lastItem       = $paginator->lastItem() ?? 0;
    $currentPage    = $paginator->currentPage();
    $lastPage       = $paginator->lastPage();
    $perPageOptions = PerPage::OPTIONS;

    // Tenta ler o perPage atual do componente Livewire ou query string
    $currentPerPage = \App\Support\PerPage::resolve();
@endphp

<div
    class="nexpag"
    role="navigation"
    aria-label="Navegação de páginas"
    x-data="nexPaginationLw()"
    x-init="init()"
>
    {{-- ── Coluna esquerda: resumo de registros ─────────────────── --}}
    <div class="nexpag-info">
        @if($totalItems > 0)
            <span class="nexpag-count">
                Mostrando
                <strong>{{ number_format($firstItem, 0, ',', '.') }}–{{ number_format($lastItem, 0, ',', '.') }}</strong>
                de
                <strong>{{ number_format($totalItems, 0, ',', '.') }}</strong>
                {{ $totalItems === 1 ? 'registro' : 'registros' }}
            </span>
        @else
            <span class="nexpag-count nexpag-count--empty">Nenhum registro encontrado</span>
        @endif
    </div>

    {{-- ── Coluna central: navegação ─────────────────────────────── --}}
    <div class="nexpag-nav-wrap">

        {{-- Mobile: < Página X de Y > --}}
        <div class="nexpag-mobile">
            @if ($paginator->onFirstPage())
                <span class="nexpag-btn nexpag-btn--arrow nexpag-btn--disabled" aria-disabled="true">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </span>
            @else
                <button
                    type="button"
                    class="nexpag-btn nexpag-btn--arrow"
                    wire:click="previousPage('{{ $pageName }}')"
                    wire:loading.attr="disabled"
                    aria-label="Página anterior"
                >
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
            @endif

            <span class="nexpag-mobile-label">
                Página <strong>{{ $currentPage }}</strong> de <strong>{{ $lastPage }}</strong>
            </span>

            @if ($paginator->hasMorePages())
                <button
                    type="button"
                    class="nexpag-btn nexpag-btn--arrow"
                    wire:click="nextPage('{{ $pageName }}')"
                    wire:loading.attr="disabled"
                    aria-label="Próxima página"
                >
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </button>
            @else
                <span class="nexpag-btn nexpag-btn--arrow nexpag-btn--disabled" aria-disabled="true">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </span>
            @endif
        </div>

        {{-- Desktop: Anterior | 1 ... 5 6 [7] 8 9 ... 20 | Próximo --}}
        <nav class="nexpag-nav nexpag-desktop" aria-label="Páginas">
            <div class="nexpag-pages">

                {{-- Botão Anterior --}}
                @if ($paginator->onFirstPage())
                    <span class="nexpag-btn nexpag-btn--prev nexpag-btn--disabled" aria-disabled="true">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                        <span>Anterior</span>
                    </span>
                @else
                    <button
                        type="button"
                        class="nexpag-btn nexpag-btn--prev"
                        wire:click="previousPage('{{ $pageName }}')"
                        wire:loading.attr="disabled"
                        dusk="previousPage{{ $pageName == 'page' ? '' : '.' . $pageName }}"
                    >
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                        <span>Anterior</span>
                    </button>
                @endif

                <div class="nexpag-separator" aria-hidden="true"></div>

                {{-- Números de página --}}
                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="nexpag-btn nexpag-btn--dots" aria-hidden="true">
                            <svg width="14" height="4" viewBox="0 0 24 4" fill="currentColor"><circle cx="3" cy="2" r="2"/><circle cx="12" cy="2" r="2"/><circle cx="21" cy="2" r="2"/></svg>
                        </span>
                    @endif
                    @if (is_array($element))
                        @foreach ($element as $page => $url)
                            @if ($page == $paginator->currentPage())
                                <span class="nexpag-btn nexpag-btn--page nexpag-btn--active" aria-current="page">{{ $page }}</span>
                            @else
                                <button
                                    type="button"
                                    class="nexpag-btn nexpag-btn--page"
                                    wire:click="gotoPage({{ $page }}, '{{ $pageName }}')"
                                    wire:loading.attr="disabled"
                                    aria-label="Página {{ $page }}"
                                    dusk="page{{ $page }}"
                                >{{ $page }}</button>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                <div class="nexpag-separator" aria-hidden="true"></div>

                {{-- Botão Próximo --}}
                @if ($paginator->hasMorePages())
                    <button
                        type="button"
                        class="nexpag-btn nexpag-btn--next"
                        wire:click="nextPage('{{ $pageName }}')"
                        wire:loading.attr="disabled"
                        dusk="nextPage{{ $pageName == 'page' ? '' : '.' . $pageName }}"
                    >
                        <span>Próximo</span>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                @else
                    <span class="nexpag-btn nexpag-btn--next nexpag-btn--disabled" aria-disabled="true">
                        <span>Próximo</span>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </span>
                @endif

            </div>
        </nav>

    </div>

    {{-- ── Coluna direita: seletor de itens por página ───────────── --}}
    <div class="nexpag-perpage">
        <label for="nexpag-lw-sel" class="nexpag-perpage-label">Itens por página</label>
        <div class="nexpag-perpage-select-wrap">
            <select
                id="nexpag-lw-sel"
                class="nexpag-perpage-select"
                @change="onPerPageChange($event)"
                aria-label="Itens por página"
                wire:loading.attr="disabled"
            >
                @foreach($perPageOptions as $opt)
                    <option value="{{ $opt }}" @selected($currentPerPage === $opt)>{{ $opt }}</option>
                @endforeach
            </select>
            <svg class="nexpag-perpage-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
        </div>
    </div>

    {{-- Loading overlay discreto --}}
    <div
        class="nexpag-loading"
        wire:loading.class.remove="nexpag-loading--hidden"
        wire:loading.class="nexpag-loading--visible"
        style="display:none"
        x-bind:style="$wire.__isBusy ? 'display:block' : 'display:none'"
    ></div>

</div>

@once
<script>
function nexPaginationLw() {
    return {
        init() {
            // Aplica preferência salva do localStorage no select
            const saved = parseInt(localStorage.getItem('nex_per_page'));
            const allowed = [10, 25, 50, 100];
            if (saved && allowed.includes(saved)) {
                const sel = document.getElementById('nexpag-lw-sel');
                if (sel && parseInt(sel.value) !== saved) {
                    sel.value = saved;
                }
            }
        },
        onPerPageChange(event) {
            const value = parseInt(event.target.value);
            if ([10, 25, 50, 100].includes(value)) {
                localStorage.setItem('nex_per_page', value);
            }
            // Usa $wire.set() para atualizar perPage via Livewire AJAX
            if (typeof this.$wire !== 'undefined' && 'perPage' in this.$wire) {
                this.$wire.set('perPage', value);
            } else {
                // Fallback: form submit via URL
                const url = new URL(window.location.href);
                url.searchParams.set('per_page', value);
                url.searchParams.delete('page');
                window.location.href = url.toString();
            }
        }
    }
}
</script>
@endonce

