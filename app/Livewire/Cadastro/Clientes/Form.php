<?php

namespace App\Livewire\Cadastro\Clientes;

use App\Livewire\Forms\ClientForm;
use App\Models\Client;
use App\Services\BrasilAPIService;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?Client $client = null;
    public ClientForm $form;

    public ?string $cnpjSituacao = null;
    public ?string $cnpjAtividade = null;
    public ?string $cnpjError    = null;
    public ?string $cepError     = null;

    public function mount(?Client $client = null): void
    {
        $this->client = $client && $client->exists ? $client : null;

        if ($this->client) {
            $this->form->fill([
                'tipo_pessoa'       => $this->client->tipo_pessoa?->value ?? 'PJ',
                'name'              => $this->client->name,
                'social_name'       => $this->client->social_name,
                'taxNumber'         => $this->client->taxNumber,
                'email'             => $this->client->email,
                'phone_number'      => $this->client->phone_number,
                'address_zip_code'  => $this->client->address_zip_code,
                'address_street'    => $this->client->address_street,
                'address_number'    => $this->client->address_number,
                'address_complement'=> $this->client->address_complement,
                'address_district'  => $this->client->address_district,
                'address_city'      => $this->client->address_city,
                'address_state'     => $this->client->address_state,
            ]);
        }
    }

    public function buscarCnpj(BrasilAPIService $brasilApi): void
    {
        $this->cnpjError = null;

        $dados = $brasilApi->consultarCnpj($this->form->taxNumber);

        if (!$dados) {
            $this->cnpjError = 'CNPJ não encontrado ou serviço indisponível. Preencha os dados manualmente.';
            return;
        }

        $this->form->social_name       = $dados['razao_social'] ?? '';
        $this->form->name              = $dados['nome_fantasia'] ?: ($dados['razao_social'] ?? '');
        $this->form->address_zip_code  = preg_replace('/\D/', '', $dados['cep'] ?? '');
        $this->form->address_street    = $dados['logradouro'] ?? '';
        $this->form->address_number    = $dados['numero'] ?? '';
        $this->form->address_complement= $dados['complemento'] ?? '';
        $this->form->address_district  = $dados['bairro'] ?? '';
        $this->form->address_city      = $dados['municipio'] ?? '';
        $this->form->address_state     = $dados['uf'] ?? '';

        if (!empty($dados['ddd_telefone_1'])) {
            $this->form->phone_number = $dados['ddd_telefone_1'];
        }
        if (!empty($dados['email'])) {
            $this->form->email = $dados['email'];
        }

        $this->cnpjSituacao  = $dados['descricao_situacao_cadastral'] ?? null;
        $this->cnpjAtividade = $dados['cnae_fiscal_descricao'] ?? null;
    }

    public function buscarCep(BrasilAPIService $brasilApi): void
    {
        $this->cepError = null;

        $dados = $brasilApi->consultarCep($this->form->address_zip_code ?? '');

        if (!$dados) {
            $this->cepError = 'CEP não encontrado. Verifique o número informado.';
            return;
        }

        $this->form->address_street   = $dados['street']       ?? '';
        $this->form->address_district = $dados['neighborhood'] ?? '';
        $this->form->address_city     = $dados['city']         ?? '';
        $this->form->address_state    = $dados['state']        ?? '';
    }

    public function save()
    {
        $clientId = $this->client?->id;

        $this->form->validate();

        $this->validate([
            'form.taxNumber' => ['required', 'string', Rule::unique('clients', 'taxNumber')->ignore($clientId)],
            'form.email'     => ['required', 'email',  Rule::unique('clients', 'email')->ignore($clientId)],
        ]);

        $data = [
            'tipo_pessoa'        => $this->form->tipo_pessoa,
            'name'               => $this->form->name,
            'social_name'        => $this->form->social_name,
            'taxNumber'          => $this->form->taxNumber,
            'email'              => $this->form->email,
            'phone_number'       => $this->form->phone_number,
            'address_zip_code'   => $this->form->address_zip_code,
            'address_street'     => $this->form->address_street,
            'address_number'     => $this->form->address_number,
            'address_complement' => $this->form->address_complement,
            'address_district'   => $this->form->address_district,
            'address_city'       => $this->form->address_city,
            'address_state'      => $this->form->address_state,
        ];

        if ($this->client) {
            $this->client->update($data);
            $message = 'Cliente atualizado com sucesso!';
        } else {
            Client::create($data);
            $message = 'Cliente cadastrado com sucesso!';
        }

        return redirect()
            ->route('clients.index')
            ->with('success', $message);
    }

    public function render()
    {
        return view('livewire.cadastro.clientes.form', [
            'isEditing' => (bool) $this->client,
        ])->title($this->client ? 'Editar Cliente' : 'Novo Cliente');
    }
}


