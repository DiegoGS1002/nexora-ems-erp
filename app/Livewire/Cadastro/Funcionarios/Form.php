<?php

namespace App\Livewire\Cadastro\Funcionarios;

use App\Livewire\Forms\EmployeeForm;
use App\Models\Employees;
use App\Services\BrasilAPIService;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Form extends Component
{
    use WithFileUploads;

    public ?Employees $employee = null;

    public EmployeeForm $form;

    public string $activeTab = 'dados-pessoais';

    public ?TemporaryUploadedFile $photo = null;

    public ?string $cepError = null;

    public function mount(?Employees $employee = null): void
    {
        $this->employee = $employee && $employee->exists ? $employee : null;

        if ($this->employee) {
            $this->form->fill([
                'name'                           => $this->employee->name,
                'social_name'                    => $this->employee->social_name ?? '',
                'identification_number'          => $this->employee->identification_number,
                'rg'                             => $this->employee->rg ?? '',
                'rg_issuer'                      => $this->employee->rg_issuer ?? '',
                'rg_date'                        => $this->employee->rg_date?->format('Y-m-d') ?? '',
                'birth_date'                     => $this->employee->birth_date?->format('Y-m-d') ?? '',
                'gender'                         => $this->employee->gender ?? '',
                'marital_status'                 => $this->employee->marital_status ?? '',
                'nationality'                    => $this->employee->nationality ?? 'Brasileiro(a)',
                'birthplace'                     => $this->employee->birthplace ?? '',
                'email'                          => $this->employee->email,
                'phone_number'                   => $this->employee->phone_number,
                'phone_secondary'                => $this->employee->phone_secondary ?? '',
                'zip_code'                       => $this->employee->zip_code ?? '',
                'street'                         => $this->employee->street ?? '',
                'number'                         => $this->employee->number ?? '',
                'complement'                     => $this->employee->complement ?? '',
                'neighborhood'                   => $this->employee->neighborhood ?? '',
                'city'                           => $this->employee->city ?? '',
                'state'                          => $this->employee->state ?? '',
                'country'                        => $this->employee->country ?? 'Brasil',
                'address'                        => $this->employee->address ?? '',
                'emergency_contact_name'         => $this->employee->emergency_contact_name ?? '',
                'emergency_contact_relationship' => $this->employee->emergency_contact_relationship ?? '',
                'emergency_contact_phone'        => $this->employee->emergency_contact_phone ?? '',
                'role'                           => $this->employee->role,
                'department'                     => $this->employee->department ?? '',
                'access_profile'                 => $this->employee->access_profile ?? '',
                'is_active'                      => (bool) ($this->employee->is_active ?? true),
                'admission_date'                 => $this->employee->admission_date?->format('Y-m-d') ?? '',
                'work_schedule'                  => $this->employee->work_schedule ?? '',
                'allow_system_access'            => (bool) ($this->employee->allow_system_access ?? false),
                'salary'                         => $this->employee->salary ? (string) $this->employee->salary : '',
                'internal_code'                  => $this->employee->internal_code ?? '',
                'observations'                   => $this->employee->observations ?? '',
            ]);
        }
    }

    public function buscarCep(BrasilAPIService $brasilApi): void
    {
        $this->cepError = null;

        $dados = $brasilApi->consultarCep($this->form->zip_code);

        if (! $dados) {
            $this->cepError = 'CEP não encontrado. Verifique o número informado.';
            return;
        }

        $this->form->street       = $dados['street']       ?? '';
        $this->form->neighborhood = $dados['neighborhood'] ?? '';
        $this->form->city         = $dados['city']         ?? '';
        $this->form->state        = $dados['state']        ?? '';
    }

    public function save(): mixed
    {
        $employeeId = $this->employee?->id;

        $this->form->validate();

        $this->validate([
            'form.identification_number' => [
                'required', 'string',
                Rule::unique('employees', 'identification_number')->ignore($employeeId),
            ],
            'form.email' => [
                'required', 'email',
                Rule::unique('employees', 'email')->ignore($employeeId),
            ],
        ]);

        $payload = $this->buildPayload();

        if ($this->employee) {
            $this->employee->update($payload);

            if ($this->photo) {
                $this->employee->update(['photo' => $this->photo->store('employees', 'public')]);
            }

            return redirect()->route('employees.index')
                ->with('success', 'Funcionário atualizado com sucesso!');
        }

        if ($this->photo) {
            $payload['photo'] = $this->photo->store('employees', 'public');
        }

        Employees::create($payload);

        return redirect()->route('employees.index')
            ->with('success', 'Funcionário cadastrado com sucesso!');
    }

    private function buildPayload(): array
    {
        $street = trim($this->form->street);
        $number = trim($this->form->number);
        $city   = trim($this->form->city);
        $state  = trim($this->form->state);

        $address = implode(', ', array_filter([$street, $number, $city, $state]));

        return [
            'name'                           => $this->form->name,
            'social_name'                    => $this->form->social_name ?: null,
            'identification_number'          => $this->form->identification_number,
            'rg'                             => $this->form->rg ?: null,
            'rg_issuer'                      => $this->form->rg_issuer ?: null,
            'rg_date'                        => $this->form->rg_date ?: null,
            'birth_date'                     => $this->form->birth_date ?: null,
            'gender'                         => $this->form->gender ?: null,
            'marital_status'                 => $this->form->marital_status ?: null,
            'nationality'                    => $this->form->nationality ?: 'Brasileiro(a)',
            'birthplace'                     => $this->form->birthplace ?: null,
            'email'                          => $this->form->email,
            'phone_number'                   => $this->form->phone_number,
            'phone_secondary'                => $this->form->phone_secondary ?: null,
            'address'                        => $address ?: $this->form->address,
            'zip_code'                       => $this->form->zip_code ?: null,
            'street'                         => $this->form->street ?: null,
            'number'                         => $this->form->number ?: null,
            'complement'                     => $this->form->complement ?: null,
            'neighborhood'                   => $this->form->neighborhood ?: null,
            'city'                           => $this->form->city ?: null,
            'state'                          => $this->form->state ?: null,
            'country'                        => $this->form->country ?: 'Brasil',
            'emergency_contact_name'         => $this->form->emergency_contact_name ?: null,
            'emergency_contact_relationship' => $this->form->emergency_contact_relationship ?: null,
            'emergency_contact_phone'        => $this->form->emergency_contact_phone ?: null,
            'role'                           => $this->form->role,
            'department'                     => $this->form->department ?: null,
            'access_profile'                 => $this->form->access_profile ?: null,
            'is_active'                      => $this->form->is_active,
            'admission_date'                 => $this->form->admission_date ?: null,
            'work_schedule'                  => $this->form->work_schedule ?: null,
            'allow_system_access'            => $this->form->allow_system_access,
            'salary'                         => $this->form->salary !== '' ? $this->form->salary : null,
            'internal_code'                  => $this->form->internal_code ?: null,
            'observations'                   => $this->form->observations ?: null,
        ];
    }

    public function render()
    {
        $title = $this->employee ? 'Editar Funcionário' : 'Novo Funcionário';

        return view('livewire.cadastro.funcionarios.form', [
            'isEditing' => (bool) $this->employee,
        ])->title($title);
    }
}

