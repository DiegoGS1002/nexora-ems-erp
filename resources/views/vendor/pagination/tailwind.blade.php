@if ($paginator->hasPages())
<nav class="nx-pag-nav" role="navigation" aria-label="Navegação de páginas">
    <span class="nx-pag-wrap">

        {{-- Botão Anterior --}}
        @if ($paginator->onFirstPage())
            <span class="nx-pag-btn nx-pag-btn-arrow nx-pag-disabled" aria-disabled="true" aria-label="Página anterior">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            </span>
        @else
            <a wire:navigate href="{{ $paginator->previousPageUrl() }}" rel="prev" class="nx-pag-btn nx-pag-btn-arrow" aria-label="Página anterior">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            </a>
        @endif

        {{-- Números de página --}}
        @foreach ($elements as $element)

            {{-- Reticências "..." --}}
            @if (is_string($element))
                <span class="nx-pag-btn nx-pag-dots" aria-hidden="true">…</span>
            @endif

            {{-- Array de links --}}
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <span class="nx-pag-btn nx-pag-active" aria-current="page">{{ $page }}</span>
                    @else
                        <a wire:navigate href="{{ $url }}" class="nx-pag-btn" aria-label="Ir para página {{ $page }}">{{ $page }}</a>
                    @endif
                @endforeach
            @endif

        @endforeach

        {{-- Botão Próximo --}}
        @if ($paginator->hasMorePages())
            <a wire:navigate href="{{ $paginator->nextPageUrl() }}" rel="next" class="nx-pag-btn nx-pag-btn-arrow" aria-label="Próxima página">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
            </a>
        @else
            <span class="nx-pag-btn nx-pag-btn-arrow nx-pag-disabled" aria-disabled="true" aria-label="Próxima página">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
            </span>
        @endif

    </span>
</nav>
@endif

