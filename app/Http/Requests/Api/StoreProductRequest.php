<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                => 'required|string|min:3|max:255|unique:products,name',
            'ean'                 => 'nullable|string|size:13|unique:products,ean',
            'ncm'                 => 'nullable|string|max:10',
            'cfop_saida'          => 'nullable|string|max:10',
            'cfop_entrada'        => 'nullable|string|max:10',
            'grupo_tributario_id' => 'nullable|exists:grupo_tributarios,id',
            'unit_of_measure_id'  => 'nullable|exists:unit_of_measures,id',
            'unit_of_measure'     => 'nullable|string|max:20',
            'product_category_id' => 'nullable|exists:product_categories,id',
            'category'            => 'nullable|string|max:255',
            'description'         => 'nullable|string|max:500',
            'short_description'   => 'nullable|string|max:255',
            'full_description'    => 'nullable|string',
            'brand'               => 'nullable|string|max:255',
            'product_type'        => 'nullable|string|in:produto_fisico,servico',
            'nature'              => 'nullable|string|in:mercadoria_revenda,uso_consumo,materia_prima,produto_acabado,embalagem,ativo_imobilizado',
            'product_line'        => 'nullable|string|max:255',
            'sale_price'          => 'required|numeric|min:0',
            'cost_price'          => 'nullable|numeric|min:0',
            'stock'               => 'nullable|integer|min:0',
            'stock_min'           => 'nullable|integer|min:0',
            'expiration_date'     => 'nullable|date',
            'weight_net'          => 'nullable|numeric|min:0',
            'weight_gross'        => 'nullable|numeric|min:0',
            'height'              => 'nullable|numeric|min:0',
            'width'               => 'nullable|numeric|min:0',
            'depth'               => 'nullable|numeric|min:0',
            'is_active'           => 'nullable|boolean',
            'highlights'          => 'nullable|array',
            'tags'                => 'nullable|array',
            'image'               => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }
}

