<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSupplierRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'               => 'required|string|max:255',
            'social_name'        => 'required|string|max:255',
            'taxNumber'          => 'required|unique:suppliers,taxNumber|max:14',
            'email'              => 'required|email|unique:suppliers,email',
            'phone_number'       => 'required',
            'address_zip_code'   => 'required',
            'address_street'     => 'required',
            'address_number'     => 'required',
            'address_complement' => 'nullable',
            'address_district'   => 'required',
            'address_city'       => 'required',
            'address_state'      => 'required',
        ];
    }

    public function messages(): array
    {
        return [
            'taxNumber.unique' => 'Este CNPJ já está cadastrado.',
        ];
    }
}

