<?php

namespace App\Observers;

use App\Models\SalesOrder;
use App\Enums\SalesOrderStatus;
use Illuminate\Support\Facades\Log;

class SalesOrderObserver
{
    /**
     * Handle the SalesOrder "created" event.
     */
    public function created(SalesOrder $salesOrder): void
    {
        // Garante que os itens e produtos estejam carregados
        $salesOrder->load('items.product');

        // Verifica se precisa de aprovação
        $this->checkIfNeedsApproval($salesOrder);

        // Notificação (implementar conforme necessário)
        // event(new SalesOrderCreated($salesOrder));

        Log::info("Pedido {$salesOrder->order_number} criado", [
            'order_id' => $salesOrder->id,
            'client_id' => $salesOrder->client_id,
            'total_amount' => $salesOrder->total_amount,
        ]);
    }

    /**
     * Handle the SalesOrder "updated" event.
     */
    public function updated(SalesOrder $salesOrder): void
    {
        // Se mudou de status
        if ($salesOrder->wasChanged('status')) {
            $this->handleStatusChange($salesOrder);
        }

        // Se foi aprovado
        if ($salesOrder->wasChanged('approved_at') && $salesOrder->approved_at) {
            $this->handleApproval($salesOrder);
        }
    }

    /**
     * Handle the SalesOrder "deleted" event.
     */
    public function deleted(SalesOrder $salesOrder): void
    {
        Log::warning("Pedido {$salesOrder->order_number} deletado", [
            'order_id' => $salesOrder->id,
        ]);
    }

    /**
     * Verifica se o pedido precisa de aprovação
     */
    protected function checkIfNeedsApproval(SalesOrder $salesOrder): void
    {
        $needsApproval = false;
        $reasons = [];

        // Verifica NCM e Grupo Tributário para pedidos fiscais
        if ($salesOrder->is_fiscal) {
            $itemsWithoutNCM = [];
            $itemsWithoutGrupoTributario = [];

            foreach ($salesOrder->items as $item) {
                // Verifica NCM no item ou no produto
                $hasNCM = !empty($item->ncm) || (!empty($item->product) && !empty($item->product->ncm));
                if (!$hasNCM) {
                    $itemsWithoutNCM[] = $item->description ?? $item->product?->name ?? "Item #{$item->id}";
                }

                // Verifica Grupo Tributário no produto
                if ($item->product && empty($item->product->grupo_tributario_id)) {
                    $itemsWithoutGrupoTributario[] = $item->product->name ?? "Item #{$item->id}";
                }
            }

            if (count($itemsWithoutNCM) > 0) {
                $needsApproval = true;
                $reasons[] = "Produtos sem NCM: " . implode(', ', array_slice($itemsWithoutNCM, 0, 3)) .
                            (count($itemsWithoutNCM) > 3 ? " e mais " . (count($itemsWithoutNCM) - 3) : "");
            }

            if (count($itemsWithoutGrupoTributario) > 0) {
                $needsApproval = true;
                $reasons[] = "Produtos sem Grupo Tributário: " . implode(', ', array_slice($itemsWithoutGrupoTributario, 0, 3)) .
                            (count($itemsWithoutGrupoTributario) > 3 ? " e mais " . (count($itemsWithoutGrupoTributario) - 3) : "");
            }
        }

        // Verifica desconto alto (acima de 10%)
        if ($salesOrder->total_amount > 0 && $salesOrder->gross_total > 0) {
            $discountPercent = ($salesOrder->discount_amount / $salesOrder->gross_total) * 100;
            if ($discountPercent > 10) {
                $needsApproval = true;
                $reasons[] = "Desconto acima de 10% ({$discountPercent}%)";
            }
        }

        // Verifica se cliente é inadimplente
        if ($salesOrder->client && $salesOrder->client->isDefaulter()) {
            $needsApproval = true;
            $reasons[] = "Cliente inadimplente";
        }

        // Verifica limite de crédito
        if ($salesOrder->client && !$salesOrder->client->hasAvailableCredit($salesOrder->total_amount ?? 0)) {
            $needsApproval = true;
            $reasons[] = "Limite de crédito excedido";
        }

        // Verifica valor alto (acima de R$ 10.000)
        if (($salesOrder->total_amount ?? 0) > 10000) {
            $needsApproval = true;
            $reasons[] = "Valor acima de R$ 10.000,00";
        }

        if ($needsApproval) {
            $salesOrder->needs_approval = true;
            $salesOrder->approval_reason = implode('; ', $reasons);
            $salesOrder->saveQuietly(); // Salva sem disparar eventos novamente

            Log::info("Pedido {$salesOrder->order_number} marcado para aprovação", [
                'reasons' => $reasons,
            ]);
        }
    }

    /**
     * Trata mudança de status
     */
    protected function handleStatusChange(SalesOrder $salesOrder): void
    {
        $oldStatus = $salesOrder->getOriginal('status');
        $newStatus = $salesOrder->status;

        Log::info("Pedido {$salesOrder->order_number} mudou de status", [
            'old_status' => $oldStatus,
            'new_status' => $newStatus->value,
        ]);

        match ($newStatus) {
            SalesOrderStatus::Approved => $this->onApproved($salesOrder),
            SalesOrderStatus::EmSeparacao => $this->onSeparation($salesOrder),
            SalesOrderStatus::Invoiced => $this->onInvoiced($salesOrder),
            SalesOrderStatus::Cancelled => $this->onCancelled($salesOrder),
            default => null,
        };
    }

    /**
     * Quando pedido é aprovado
     */
    protected function onApproved(SalesOrder $salesOrder): void
    {
        // Aqui você pode:
        // - Reservar estoque
        // - Enviar notificação para separação
        // - Criar registros financeiros

        Log::info("Pedido {$salesOrder->order_number} aprovado - iniciando reserva de estoque");

        // Exemplo: reservar estoque
        // foreach ($salesOrder->items as $item) {
        //     StockReservation::create([...]);
        // }
    }

    /**
     * Quando pedido entra em separação
     */
    protected function onSeparation(SalesOrder $salesOrder): void
    {
        // Atualiza data de separação se não foi definida
        if (!$salesOrder->separation_date) {
            $salesOrder->separation_date = now();
            $salesOrder->saveQuietly();
        }

        Log::info("Pedido {$salesOrder->order_number} entrou em separação");

        // Notificar equipe de separação
        // event(new SalesOrderReadyForSeparation($salesOrder));
    }

    /**
     * Quando pedido é faturado
     */
    protected function onInvoiced(SalesOrder $salesOrder): void
    {
        Log::info("Pedido {$salesOrder->order_number} faturado");

        // Aqui você pode:
        // - Dar baixa no estoque (converter reserva em movimentação)
        // - Gerar contas a receber
        // - Enviar email para cliente com NF-e

        // Exemplo: criar contas a receber
        // foreach ($salesOrder->installments as $installment) {
        //     AccountReceivable::create([...]);
        // }
    }

    /**
     * Quando pedido é cancelado
     */
    protected function onCancelled(SalesOrder $salesOrder): void
    {
        Log::warning("Pedido {$salesOrder->order_number} cancelado");

        // Aqui você pode:
        // - Devolver estoque reservado
        // - Cancelar contas a receber
        // - Cancelar NF-e se já foi emitida

        // Exemplo: devolver estoque
        // foreach ($salesOrder->items as $item) {
        //     StockReservation::where('sales_order_id', $salesOrder->id)->delete();
        // }
    }

    /**
     * Quando pedido é aprovado (approved_at preenchido)
     */
    protected function handleApproval(SalesOrder $salesOrder): void
    {
        Log::info("Pedido {$salesOrder->order_number} aprovado por {$salesOrder->approver->name}");

        // Notificar vendedor
        // Notificar cliente
        // event(new SalesOrderApproved($salesOrder));
    }
}

