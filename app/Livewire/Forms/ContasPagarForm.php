<?php

namespace App\Livewire\Forms;

use App\Enums\PayableStatus;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ContasPagarForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $description_title = '';

    #[Validate('nullable|exists:suppliers,id')]
    public ?string $supplier_id = null;

    #[Validate('nullable|exists:plans_of_accounts,id')]
    public ?int $chart_of_account_id = null;

    #[Validate('required|numeric|min:0.01')]
    public string $amount = '';

    #[Validate('required|date')]
    public string $due_date_at = '';

    #[Validate('nullable|in:pending,paid,overdue,cancelled')]
    public string $status = 'pending';

    #[Validate('nullable|string|max:1000')]
    public ?string $observation = null;

    #[Validate('nullable|boolean')]
    public bool $is_recurring = false;

    #[Validate('nullable|integer|min:1|max:31')]
    public ?int $recurrence_day = null;

    public function messages(): array
    {
        return [
            'description_title.required' => 'A descrição é obrigatória.',
            'amount.required'            => 'O valor é obrigatório.',
            'amount.numeric'             => 'O valor deve ser numérico.',
            'amount.min'                 => 'O valor deve ser maior que zero.',
            'due_date_at.required'       => 'A data de vencimento é obrigatória.',
            'due_date_at.date'           => 'Data de vencimento inválida.',
            'supplier_id.exists'         => 'Fornecedor inválido.',
            'chart_of_account_id.exists' => 'Categoria inválida.',
        ];
    }
}

