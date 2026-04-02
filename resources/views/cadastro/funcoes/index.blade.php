@extends('layouts.app')
@section('title', 'Funções')
@section('content')

<div class="nx-list-page">

    <div class="nx-page-header">
        <div class="nx-page-header-left">
            <h1 class="nx-page-title">Funções</h1>
            <p class="nx-page-subtitle">Gerencie os cargos e funções dos funcionários</p>
        </div>
        <div class="nx-page-actions">
            <a href="{{ route('role.create') }}" class="nx-btn nx-btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                Nova Função
            </a>
        </div>
    </div>

    <div class="nx-card">
        @forelse($roles ?? [] as $role)
            @if($loop->first)
            <div class="nx-table-wrap">
                <table class="nx-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Descrição</th>
                            <th class="nx-th-actions">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
            @endif
                        <tr>
                            <td><strong>{{ $role->name }}</strong></td>
                            <td>{{ $role->description ?: '—' }}</td>
                            <td class="nx-td-actions">
                                <a href="{{ route('role.edit', $role) }}" class="nx-action-btn nx-action-edit" title="Editar">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                </a>
                                <form action="{{ route('role.destroy', $role) }}" method="POST" style="display:inline;">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="nx-action-btn nx-action-delete" title="Excluir"
                                            onclick="return confirm('Deseja excluir a função {{ addslashes($role->name) }}?')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4h6v2"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
            @if($loop->last)
                    </tbody>
                </table>
            </div>
            <div class="nx-table-footer">
                <span class="nx-table-count">{{ count($roles) }} {{ count($roles) === 1 ? 'função' : 'funções' }}</span>
            </div>
            @endif
        @empty
            @include('partials.empty-state', [
                'title'       => 'Nenhuma função cadastrada',
                'description' => 'Cadastre funções para organizar os cargos dos funcionários.',
                'actionLabel' => 'Nova Função',
                'actionRoute' => route('role.create'),
            ])
        @endforelse
    </div>

</div>
@endsection
