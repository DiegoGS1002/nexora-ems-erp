<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserCompanySeeder extends Seeder
{
    public function run(): void
    {
        $companyAlimentos  = Company::where('cnpj', '12.345.678/0001-90')->first();
        $companyLogistica  = Company::where('cnpj', '23.456.789/0001-12')->first();
        $companyIndustria  = Company::where('cnpj', '34.567.890/0001-34')->first();
        $companyVarejo     = Company::where('cnpj', '45.678.901/0001-56')->first();

        $users = [
            // ── Nexora Alimentos ────────────────────────────────────────────
            [
                'name'       => 'Carlos Eduardo Mendes',
                'email'      => 'teste@nexora.local',
                'password'   => Hash::make('12345678'),
                'job_title'  => 'Gerente Comercial',
                'department' => 'Comercial',
                'phone'      => '(11) 91234-5678',
                'is_admin'   => false,
                'is_active'  => true,
                'has_license'=> true,
                'company_id' => $companyAlimentos?->id,
                'modules'    => ['vendas', 'clientes', 'produtos'],
            ],
            [
                'name'       => 'Ana Paula Ferreira',
                'email'      => 'ana.ferreira@nexora-alimentos.com.br',
                'password'   => Hash::make('senha@123'),
                'job_title'  => 'Analista Financeiro',
                'department' => 'Financeiro',
                'phone'      => '(11) 92345-6789',
                'is_admin'   => false,
                'is_active'  => true,
                'has_license'=> true,
                'company_id' => $companyAlimentos?->id,
                'modules'    => ['financeiro', 'contas_pagar', 'contas_receber'],
            ],
            [
                'name'       => 'Roberto Silva Santos',
                'email'      => 'roberto.santos@nexora-alimentos.com.br',
                'password'   => Hash::make('senha@123'),
                'job_title'  => 'Vendedor Externo',
                'department' => 'Vendas',
                'phone'      => '(11) 93456-7890',
                'is_admin'   => false,
                'is_active'  => true,
                'has_license'=> true,
                'company_id' => $companyAlimentos?->id,
                'modules'    => ['vendas', 'pedidos'],
            ],

            // ── Nexora Logística ─────────────────────────────────────────────
            [
                'name'       => 'Fernanda Lima Costa',
                'email'      => 'fernanda.costa@nexora-logistica.com.br',
                'password'   => Hash::make('senha@123'),
                'job_title'  => 'Coordenadora de Frota',
                'department' => 'Operações',
                'phone'      => '(21) 91234-0001',
                'is_admin'   => false,
                'is_active'  => true,
                'has_license'=> true,
                'company_id' => $companyLogistica?->id,
                'modules'    => ['logistica', 'veiculos', 'rotas'],
            ],
            [
                'name'       => 'Marcos Vinicius Pereira',
                'email'      => 'marcos.pereira@nexora-logistica.com.br',
                'password'   => Hash::make('senha@123'),
                'job_title'  => 'Motorista Sênior',
                'department' => 'Operações',
                'phone'      => '(21) 99876-5432',
                'is_admin'   => false,
                'is_active'  => true,
                'has_license'=> true,
                'company_id' => $companyLogistica?->id,
                'modules'    => ['rotas', 'entregas'],
            ],

            // ── Nexora Indústria ─────────────────────────────────────────────
            [
                'name'       => 'João Batista Oliveira',
                'email'      => 'joao.oliveira@nexora-industria.com.br',
                'password'   => Hash::make('senha@123'),
                'job_title'  => 'Supervisor de Produção',
                'department' => 'Produção',
                'phone'      => '(31) 91111-2222',
                'is_admin'   => false,
                'is_active'  => true,
                'has_license'=> true,
                'company_id' => $companyIndustria?->id,
                'modules'    => ['producao', 'ordens_producao', 'estoque'],
            ],
            [
                'name'       => 'Patrícia Souza Ramos',
                'email'      => 'patricia.ramos@nexora-industria.com.br',
                'password'   => Hash::make('senha@123'),
                'job_title'  => 'Analista de Qualidade',
                'department' => 'Qualidade',
                'phone'      => '(31) 92222-3333',
                'is_admin'   => false,
                'is_active'  => true,
                'has_license'=> true,
                'company_id' => $companyIndustria?->id,
                'modules'    => ['producao', 'qualidade'],
            ],

            // ── Nexora Varejo ────────────────────────────────────────────────
            [
                'name'       => 'Lucas Henrique Alves',
                'email'      => 'lucas.alves@nexora-varejo.com.br',
                'password'   => Hash::make('senha@123'),
                'job_title'  => 'Gerente de Loja',
                'department' => 'Varejo',
                'phone'      => '(41) 93333-4444',
                'is_admin'   => false,
                'is_active'  => true,
                'has_license'=> true,
                'company_id' => $companyVarejo?->id,
                'modules'    => ['vendas', 'estoque', 'caixa'],
            ],
            [
                'name'       => 'Camila Torres Martins',
                'email'      => 'camila.martins@nexora-varejo.com.br',
                'password'   => Hash::make('senha@123'),
                'job_title'  => 'Assistente de Vendas',
                'department' => 'Vendas',
                'phone'      => '(41) 94444-5555',
                'is_admin'   => false,
                'is_active'  => true,
                'has_license'=> true,
                'company_id' => $companyVarejo?->id,
                'modules'    => ['vendas', 'pedidos'],
            ],
        ];

        $created = 0;
        foreach ($users as $userData) {
            // O módulo 'administracao' é exclusivo de usuários com is_admin = true
            if (empty($userData['is_admin'])) {
                $userData['modules'] = array_values(
                    array_filter($userData['modules'], fn($m) => $m !== 'administracao')
                );
            }

            User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            $created++;
        }

        $this->command->info("✅ {$created} usuários semeados e vinculados às empresas.");
    }
}

