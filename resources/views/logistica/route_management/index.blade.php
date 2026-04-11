@extends('layouts.app')

@section('title', 'Gestao de Rotas')

@section('content')
<div class="mod-header">
    <div class="mod-header-inner">
        <div class="mod-header-icon" style="--mod-color: #0EA5E9">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                <circle cx="3" cy="12" r="2"/>
                <circle cx="21" cy="5" r="2"/>
                <circle cx="21" cy="19" r="2"/>
                <line x1="5" y1="12" x2="19" y2="5.5"/>
                <line x1="5" y1="12" x2="19" y2="18.5"/>
            </svg>
        </div>

        <div class="mod-header-info">
            <nav class="mod-breadcrumb" aria-label="breadcrumb">
                <a href="{{ route('home') }}" class="mod-breadcrumb-link">Inicio</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
                <a href="{{ route('module.show', 'transporte') }}" class="mod-breadcrumb-link">Transporte</a>
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="9 18 15 12 9 6"/>
                </svg>
                <span class="mod-breadcrumb-current">Gestao de Rotas</span>
            </nav>

            <h1 class="mod-title">Gestao de Rotas</h1>
            <p class="mod-description">Planejamento operacional das entregas com foco em prazo, custo e eficiencia.</p>
        </div>
    </div>
</div>

<div class="route-page">
    <div class="route-kpi-grid">
        <article class="route-kpi-card">
            <p class="route-kpi-label">Total de rotas</p>
            <p class="route-kpi-value">{{ $stats['total'] }}</p>
        </article>

        <article class="route-kpi-card">
            <p class="route-kpi-label">Rotas detalhadas</p>
            <p class="route-kpi-value">{{ $stats['withDescription'] }}</p>
        </article>

        <article class="route-kpi-card">
            <p class="route-kpi-label">Criadas hoje</p>
            <p class="route-kpi-value">{{ $stats['createdToday'] }}</p>
        </article>
    </div>

    <div class="route-content-grid">
        <section class="route-panel">
            <div class="route-panel-head">
                <h2 class="route-panel-title">Rotas cadastradas</h2>
                <p class="route-panel-subtitle">Cadastro base para gestao e roteirizacao.</p>
            </div>

            <form method="GET" action="{{ route('route_management.index') }}" class="route-search-row">
                <input
                    type="text"
                    name="search"
                    value="{{ $search }}"
                    class="route-search-input"
                    placeholder="Buscar por nome ou descricao"
                >
                <button type="submit" class="route-btn route-btn-primary">Buscar</button>
                @if($search !== '')
                    <a href="{{ route('route_management.index') }}" class="route-btn route-btn-ghost">Limpar</a>
                @endif
            </form>

            @if($routes->isEmpty())
                <div class="route-empty-state">
                    <p class="route-empty-title">Nenhuma rota encontrada.</p>
                    <p class="route-empty-text">Cadastre registros em `route_managements` para visualizar aqui.</p>
                </div>
            @else
                <div class="nx-table-wrap">
                    <table class="nx-table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Descricao</th>
                                <th>Criada em</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($routes as $routeItem)
                                <tr>
                                    <td>{{ $routeItem->name ?: '-' }}</td>
                                    <td>{{ $routeItem->description ?: '-' }}</td>
                                    <td>{{ $routeItem->created_at?->format('d/m/Y H:i') ?: '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="route-pagination">
                    {{ $routes->links() }}
                </div>
            @endif
        </section>

        <section class="route-panel">
            <div class="route-panel-head">
                <h2 class="route-panel-title">Parametros de roteirizacao</h2>
                <p class="route-panel-subtitle">Baseado no guia do modulo de transporte.</p>
            </div>

            <div class="route-chip-group">
                @foreach($parameters as $parameter)
                    <span class="route-chip">{{ $parameter }}</span>
                @endforeach
            </div>

            <h3 class="route-subtitle">Tipos suportados</h3>
            <ul class="route-list">
                @foreach($routingTypes as $routingType)
                    <li>{{ $routingType }}</li>
                @endforeach
            </ul>
        </section>
    </div>
</div>
@endsection

