<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class CompanyForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('nullable|string|max:255')]
    public ?string $social_name = null;

    #[Validate('nullable|string|max:18')]
    public ?string $cnpj = null;

    #[Validate('nullable|string|max:30')]
    public ?string $inscricao_estadual = null;

    #[Validate('nullable|string|max:30')]
    public ?string $inscricao_municipal = null;

    #[Validate('nullable|email|max:255')]
    public ?string $email = null;

    #[Validate('nullable|string|max:20')]
    public ?string $phone = null;

    #[Validate('nullable|url|max:255')]
    public ?string $website = null;

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

    #[Validate('nullable|string|max:100')]
    public ?string $segment = null;

    #[Validate('boolean')]
    public bool $is_active = true;

    #[Validate('nullable|string')]
    public ?string $notes = null;
}

