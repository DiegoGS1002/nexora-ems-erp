<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductApiController extends Controller
{
    public function index()
    {
        return Product::with('suppliers')->get();
    }

    public function show(Product $product)
    {
        return response()->json($product->load('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:255|unique:products,name',
            'ean' => 'required|string|size:13|unique:products,ean',
            'description' => 'required|string|max:255',
            'unit_of_measure' => 'required|string',
            'sale_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'expiration_date' => 'nullable|date',
            'category' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $imagePath = null;

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = Product::create([
            'name' => $request->name,
            'ean' => $request->ean,
            'description' => $request->description,
            'unit_of_measure' => $request->unit_of_measure,
            'sale_price' => $request->sale_price,
            'stock' => $request->stock,
            'expiration_date' => $request->expiration_date,
            'category' => $request->category,
            'image' => $imagePath,
        ]);

        return response()->json([
            'message' => 'Produto criado com sucesso',
            'data' => $product
        ], 201);
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|min:3|max:255|unique:products,name,' . $product->id,
            'ean' => 'required|string|size:13|unique:products,ean,' . $product->id,
            'description' => 'required|string|max:255',
            'unit_of_measure' => 'required|string',
            'sale_price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'expiration_date' => 'nullable|date',
            'category' => 'required|string',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->image = $request->file('image')->store('products', 'public');
        }

        $product->update($request->only([
            'name',
            'ean',
            'description',
            'unit_of_measure',
            'sale_price',
            'stock',
            'expiration_date',
            'category',
        ]));

        if ($request->hasFile('image')) {
            $product->save();
        }

        return response()->json([
            'message' => 'Produto atualizado com sucesso',
            'data' => $product
        ]);
    }

    public function destroy(Product $product)
    {
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return response()->json([
            'message' => 'Produto deletado com sucesso'
        ]);
    }
}
