<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateSupplierRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $supplier = $this->route('supplier');

        return [
            'name'               => 'required|string|max:255',
            'social_name'        => 'required|string|max:255',
            'taxNumber'          => ['required', Rule::unique('suppliers', 'taxNumber')->ignore($supplier)],
            'email'              => ['required', 'email', Rule::unique('suppliers', 'email')->ignore($supplier)],
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
}

