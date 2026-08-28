<?php

namespace App\Services;

use App\Enums\PayableStatus;
use App\Enums\PurchaseOrderStatus;
use App\Models\AccountPayable;
use App\Models\FiscalInvoiceIn;
use App\Models\PurchaseOrderItem;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class FiscalEntryReversalService
{
    public function reverse(FiscalInvoiceIn $invoice, User $user, ?string $eventProtocol = null): void
    {
        DB::transaction(function () use ($invoice, $user, $eventProtocol): void {
            $invoice = FiscalInvoiceIn::where('id', $invoice->id)->lockForUpdate()->with(['items', 'purchaseOrder.items'])->firstOrFail();
            if ($invoice->reversed_at) {
                return;
            }

            $payables = AccountPayable::where('fiscal_invoice_in_id', $invoice->id)->lockForUpdate()->get();
            if ($payables->contains(fn (AccountPayable $payable): bool => $payable->status === PayableStatus::Paid)) {
                throw new \RuntimeException('A NF-e possui título pago; estorne o pagamento antes de cancelar a entrada.');
            }

            if ($invoice->escriturada_at) {
                foreach ($invoice->items as $item) {
                    if ($item->product_id) {
                        StockMovement::create([
                            'product_id' => $item->product_id,
                            'user_id' => $user->id,
                            'quantity' => -((float) $item->quantity),
                            'type' => 'adjustment',
                            'origin' => "fiscal_invoice_reversal:{$invoice->id}",
                            'unit_cost' => $item->unit_price,
                            'observation' => "Estorno NF-e Entrada #{$invoice->invoice_number}",
                        ]);
                    }
                }
                $payables->each->update(['status' => PayableStatus::Cancelled]);
            }

            if ($invoice->receipt_applied_at && $invoice->purchaseOrder) {
                foreach ($invoice->items as $invoiceItem) {
                    $orderItem = $invoice->purchaseOrder->items->first(fn (PurchaseOrderItem $item): bool =>
                        ($invoiceItem->product_id && $item->product_id === $invoiceItem->product_id)
                        || ($invoiceItem->product_code && $item->sku === $invoiceItem->product_code)
                    );
                    if ($orderItem) {
                        $orderItem->update(['received_quantity' => max(0, (float) $orderItem->received_quantity - (float) $invoiceItem->quantity)]);
                    }
                }
                $hasReceipt = $invoice->purchaseOrder->items()->where('received_quantity', '>', 0)->exists();
                $invoice->purchaseOrder->update([
                    'status' => $hasReceipt ? PurchaseOrderStatus::RecebidoParcial : PurchaseOrderStatus::Aprovado,
                    'received_at' => null,
                ]);
            }

            $invoice->update([
                'status' => 'cancelada',
                'reversed_at' => now(),
                'cancellation_event_protocol' => $eventProtocol,
            ]);
        });
    }
}
