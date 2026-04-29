<?php

namespace App\Livewire\Administracao\Empresas;

use App\Models\Company;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Gerenciamento de Empresas')]
class Index extends Component
{
    public string $search = '';
    public string $statusFilter = '';

    public function deleteCompany(int $companyId): void
    {
        $company = Company::findOrFail($companyId);

        if ($company->users()->count() > 0) {
            session()->flash('error', 'Não é possível excluir uma empresa com usuários vinculados.');
            return;
        }

        if ($company->logo) {
            Storage::disk('public')->delete($company->logo);
        }

        $company->delete();
        session()->flash('success', 'Empresa removida com sucesso!');
    }

    public function toggleStatus(int $companyId): void
    {
        $company = Company::findOrFail($companyId);
        $company->update(['is_active' => ! $company->is_active]);
        session()->flash('success', $company->is_active ? 'Empresa ativada!' : 'Empresa desativada!');
    }

    #[Computed]
    public function companies()
    {
        return Company::query()
            ->withCount('users')
            ->when($this->search !== '', function ($query) {
                $query->where(function ($sub) {
                    $sub->where('name', 'like', "%{$this->search}%")
                        ->orWhere('social_name', 'like', "%{$this->search}%")
                        ->orWhere('cnpj', 'like', "%{$this->search}%")
                        ->orWhere('address_city', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter !== '', function ($query) {
                $query->where('is_active', $this->statusFilter === 'ativo');
            })
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total'    => Company::count(),
            'ativas'   => Company::where('is_active', true)->count(),
            'inativas' => Company::where('is_active', false)->count(),
            'usuarios' => \App\Models\User::whereNotNull('company_id')->count(),
        ];
    }

    public function render()
    {
        return view('livewire.administracao.empresas.index');
    }
}

