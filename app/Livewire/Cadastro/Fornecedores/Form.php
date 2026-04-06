<?php

namespace App\Livewire\Cadastro\Fornecedores;

use App\Livewire\Forms\SupplierForm;
use App\Models\Supplier;
use App\Services\BrasilAPIService;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?Supplier $supplier = null;
    public SupplierForm $form;

    public ?string $cnpjSituacao = null;
    public ?string $cnpjAtividade = null;
    public ?string $cnpjError    = null;
    public ?string $cepError     = null;

    public function mount(?Supplier $supplier = null): void
    {
        $this->supplier = $supplier && $supplier->exists ? $supplier : null;

        if ($this->supplier) {
            $this->form->fill([
                'social_name'        => $this->supplier->social_name,
                'taxNumber'          => $this->supplier->taxNumber,
                'name'               => $this->supplier->name,
                'email'              => $this->supplier->email,
                'phone_number'       => $this->supplier->phone_number,
                'address_zip_code'   => $this->supplier->address_zip_code,
                'address_street'     => $this->supplier->address_street,
                'address_number'     => $this->supplier->address_number,
                'address_complement' => $this->supplier->address_complement,
                'address_district'   => $this->supplier->address_district,
                'address_city'       => $this->supplier->address_city,
                'address_state'      => $this->supplier->address_state,
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

        $dados = $brasilApi->consultarCep($this->form->address_zip_code);

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
        $supplierId = $this->supplier?->id;

        $this->form->validate();

        $this->validate([
            'form.taxNumber' => ['required', 'string', Rule::unique('suppliers', 'taxNumber')->ignore($supplierId)],
            'form.email'     => ['required', 'email',  Rule::unique('suppliers', 'email')->ignore($supplierId)],
        ]);

        $data = [
            'social_name'        => $this->form->social_name,
            'taxNumber'          => $this->form->taxNumber,
            'name'               => $this->form->name,
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

        if ($this->supplier) {
            $this->supplier->update($data);
            $message = 'Fornecedor atualizado com sucesso!';
        } else {
            Supplier::create($data);
            $message = 'Fornecedor cadastrado com sucesso!';
        }

        return redirect()
            ->route('suppliers.index')
            ->with('success', $message);
    }

    public function render()
    {
        return view('livewire.cadastro.fornecedores.form', [
            'isEditing' => (bool) $this->supplier,
        ])->title($this->supplier ? 'Editar Fornecedor' : 'Novo Fornecedor');
    }
}

