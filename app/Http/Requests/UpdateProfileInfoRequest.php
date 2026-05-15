<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateProfileInfoRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        return [
            'name'       => ['required', 'string', 'max:100'],
            'phone'      => ['nullable', 'string', 'max:20'],
            'job_title'  => ['nullable', 'string', 'max:100'],
            'department' => ['nullable', 'string', 'max:100'],
            'bio'        => ['nullable', 'string', 'max:500'],
        ];
    }
}

