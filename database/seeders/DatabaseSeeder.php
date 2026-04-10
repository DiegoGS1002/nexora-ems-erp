<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
            SettingsSeeder::class,
            UnitOfMeasureSeeder::class,
            ProductCategorySeeder::class,
            PlanoContasSeeder::class,
            SupplierSeeder::class,
            ClientSeeder::class,
            ProductSeeder::class,
            RoleSeeder::class,
            EmployeeSeeder::class,
        ]);
    }
}
