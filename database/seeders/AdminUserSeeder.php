<?php


namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@nexora.local'],
            [
                'name' => 'Administrador',
                'password' => 'admin12345',
                'is_admin' => true,
                'is_active' => true,
                'has_license' => true,
                'modules' => [],
                'last_login_at' => null,
            ],

            ['email' => 'teste@nexora.com'],
            [
                'name' => 'teste',
                'password' => '12345678',
                'is_admin' => false,
                'is_active' => true,
                'has_license' => true,
                'modules' => [],
                'last_login_at' => null,
            ],

            ['email' => 'teste1@nexora.com'],
            [
                'name' => 'teste1',
                'password' => '12345678',
                'is_admin' => false,
                'is_active' => true,
                'has_license' => true,
                'modules' => [],
                'last_login_at' => null,
            ],

            ['email' => 'teste2@nexora.com'],
            [
                'name' => 'teste2',
                'password' => '12345678',
                'is_admin' => false,
                'is_active' => true,
                'has_license' => true,
                'modules' => [],
                'last_login_at' => null,
            ],

            ['email' => 'teste3@nexora.com'],
            [
                'name' => 'teste3',
                'password' => '12345678',
                'is_admin' => false,
                'is_active' => true,
                'has_license' => true,
                'modules' => [],
                'last_login_at' => null,
            ]
        );
    }
}

