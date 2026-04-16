<?php

namespace App\Livewire\Administracao\Empresas;

use App\Livewire\Forms\CompanyForm;
use App\Models\Company;
use App\Services\BrasilAPIService;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?Company $company = null;
    public CompanyForm $form;

    public ?string $cnpjError = null;
    public ?string $cepError  = null;

    public function mount(?Company $company = null): void
    {
        $this->company = $company && $company->exists ? $company : null;

        if ($this->company) {
            $this->form->fill([
                'name'                => $this->company->name,
                'social_name'         => $this->company->social_name,
                'cnpj'                => $this->company->cnpj,
                'inscricao_estadual'  => $this->company->inscricao_estadual,
                'inscricao_municipal' => $this->company->inscricao_municipal,
                'email'               => $this->company->email,
                'phone'               => $this->company->phone,
                'website'             => $this->company->website,
                'address_zip_code'    => $this->company->address_zip_code,
                'address_street'      => $this->company->address_street,
                'address_number'      => $this->company->address_number,
                'address_complement'  => $this->company->address_complement,
                'address_district'    => $this->company->address_district,
                'address_city'        => $this->company->address_city,
                'address_state'       => $this->company->address_state,
                'segment'             => $this->company->segment,
                'is_active'           => $this->company->is_active,
                'notes'               => $this->company->notes,
            ]);
        }
    }

    // ── Auto-pesquisa: dispara quando CNPJ atinge 14 dígitos ──
    public function updatedFormCnpj(): void
    {
        $this->cnpjError = null;

        $digits = preg_replace('/\D/', '', $this->form->cnpj ?? '');

        if (strlen($digits) === 14) {
            $this->buscarCnpj(app(BrasilAPIService::class));
        }
    }

    // ── Auto-pesquisa: dispara quando CEP atinge 8 dígitos ──
    public function updatedFormAddressZipCode(): void
    {
        $this->cepError = null;

        $digits = preg_replace('/\D/', '', $this->form->address_zip_code ?? '');

        if (strlen($digits) === 8) {
            $this->buscarCep(app(BrasilAPIService::class));
        }
    }

    public function buscarCnpj(BrasilAPIService $brasilApi): void
    {
        $this->cnpjError = null;

        $digits = preg_replace('/\D/', '', $this->form->cnpj ?? '');

        if (strlen($digits) < 14) {
            $this->cnpjError = 'Informe um CNPJ completo (14 dígitos) antes de consultar.';
            return;
        }

        $dados = $brasilApi->consultarCnpj($this->form->cnpj ?? '');

        if (! $dados) {
            $this->cnpjError = 'CNPJ não encontrado ou serviço indisponível. Preencha os dados manualmente.';
            return;
        }

        $this->form->social_name        = $dados['razao_social'] ?? '';
        $this->form->name               = $dados['nome_fantasia'] ?: ($dados['razao_social'] ?? '');
        $this->form->address_zip_code   = preg_replace('/\D/', '', $dados['cep'] ?? '');
        $this->form->address_street     = $dados['logradouro'] ?? '';
        $this->form->address_number     = $dados['numero'] ?? '';
        $this->form->address_complement = $dados['complemento'] ?? '';
        $this->form->address_district   = $dados['bairro'] ?? '';
        $this->form->address_city       = $dados['municipio'] ?? '';
        $this->form->address_state      = $dados['uf'] ?? '';

        if (! empty($dados['ddd_telefone_1'])) {
            $this->form->phone = $dados['ddd_telefone_1'];
        }
        if (! empty($dados['email'])) {
            $this->form->email = $dados['email'];
        }
    }

    public function buscarCep(BrasilAPIService $brasilApi): void
    {
        $this->cepError = null;

        $digits = preg_replace('/\D/', '', $this->form->address_zip_code ?? '');

        if (strlen($digits) < 8) {
            $this->cepError = 'Informe um CEP completo (8 dígitos) antes de consultar.';
            return;
        }

        $dados = $brasilApi->consultarCep($this->form->address_zip_code ?? '');

        if (! $dados) {
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
        $companyId = $this->company?->id;

        $this->form->validate();

        if ($this->form->cnpj) {
            $this->validate([
                'form.cnpj' => [
                    'nullable',
                    'string',
                    Rule::unique('companies', 'cnpj')->ignore($companyId),
                ],
            ]);
        }

        $data = [
            'name'                => $this->form->name,
            'social_name'         => $this->form->social_name,
            'cnpj'                => $this->form->cnpj,
            'inscricao_estadual'  => $this->form->inscricao_estadual,
            'inscricao_municipal' => $this->form->inscricao_municipal,
            'email'               => $this->form->email,
            'phone'               => $this->form->phone,
            'website'             => $this->form->website,
            'address_zip_code'    => $this->form->address_zip_code,
            'address_street'      => $this->form->address_street,
            'address_number'      => $this->form->address_number,
            'address_complement'  => $this->form->address_complement,
            'address_district'    => $this->form->address_district,
            'address_city'        => $this->form->address_city,
            'address_state'       => $this->form->address_state,
            'segment'             => $this->form->segment,
            'is_active'           => $this->form->is_active,
            'notes'               => $this->form->notes,
        ];

        if ($this->company) {
            $this->company->update($data);
            $message = 'Empresa atualizada com sucesso!';
        } else {
            Company::create($data);
            $message = 'Empresa cadastrada com sucesso!';
        }

        return redirect()->route('companies.index')->with('success', $message);
    }

    public function render()
    {
        return view('livewire.administracao.empresas.form', [
            'isEditing' => (bool) $this->company,
        ])->title($this->company ? 'Editar Empresa' : 'Nova Empresa');
    }
}

