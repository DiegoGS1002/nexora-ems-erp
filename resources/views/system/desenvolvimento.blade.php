@extends('layouts.app')

@section('title', ($title ?? 'Em Desenvolvimento') . ' — Em Breve')

@section('content')

{{-- ══════════════════════════════════════
     CABEÇALHO COM BREADCRUMB
══════════════════════════════════════ --}}
<div class="mod-header">
    <div class="mod-header-inner">
        <div class="mod-header-icon" style="--mod-color: {{ $color ?? '#3B82F6' }}">
            {!! $icon ?? '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>' !!}
        </div>
        <div class="mod-header-info">
            <nav class="mod-breadcrumb" aria-label="breadcrumb">
                <a href="{{ route('home') }}" class="mod-breadcrumb-link">Início</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                @if(isset($moduleSlug))
                    <a href="{{ route('module.show', $moduleSlug) }}" class="mod-breadcrumb-link">{{ $moduleName ?? $moduleSlug }}</a>
                    <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
                @endif
                <span class="mod-breadcrumb-current">{{ $title ?? 'Em Desenvolvimento' }}</span>
            </nav>
            <div style="display:flex; align-items:center; gap:12px; flex-wrap:wrap;">
                <h1 class="mod-title">{{ $title ?? 'Em Desenvolvimento' }}</h1>
                <span class="eb-badge-inline">Em Breve</span>
            </div>
            <p class="mod-description">{{ $description ?? 'Esta funcionalidade está sendo desenvolvida.' }}</p>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════
     CONTEÚDO — ESTADO EM BREVE
══════════════════════════════════════ --}}
<div class="eb-wrapper">
    <div class="eb-card" style="--mod-color: {{ $color ?? '#3B82F6' }}">

        <div class="eb-icon-wrap">
            <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24"
                 fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
            </svg>
        </div>

        <span class="eb-badge">Em Breve</span>

        <h2 class="eb-title">Funcionalidade em Desenvolvimento</h2>
        <p class="eb-desc">
            A página <strong>{{ $title ?? 'esta funcionalidade' }}</strong> está sendo construída pela nossa equipe
            e estará disponível em breve. Agradecemos a paciência!
        </p>

        <div class="eb-actions">
            @if(isset($moduleSlug))
                <a href="{{ route('module.show', $moduleSlug) }}" class="eb-btn eb-btn--primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                    Voltar para {{ $moduleName ?? 'o módulo' }}
                </a>
            @endif
            <a href="{{ route('home') }}" class="eb-btn eb-btn--ghost">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
                     fill="none" stroke="currentColor" stroke-width="2">
                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                    <polyline points="9 22 9 12 15 12 15 22"/>
                </svg>
                Início
            </a>
        </div>
    </div>
</div>

@endsection
