<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvePdfLogo;
use App\Models\Employees;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    use ResolvePdfLogo;

    /**
     * Exporta a listagem de funcionarios em PDF.
     */
    public function print(Request $request)
    {
        $employees = Employees::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = (string) $request->string('search');

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('identification_number', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get();

        $pdf = Pdf::loadView('cadastro.funcionarios.print', array_merge([
            'employees'  => $employees,
            'printedAt'  => now(),
            'search'     => $request->string('search')->toString(),
        ], $this->resolvePdfLogo()))->setPaper('a4', 'landscape');

        return $pdf->download('funcionarios-' . now()->format('Y-m-d-His') . '.pdf');
    }
}
