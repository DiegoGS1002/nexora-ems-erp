<?php


namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'teste@nexora.local'],
            [
                'name' => 'Teste',
                'password' => 'admin12345',
                'is_admin' => false,
                'is_active' => true,
                'has_license' => true,
                'modules' => [],
                'last_login_at' => null,
            ]
        );
    }
}

