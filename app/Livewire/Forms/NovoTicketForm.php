<?php

namespace App\Livewire\Forms;

use App\Enums\PrioridadeTicketSuporte;
use Livewire\Attributes\Validate;
use Livewire\Form;

class NovoTicketForm extends Form
{
    #[Validate('required|min:5|max:255')]
    public string $assunto = '';

    #[Validate('required')]
    public string $prioridade = '';

    #[Validate('nullable|max:100')]
    public ?string $categoria = null;

    #[Validate('required|min:10')]
    public string $mensagem = '';

    public function resetComPadrao(): void
    {
        $this->reset();
        $this->prioridade = PrioridadeTicketSuporte::Media->value;
    }
}

