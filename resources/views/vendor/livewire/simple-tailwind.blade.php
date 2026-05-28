{{-- NexPagination Simple — Livewire (usado por simplePaginate()) --}}
@php
    $pageName = $paginator->getPageName();
@endphp

<div class="nexpag nexpag--simple" role="navigation" aria-label="Navegação de páginas">

    <div class="nexpag-info">
        <span class="nexpag-count">
            Página <strong>{{ $paginator->currentPage() }}</strong>
            @if(!$paginator->onFirstPage() || $paginator->hasMorePages())
                de pelo menos <strong>{{ $paginator->currentPage() }}</strong>
            @endif
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
                <button type="button" class="nexpag-btn nexpag-btn--prev" wire:click="previousPage('{{ $pageName }}')" wire:loading.attr="disabled">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    <span>Anterior</span>
                </button>
            @endif

            <div class="nexpag-separator" aria-hidden="true"></div>

            @if ($paginator->hasMorePages())
                <button type="button" class="nexpag-btn nexpag-btn--next" wire:click="nextPage('{{ $pageName }}')" wire:loading.attr="disabled">
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
    </div>

    <div class="nexpag-perpage" style="visibility:hidden;pointer-events:none;"></div>
</div>

