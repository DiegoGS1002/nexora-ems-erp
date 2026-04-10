<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class ProductCategoryForm extends Form
{
    #[Validate('required|string|min:2|max:100')]
    public string $name = '';

    #[Validate('nullable|string|max:255')]
    public ?string $description = null;

    #[Validate('required|string|size:7')]
    public string $color = '#6366F1';

    #[Validate('boolean')]
    public bool $is_active = true;
}

