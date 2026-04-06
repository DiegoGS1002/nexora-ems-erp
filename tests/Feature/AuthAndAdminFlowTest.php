<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('shows login screen for guests', function () {
    $response = $this->get(route('login'));

    $response->assertOk();
    $response->assertSee('Entrar no Sistema');
});

it('authenticates user and updates last login timestamp', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => 'password',
    ]);

    $response = $this->post(route('login.store'), [
        'email' => 'user@example.com',
        'password' => 'password',
    ]);

    $response->assertRedirect(route('home'));
    $this->assertAuthenticatedAs($user);

    expect($user->fresh()->last_login_at)->not->toBeNull();
});

it('logs out and redirects to login', function () {
    $this->actingAs(User::factory()->create([
        'last_login_at' => now(),
    ]));

    $response = $this->post(route('logout'));

    $response->assertRedirect(route('login'));
    $this->assertGuest();
});

it('expires authenticated session after midnight boundary', function () {
    $user = User::factory()->create([
        'last_login_at' => now()->subDay(),
    ]);

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertRedirect(route('login'));
    $this->assertGuest();
});

it('blocks non-admin from user management area', function () {
    $response = $this->actingAs(User::factory()->create([
        'is_admin' => false,
        'last_login_at' => now(),
    ]))
        ->get(route('users.index'));

    $response->assertForbidden();
});

it('allows admin to create users', function () {
    $admin = User::factory()->create([
        'is_admin' => true,
        'last_login_at' => now(),
    ]);

    $response = $this->actingAs($admin)->post(route('users.store'), [
        'name' => 'Novo Usuario',
        'email' => 'novo.usuario@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'is_admin' => '1',
        'is_active' => '1',
        'has_license' => '1',
    ]);

    $response->assertRedirect(route('users.index'));

    $this->assertDatabaseHas('users', [
        'email' => 'novo.usuario@example.com',
        'is_admin' => 1,
    ]);
});


