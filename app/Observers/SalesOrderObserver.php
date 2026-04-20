<?php

namespace App\Observers;

use App\Models\AccountReceivable;
use App\Models\FiscalNote;
use App\Models\SalesOrder;
use App\Models\StockMovement;
use App\Enums\SalesOrderStatus;
use App\Enums\ReceivableStatus;
use App\Enums\FiscalNoteStatus;
use Illuminate\Support\Facades\DB;
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

        // Registrar log de auditoria
        $salesOrder->logAction('created', 'Pedido criado');

        // Verifica se precisa de aprovação
        $this->checkIfNeedsApproval($salesOrder);

        Log::info("Pedido {$salesOrder->order_number} criado", [
            'order_id'     => $salesOrder->id,
            'client_id'    => $salesOrder->client_id,
            'total_amount' => $salesOrder->total_amount,
        ]);
    }

    /**
     * Handle the SalesOrder "updated" event.
     */
    public function updated(SalesOrder $salesOrder): void
    {
        // Se mudou de status — registrar log de auditoria + tratar negócio
        if ($salesOrder->wasChanged('status')) {
            $salesOrder->logAction(
                'status_changed',
                'Status alterado de ' . $salesOrder->getOriginal('status') . ' para ' . $salesOrder->status->value,
                $salesOrder->getOriginal('status'),
                $salesOrder->status->value
            );
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
     * Quando pedido entra em separação — debita estoque reservado
     */
    protected function onSeparation(SalesOrder $salesOrder): void
    {
        if (!$salesOrder->separation_date) {
            $salesOrder->separation_date = now();
            $salesOrder->saveQuietly();
        }

        Log::info("Pedido {$salesOrder->order_number} entrou em separação");

        // Estoque — saída (output) por item do pedido
        DB::transaction(function () use ($salesOrder) {
            $salesOrder->loadMissing('items.product');
            foreach ($salesOrder->items as $item) {
                if (!$item->product_id) continue;

                StockMovement::create([
                    'product_id'  => $item->product_id,
                    'user_id'     => $salesOrder->created_by ?? auth()->id(),
                    'quantity'    => $item->quantity,
                    'type'        => 'output',
                    'origin'      => 'sales_order',
                    'unit_cost'   => $item->unit_price,
                    'observation' => 'Separação do Pedido #' . $salesOrder->order_number,
                ]);

                // Decrementa o estoque do produto
                if ($item->product && $item->product->stock >= $item->quantity) {
                    $item->product->decrement('stock', $item->quantity);
                }
            }
        });
    }

    /**
     * Quando pedido é faturado — gera contas a receber e rascunho de NF-e
     */
    protected function onInvoiced(SalesOrder $salesOrder): void
    {
        Log::info("Pedido {$salesOrder->order_number} faturado");

        DB::transaction(function () use ($salesOrder) {
            $salesOrder->loadMissing('installments', 'client');

            // 1. Financeiro — criar contas a receber por parcela
            $installments = $salesOrder->installments;
            if ($installments->isNotEmpty()) {
                foreach ($installments as $i => $installment) {
                    AccountReceivable::create([
                        'description_title'  => 'Pedido #' . $salesOrder->order_number . ' - Parcela ' . ($i + 1) . '/' . $installments->count(),
                        'client_id'          => $salesOrder->client_id,
                        'amount'             => $installment->amount,
                        'received_amount'    => 0,
                        'due_date_at'        => $installment->due_date,
                        'installment_number' => $i + 1,
                        'payment_method'     => $salesOrder->payment_method ?? null,
                        'status'             => ReceivableStatus::Pending->value,
                        'observation'        => 'Gerado automaticamente do Pedido de Venda #' . $salesOrder->order_number,
                    ]);
                }
            } elseif ($salesOrder->total_amount > 0) {
                // Sem parcelas definidas — uma única conta a receber
                AccountReceivable::create([
                    'description_title'  => 'Pedido #' . $salesOrder->order_number,
                    'client_id'          => $salesOrder->client_id,
                    'amount'             => $salesOrder->total_amount,
                    'received_amount'    => 0,
                    'due_date_at'        => now()->addDays(30),
                    'installment_number' => 1,
                    'status'             => ReceivableStatus::Pending->value,
                    'observation'        => 'Gerado automaticamente do Pedido de Venda #' . $salesOrder->order_number,
                ]);
            }

            // 2. Fiscal — criar rascunho de NF-e se pedido fiscal
            if ($salesOrder->is_fiscal) {
                FiscalNote::create([
                    'client_id'      => $salesOrder->client_id,
                    'sales_order_id' => $salesOrder->id,
                    'type'           => 'nfe',
                    'environment'    => config('settings.fiscal_ambiente', 'homologation'),
                    'status'         => FiscalNoteStatus::Draft->value,
                    'amount'         => $salesOrder->total_amount,
                    'emitted_by'     => auth()->id(),
                    'notes'          => 'Gerado automaticamente do Pedido #' . $salesOrder->order_number,
                ]);
            }
        });
    }

    /**
     * Quando pedido é cancelado — reverte estoque, cancela contas a receber e NF-e rascunho
     */
    protected function onCancelled(SalesOrder $salesOrder): void
    {
        Log::warning("Pedido {$salesOrder->order_number} cancelado — iniciando reversão");

        DB::transaction(function () use ($salesOrder) {
            $salesOrder->loadMissing('items.product');

            // 1. Reverter movimentações de estoque (criar entrada de devolução)
            foreach ($salesOrder->items as $item) {
                if (!$item->product_id) continue;

                // Verifica se já havia saída de estoque para este pedido
                $hadOutput = StockMovement::where('origin', 'sales_order')
                    ->where('observation', 'like', '%#' . $salesOrder->order_number . '%')
                    ->where('type', 'output')
                    ->where('product_id', $item->product_id)
                    ->exists();

                if ($hadOutput) {
                    StockMovement::create([
                        'product_id'  => $item->product_id,
                        'user_id'     => auth()->id() ?? $salesOrder->created_by,
                        'quantity'    => $item->quantity,
                        'type'        => 'input',
                        'origin'      => 'sales_order_cancel',
                        'unit_cost'   => $item->unit_price,
                        'observation' => 'Cancelamento do Pedido #' . $salesOrder->order_number,
                    ]);

                    // Incrementa estoque do produto
                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }
            }

            // 2. Cancelar contas a receber pendentes geradas por este pedido
            AccountReceivable::where('observation', 'like', '%Pedido de Venda #' . $salesOrder->order_number . '%')
                ->whereIn('status', [ReceivableStatus::Pending->value, ReceivableStatus::Overdue->value])
                ->update(['status' => ReceivableStatus::Cancelled->value]);

            // 3. Cancelar NF-e rascunho associada (apenas rascunhos — autorizadas precisam cancelar na SEFAZ)
            FiscalNote::where('sales_order_id', $salesOrder->id)
                ->where('status', FiscalNoteStatus::Draft->value)
                ->update(['status' => FiscalNoteStatus::Cancelled->value]);

            Log::info("Pedido {$salesOrder->order_number} — reversão concluída");
        });
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

