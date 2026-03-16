<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Supplier;

class ProductSupplierController extends Controller
{
    // Tela de associação
    public function index(Product $product)
    {
        $suppliers = Supplier::all();
        $associatedSuppliers = $product->suppliers;

        return view(
            'produtos.suppliers',
            compact('product', 'suppliers', 'associatedSuppliers')
        );
    }

    // 1º e 2º cenário: associar fornecedor
    public function store(Request $request, Product $product)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        //  Produto já possui fornecedor associado
        if ($product->suppliers()->exists()) {
            return back()->withErrors([
                'supplier_id' => 'Este produto já possui um fornecedor associado!'
            ]);
        }

        //  Mesmo fornecedor já associado (proteção extra)
        if ($product->suppliers()
            ->where('supplier_id', $request->supplier_id)
            ->exists()
        ) {
            return back()->withErrors([
                'supplier_id' => 'Fornecedor já está associado a este produto!'
            ]);
        }

        // Associação bem-sucedida
        $product->suppliers()->attach($request->supplier_id);

        return back()->with(
            'success',
            'Fornecedor associado com sucesso ao produto!'
        );
    }

    // 3º cenário: desassociar fornecedor
    public function destroy(Product $product, Supplier $supplier)
    {
        $product->suppliers()->detach($supplier->id);

        return back()->with(
            'success',
            'Fornecedor desassociado com sucesso!'
        );
    }
}
