<?php

use App\Models\Company;
use App\Models\User;
use App\Tenancy\TenantContext;
use Illuminate\Support\Facades\Route;

beforeEach(function () {
    config()->set('tenancy.platform_admin_emails', ['platform@example.com']);

    Route::middleware(['web', 'auth', 'tenant'])->get('/testing/tenant-context', function (TenantContext $tenantContext) {
        return response()->json([
            'company_id' => $tenantContext->hasCompany() ? $tenantContext->companyId() : null,
            'is_platform_admin' => auth()->user()->isPlatformAdmin(),
        ]);
    });
});

function createTenantCompany(string $name, bool $isActive = true): Company
{
    return Company::create([
        'name' => $name,
        'is_active' => $isActive,
    ]);
}

it('resolves Empresa A from its authenticated user', function () {
    $companyA = createTenantCompany('Empresa A');
    $userA = User::factory()->create(['company_id' => $companyA->id]);

    $this->actingAs($userA)
        ->getJson('/testing/tenant-context')
        ->assertOk()
        ->assertJson([
            'company_id' => $companyA->id,
            'is_platform_admin' => false,
        ]);
});

it('resolves Empresa B without accepting Empresa A from the request', function () {
    $companyA = createTenantCompany('Empresa A');
    $companyB = createTenantCompany('Empresa B');
    $userB = User::factory()->create(['company_id' => $companyB->id]);

    $this->actingAs($userB)
        ->getJson("/testing/tenant-context?company_id={$companyA->id}")
        ->assertOk()
        ->assertJson([
            'company_id' => $companyB->id,
            'is_platform_admin' => false,
        ]);
});

it('rejects a business user without a company', function () {
    $user = User::factory()->create([
        'company_id' => null,
        'is_admin' => false,
    ]);

    $this->actingAs($user)
        ->getJson('/testing/tenant-context')
        ->assertForbidden();
});

it('rejects a tenant administrator without a company', function () {
    $user = User::factory()->create([
        'email' => 'tenant-admin@example.com',
        'company_id' => null,
        'is_admin' => true,
    ]);

    expect($user->isTenantAdmin())->toBeFalse()
        ->and($user->isPlatformAdmin())->toBeFalse();

    $this->actingAs($user)
        ->getJson('/testing/tenant-context')
        ->assertForbidden();
});

it('keeps a tenant administrator restricted to its company', function () {
    $company = createTenantCompany('Empresa A');
    $user = User::factory()->create([
        'email' => 'tenant-admin@example.com',
        'company_id' => $company->id,
        'is_admin' => true,
    ]);

    expect($user->isTenantAdmin())->toBeTrue()
        ->and($user->isPlatformAdmin())->toBeFalse();

    $this->actingAs($user)
        ->getJson('/testing/tenant-context')
        ->assertOk()
        ->assertJson(['company_id' => $company->id]);
});

it('allows an explicitly configured platform administrator without tenant context', function () {
    $user = User::factory()->create([
        'email' => 'platform@example.com',
        'company_id' => null,
        'is_admin' => true,
    ]);

    expect($user->isPlatformAdmin())->toBeTrue()
        ->and($user->isTenantAdmin())->toBeFalse();

    $this->actingAs($user)
        ->getJson('/testing/tenant-context')
        ->assertOk()
        ->assertJson([
            'company_id' => null,
            'is_platform_admin' => true,
        ]);
});

it('does not use the platform allowlist without the admin flag', function () {
    $user = User::factory()->create([
        'email' => 'platform@example.com',
        'company_id' => null,
        'is_admin' => false,
    ]);

    expect($user->isPlatformAdmin())->toBeFalse();

    $this->actingAs($user)
        ->getJson('/testing/tenant-context')
        ->assertForbidden();
});

it('rejects a user whose company is inactive', function () {
    $company = createTenantCompany('Empresa inativa', false);
    $user = User::factory()->create(['company_id' => $company->id]);

    $this->actingAs($user)
        ->getJson('/testing/tenant-context')
        ->assertForbidden();
});

it('clears tenant context after the response', function () {
    $company = createTenantCompany('Empresa A');
    $user = User::factory()->create(['company_id' => $company->id]);

    $this->actingAs($user)
        ->getJson('/testing/tenant-context')
        ->assertOk();

    expect(app(TenantContext::class)->hasCompany())->toBeFalse();
});
