<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSalesOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Verifica se o pedido pode ser editado
        $order = $this->route('order');
        return $order && $order->canEdit();
    }

    public function rules(): array
    {
        return [
            'client_id' => ['sometimes', 'exists:clients,id'],
            'seller_id' => ['nullable', 'exists:users,id'],
            'operation_type' => ['nullable', 'string'],
            'sales_channel' => ['nullable', 'string'],
            'company_branch' => ['nullable', 'string', 'max:255'],

            'expected_delivery_date' => ['nullable', 'date'],
            'delivery_date' => ['nullable', 'date'],

            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'shipping_amount' => ['nullable', 'numeric', 'min:0'],
            'insurance_amount' => ['nullable', 'numeric', 'min:0'],
            'other_expenses' => ['nullable', 'numeric', 'min:0'],

            'carrier_id' => ['nullable', 'exists:carriers,id'],
            'freight_type' => ['nullable', 'string'],
            'gross_weight' => ['nullable', 'numeric', 'min:0'],
            'net_weight' => ['nullable', 'numeric', 'min:0'],
            'volumes' => ['nullable', 'integer', 'min:1'],
            'tracking_code' => ['nullable', 'string', 'max:255'],

            'internal_notes' => ['nullable', 'string'],
            'customer_notes' => ['nullable', 'string'],
            'fiscal_notes_obs' => ['nullable', 'string'],

            'payment_condition' => ['nullable', 'string', 'max:255'],
            'price_table_id' => ['nullable', 'exists:price_tables,id'],

            // Itens
            'items' => ['sometimes', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.exists' => 'Cliente não encontrado.',
            'items.min' => 'É necessário ter pelo menos um item no pedido.',
        ];
    }
}

