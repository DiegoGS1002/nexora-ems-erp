<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreProductRequest;
use App\Http\Requests\Api\UpdateProductRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['suppliers', 'unitOfMeasure', 'productCategory', 'grupoTributario']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('product_code', 'like', "%{$search}%")
                  ->orWhere('ean', 'like', "%{$search}%");
            });
        }

        if ($request->filled('is_active')) {
            $query->where('is_active', filter_var($request->is_active, FILTER_VALIDATE_BOOLEAN));
        }

        if ($request->filled('product_type')) {
            $query->where('product_type', $request->product_type);
        }

        if ($request->filled('product_category_id')) {
            $query->where('product_category_id', $request->product_category_id);
        }

        $perPage = $request->get('per_page', 50);

        return $request->boolean('paginate', true)
            ? $query->paginate($perPage)
            : $query->get();
    }

    public function show(Product $product)
    {
        return response()->json($product->load(['suppliers', 'unitOfMeasure', 'productCategory', 'grupoTributario']));
    }

    public function store(StoreProductRequest $request)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Produto criado com sucesso',
            'data'    => $product->load(['suppliers', 'unitOfMeasure', 'productCategory', 'grupoTributario']),
        ], 201);
    }

    public function update(UpdateProductRequest $request, Product $product)
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return response()->json([
            'message' => 'Produto atualizado com sucesso',
            'data'    => $product->fresh()->load(['suppliers', 'unitOfMeasure', 'productCategory', 'grupoTributario']),
        ]);
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'message' => 'Produto deletado com sucesso',
        ]);
    }
}
