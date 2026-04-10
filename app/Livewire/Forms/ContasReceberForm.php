<?php

namespace App\Livewire\Forms;

use App\Enums\ReceivableStatus;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ContasReceberForm extends Form
{
    #[Validate('required|string|max:255')]
    public string $description_title = '';

    #[Validate('nullable|exists:clients,id')]
    public ?string $client_id = null;

    #[Validate('nullable|exists:plans_of_accounts,id')]
    public ?int $chart_of_account_id = null;

    #[Validate('required|numeric|min:0.01')]
    public string $amount = '';

    #[Validate('required|date')]
    public string $due_date_at = '';

    #[Validate('nullable|in:boleto,cartao,pix,dinheiro,duplicata,outros')]
    public ?string $payment_method = null;

    #[Validate('nullable|integer|min:1')]
    public int $installment_number = 1;

    #[Validate('nullable|in:pending,received,overdue,partial,cancelled')]
    public string $status = 'pending';

    #[Validate('nullable|string|max:1000')]
    public ?string $observation = null;

    public function messages(): array
    {
        return [
            'description_title.required' => 'A descrição é obrigatória.',
            'amount.required'            => 'O valor é obrigatório.',
            'amount.numeric'             => 'O valor deve ser numérico.',
            'amount.min'                 => 'O valor deve ser maior que zero.',
            'due_date_at.required'       => 'A data de vencimento é obrigatória.',
            'due_date_at.date'           => 'Data de vencimento inválida.',
            'client_id.exists'           => 'Cliente inválido.',
            'chart_of_account_id.exists' => 'Categoria inválida.',
        ];
    }
}

