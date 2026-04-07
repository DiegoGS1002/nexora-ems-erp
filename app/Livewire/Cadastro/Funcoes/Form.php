<?php

namespace App\Livewire\Cadastro\Funcoes;

use App\Livewire\Forms\RoleForm;
use App\Models\Role;
use App\Services\RoleService;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?Role $role = null;

    public RoleForm $form;

    public string $activeTab = 'permissoes';

    public bool $showCopyModal = false;

    public ?int $copyFromRoleId = null;

    public array $expandedModules = [];

    public function mount(?Role $role = null): void
    {
        $this->role = $role && $role->exists ? $role : null;

        if ($this->role) {
            $this->form->fill([
                'name'             => $this->role->name ?? '',
                'department'       => $this->role->department ?? '',
                'code'             => $this->role->code ?? '',
                'parent_role_id'   => $this->role->parent_role_id,
                'description'      => $this->role->description ?? '',
                'is_active'        => (bool) ($this->role->is_active ?? true),
                'allow_assignment' => (bool) ($this->role->allow_assignment ?? true),
                'permissions'      => $this->role->permissions ?? [],
            ]);
        }

        $this->ensurePermissionsInitialized();
    }

    private function ensurePermissionsInitialized(): void
    {
        $empty = (new RoleService())->buildEmptyPermissions();

        foreach ($empty as $module => $actions) {
            foreach ($actions as $action => $value) {
                if (! isset($this->form->permissions[$module][$action])) {
                    $this->form->permissions[$module][$action] = false;
                }
            }
        }
    }

    public function toggleModule(string $module): void
    {
        if (in_array($module, $this->expandedModules)) {
            $this->expandedModules = array_values(array_filter(
                $this->expandedModules,
                fn ($m) => $m !== $module
            ));
        } else {
            $this->expandedModules[] = $module;
        }
    }

    public function expandAll(): void
    {
        $this->expandedModules = array_keys(RoleService::MODULES);
    }

    public function collapseAll(): void
    {
        $this->expandedModules = [];
    }

    public function selectAll(): void
    {
        $this->form->permissions = (new RoleService())->selectAll($this->form->permissions);
    }

    public function clearAll(): void
    {
        $this->form->permissions = (new RoleService())->clearAll($this->form->permissions);
    }

    public function selectAllModule(string $module): void
    {
        foreach (array_keys(RoleService::ACTIONS) as $action) {
            $this->form->permissions[$module][$action] = true;
        }
    }

    public function clearModule(string $module): void
    {
        foreach (array_keys(RoleService::ACTIONS) as $action) {
            $this->form->permissions[$module][$action] = false;
        }
    }

    public function openCopyModal(): void
    {
        $this->copyFromRoleId = null;
        $this->showCopyModal  = true;
    }

    public function closeCopyModal(): void
    {
        $this->showCopyModal  = false;
        $this->copyFromRoleId = null;
    }

    public function copyPermissions(): void
    {
        if (! $this->copyFromRoleId) {
            return;
        }

        $source = Role::where('id', $this->copyFromRoleId)->first();

        if ($source) {
            $this->form->permissions = (new RoleService())->copyFromRole($source, $this->form->permissions);
        }

        $this->closeCopyModal();
    }

    public function updatedFormParentRoleId(): void
    {
        if (! $this->form->parent_role_id) {
            return;
        }

        $parent = Role::where('id', $this->form->parent_role_id)->first();

        if ($parent) {
            $this->form->permissions = (new RoleService())->mergeWithParent(
                $this->form->permissions,
                $parent
            );
        }
    }

    public function save(RoleService $roleService): mixed
    {
        $roleId = $this->role?->id;

        $this->form->validate();

        $this->validate([
            'form.code' => [
                'required',
                'string',
                'max:50',
                'regex:/^[A-Z0-9\-]+$/',
                Rule::unique('roles', 'code')->ignore($roleId),
            ],
        ]);

        $payload = [
            'name'             => $this->form->name,
            'department'       => $this->form->department,
            'code'             => strtoupper(trim($this->form->code)),
            'parent_role_id'   => $this->form->parent_role_id ?: null,
            'description'      => $this->form->description ?: null,
            'is_active'        => $this->form->is_active,
            'allow_assignment' => $this->form->allow_assignment,
            'permissions'      => $this->form->permissions,
        ];

        $roleService->save($payload, $this->role);

        return redirect()->route('roles.index')
            ->with('success', $this->role
                ? 'Função atualizada com sucesso!'
                : 'Função criada com sucesso!'
            );
    }

    #[Computed]
    public function otherRoles()
    {
        return Role::query()
            ->when($this->role, fn ($q) => $q->where('id', '!=', $this->role->id))
            ->orderBy('name')
            ->get(['id', 'name', 'department']);
    }

    #[Computed]
    public function permissionsSummary(): array
    {
        return (new RoleService())->countPermissions($this->form->permissions);
    }

    #[Computed]
    public function employeesCount(): int
    {
        return $this->role
            ? $this->role->employees()->count()
            : 0;
    }

    public function render()
    {
        $title = $this->role ? 'Editar Função' : 'Nova Função';

        return view('livewire.cadastro.funcoes.form', [
            'isEditing' => (bool) $this->role,
            'modules'   => RoleService::MODULES,
            'actions'   => RoleService::ACTIONS,
        ])->title($title);
    }
}


