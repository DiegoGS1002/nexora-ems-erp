<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TipoOperacaoVenda;
use App\Enums\CanalVenda;
use App\Enums\OrigemPedido;
use App\Enums\TipoFrete;

class StoreSalesOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Ajustar conforme suas políticas de autorização
    }

    public function rules(): array
    {
        return [
            // Cabeçalho
            'client_id' => ['required', 'exists:clients,id'],
            'seller_id' => ['nullable', 'exists:users,id'],
            'is_fiscal' => ['boolean'],
            'operation_type' => ['nullable', 'string'],
            'sales_channel' => ['nullable', 'string'],
            'origin' => ['nullable', 'string'],
            'company_branch' => ['nullable', 'string', 'max:255'],

            // Datas
            'order_date' => ['nullable', 'date'],
            'expected_delivery_date' => ['nullable', 'date', 'after:order_date'],
            'delivery_date' => ['nullable', 'date'],

            // Valores
            'discount_amount' => ['nullable', 'numeric', 'min:0'],
            'shipping_amount' => ['nullable', 'numeric', 'min:0'],
            'insurance_amount' => ['nullable', 'numeric', 'min:0'],
            'other_expenses' => ['nullable', 'numeric', 'min:0'],

            // Logística
            'carrier_id' => ['nullable', 'exists:carriers,id'],
            'freight_type' => ['nullable', 'string'],
            'gross_weight' => ['nullable', 'numeric', 'min:0'],
            'net_weight' => ['nullable', 'numeric', 'min:0'],
            'volumes' => ['nullable', 'integer', 'min:1'],
            'tracking_code' => ['nullable', 'string', 'max:255'],

            // Observações
            'internal_notes' => ['nullable', 'string'],
            'customer_notes' => ['nullable', 'string'],
            'fiscal_notes_obs' => ['nullable', 'string'],

            // Pagamento
            'payment_condition' => ['nullable', 'string', 'max:255'],
            'price_table_id' => ['nullable', 'exists:price_tables,id'],

            // Itens (obrigatório ter pelo menos 1 item)
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.001'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
            'items.*.discount_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.addition' => ['nullable', 'numeric', 'min:0'],
            'items.*.addition_percent' => ['nullable', 'numeric', 'min:0'],
            'items.*.description' => ['nullable', 'string'],
            'items.*.notes' => ['nullable', 'string'],

            // Fiscal (quando is_fiscal = true)
            'items.*.cfop' => ['required_if:is_fiscal,true', 'nullable', 'string', 'max:10'],
            'items.*.ncm' => ['required_if:is_fiscal,true', 'nullable', 'string', 'max:10'],
            'items.*.cst' => ['nullable', 'string', 'max:5'],
            'items.*.csosn' => ['nullable', 'string', 'max:5'],
            'items.*.origin' => ['nullable', 'string', 'max:1'],
            'items.*.icms_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.ipi_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.pis_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.cofins_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'items.*.fcp_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],

            // Endereços
            'addresses' => ['nullable', 'array'],
            'addresses.*.type' => ['required', 'in:billing,delivery,collection'],
            'addresses.*.zip_code' => ['nullable', 'string', 'max:10'],
            'addresses.*.street' => ['nullable', 'string', 'max:255'],
            'addresses.*.number' => ['nullable', 'string', 'max:20'],
            'addresses.*.complement' => ['nullable', 'string', 'max:255'],
            'addresses.*.district' => ['nullable', 'string', 'max:255'],
            'addresses.*.city' => ['nullable', 'string', 'max:255'],
            'addresses.*.state' => ['nullable', 'string', 'max:2'],

            // Pagamento
            'payment' => ['nullable', 'array'],
            'payment.payment_condition' => ['required_with:payment', 'string'],
            'payment.payment_method' => ['required_with:payment', 'string'],
            'payment.installments' => ['nullable', 'integer', 'min:1', 'max:120'],
            'payment.installment_details' => ['nullable', 'array'],
            'payment.installment_details.*.number' => ['required', 'integer', 'min:1'],
            'payment.installment_details.*.amount' => ['required', 'numeric', 'min:0.01'],
            'payment.installment_details.*.due_date' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'client_id.required' => 'O cliente é obrigatório.',
            'client_id.exists' => 'Cliente não encontrado.',
            'items.required' => 'É necessário adicionar pelo menos um item ao pedido.',
            'items.min' => 'É necessário adicionar pelo menos um item ao pedido.',
            'items.*.product_id.required' => 'O produto é obrigatório.',
            'items.*.product_id.exists' => 'Produto não encontrado.',
            'items.*.quantity.required' => 'A quantidade é obrigatória.',
            'items.*.quantity.min' => 'A quantidade deve ser maior que zero.',
            'items.*.unit_price.required' => 'O preço unitário é obrigatório.',
            'items.*.unit_price.min' => 'O preço unitário deve ser maior ou igual a zero.',
            'items.*.cfop.required_if' => 'O CFOP é obrigatório para pedidos fiscais.',
            'items.*.ncm.required_if' => 'O NCM é obrigatório para pedidos fiscais.',
            'expected_delivery_date.after' => 'A data prevista de entrega deve ser posterior à data do pedido.',
        ];
    }

    public function attributes(): array
    {
        return [
            'client_id' => 'cliente',
            'seller_id' => 'vendedor',
            'is_fiscal' => 'fiscal',
            'items.*.product_id' => 'produto',
            'items.*.quantity' => 'quantidade',
            'items.*.unit_price' => 'preço unitário',
            'items.*.discount' => 'desconto',
            'items.*.cfop' => 'CFOP',
            'items.*.ncm' => 'NCM',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Converte valores booleanos se necessário
        if ($this->has('is_fiscal') && is_string($this->is_fiscal)) {
            $this->merge([
                'is_fiscal' => filter_var($this->is_fiscal, FILTER_VALIDATE_BOOLEAN),
            ]);
        }
    }
}

