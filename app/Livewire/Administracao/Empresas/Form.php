<?php

namespace App\Livewire\Administracao\Empresas;

use App\Livewire\Forms\CompanyForm;
use App\Models\Company;
use App\Services\BrasilAPIService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Form extends Component
{
    use WithFileUploads;

    public ?Company $company = null;
    public CompanyForm $form;
    public ?TemporaryUploadedFile $logoFile = null;
    public ?string $currentLogo = null;
    public bool $removeLogo = false;

    public ?string $cnpjError = null;
    public ?string $cepError  = null;
    public bool $isSearchingCnpj = false;
    public bool $isSearchingCep = false;
    public ?string $lastCnpjLookup = null;
    public ?string $lastCepLookup  = null;

    public function mount(?Company $company = null): void
    {
        $this->company = $company && $company->exists ? $company : null;

        if ($this->company) {
            $this->currentLogo = $this->company->logo;

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

        if (strlen($digits) < 14) {
            $this->lastCnpjLookup = null;
            return;
        }

        if (strlen($digits) === 14) {
            $this->buscarCnpj();
        }
    }

    // ── Auto-pesquisa: dispara quando CEP atinge 8 dígitos ──
    public function updatedFormAddressZipCode(): void
    {
        $this->cepError = null;

        $digits = preg_replace('/\D/', '', $this->form->address_zip_code ?? '');

        if (strlen($digits) < 8) {
            $this->lastCepLookup = null;
            return;
        }

        if (strlen($digits) === 8) {
            $this->buscarCep();
        }
    }

    public function buscarCnpj(bool $force = false): void
    {
        $this->cnpjError = null;

        $digits = preg_replace('/\D/', '', $this->form->cnpj ?? '');

        if (strlen($digits) < 14) {
            $this->cnpjError = 'Informe um CNPJ completo (14 dígitos) antes de consultar.';
            return;
        }

        if (! $force && $this->lastCnpjLookup === $digits) {
            return;
        }

        $this->lastCnpjLookup = $digits;
        $this->isSearchingCnpj = true;

        try {
            /** @var BrasilAPIService $brasilApi */
            $brasilApi = app(BrasilAPIService::class);

            $dados = $brasilApi->consultarCnpj($digits);

            if (! $dados) {
                $this->cnpjError = 'CNPJ não encontrado ou serviço indisponível. Preencha os dados manualmente.';
                return;
            }

            $razaoSocial = $dados['razao_social'] ?? '';
            $nomeFantasia = $dados['nome_fantasia'] ?? '';

            $this->form->social_name        = $razaoSocial;
            $this->form->name               = $nomeFantasia !== '' ? $nomeFantasia : $razaoSocial;
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
        } finally {
            $this->isSearchingCnpj = false;
        }
    }

    public function buscarCep(bool $force = false): void
    {
        $this->cepError = null;

        $digits = preg_replace('/\D/', '', $this->form->address_zip_code ?? '');

        if (strlen($digits) < 8) {
            $this->cepError = 'Informe um CEP completo (8 dígitos) antes de consultar.';
            return;
        }

        if (! $force && $this->lastCepLookup === $digits) {
            return;
        }

        $this->lastCepLookup = $digits;
        $this->isSearchingCep = true;

        try {
            /** @var BrasilAPIService $brasilApi */
            $brasilApi = app(BrasilAPIService::class);

            $dados = $brasilApi->consultarCep($digits);

            if (! $dados) {
                $this->cepError = 'CEP não encontrado. Verifique o número informado.';
                return;
            }

            $this->form->address_street   = $dados['street']       ?? '';
            $this->form->address_district = $dados['neighborhood'] ?? '';
            $this->form->address_city     = $dados['city']         ?? '';
            $this->form->address_state    = $dados['state']        ?? '';
        } finally {
            $this->isSearchingCep = false;
        }
    }

    public function updatedLogoFile(): void
    {
        $this->validate([
            'logoFile' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $this->removeLogo = false;
    }

    public function removeCompanyLogo(): void
    {
        $this->logoFile = null;
        $this->currentLogo = null;
        $this->removeLogo = true;
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
                'logoFile' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            ]);
        } else {
            $this->validate([
                'logoFile' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            ]);
        }

        $logoPath = $this->company?->logo;

        if ($this->logoFile) {
            if ($logoPath) {
                Storage::disk('public')->delete($logoPath);
            }

            $logoPath = $this->logoFile->store('companies/logos', 'public');
        } elseif ($this->removeLogo && $logoPath) {
            Storage::disk('public')->delete($logoPath);
            $logoPath = null;
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
            'logo'                => $logoPath,
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

