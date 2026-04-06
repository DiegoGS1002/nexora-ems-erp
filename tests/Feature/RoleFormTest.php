<?php

use App\Livewire\Cadastro\Funcoes\Form;
use App\Livewire\Cadastro\Funcoes\Index;
use App\Models\Role;
use App\Services\RoleService;
use Livewire\Livewire;

beforeEach(function () {
    $this->actingAs(\App\Models\User::factory()->create());
});

// ── INDEX ──────────────────────────────────────────────

it('renderiza a listagem de funções', function () {
    Livewire::test(Index::class)
        ->assertSee('Funções / Cargos')
        ->assertSee('Nova Função');
});

it('lista as funções cadastradas', function () {
    Role::create([
        'name'       => 'Analista Financeiro',
        'department' => 'Financeiro',
        'code'       => 'FIN-ANL',
        'is_active'  => true,
    ]);

    Livewire::test(Index::class)
        ->assertSee('Analista Financeiro')
        ->assertSee('Financeiro');
});

it('filtra funções pela busca', function () {
    Role::create(['name' => 'Analista Financeiro', 'code' => 'FIN-ANL', 'department' => 'Financeiro', 'is_active' => true]);
    Role::create(['name' => 'Coordenador de RH',   'code' => 'RH-CORD', 'department' => 'RH',         'is_active' => true]);

    Livewire::test(Index::class)
        ->set('search', 'Financeiro')
        ->assertSee('Analista Financeiro')
        ->assertDontSee('Coordenador de RH');
});

it('exclui uma função', function () {
    $role = Role::create(['name' => 'Cargo Teste', 'code' => 'TST-001', 'is_active' => true]);

    Livewire::test(Index::class)
        ->call('deleteRole', $role->id);

    expect(Role::where('id', $role->id)->exists())->toBeFalse();
});

// ── FORM — CREATE ──────────────────────────────────────

it('renderiza o formulário de criação de função', function () {
    Livewire::test(Form::class)
        ->assertSee('Cadastro de Funções')
        ->assertSee('Salvar Função')
        ->assertSee('Permissões');
});

it('valida campos obrigatórios ao salvar', function () {
    Livewire::test(Form::class)
        ->call('save')
        ->assertHasErrors(['form.name', 'form.department', 'form.code']);
});

it('valida que o código aceita apenas letras maiúsculas, números e hífen', function () {
    Livewire::test(Form::class)
        ->set('form.name', 'Analista')
        ->set('form.department', 'Financeiro')
        ->set('form.code', 'codigo com espaço')
        ->call('save')
        ->assertHasErrors(['form.code']);
});

it('cria uma nova função com dados válidos', function () {
    Livewire::test(Form::class)
        ->set('form.name', 'Analista Financeiro')
        ->set('form.department', 'Financeiro')
        ->set('form.code', 'FIN-ANL')
        ->set('form.description', 'Responsável pela análise financeira')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('roles.index'));

    expect(Role::where('code', 'FIN-ANL')->exists())->toBeTrue();
});

it('valida unicidade do código ao criar', function () {
    Role::create(['name' => 'Cargo Existente', 'code' => 'FIN-ANL', 'department' => 'Financeiro', 'is_active' => true]);

    Livewire::test(Form::class)
        ->set('form.name', 'Outro Cargo')
        ->set('form.department', 'Financeiro')
        ->set('form.code', 'FIN-ANL')
        ->call('save')
        ->assertHasErrors(['form.code']);
});

// ── FORM — EDIT ────────────────────────────────────────

it('renderiza o formulário de edição com dados preenchidos', function () {
    $role = Role::create([
        'name'       => 'Analista Financeiro',
        'department' => 'Financeiro',
        'code'       => 'FIN-ANL',
        'description'=> 'Cargo de análise',
        'is_active'  => true,
    ]);

    Livewire::test(Form::class, ['role' => $role])
        ->assertSee('Editar Função')
        ->assertSet('form.name', 'Analista Financeiro')
        ->assertSet('form.department', 'Financeiro')
        ->assertSet('form.code', 'FIN-ANL');
});

it('atualiza uma função existente', function () {
    $role = Role::create([
        'name'       => 'Cargo Antigo',
        'department' => 'RH',
        'code'       => 'RH-001',
        'is_active'  => true,
    ]);

    Livewire::test(Form::class, ['role' => $role])
        ->set('form.name', 'Cargo Atualizado')
        ->set('form.department', 'RH')
        ->set('form.code', 'RH-001')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('roles.index'));

    expect(Role::where('name', 'Cargo Atualizado')->exists())->toBeTrue();
});

it('permite código duplicado ao editar a própria função', function () {
    $role = Role::create([
        'name'       => 'Analista',
        'department' => 'Financeiro',
        'code'       => 'FIN-ANL',
        'is_active'  => true,
    ]);

    Livewire::test(Form::class, ['role' => $role])
        ->set('form.name', 'Analista Atualizado')
        ->set('form.department', 'Financeiro')
        ->set('form.code', 'FIN-ANL')
        ->call('save')
        ->assertHasNoErrors();
});

// ── PERMISSIONS ────────────────────────────────────────

it('seleciona todas as permissões ao chamar selectAll', function () {
    $component = Livewire::test(Form::class)
        ->call('selectAll');

    $permissions = $component->get('form.permissions');

    foreach (array_keys(RoleService::MODULES) as $module) {
        foreach (array_keys(RoleService::ACTIONS) as $action) {
            expect($permissions[$module][$action])->toBeTrue();
        }
    }
});

it('limpa todas as permissões ao chamar clearAll', function () {
    $component = Livewire::test(Form::class)
        ->call('selectAll')
        ->call('clearAll');

    $permissions = $component->get('form.permissions');

    foreach (array_keys(RoleService::MODULES) as $module) {
        foreach (array_keys(RoleService::ACTIONS) as $action) {
            expect($permissions[$module][$action])->toBeFalse();
        }
    }
});

it('copia permissões de outra função', function () {
    $source = Role::create([
        'name'        => 'Gerente',
        'department'  => 'Administrativo',
        'code'        => 'ADM-GER',
        'is_active'   => true,
        'permissions' => ['dashboard' => ['view' => true, 'create' => false, 'edit' => false, 'delete' => false, 'export' => false]],
    ]);

    Livewire::test(Form::class)
        ->set('copyFromRoleId', $source->id)
        ->call('copyPermissions')
        ->assertSet('showCopyModal', false);
});

it('herda permissões ao selecionar função superior', function () {
    $service = new RoleService();
    $emptyPerms = $service->buildEmptyPermissions();
    $parentPerms = $emptyPerms;
    $parentPerms['dashboard']['view'] = true;

    $parent = Role::create([
        'name'        => 'Gerente',
        'department'  => 'Administrativo',
        'code'        => 'ADM-GER',
        'is_active'   => true,
        'permissions' => $parentPerms,
    ]);

    $component = Livewire::test(Form::class)
        ->set('form.parent_role_id', $parent->id);

    $permissions = $component->get('form.permissions');
    expect($permissions['dashboard']['view'])->toBeTrue();
});

// ── ROLE SERVICE ────────────────────────────────────────

it('RoleService::buildEmptyPermissions retorna estrutura correta', function () {
    $service = new RoleService();
    $perms = $service->buildEmptyPermissions();

    expect($perms)->toHaveKey('dashboard');
    expect($perms['dashboard'])->toHaveKey('view');
    expect($perms['dashboard']['view'])->toBeFalse();
});

it('RoleService::countPermissions conta corretamente', function () {
    $service = new RoleService();
    $perms = $service->buildEmptyPermissions();
    $perms['dashboard']['view'] = true;
    $perms['cadastros']['create'] = true;

    $result = $service->countPermissions($perms);

    expect($result['allowed'])->toBe(2);
    expect($result['total'])->toBe(count(RoleService::MODULES) * count(RoleService::ACTIONS));
});


