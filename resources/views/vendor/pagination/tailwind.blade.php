@php
    $currentPerPage = \App\Support\PerPage::resolve();
@endphp

<div class="nx-pag-layout" role="navigation" aria-label="Navegação de páginas">
    <div class="nx-pag-meta">
        <span class="nx-pag-count">
            Mostrando {{ number_format((int) ($paginator->firstItem() ?? 0), 0, ',', '.') }}
            a {{ number_format((int) ($paginator->lastItem() ?? 0), 0, ',', '.') }}
            de {{ number_format((int) $paginator->total(), 0, ',', '.') }} registros
        </span>

        <form method="GET" class="nx-pag-size-form">
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

            <label for="nx-per-page" class="nx-pag-size-label">Itens por página</label>
            <select id="nx-per-page" name="per_page" class="nx-pag-size-select" onchange="this.form.submit()">
                @foreach(\App\Support\PerPage::OPTIONS as $option)
                    <option value="{{ $option }}" @selected($currentPerPage === $option)>{{ $option }}</option>
                @endforeach
            </select>
        </form>
    </div>

    @if ($paginator->hasPages())
        <nav class="nx-pag-nav" aria-label="Páginas">
            <span class="nx-pag-wrap">
                @if ($paginator->onFirstPage())
                    <span class="nx-pag-btn nx-pag-btn-arrow nx-pag-disabled" aria-disabled="true" aria-label="Página anterior">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    </span>
                @else
                    <a wire:navigate href="{{ $paginator->previousPageUrl() }}" rel="prev" class="nx-pag-btn nx-pag-btn-arrow" aria-label="Página anterior">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                    </a>
                @endif

                @foreach ($elements as $element)
                    @if (is_string($element))
                        <span class="nx-pag-btn nx-pag-dots" aria-hidden="true">...</span>
                    @endif

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
</div>

