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
            // ── Base ─────────────────────────────────────────────────────────
            AdminUserSeeder::class,
            SettingsSeeder::class,
            CompanySeeder::class,
            UserCompanySeeder::class,

            // ── Cadastros ─────────────────────────────────────────────────────
            UnitOfMeasureSeeder::class,
            ProductCategorySeeder::class,
            PlanoContasSeeder::class,
            RoleSeeder::class,
            SupplierSeeder::class,
            ClientSeeder::class,
            CarrierSeeder::class,
            ProductSeeder::class,
            PriceTableSeeder::class,
            VehicleSeeder::class,

            // ── RH / Ponto ────────────────────────────────────────────────────
            EmployeeSeeder::class,
            WorkShiftSeeder::class,
            TimeRecordSeeder::class,
            PayrollSeeder::class,

            // ── Fiscal ───────────────────────────────────────────────────────
            TipoOperacaoFiscalSeeder::class,
            GrupoTributarioSeeder::class,

            // ── Compras ──────────────────────────────────────────────────────
            PurchaseRequisitionSeeder::class,
            CotacaoSeeder::class,
            PurchaseOrderSeeder::class,

            // ── Vendas / Estoque ──────────────────────────────────────────────
            SalesOrderSeeder::class,
            StockMovementSeeder::class,
            ProductionOrderSeeder::class,

            // ── Financeiro ────────────────────────────────────────────────────
            BankAccountSeeder::class,
            AccountPayableSeeder::class,
            AccountReceivableSeeder::class,
            CashFlowSeeder::class,

            // ── Suporte / Logs ────────────────────────────────────────────────
            SystemLogSeeder::class,
        ]);
    }
}
