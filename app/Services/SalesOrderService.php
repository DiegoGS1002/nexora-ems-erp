<?php

namespace App\Services;

use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\SalesOrderAddress;
use App\Models\SalesOrderPayment;
use App\Models\SalesOrderInstallment;
use App\Models\Client;
use App\Models\Product;
use App\Enums\SalesOrderStatus;
use App\Enums\TipoOperacaoVenda;
use App\Enums\CanalVenda;
use App\Enums\OrigemPedido;
use Illuminate\Support\Facades\DB;
use Exception;

class SalesOrderService
{
    public function createOrder(array $data): SalesOrder
    {
        return DB::transaction(function () use ($data) {
            $this->validateOrder($data);

            $order = $this->createMainOrder($data);

            if (isset($data['items'])) {
                $this->addItems($order, $data['items']);
            }

            if (isset($data['addresses'])) {
                $this->addAddresses($order, $data['addresses']);
            }

            if (isset($data['payment'])) {
                $this->addPayment($order, $data['payment']);
            }

            $order->refresh();
            $order->calculateTotals();
            $order->save();

            return $order->load(['items', 'addresses', 'payments.installments']);
        });
    }

    protected function validateOrder(array $data): void
    {
        $client = Client::findOrFail($data['client_id']);

        if (!$client->isActive()) {
            throw new Exception('Cliente inativo. Não é possível criar pedido.');
        }

        if ($client->isDefaulter()) {
            throw new Exception('Cliente inadimplente. Necessária aprovação gerencial.');
        }

        if (isset($data['total_amount']) && !$client->hasAvailableCredit($data['total_amount'])) {
            throw new Exception('Cliente sem limite de crédito disponível. Necessária aprovação gerencial.');
        }
    }

    protected function createMainOrder(array $data): SalesOrder
    {
        $client = Client::findOrFail($data['client_id']);

        $orderData = [
            'client_id'      => $client->id,
            'user_id'        => auth()->id() ?? $data['user_id'] ?? null,
            'seller_id'      => $data['seller_id'] ?? auth()->id(),
            'is_fiscal'      => $data['is_fiscal'] ?? false,
            'status'         => $data['status'] ?? SalesOrderStatus::Draft,
            'operation_type' => $data['operation_type'] ?? TipoOperacaoVenda::VendaNormal,
            'sales_channel'  => $data['sales_channel'] ?? CanalVenda::Balcao,
            'origin'         => $data['origin'] ?? OrigemPedido::Manual,
            'company_branch' => $data['company_branch'] ?? null,

            'client_cpf_cnpj'       => $client->taxNumber,
            'client_ie'             => $client->inscricao_estadual,
            'client_type'           => $client->tipo_pessoa->value,
            'client_credit_limit'   => $client->credit_limit,
            'client_situation'      => $client->situation,
            'client_contact_phone'  => $client->phone_number,
            'client_contact_email'  => $client->email,

            'price_table_id' => $data['price_table_id'] ?? $client->price_table_id,

            'order_date'              => $data['order_date'] ?? now(),
            'expected_delivery_date'  => $data['expected_delivery_date'] ?? null,
            'delivery_date'           => $data['delivery_date'] ?? null,

            'carrier_id'   => $data['carrier_id'] ?? null,
            'freight_type' => $data['freight_type'] ?? null,

            'internal_notes'    => $data['internal_notes'] ?? null,
            'customer_notes'    => $data['customer_notes'] ?? null,
            'fiscal_notes_obs'  => $data['fiscal_notes_obs'] ?? null,

            'payment_condition' => $data['payment_condition'] ?? $client->payment_condition_default,
        ];

        return SalesOrder::create($orderData);
    }

    protected function addItems(SalesOrder $order, array $items): void
    {
        foreach ($items as $itemData) {
            $product = Product::findOrFail($itemData['product_id']);
            $price = $this->getProductPrice($order, $product, $itemData['quantity'] ?? 1);

            $item = [
                'product_id'       => $product->id,
                'sku'              => $product->sku ?? $product->internal_code,
                'ean'              => $product->ean,
                'description'      => $itemData['description'] ?? $product->name,
                'unit'             => $product->unit_of_measure_id ?? 'UN',
                'quantity'         => $itemData['quantity'],
                'unit_price'       => $itemData['unit_price'] ?? $price,
                'discount'         => $itemData['discount'] ?? 0,
                'discount_percent' => $itemData['discount_percent'] ?? 0,
                'addition'         => $itemData['addition'] ?? 0,
                'addition_percent' => $itemData['addition_percent'] ?? 0,
                'notes'            => $itemData['notes'] ?? null,
            ];

            if ($order->is_fiscal) {
                $item = array_merge($item, [
                    'cfop'          => $itemData['cfop'] ?? '5102',
                    'ncm'           => $itemData['ncm'] ?? $product->ncm,
                    'cst'           => $itemData['cst'] ?? null,
                    'csosn'         => $itemData['csosn'] ?? null,
                    'origin'        => $itemData['origin'] ?? '0',
                    'icms_percent'  => $itemData['icms_percent'] ?? 0,
                    'ipi_percent'   => $itemData['ipi_percent'] ?? 0,
                    'pis_percent'   => $itemData['pis_percent'] ?? 0,
                    'cofins_percent' => $itemData['cofins_percent'] ?? 0,
                ]);
            }

            $order->items()->create($item);
        }
    }

    protected function getProductPrice(SalesOrder $order, Product $product, float $quantity = 1): float
    {
        if ($order->price_table_id) {
            $priceTableItem = $order->priceTable
                ->items()
                ->where('product_id', $product->id)
                ->first();

            if ($priceTableItem) {
                return $priceTableItem->getEffectivePrice($quantity);
            }
        }

        return $product->sale_price ?? $product->price ?? 0;
    }

    protected function addAddresses(SalesOrder $order, array $addresses): void
    {
        foreach ($addresses as $type => $addressData) {
            $order->addresses()->create([
                'type'       => $type,
                'zip_code'   => $addressData['zip_code'] ?? null,
                'street'     => $addressData['street'] ?? null,
                'number'     => $addressData['number'] ?? null,
                'complement' => $addressData['complement'] ?? null,
                'district'   => $addressData['district'] ?? null,
                'city'       => $addressData['city'] ?? null,
                'state'      => $addressData['state'] ?? null,
                'country'    => $addressData['country'] ?? 'Brasil',
                'ibge_code'  => $addressData['ibge_code'] ?? null,
            ]);
        }
    }

    protected function addPayment(SalesOrder $order, array $paymentData): void
    {
        $payment = $order->payments()->create([
            'payment_condition' => $paymentData['payment_condition'],
            'payment_method'    => $paymentData['payment_method'],
            'installments'      => $paymentData['installments'] ?? 1,
            'total_amount'      => $paymentData['total_amount'] ?? $order->total_amount,
        ]);

        if (isset($paymentData['installment_details'])) {
            foreach ($paymentData['installment_details'] as $installmentData) {
                $payment->installments()->create([
                    'sales_order_id'     => $order->id,
                    'installment_number' => $installmentData['number'],
                    'amount'             => $installmentData['amount'],
                    'due_date'           => $installmentData['due_date'],
                    'status'             => 'pending',
                ]);
            }
        } else {
            $this->generateInstallments($order, $payment);
        }
    }

    protected function generateInstallments(SalesOrder $order, SalesOrderPayment $payment): void
    {
        $installmentCount = $payment->installments;
        $totalAmount = $payment->total_amount;
        $installmentAmount = $totalAmount / $installmentCount;

        for ($i = 1; $i <= $installmentCount; $i++) {
            $amount = ($i == $installmentCount)
                ? $totalAmount - ($installmentAmount * ($installmentCount - 1))
                : $installmentAmount;

            $payment->installments()->create([
                'sales_order_id'     => $order->id,
                'installment_number' => $i,
                'amount'             => round($amount, 2),
                'due_date'           => now()->addDays(30 * $i),
                'status'             => 'pending',
            ]);
        }
    }

    public function approveOrder(SalesOrder $order, string $reason = null): bool
    {
        if ($order->status !== SalesOrderStatus::Draft && $order->status !== SalesOrderStatus::Aberto) {
            throw new Exception('Pedido não pode ser aprovado no status atual.');
        }

        return $order->approve(auth()->user(), $reason);
    }

    public function cancelOrder(SalesOrder $order, string $reason): bool
    {
        if (!$order->canCancel()) {
            throw new Exception('Pedido não pode ser cancelado no status atual.');
        }

        return DB::transaction(fn() => $order->cancel($reason));
    }

    public function updateOrder(SalesOrder $order, array $data): SalesOrder
    {
        if (!$order->canEdit()) {
            throw new Exception('Pedido não pode ser editado no status atual.');
        }

        return DB::transaction(function () use ($order, $data) {
            $order->update(array_intersect_key($data, array_flip($order->getFillable())));

            if (isset($data['items'])) {
                $order->items()->delete();
                $this->addItems($order, $data['items']);
            }

            $order->refresh();
            $order->calculateTotals();
            $order->save();

            return $order;
        });
    }
}

