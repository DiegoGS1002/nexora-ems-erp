<?php

use App\Enums\FiscalNoteStatus;
use App\Enums\NFeTransmissionState;
use App\Enums\SalesOrderStatus;
use App\Livewire\Vendas\PedidoForm;
use App\Models\Client;
use App\Models\Company;
use App\Models\FiscalNote;
use App\Models\SalesOrder;
use App\Models\User;
use App\Services\NFeService;
use Livewire\Livewire;

function orderAuthorizationService(): object
{
    return new class extends NFeService
    {
        public function __construct() {}

        public function apply(FiscalNote $note, NFeTransmissionState $state): void
        {
            $authorized = $state === NFeTransmissionState::Authorized;
            $this->applyTransmissionResult($note, [
                'state' => $state,
                'authorized' => $authorized,
                'access_key' => $authorized ? str_repeat('1', 44) : null,
                'protocol' => $authorized ? '135260000000003' : null,
                'receipt' => $state === NFeTransmissionState::Processing ? '123456789012345' : null,
                'message' => $state->value,
                'received_at' => $authorized ? '2026-08-27T16:00:00-03:00' : null,
            ]);
        }
    };
}

function salesOrderForFiscalAuthorization(SalesOrderStatus $status = SalesOrderStatus::Approved): array
{
    $company = Company::factory()->create();
    $user = User::factory()->create(['company_id' => $company->id]);
    $client = Client::factory()->create();
    $order = SalesOrder::create([
        'order_number' => 'PV-'.fake()->unique()->numerify('######'),
        'client_id' => $client->id,
        'user_id' => $user->id,
        'is_fiscal' => true,
        'status' => $status,
        'total_amount' => 100,
    ]);

    return [$company, $user, $order];
}

it('keeps the sales order status when only a draft fiscal note is generated', function () {
    [, $user, $order] = salesOrderForFiscalAuthorization();

    Livewire::actingAs($user)
        ->test(PedidoForm::class, ['orderId' => $order->id])
        ->call('gerarNotaFiscal');

    expect($order->fresh()->status)->toBe(SalesOrderStatus::Approved);

    $note = FiscalNote::where('sales_order_id', $order->id)->sole();
    expect($note->status)->toBe(FiscalNoteStatus::Draft);
});

it('marks the order as transmitted only after protocol authorization', function () {
    [$company, , $order] = salesOrderForFiscalAuthorization();
    $note = FiscalNote::factory()->create([
        'company_id' => $company->id,
        'sales_order_id' => $order->id,
        'invoice_number' => '000000123',
        'series' => '1',
    ]);

    orderAuthorizationService()->apply($note, NFeTransmissionState::Authorized);
    $order->refresh();

    expect($order->status)->toBe(SalesOrderStatus::NfTransmitida)
        ->and($order->fiscal_note_id)->toBe($note->id)
        ->and($order->nfe_number)->toBe('000000123')
        ->and($order->nfe_key)->toBe(str_repeat('1', 44))
        ->and($order->nfe_protocol)->toBe('135260000000003')
        ->and($order->nfe_status)->toBe('authorized')
        ->and($order->nfe_issued_at)->not->toBeNull();
});

it('does not invoice the order for processing rejected denied or indeterminate results', function (NFeTransmissionState $state) {
    [$company, , $order] = salesOrderForFiscalAuthorization();
    $note = FiscalNote::factory()->create([
        'company_id' => $company->id,
        'sales_order_id' => $order->id,
    ]);

    orderAuthorizationService()->apply($note, $state);

    expect($order->fresh()->status)->toBe(SalesOrderStatus::Approved)
        ->and($order->fresh()->nfe_status)->toBe($state->value);
})->with([
    NFeTransmissionState::Processing,
    NFeTransmissionState::Rejected,
    NFeTransmissionState::Denied,
    NFeTransmissionState::Indeterminate,
]);
