{{--
    ╔══════════════════════════════════════════════════════════════════╗
    ║          NexPagination — Paginação Global Nexora EMS ERP         ║
    ║  Componente enterprise-level reutilizável em todo o ecossistema  ║
    ╚══════════════════════════════════════════════════════════════════╝
    Uso: {{ $items->links() }}
    Backend: ->paginate(PerPage::resolve())
    Suporta: ?page=2&per_page=25&search=...
--}}
@php
    use App\Support\PerPage;
    $currentPerPage  = PerPage::resolve();
    $perPageOptions  = PerPage::OPTIONS;
    $totalItems      = $paginator->total();
    $firstItem       = $paginator->firstItem() ?? 0;
    $lastItem        = $paginator->lastItem() ?? 0;
    $currentPage     = $paginator->currentPage();
    $lastPage        = $paginator->lastPage();
    $formattedTotal  = number_format($totalItems, 0, ',', '.');
    $formattedFirst  = number_format($firstItem, 0, ',', '.');
    $formattedLast   = number_format($lastItem, 0, ',', '.');
    $itemLabel       = $totalItems === 1 ? 'registro' : 'registros';
@endphp

<div
    class="nexpag"
    role="navigation"
    aria-label="Navegação de páginas"
    x-data="nexPagination()"
    x-init="init()"
>

    {{-- ── Coluna esquerda: resumo de registros ─────────────────── --}}
    <div class="nexpag-info">
        @if($totalItems > 0)
            <span class="nexpag-count">
                Mostrando
                <strong>{{ $formattedFirst }}–{{ $formattedLast }}</strong>
                de
                <strong>{{ $formattedTotal }}</strong>
                {{ $itemLabel }}
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
                <a wire:navigate href="{{ $paginator->previousPageUrl() }}" rel="prev" class="nexpag-btn nexpag-btn--arrow" @click="scrollTop()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </a>
            @endif
            <span class="nexpag-mobile-label">
                Página <strong>{{ $currentPage }}</strong> de <strong>{{ $lastPage }}</strong>
            </span>
            @if ($paginator->hasMorePages())
                <a wire:navigate href="{{ $paginator->nextPageUrl() }}" rel="next" class="nexpag-btn nexpag-btn--arrow" @click="scrollTop()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </a>
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
                    <a wire:navigate href="{{ $paginator->previousPageUrl() }}" rel="prev" class="nexpag-btn nexpag-btn--prev" @click="scrollTop()">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                        <span>Anterior</span>
                    </a>
                @endif

                <div class="nexpag-separator" aria-hidden="true"></div>

                {{-- Números --}}
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
                                <a wire:navigate href="{{ $url }}" class="nexpag-btn nexpag-btn--page" aria-label="Página {{ $page }}" @click="scrollTop()">{{ $page }}</a>
                            @endif
                        @endforeach
                    @endif
                @endforeach

                <div class="nexpag-separator" aria-hidden="true"></div>

                {{-- Botão Próximo --}}
                @if ($paginator->hasMorePages())
                    <a wire:navigate href="{{ $paginator->nextPageUrl() }}" rel="next" class="nexpag-btn nexpag-btn--next" @click="scrollTop()">
                        <span>Próximo</span>
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </a>
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
        <form method="GET" class="nexpag-perpage-form" x-ref="perPageForm">

            @foreach(request()->query() as $key => $value)
                @continue(in_array($key, ['page', 'per_page'], true))
                @if(is_array($value))
                    @foreach($value as $item)
                        <input type="hidden" name="{{ $key }}[]" value="{{ $item }}">
                    @endforeach
                @else
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endif
            @endforeach

            <label for="nexpag-perpage-sel" class="nexpag-perpage-label">Itens por página</label>
            <div class="nexpag-perpage-select-wrap">
                <select
                    id="nexpag-perpage-sel"
                    name="per_page"
                    class="nexpag-perpage-select"
                    @change="onPerPageChange($event)"
                    aria-label="Itens por página"
                >
                    @foreach($perPageOptions as $option)
                        <option value="{{ $option }}" @selected($currentPerPage === $option)>{{ $option }}</option>
                    @endforeach
                </select>
                <svg class="nexpag-perpage-chevron" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
            </div>
        </form>
    </div>

</div>

@once
<script>
function nexPagination() {
    return {
        init() {
            // Sincroniza select com preferência salva no localStorage
            const saved = parseInt(localStorage.getItem('nex_per_page'));
            const allowed = [10, 25, 50, 100];
            const current = {{ $currentPerPage }};
            if (saved && allowed.includes(saved) && saved !== current) {
                const sel = document.getElementById('nexpag-perpage-sel');
                if (sel) sel.value = saved;
            }
        },
        onPerPageChange(event) {
            const value = parseInt(event.target.value);
            if ([10, 25, 50, 100].includes(value)) {
                localStorage.setItem('nex_per_page', value);
            }
            this.$refs.perPageForm.submit();
        },
        scrollTop() {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    }
}
</script>
@endonce
