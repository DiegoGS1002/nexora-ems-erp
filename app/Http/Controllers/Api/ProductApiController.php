<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|min:3|max:255|unique:products,name',
            'ean'                   => 'nullable|string|size:13|unique:products,ean',
            'ncm'                   => 'nullable|string|max:10',
            'cfop_saida'            => 'nullable|string|max:10',
            'cfop_entrada'          => 'nullable|string|max:10',
            'grupo_tributario_id'   => 'nullable|exists:grupo_tributarios,id',
            'unit_of_measure_id'    => 'nullable|exists:unit_of_measures,id',
            'unit_of_measure'       => 'nullable|string|max:20',
            'product_category_id'   => 'nullable|exists:product_categories,id',
            'category'              => 'nullable|string|max:255',
            'description'           => 'nullable|string|max:500',
            'short_description'     => 'nullable|string|max:255',
            'full_description'      => 'nullable|string',
            'brand'                 => 'nullable|string|max:255',
            'product_type'          => 'nullable|string|in:produto_fisico,servico',
            'nature'                => 'nullable|string|in:mercadoria_revenda,uso_consumo,materia_prima,produto_acabado,embalagem,ativo_imobilizado',
            'product_line'          => 'nullable|string|max:255',
            'sale_price'            => 'required|numeric|min:0',
            'cost_price'            => 'nullable|numeric|min:0',
            'stock'                 => 'nullable|integer|min:0',
            'stock_min'             => 'nullable|integer|min:0',
            'expiration_date'       => 'nullable|date',
            'weight_net'            => 'nullable|numeric|min:0',
            'weight_gross'          => 'nullable|numeric|min:0',
            'height'                => 'nullable|numeric|min:0',
            'width'                 => 'nullable|numeric|min:0',
            'depth'                 => 'nullable|numeric|min:0',
            'is_active'             => 'nullable|boolean',
            'highlights'            => 'nullable|array',
            'tags'                  => 'nullable|array',
            'image'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('products', 'public');
        }

        $product = Product::create($validated);

        return response()->json([
            'message' => 'Produto criado com sucesso',
            'data'    => $product->load(['suppliers', 'unitOfMeasure', 'productCategory', 'grupoTributario']),
        ], 201);
    }

    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|min:3|max:255|unique:products,name,' . $product->id,
            'ean'                   => 'nullable|string|size:13|unique:products,ean,' . $product->id,
            'ncm'                   => 'nullable|string|max:10',
            'cfop_saida'            => 'nullable|string|max:10',
            'cfop_entrada'          => 'nullable|string|max:10',
            'grupo_tributario_id'   => 'nullable|exists:grupo_tributarios,id',
            'unit_of_measure_id'    => 'nullable|exists:unit_of_measures,id',
            'unit_of_measure'       => 'nullable|string|max:20',
            'product_category_id'   => 'nullable|exists:product_categories,id',
            'category'              => 'nullable|string|max:255',
            'description'           => 'nullable|string|max:500',
            'short_description'     => 'nullable|string|max:255',
            'full_description'      => 'nullable|string',
            'brand'                 => 'nullable|string|max:255',
            'product_type'          => 'nullable|string|in:produto_fisico,servico',
            'nature'                => 'nullable|string|in:mercadoria_revenda,uso_consumo,materia_prima,produto_acabado,embalagem,ativo_imobilizado',
            'product_line'          => 'nullable|string|max:255',
            'sale_price'            => 'required|numeric|min:0',
            'cost_price'            => 'nullable|numeric|min:0',
            'stock'                 => 'nullable|integer|min:0',
            'stock_min'             => 'nullable|integer|min:0',
            'expiration_date'       => 'nullable|date',
            'weight_net'            => 'nullable|numeric|min:0',
            'weight_gross'          => 'nullable|numeric|min:0',
            'height'                => 'nullable|numeric|min:0',
            'width'                 => 'nullable|numeric|min:0',
            'depth'                 => 'nullable|numeric|min:0',
            'is_active'             => 'nullable|boolean',
            'highlights'            => 'nullable|array',
            'tags'                  => 'nullable|array',
            'image'                 => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

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
