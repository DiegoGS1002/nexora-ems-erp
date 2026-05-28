<?php

namespace Database\Seeders;

use App\Models\Employees;
use App\Models\Payroll;
use App\Models\PayrollItem;
use Illuminate\Database\Seeder;

class PayrollSeeder extends Seeder
{
    public function run(): void
    {
        if (Payroll::count() > 0) {
            $this->command->warn('Folhas de pagamento já existem. Seeder ignorado.');
            return;
        }

        $employees = Employees::take(15)->get();

        if ($employees->isEmpty()) {
            $this->command->warn('Nenhum funcionário encontrado. Execute EmployeeSeeder primeiro.');
            return;
        }

        $months = ['2026-04-01', '2026-05-01'];
        $count  = 0;

        foreach ($months as $month) {
            foreach ($employees as $emp) {
                $baseSalary      = (float) ($emp->salary ?? 2500.00);
                $vt               = min($baseSalary * 0.06, 350.00);   // Vale Transporte (desconto 6%)
                $vr               = 22 * 30;                             // Vale Refeição
                $inss             = $this->calcInss($baseSalary);
                $irrf             = $this->calcIrrf($baseSalary - $inss);
                $totalEarnings    = $baseSalary + $vr;
                $totalDeductions  = $inss + $irrf + $vt;
                $netSalary        = $totalEarnings - $totalDeductions;

                $payroll = Payroll::create([
                    'employee_id'      => $emp->id,
                    'reference_month'  => $month,
                    'base_salary'      => $baseSalary,
                    'total_earnings'   => $totalEarnings,
                    'total_deductions' => $totalDeductions,
                    'net_salary'       => $netSalary,
                    'status'           => $month === '2026-04-01' ? 'paid' : 'closed',
                    'payment_date'     => $month === '2026-04-01' ? date('Y-m-d', strtotime($month . ' +4 days')) : null,
                ]);

                // Items de provento
                PayrollItem::create([
                    'payroll_id'  => $payroll->id,
                    'type'        => 'earning',
                    'description' => 'Salário Base',
                    'amount'      => $baseSalary,
                ]);
                PayrollItem::create([
                    'payroll_id'  => $payroll->id,
                    'type'        => 'earning',
                    'description' => 'Vale Refeição',
                    'amount'      => $vr,
                ]);

                // Items de desconto
                PayrollItem::create([
                    'payroll_id'  => $payroll->id,
                    'type'        => 'deduction',
                    'description' => 'INSS',
                    'amount'      => $inss,
                ]);
                if ($irrf > 0) {
                    PayrollItem::create([
                        'payroll_id'  => $payroll->id,
                        'type'        => 'deduction',
                        'description' => 'IRRF',
                        'amount'      => $irrf,
                    ]);
                }
                PayrollItem::create([
                    'payroll_id'  => $payroll->id,
                    'type'        => 'deduction',
                    'description' => 'Vale Transporte',
                    'amount'      => $vt,
                ]);

                $count++;
            }
        }

        $this->command->info("✅ {$count} folhas de pagamento criadas (com itens).");
    }

    private function calcInss(float $salary): float
    {
        // Tabela INSS 2026 simplificada
        if ($salary <= 1412.00)  return round($salary * 0.075, 2);
        if ($salary <= 2666.68)  return round($salary * 0.09, 2);
        if ($salary <= 4000.03)  return round($salary * 0.12, 2);
        if ($salary <= 7786.02)  return round($salary * 0.14, 2);
        return round(7786.02 * 0.14, 2); // teto
    }

    private function calcIrrf(float $base): float
    {
        if ($base <= 2259.20)  return 0;
        if ($base <= 2826.65)  return round($base * 0.075 - 169.44, 2);
        if ($base <= 3751.05)  return round($base * 0.15  - 381.44, 2);
        if ($base <= 4664.68)  return round($base * 0.225 - 662.77, 2);
        return round($base * 0.275 - 896.00, 2);
    }
}


