<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\Setting;

class HoleriteController extends Controller
{
    public function print(int $id)
    {
        $payroll = Payroll::with(['items', 'employee'])->findOrFail($id);

        $company = [
            'name'    => Setting::get('company_name', 'Nexora ERP'),
            'cnpj'    => Setting::get('company_cnpj', ''),
            'address' => Setting::get('company_address', ''),
            'city'    => Setting::get('company_city', ''),
            'state'   => Setting::get('company_state', ''),
            'phone'   => Setting::get('company_phone', ''),
        ];

        $earnings   = $payroll->items->where('type', 'earning');
        $deductions = $payroll->items->where('type', 'deduction');

        $baseSalary      = (float) $payroll->base_salary;
        $totalEarnings   = (float) $payroll->total_earnings;
        $totalDeductions = (float) $payroll->total_deductions;
        $netSalary       = (float) $payroll->net_salary;
        $totalVencimentos = $baseSalary + $totalEarnings;

        $baseInss = $totalVencimentos;
        $baseFgts = $totalVencimentos;
        $baseIrrf = max(0, $totalVencimentos - $totalDeductions);

        return view('rh.holerite.print', compact(
            'payroll',
            'company',
            'earnings',
            'deductions',
            'baseSalary',
            'totalEarnings',
            'totalDeductions',
            'netSalary',
            'totalVencimentos',
            'baseInss',
            'baseFgts',
            'baseIrrf'
        ));
    }
}

