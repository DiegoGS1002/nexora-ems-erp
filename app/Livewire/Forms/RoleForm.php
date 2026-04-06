<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class RoleForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|string|max:100')]
    public string $department = '';

    #[Validate('required|string|max:50|regex:/^[A-Z0-9\-]+$/')]
    public string $code = '';

    public ?int $parent_role_id = null;

    #[Validate('nullable|string|max:300')]
    public string $description = '';

    public bool $is_active = true;

    public bool $allow_assignment = true;

    public array $permissions = [];
}

