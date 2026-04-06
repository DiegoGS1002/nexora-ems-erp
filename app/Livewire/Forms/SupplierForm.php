<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class SupplierForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $social_name = '';

    #[Validate('required|string|max:18')]
    public string $taxNumber = '';

    #[Validate('nullable|string|max:255')]
    public ?string $name = null;

    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('required|string|max:20')]
    public string $phone_number = '';

    #[Validate('required|string|max:9')]
    public string $address_zip_code = '';

    #[Validate('required|string|max:255')]
    public string $address_street = '';

    #[Validate('required|string|max:20')]
    public string $address_number = '';

    #[Validate('nullable|string|max:100')]
    public ?string $address_complement = null;

    #[Validate('required|string|max:100')]
    public string $address_district = '';

    #[Validate('required|string|max:100')]
    public string $address_city = '';

    #[Validate('required|string|size:2')]
    public string $address_state = '';
}


