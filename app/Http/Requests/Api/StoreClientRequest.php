<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'tipo_pessoa'               => ['required', Rule::in(['PF', 'PJ'])],
            'name'                      => 'required|string|max:255',
            'social_name'               => 'nullable|string|max:255',
            'taxNumber'                 => 'required|unique:clients,taxNumber',
            'inscricao_estadual'        => 'nullable|string|max:50',
            'email'                     => 'required|email|unique:clients,email',
            'phone_number'              => 'required|string|max:20',
            'address'                   => 'nullable|string|max:500',
            'address_zip_code'          => 'nullable|string|max:10',
            'address_street'            => 'nullable|string|max:255',
            'address_number'            => 'nullable|string|max:20',
            'address_complement'        => 'nullable|string|max:100',
            'address_district'          => 'nullable|string|max:100',
            'address_city'              => 'nullable|string|max:100',
            'address_state'             => 'nullable|string|max:2',
            'credit_limit'              => 'nullable|numeric|min:0',
            'payment_condition_default' => 'nullable|string|max:100',
            'situation'                 => 'nullable|string|in:active,inactive,defaulter',
            'price_table_id'            => 'nullable|exists:price_tables,id',
            'discount_limit'            => 'nullable|numeric|min:0|max:100',
        ];
    }
}

