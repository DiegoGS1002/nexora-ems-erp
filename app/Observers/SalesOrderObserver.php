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
    public function created(SalesOrder $salesOrder): void
    {
        $salesOrder->load('items.product');
        $salesOrder->logAction('created', 'Pedido criado');
        $this->checkIfNeedsApproval($salesOrder);

        Log::info("Pedido {$salesOrder->order_number} criado", [
            'order_id'     => $salesOrder->id,
            'client_id'    => $salesOrder->client_id,
            'total_amount' => $salesOrder->total_amount,
        ]);
    }

    public function updated(SalesOrder $salesOrder): void
    {
        if ($salesOrder->wasChanged('status')) {
            $oldRaw = $salesOrder->getOriginal('status');
            $oldValue = $oldRaw instanceof \BackedEnum ? $oldRaw->value : (string) $oldRaw;

            $salesOrder->logAction(
                'status_changed',
                'Status alterado de ' . $oldValue . ' para ' . $salesOrder->status->value,
                $oldValue,
                $salesOrder->status->value
            );
            $this->handleStatusChange($salesOrder);
        }

        if ($salesOrder->wasChanged('approved_at') && $salesOrder->approved_at) {
            $this->handleApproval($salesOrder);
        }
    }

    public function deleted(SalesOrder $salesOrder): void
    {
        Log::warning("Pedido {$salesOrder->order_number} deletado", [
            'order_id' => $salesOrder->id,
        ]);
    }

    protected function checkIfNeedsApproval(SalesOrder $salesOrder): void
    {
        $needsApproval = false;
        $reasons = [];

        if ($salesOrder->is_fiscal) {
            $itemsWithoutNCM = [];
            $itemsWithoutGrupoTributario = [];

            foreach ($salesOrder->items as $item) {
                $hasNCM = !empty($item->ncm) || (!empty($item->product) && !empty($item->product->ncm));
                if (!$hasNCM) {
                    $itemsWithoutNCM[] = $item->description ?? $item->product?->name ?? "Item #{$item->id}";
                }

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

        if ($salesOrder->total_amount > 0 && $salesOrder->gross_total > 0) {
            $discountPercent = ($salesOrder->discount_amount / $salesOrder->gross_total) * 100;
            if ($discountPercent > 10) {
                $needsApproval = true;
                $reasons[] = "Desconto acima de 10% ({$discountPercent}%)";
            }
        }

        if ($salesOrder->client && $salesOrder->client->isDefaulter()) {
            $needsApproval = true;
            $reasons[] = "Cliente inadimplente";
        }

        if ($salesOrder->client && !$salesOrder->client->hasAvailableCredit($salesOrder->total_amount ?? 0)) {
            $needsApproval = true;
            $reasons[] = "Limite de crédito excedido";
        }

        if (($salesOrder->total_amount ?? 0) > 10000) {
            $needsApproval = true;
            $reasons[] = "Valor acima de R$ 10.000,00";
        }

        if ($needsApproval) {
            $salesOrder->needs_approval = true;
            $salesOrder->approval_reason = implode('; ', $reasons);
            $salesOrder->saveQuietly();

            Log::info("Pedido {$salesOrder->order_number} marcado para aprovação", [
                'reasons' => $reasons,
            ]);
        }
    }

    protected function handleStatusChange(SalesOrder $salesOrder): void
    {
        $oldRaw = $salesOrder->getOriginal('status');
        $oldStatus = $oldRaw instanceof \BackedEnum ? $oldRaw->value : (string) $oldRaw;
        $newStatus = $salesOrder->status;

        Log::info("Pedido {$salesOrder->order_number} mudou de status", [
            'old_status' => $oldStatus,
            'new_status' => $newStatus->value,
        ]);

        match ($newStatus) {
            SalesOrderStatus::Approved      => $this->onApproved($salesOrder),
            SalesOrderStatus::EmSeparacao   => $this->onSeparation($salesOrder),
            SalesOrderStatus::Invoiced      => $this->onInvoiced($salesOrder),
            SalesOrderStatus::NfTransmitida => $this->onNfTransmitida($salesOrder),
            SalesOrderStatus::Cancelled     => $this->onCancelled($salesOrder),
            default => null,
        };
    }

    protected function onApproved(SalesOrder $salesOrder): void
    {
        Log::info("Pedido {$salesOrder->order_number} aprovado - iniciando reserva de estoque");
    }

    protected function onSeparation(SalesOrder $salesOrder): void
    {
        if (!$salesOrder->separation_date) {
            $salesOrder->separation_date = now();
            $salesOrder->saveQuietly();
        }

        Log::info("Pedido {$salesOrder->order_number} entrou em separação");

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

                if ($item->product && $item->product->stock >= $item->quantity) {
                    $item->product->decrement('stock', $item->quantity);
                }
            }
        });
    }

    protected function onInvoiced(SalesOrder $salesOrder): void
    {
        Log::info("Pedido {$salesOrder->order_number} faturado");

        DB::transaction(function () use ($salesOrder) {
            $salesOrder->loadMissing('installments', 'client');

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

    protected function onCancelled(SalesOrder $salesOrder): void
    {
        Log::warning("Pedido {$salesOrder->order_number} cancelado — iniciando reversão");

        DB::transaction(function () use ($salesOrder) {
            $salesOrder->loadMissing('items.product');

            foreach ($salesOrder->items as $item) {
                if (!$item->product_id) continue;

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

                    if ($item->product) {
                        $item->product->increment('stock', $item->quantity);
                    }
                }
            }

            AccountReceivable::where('observation', 'like', '%Pedido de Venda #' . $salesOrder->order_number . '%')
                ->whereIn('status', [ReceivableStatus::Pending->value, ReceivableStatus::Overdue->value])
                ->update(['status' => ReceivableStatus::Cancelled->value]);

            FiscalNote::where('sales_order_id', $salesOrder->id)
                ->where('status', FiscalNoteStatus::Draft->value)
                ->update(['status' => FiscalNoteStatus::Cancelled->value]);

            Log::info("Pedido {$salesOrder->order_number} — reversão concluída");
        });
    }

    protected function onNfTransmitida(SalesOrder $salesOrder): void
    {
        Log::info("Pedido {$salesOrder->order_number} — NF-e transmitida/autorizada");
    }

    protected function handleApproval(SalesOrder $salesOrder): void
    {
        Log::info("Pedido {$salesOrder->order_number} aprovado por {$salesOrder->approver->name}");
    }
}
