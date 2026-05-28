{{-- NexPagination Simple — Laravel Controller (simplePaginate) --}}
<div class="nexpag nexpag--simple" role="navigation" aria-label="Navegação de páginas">
    <div class="nexpag-info">
        <span class="nexpag-count">
            Página <strong>{{ $paginator->currentPage() }}</strong>
        </span>
    </div>
    <div class="nexpag-nav-wrap">
        <div class="nexpag-pages">
            @if ($paginator->onFirstPage())
                <span class="nexpag-btn nexpag-btn--prev nexpag-btn--disabled" aria-disabled="true">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    <span>Anterior</span>
                </span>
            @else
                <a wire:navigate href="{{ $paginator->previousPageUrl() }}" rel="prev" class="nexpag-btn nexpag-btn--prev">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    <span>Anterior</span>
                </a>
            @endif
            <div class="nexpag-separator"></div>
            @if ($paginator->hasMorePages())
                <a wire:navigate href="{{ $paginator->nextPageUrl() }}" rel="next" class="nexpag-btn nexpag-btn--next">
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
    </div>
    <div class="nexpag-perpage" style="visibility:hidden;pointer-events:none;"></div>
</div>

