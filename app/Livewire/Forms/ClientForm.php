<?php

namespace App\Livewire\Forms;

use App\Enums\TipoPessoa;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ClientForm extends Form
{
    public string $tipo_pessoa = 'PJ';

    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:255')]
    public ?string $social_name = null;

    #[Validate('required|string|max:18')]
    public string $taxNumber = '';

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('nullable|string|max:20')]
    public ?string $phone_number = null;

    #[Validate('nullable|string|max:9')]
    public ?string $address_zip_code = null;

    #[Validate('nullable|string|max:255')]
    public ?string $address_street = null;

    #[Validate('nullable|string|max:20')]
    public ?string $address_number = null;

    #[Validate('nullable|string|max:100')]
    public ?string $address_complement = null;

    #[Validate('nullable|string|max:100')]
    public ?string $address_district = null;

    #[Validate('nullable|string|max:100')]
    public ?string $address_city = null;

    #[Validate('nullable|string|size:2')]
    public ?string $address_state = null;
}


