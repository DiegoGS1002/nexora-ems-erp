<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool { return true; }

    public function rules(): array
    {
        $userId = $this->route('user')?->id ?? $this->route('user');

        return [
            'name'        => 'required|string|max:255',
            'email'       => "required|email|unique:users,email,{$userId}",
            'password'    => ['nullable', 'confirmed', 'not_regex:/\s/', Password::min(8)->mixedCase()->numbers()->symbols()],
            'is_admin'    => 'required|in:0,1',
            'is_active'   => 'required|in:0,1',
            'has_license' => 'required|in:0,1',
            'modules'     => 'nullable|array',
            'modules.*'   => 'string',
            'company_id'  => 'nullable|exists:companies,id',
        ];
    }
}

