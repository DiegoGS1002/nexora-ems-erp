<?php

namespace App\Services;

use App\Enums\FiscalPaymentMethod;
use App\Models\SalesOrder;

class FiscalPaymentService
{
    public function snapshot(SalesOrder $order): array
    {
        $payments = $order->payments->map(fn ($payment): array => [
            'indPag' => $payment->installments > 1 ? 1 : 0,
            'tPag' => $this->method($payment->payment_method)->value,
            'vPag' => (float) $payment->total_amount,
        ])->values()->all();

        $installments = $order->installments->map(fn ($installment): array => [
            'nDup' => str_pad((string) $installment->installment_number, 3, '0', STR_PAD_LEFT),
            'dVenc' => $installment->due_date->format('Y-m-d'),
            'vDup' => (float) $installment->amount,
        ])->values()->all();

        return [
            'payment_details' => $payments ?: [[
                'indPag' => 0,
                'tPag' => FiscalPaymentMethod::Other->value,
                'xPag' => 'Não informado no pedido',
                'vPag' => (float) $order->total_amount,
            ]],
            'invoice_data' => $installments ? [
                'nFat' => $order->order_number,
                'vOrig' => (float) $order->total_amount,
                'vDesc' => (float) $order->discount_amount,
                'vLiq' => (float) $order->total_amount,
            ] : null,
            'installment_data' => $installments ?: null,
        ];
    }

    protected function method(?string $method): FiscalPaymentMethod
    {
        return match (strtolower((string) $method)) {
            'dinheiro', 'cash' => FiscalPaymentMethod::Cash,
            'pix' => FiscalPaymentMethod::Pix,
            'boleto' => FiscalPaymentMethod::Boleto,
            'cartao_credito', 'credit_card' => FiscalPaymentMethod::CreditCard,
            'cartao_debito', 'debit_card' => FiscalPaymentMethod::DebitCard,
            'transferencia', 'transfer' => FiscalPaymentMethod::BankTransfer,
            'sem_pagamento', 'none' => FiscalPaymentMethod::NoPayment,
            default => FiscalPaymentMethod::Other,
        };
    }
}
