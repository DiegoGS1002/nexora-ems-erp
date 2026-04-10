<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class UnitOfMeasureForm extends Form
{
    #[Validate('required|string|min:2|max:100')]
    public string $name = '';

    #[Validate('required|string|max:20')]
    public string $abbreviation = '';

    #[Validate('nullable|string|max:255')]
    public ?string $description = null;

    #[Validate('boolean')]
    public bool $is_active = true;
}

