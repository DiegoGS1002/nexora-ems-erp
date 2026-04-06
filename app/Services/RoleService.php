<?php

namespace App\Services;

use App\Models\Role;

class RoleService
{
    public const MODULES = [
        'dashboard'     => 'Dashboard',
        'cadastros'     => 'Cadastros',
        'vendas'        => 'Vendas',
        'compras'       => 'Compras',
        'financeiro'    => 'Financeiro',
        'estoque'       => 'Estoque',
        'producao'      => 'Produção',
        'logistica'     => 'Logística',
        'fiscal'        => 'Fiscal',
        'rh'            => 'Recursos Humanos',
        'administracao' => 'Administração',
    ];

    public const ACTIONS = [
        'view'   => 'Visualizar',
        'create' => 'Criar',
        'edit'   => 'Editar',
        'delete' => 'Excluir',
        'export' => 'Exportar',
    ];

    public function buildEmptyPermissions(): array
    {
        $permissions = [];
        foreach (array_keys(self::MODULES) as $module) {
            foreach (array_keys(self::ACTIONS) as $action) {
                $permissions[$module][$action] = false;
            }
        }
        return $permissions;
    }

    public function mergeWithParent(array $permissions, ?Role $parentRole): array
    {
        if (! $parentRole) {
            return $permissions;
        }

        $parentPerms = $parentRole->permissions ?? [];

        foreach (array_keys(self::MODULES) as $module) {
            foreach (array_keys(self::ACTIONS) as $action) {
                if (! ($permissions[$module][$action] ?? false)) {
                    $permissions[$module][$action] = $parentPerms[$module][$action] ?? false;
                }
            }
        }

        return $permissions;
    }

    public function countPermissions(array $permissions): array
    {
        $allowed = 0;
        $total   = 0;

        foreach (array_keys(self::MODULES) as $module) {
            foreach (array_keys(self::ACTIONS) as $action) {
                $total++;
                if ($permissions[$module][$action] ?? false) {
                    $allowed++;
                }
            }
        }

        return [
            'allowed'       => $allowed,
            'denied'        => 0,
            'unconfigured'  => $total - $allowed,
            'total'         => $total,
        ];
    }

    public function selectAll(array $permissions): array
    {
        foreach (array_keys(self::MODULES) as $module) {
            foreach (array_keys(self::ACTIONS) as $action) {
                $permissions[$module][$action] = true;
            }
        }
        return $permissions;
    }

    public function clearAll(array $permissions): array
    {
        foreach (array_keys(self::MODULES) as $module) {
            foreach (array_keys(self::ACTIONS) as $action) {
                $permissions[$module][$action] = false;
            }
        }
        return $permissions;
    }

    public function copyFromRole(Role $source, array $targetPermissions): array
    {
        $sourcePerms = $source->permissions ?? [];

        foreach (array_keys(self::MODULES) as $module) {
            foreach (array_keys(self::ACTIONS) as $action) {
                $targetPermissions[$module][$action] = $sourcePerms[$module][$action] ?? false;
            }
        }

        return $targetPermissions;
    }

    public function save(array $data, ?Role $role = null): Role
    {
        if ($role) {
            $role->update($data);
            return $role;
        }

        return Role::create($data);
    }
}

