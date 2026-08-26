<?php

use App\Enums\SalesOrderStatus;
use App\Models\Client;
use App\Models\Company;
use App\Models\Product;
use App\Models\SalesOrder;
use App\Models\User;

dataset('private API endpoints', [
    'CNPJ proxy' => ['getJson', '/api/proxy/cnpj/19131243000197'],
    'CEP proxy' => ['getJson', '/api/proxy/cep/01310100'],
    'list products' => ['getJson', '/api/products'],
    'create product' => ['postJson', '/api/products'],
    'read product' => ['getJson', '/api/products/00000000-0000-0000-0000-000000000000'],
    'replace product' => ['putJson', '/api/products/00000000-0000-0000-0000-000000000000'],
    'update product' => ['patchJson', '/api/products/00000000-0000-0000-0000-000000000000'],
    'delete product' => ['deleteJson', '/api/products/00000000-0000-0000-0000-000000000000'],
    'list suppliers' => ['getJson', '/api/suppliers'],
    'create supplier' => ['postJson', '/api/suppliers'],
    'read supplier' => ['getJson', '/api/suppliers/00000000-0000-0000-0000-000000000000'],
    'replace supplier' => ['putJson', '/api/suppliers/00000000-0000-0000-0000-000000000000'],
    'update supplier' => ['patchJson', '/api/suppliers/00000000-0000-0000-0000-000000000000'],
    'delete supplier' => ['deleteJson', '/api/suppliers/00000000-0000-0000-0000-000000000000'],
    'list clients' => ['getJson', '/api/clients'],
    'create client' => ['postJson', '/api/clients'],
    'read client' => ['getJson', '/api/clients/00000000-0000-0000-0000-000000000000'],
    'replace client' => ['putJson', '/api/clients/00000000-0000-0000-0000-000000000000'],
    'update client' => ['patchJson', '/api/clients/00000000-0000-0000-0000-000000000000'],
    'delete client' => ['deleteJson', '/api/clients/00000000-0000-0000-0000-000000000000'],
    'list product suppliers' => ['getJson', '/api/products/00000000-0000-0000-0000-000000000000/suppliers'],
    'attach product supplier' => ['postJson', '/api/products/00000000-0000-0000-0000-000000000000/suppliers'],
    'detach product supplier' => ['deleteJson', '/api/products/00000000-0000-0000-0000-000000000000/suppliers/00000000-0000-0000-0000-000000000000'],
    'list stock movements' => ['getJson', '/api/stock-movements'],
    'create stock movement' => ['postJson', '/api/stock-movements'],
    'read stock movement' => ['getJson', '/api/stock-movements/1'],
    'replace stock movement' => ['putJson', '/api/stock-movements/1'],
    'update stock movement' => ['patchJson', '/api/stock-movements/1'],
    'delete stock movement' => ['deleteJson', '/api/stock-movements/1'],
    'list accounts payable' => ['getJson', '/api/accounts-payable'],
    'create account payable' => ['postJson', '/api/accounts-payable'],
    'read account payable' => ['getJson', '/api/accounts-payable/1'],
    'replace account payable' => ['putJson', '/api/accounts-payable/1'],
    'update account payable' => ['patchJson', '/api/accounts-payable/1'],
    'delete account payable' => ['deleteJson', '/api/accounts-payable/1'],
    'list accounts receivable' => ['getJson', '/api/accounts-receivable'],
    'create account receivable' => ['postJson', '/api/accounts-receivable'],
    'read account receivable' => ['getJson', '/api/accounts-receivable/1'],
    'replace account receivable' => ['putJson', '/api/accounts-receivable/1'],
    'update account receivable' => ['patchJson', '/api/accounts-receivable/1'],
    'delete account receivable' => ['deleteJson', '/api/accounts-receivable/1'],
    'list sales orders' => ['getJson', '/api/sales-orders'],
    'create sales order' => ['postJson', '/api/sales-orders'],
    'sales order statistics' => ['getJson', '/api/sales-orders/statistics'],
    'calculate sales order' => ['postJson', '/api/sales-orders/calculate'],
    'read sales order' => ['getJson', '/api/sales-orders/1'],
    'replace sales order' => ['putJson', '/api/sales-orders/1'],
    'update sales order' => ['patchJson', '/api/sales-orders/1'],
    'delete sales order' => ['deleteJson', '/api/sales-orders/1'],
    'approve sales order' => ['postJson', '/api/sales-orders/1/approve'],
    'cancel sales order' => ['postJson', '/api/sales-orders/1/cancel'],
    'sales order logs' => ['getJson', '/api/sales-orders/1/logs'],
    'sales order attachments' => ['getJson', '/api/sales-orders/1/attachments'],
]);

it('rejects unauthenticated requests to private API endpoints', function (string $method, string $uri) {
    $this->{$method}($uri)->assertUnauthorized();
})->with('private API endpoints');

it('allows an authenticated user to read, create, update and delete through the API', function () {
    $company = Company::create(['name' => 'Empresa da API']);

    $this->actingAs(User::factory()->create(['company_id' => $company->id]));

    $existingProduct = Product::factory()->create();

    $this->getJson('/api/products')->assertOk();

    $createdProductId = $this->postJson('/api/products', [
        'name' => 'Produto protegido pela API',
        'sale_price' => 125.50,
    ])->assertCreated()->json('data.id');

    $this->putJson("/api/products/{$existingProduct->id}", [
        'name' => 'Produto atualizado pela API',
        'sale_price' => 210,
    ])->assertOk();

    $this->deleteJson("/api/products/{$createdProductId}")->assertOk();

    $this->assertSoftDeleted('products', ['id' => $createdProductId]);
});

it('allows an authenticated user to approve and cancel sales orders', function () {
    $company = Company::create(['name' => 'Empresa da API']);
    $user = User::factory()->create(['company_id' => $company->id]);
    $client = Client::factory()->create();

    $this->actingAs($user);

    $orderToApprove = SalesOrder::create([
        'client_id' => $client->id,
        'user_id' => $user->id,
        'status' => SalesOrderStatus::Draft,
    ]);
    $orderToCancel = SalesOrder::create([
        'client_id' => $client->id,
        'user_id' => $user->id,
        'status' => SalesOrderStatus::Draft,
    ]);

    $this->postJson("/api/sales-orders/{$orderToApprove->id}/approve")
        ->assertOk();
    $this->postJson("/api/sales-orders/{$orderToCancel->id}/cancel", [
        'reason' => 'Cancelamento solicitado no teste da API.',
    ])->assertOk();

    expect($orderToApprove->fresh()->status)->toBe(SalesOrderStatus::Approved)
        ->and($orderToCancel->fresh()->status)->toBe(SalesOrderStatus::Cancelled);
});
