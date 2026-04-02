@extends('layouts.app')

@section('title', $module['name'])

@section('content')

{{-- ══════════════════════════════════════
     CABEÇALHO DO MÓDULO
══════════════════════════════════════ --}}
<div class="mod-header">
    <div class="mod-header-inner">
        <div class="mod-header-icon" style="--mod-color: {{ $module['color'] }}">
            {!! $module['icon'] !!}
        </div>
        <div class="mod-header-info">
            <nav class="mod-breadcrumb" aria-label="breadcrumb">
                <a href="{{ route('home') }}" class="mod-breadcrumb-link">Início</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                <span class="mod-breadcrumb-current">{{ $module['name'] }}</span>
            </nav>
            <h1 class="mod-title">{{ $module['name'] }}</h1>
            <p class="mod-description">{{ $module['description'] }}</p>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     GRADE DE PÁGINAS DO MÓDULO
══════════════════════════════════════ --}}
<div class="mod-grid">
    @foreach($module['items'] as $item)
        @php
            $href = $item['route'] ? route($item['route']) : '#';
        @endphp
        <a href="{{ $href }}"
           class="mod-card"
           style="--mod-color: {{ $module['color'] }}">

            <div class="mod-card-icon">
                {!! $item['icon'] !!}
            </div>

            <div class="mod-card-body">
                <h3 class="mod-card-title">{{ $item['title'] }}</h3>
                <p class="mod-card-desc">{{ $item['description'] }}</p>
            </div>

            <div class="mod-card-arrow">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
            </div>
        </a>
    @endforeach
</div>

@endsection

