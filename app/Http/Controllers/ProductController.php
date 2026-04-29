<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvePdfLogo;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    use ResolvePdfLogo;

    /**
     * Show printable list of products
     */
    public function print(Request $request)
    {
        $query = Product::query()->with('suppliers');

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('supplier_id')) {
            $query->whereHas('suppliers', function ($q) use ($request) {
                $q->where('suppliers.id', $request->supplier_id);
            });
        }

        if ($request->filled('unit_of_measure')) {
            $query->where('unit_of_measure', $request->unit_of_measure);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->expiration_date === 'expired') {
            $query->whereDate('expiration_date', '<', now());
        }

        if ($request->expiration_date === 'valid') {
            $query->whereDate('expiration_date', '>=', now());
        }

        if ($request->expiration_date === 'na') {
            $query->whereNull('expiration_date');
        }

        $products = $query->orderBy('name')->get();

        $pdf = Pdf::loadView('cadastro.produtos.print', array_merge([
            'products'   => $products,
            'printedAt'  => now(),
        ], $this->resolvePdfLogo()))->setPaper('a4', 'landscape');

        return $pdf->download('produtos-' . now()->format('Y-m-d-His') . '.pdf');
    }
}
