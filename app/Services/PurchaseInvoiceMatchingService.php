<?php

namespace App\Services;

use App\Enums\PurchaseOrderStatus;
use App\Models\FiscalInvoiceIn;
use App\Models\PurchaseOrderItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PurchaseInvoiceMatchingService
{
    public function compare(FiscalInvoiceIn $invoice, float $quantityTolerance = 0, float $priceTolerancePercent = 0): array
    {
        $order = $invoice->purchaseOrder?->load('items');
        if (! $order) {
            return [];
        }

        $divergences = [];
        foreach ($invoice->items as $invoiceItem) {
            $orderItem = $order->items->first(fn (PurchaseOrderItem $item): bool =>
                ($invoiceItem->product_id && $item->product_id === $invoiceItem->product_id)
                || ($invoiceItem->product_code && $item->sku === $invoiceItem->product_code)
            );

            if (! $orderItem) {
                $divergences[] = ['type' => 'unmatched_item', 'invoice_item_id' => $invoiceItem->id];
                continue;
            }

            $remaining = (float) $orderItem->quantity - (float) $orderItem->received_quantity;
            if ((float) $invoiceItem->quantity - $remaining > $quantityTolerance) {
                $divergences[] = ['type' => 'quantity', 'invoice_item_id' => $invoiceItem->id, 'expected' => $remaining, 'received' => (float) $invoiceItem->quantity];
            }

            $priceDifference = (float) $orderItem->unit_price > 0
                ? abs((float) $invoiceItem->unit_price - (float) $orderItem->unit_price) / (float) $orderItem->unit_price * 100
                : 0;
            if ($priceDifference > $priceTolerancePercent) {
                $divergences[] = ['type' => 'price', 'invoice_item_id' => $invoiceItem->id, 'expected' => (float) $orderItem->unit_price, 'received' => (float) $invoiceItem->unit_price];
            }
        }

        foreach ([
            'shipping_total' => 'shipping_amount',
            'discount_total' => 'discount_amount',
            'other_expenses' => 'other_expenses',
        ] as $invoiceField => $orderField) {
            if (abs((float) $invoice->{$invoiceField} - (float) $order->{$orderField}) > 0.01) {
                $divergences[] = ['type' => $invoiceField, 'expected' => (float) $order->{$orderField}, 'received' => (float) $invoice->{$invoiceField}];
            }
        }

        $invoice->update([
            'matching_status' => $divergences === [] ? 'matched' : 'divergent',
            'matching_divergences' => $divergences ?: null,
        ]);

        return $divergences;
    }

    public function approveDivergences(FiscalInvoiceIn $invoice, User $user): void
    {
        $invoice->update([
            'matching_status' => 'approved',
            'matching_approved_by' => $user->id,
            'matching_approved_at' => now(),
        ]);
    }

    public function applyReceipt(FiscalInvoiceIn $invoice): void
    {
        DB::transaction(function () use ($invoice): void {
            $invoice = FiscalInvoiceIn::where('id', $invoice->id)->lockForUpdate()->firstOrFail();
            if ($invoice->receipt_applied_at || ! $invoice->purchaseOrder) {
                return;
            }
            if ($invoice->matching_status === 'divergent') {
                throw new \RuntimeException('Divergências da conferência precisam ser aprovadas antes da escrituração.');
            }

            foreach ($invoice->items as $invoiceItem) {
                $orderItem = $invoice->purchaseOrder->items->first(fn (PurchaseOrderItem $item): bool =>
                    ($invoiceItem->product_id && $item->product_id === $invoiceItem->product_id)
                    || ($invoiceItem->product_code && $item->sku === $invoiceItem->product_code)
                );
                $orderItem?->increment('received_quantity', $invoiceItem->quantity);
            }

            $complete = $invoice->purchaseOrder->items()->get()->every(fn (PurchaseOrderItem $item): bool =>
                (float) $item->received_quantity >= (float) $item->quantity
            );
            $invoice->purchaseOrder->update([
                'status' => $complete ? PurchaseOrderStatus::RecebidoTotal : PurchaseOrderStatus::RecebidoParcial,
                'received_at' => $complete ? now() : null,
            ]);
            $invoice->update(['receipt_applied_at' => now()]);
        });
    }
}
