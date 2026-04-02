@extends('layouts.app')

@section('title', 'Início — Módulos')

@section('content')

{{-- ══════════════════════════════════════
     BANNER DE BOAS-VINDAS
══════════════════════════════════════ --}}
<div class="hub-banner">
    <div class="hub-banner-content">
        <div class="hub-banner-text">
            <span class="hub-banner-tag">Nexora ERP</span>
            <h1 class="hub-banner-title">Bem-vindo ao Sistema</h1>
            <p class="hub-banner-subtitle">
                Acesse os módulos abaixo para gerenciar todos os processos da sua empresa em um só lugar.
            </p>
        </div>
        <div class="hub-banner-stats">
            <div class="hub-stat">
                <span class="hub-stat-value">{{ count($modules) }}</span>
                <span class="hub-stat-label">Módulos</span>
            </div>
            <div class="hub-stat-divider"></div>
            <div class="hub-stat">
                <span class="hub-stat-value">{{ collect($modules)->sum(fn($m) => count($m['items'])) }}</span>
                <span class="hub-stat-label">Funcionalidades</span>
            </div>
        </div>
    </div>
    <div class="hub-banner-deco" aria-hidden="true">
        <div class="hub-deco-circle hub-deco-circle--1"></div>
        <div class="hub-deco-circle hub-deco-circle--2"></div>
        <div class="hub-deco-circle hub-deco-circle--3"></div>
    </div>
</div>

{{-- ══════════════════════════════════════
     CABEÇALHO DA SECÇÃO
══════════════════════════════════════ --}}
<div class="hub-section-header">
    <h2 class="hub-section-title">
        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none"
             stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/>
            <rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/>
        </svg>
        Todos os Módulos
    </h2>
    <span class="hub-section-count">{{ count($modules) }} módulos disponíveis</span>
</div>

{{-- ══════════════════════════════════════
     GRADE DE MÓDULOS
══════════════════════════════════════ --}}
<div class="hub-grid">
    @foreach($modules as $slug => $module)
        @php $count = count($module['items']); @endphp
        <a href="{{ route('module.show', $slug) }}"
           class="hub-card"
           style="--hub-color: {{ $module['color'] }}">

            {{-- Barra colorida no topo --}}
            <div class="hub-card-bar"></div>

            {{-- Cabeçalho do card --}}
            <div class="hub-card-header">
                <div class="hub-card-icon">
                    {!! $module['icon'] !!}
                </div>
            </div>

            {{-- Corpo do card --}}
            <div class="hub-card-body">
                <h3 class="hub-card-title">{{ $module['name'] }}</h3>
                <p class="hub-card-desc">{{ $module['description'] }}</p>
            </div>

            {{-- Prévia dos itens --}}
            <ul class="hub-card-items">
                @foreach(array_slice($module['items'], 0, 3) as $item)
                    <li class="hub-card-item">
                        <span class="hub-card-item-dot"></span>
                        {{ $item['title'] }}
                    </li>
                @endforeach
                @if($count > 3)
                    <li class="hub-card-item hub-card-item--more">+{{ $count - 3 }} mais</li>
                @endif
            </ul>

            {{-- Rodapé do card --}}
            <div class="hub-card-footer">
                <span class="hub-card-action">Acessar módulo</span>
                <svg class="hub-card-arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="5" y1="12" x2="19" y2="12"/>
                    <polyline points="12 5 19 12 12 19"/>
                </svg>
            </div>

        </a>
    @endforeach
</div>

@endsection
