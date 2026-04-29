<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvePdfLogo;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    use ResolvePdfLogo;

    /**
     * Exporta a listagem de fornecedores em PDF.
     */
    public function print(Request $request)
    {
        $suppliers = Supplier::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = (string) $request->string('search');

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('social_name', 'like', "%{$search}%")
                        ->orWhere('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('taxNumber', 'like', "%{$search}%")
                        ->orWhere('address_city', 'like', "%{$search}%");
                });
            })
            ->orderBy('social_name')
            ->get();

        $pdf = Pdf::loadView('cadastro.fornecedores.print', array_merge([
            'suppliers'  => $suppliers,
            'printedAt'  => now(),
            'search'     => $request->string('search')->toString(),
        ], $this->resolvePdfLogo()))->setPaper('a4', 'landscape');

        return $pdf->download('fornecedores-' . now()->format('Y-m-d-His') . '.pdf');
    }
}
