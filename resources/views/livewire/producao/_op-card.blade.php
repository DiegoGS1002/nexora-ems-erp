{{-- OP Card Component --}}
@php
    use App\Enums\ProductionOrderStatus;
    $totalTarget = $order->orderProducts->sum('target_quantity');
    $totalProduced = $order->orderProducts->sum('produced_quantity');
@endphp

<div class="nx-op-card {{ in_array($order->status, [ProductionOrderStatus::Paused, ProductionOrderStatus::Cancelled]) ? 'nx-op-card--muted' : '' }}" wire:key="op-{{ $order->id }}">

    {{-- Card Header --}}
    <div class="nx-op-card-header">
        <span class="nx-op-card-code">#OP-{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</span>
        @if(in_array($order->status, [ProductionOrderStatus::Paused, ProductionOrderStatus::Cancelled]))
            <span class="nx-op-badge {{ $order->status->badgeClass() }}">{{ $order->status->label() }}</span>
        @else
            <span class="nx-op-status-dot {{ $order->status === ProductionOrderStatus::InProgress ? 'nx-op-dot--pulse' : '' }}"
                  style="background:{{ $order->status->color() }}"></span>
        @endif
    </div>

    {{-- Products List --}}
    @if($order->orderProducts->count() > 0)
        <div class="nx-op-card-products">
            @foreach($order->orderProducts->take(2) as $op)
                <div class="nx-op-card-product-item">
                    <span class="nx-op-card-product-name">{{ $op->product?->name ?? 'Produto' }}</span>
                    <span class="nx-op-card-product-qty">{{ number_format($op->target_quantity, 0, ',', '.') }} UN</span>
                </div>
            @endforeach
            @if($order->orderProducts->count() > 2)
                <span class="nx-op-card-more">+ {{ $order->orderProducts->count() - 2 }} produto(s)</span>
            @endif
        </div>
    @else
        <h4 class="nx-op-card-product">{{ $order->name ?? 'Ordem de Produção' }}</h4>
    @endif

    {{-- Progress Bar --}}
    @php $pct = $order->progress_percentage; @endphp
    <div class="nx-op-progress-wrap">
        <div class="nx-op-progress-header">
            <span class="nx-op-progress-label">
                <svg xmlns="http://www.w3.org/2000/svg" width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/></svg>
                {{ number_format($totalProduced, 0, ',', '.') }} / {{ number_format($totalTarget, 0, ',', '.') }} UN
            </span>
            <span class="nx-op-progress-pct">{{ $pct }}%</span>
        </div>
        <div class="nx-op-progress-track">
            <div class="nx-op-progress-fill" style="width:{{ $pct }}%;background:{{ $order->status->color() }}"></div>
        </div>
    </div>

    {{-- Dates --}}
    @if($order->start_date || $order->end_date)
        <div class="nx-op-card-dates">
            @if($order->start_date)
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    Início: {{ $order->start_date->format('d/m/Y') }}
                </span>
            @endif
            @if($order->end_date)
                <span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    Fim: {{ $order->end_date->format('d/m/Y') }}
                </span>
            @endif
        </div>
    @endif

    {{-- Lot --}}
    @if($order->lot_number)
        <div class="nx-op-card-lot">
            <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
            Lote: {{ $order->lot_number }}
        </div>
    @endif

    {{-- Actions --}}
    <div class="nx-op-card-actions">
        <button type="button" wire:click="openDetail({{ $order->id }})"
                class="nx-op-action-btn nx-op-action-view" title="Ver detalhes">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
        </button>
        <button type="button" wire:click="edit({{ $order->id }})"
                class="nx-op-action-btn nx-op-action-edit" title="Editar">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        </button>

        {{-- Status quick-change --}}
        @if($order->status === ProductionOrderStatus::Planned)
            <button type="button" wire:click="changeStatus({{ $order->id }}, 'in_progress')"
                    class="nx-op-action-btn nx-op-action-start" title="Iniciar produção">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            </button>
        @elseif($order->status === ProductionOrderStatus::InProgress)
            <button type="button" wire:click="openProgressModal({{ $order->id }}, false)"
                    class="nx-op-action-btn nx-op-action-progress" title="Lançar progresso"
                    style="background:#ECFDF5;border-color:#6EE7B7;color:#059669;">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
            </button>
            <button type="button" wire:click="openProgressModal({{ $order->id }}, true)"
                    class="nx-op-action-btn nx-op-action-pause" title="Pausar">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><rect x="6" y="4" width="4" height="16"/><rect x="14" y="4" width="4" height="16"/></svg>
            </button>
            <button type="button" wire:click="changeStatus({{ $order->id }}, 'completed')"
                    wire:confirm="Confirmar conclusão desta ordem?"
                    class="nx-op-action-btn nx-op-action-finish" title="Finalizar">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
            </button>
        @elseif($order->status === ProductionOrderStatus::Paused)
            <button type="button" wire:click="changeStatus({{ $order->id }}, 'in_progress')"
                    class="nx-op-action-btn nx-op-action-start" title="Retomar produção">
                <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polygon points="5 3 19 12 5 21 5 3"/></svg>
            </button>
        @endif

        <button type="button" wire:click="delete({{ $order->id }})"
                wire:confirm="Tem certeza que deseja excluir esta OP?"
                class="nx-op-action-btn nx-op-action-delete" title="Excluir">
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
        </button>
    </div>

</div>

