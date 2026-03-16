<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;

class ProductSupplierApiController extends Controller
{
    public function index(Product $product)
    {
        return response()->json($product->suppliers);
    }

    public function attach(Request $request, Product $product)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id'
        ]);

        $product->suppliers()->syncWithoutDetaching($request->supplier_id);

        return response()->json([
            'message' => 'Fornecedor associado com sucesso',
            'data' => $product->load('suppliers')
        ]);
    }

    public function detach(Product $product, Supplier $supplier)
    {
        $product->suppliers()->detach($supplier->id);

        return response()->json([
            'message' => 'Fornecedor desassociado com sucesso',
            'data' => $product->load('suppliers')
        ]);
    }
}
