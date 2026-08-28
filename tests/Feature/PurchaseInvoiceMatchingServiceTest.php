<?php

use App\Enums\PurchaseOrderOrigin;
use App\Enums\PurchaseOrderStatus;
use App\Models\FiscalInvoiceIn;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Services\PurchaseInvoiceMatchingService;

it('blocks purchase invoice posting until quantity and price divergences are approved', function () {
    $supplier = Supplier::factory()->create();
    $order = PurchaseOrder::create([
        'supplier_id' => $supplier->id,
        'status' => PurchaseOrderStatus::Aprovado,
        'origin' => PurchaseOrderOrigin::Manual,
        'order_date' => now(),
        'subtotal' => 100,
        'total_amount' => 100,
    ]);
    $order->items()->create([
        'description' => 'Produto Teste',
        'sku' => 'SKU-1',
        'unit' => 'UN',
        'quantity' => 10,
        'unit_price' => 10,
        'total' => 100,
    ]);
    $invoice = FiscalInvoiceIn::create([
        'supplier_id' => $supplier->id,
        'supplier_name' => $supplier->name,
        'purchase_order_id' => $order->id,
        'invoice_number' => '1',
        'series' => '1',
        'entry_date' => now(),
        'status' => 'importada',
        'products_total' => 132,
        'total_value' => 132,
    ]);
    $invoice->items()->create([
        'product_code' => 'SKU-1',
        'product_name' => 'Produto Teste',
        'quantity' => 12,
        'unit_price' => 11,
        'total_price' => 132,
    ]);

    $divergences = app(PurchaseInvoiceMatchingService::class)->compare($invoice->fresh(['items', 'purchaseOrder.items']));

    expect(collect($divergences)->pluck('type')->all())->toContain('quantity', 'price')
        ->and($invoice->fresh()->matching_status)->toBe('divergent')
        ->and($invoice->fresh()->isEscrituravel())->toBeFalse();
});
