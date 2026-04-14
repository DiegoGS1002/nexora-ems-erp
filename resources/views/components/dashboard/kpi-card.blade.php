@props([
    'title',
    'value' => 0,
    'icon' => null,
    'iconBg' => '#EFF6FF',
    'iconColor' => '#3B82F6',
    'currency' => false,
    'trend' => null,
    'subtitle' => null,
])

@php
    $formattedValue = $currency
        ? 'R$ ' . number_format((float) $value, 2, ',', '.')
        : number_format((float) $value, 0, ',', '.');

    $trendText  = is_string($trend) ? trim($trend) : null;
    $isNegative = $trendText && str_starts_with($trendText, '-');
    $trendClass = $isNegative ? 'is-negative' : 'is-positive';
@endphp

<div class="nx-kpi-card">
    <div class="nx-kpi-card-inner">
        <div class="nx-kpi-card-left">
            <p class="nx-kpi-card-title">
                {{ $title }}
                @if($subtitle)
                    <span class="nx-kpi-card-subtitle">{{ $subtitle }}</span>
                @endif
            </p>
            <h2 class="nx-kpi-card-value">{{ $formattedValue }}</h2>
            <span class="nx-kpi-card-trend {{ $trendText ? $trendClass : '' }} {{ !$trendText ? 'nx-trend-empty' : '' }}">
                @if($trendText)
                    @if(!$isNegative)
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="18 15 12 9 6 15"/></svg>
                    @else
                        <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    @endif
                    {{ $trendText }} vs mês ant.
                @else
                    &nbsp;
                @endif
            </span>
        </div>
        @if($icon)
            <div class="nx-kpi-card-icon" style="background: {{ $iconBg }}; color: {{ $iconColor }}; border-color: transparent;">
                {!! $icon !!}
            </div>
        @endif
    </div>
</div>
