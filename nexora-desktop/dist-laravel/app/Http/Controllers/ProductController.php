<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Supplier;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // NÃO PAGINAR AQUI
        $query = Product::with('suppliers');

        // Pesquisa por nome
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Filtro por fornecedor (N:N)
        if ($request->filled('supplier_id')) {
            $query->whereHas('suppliers', function ($q) use ($request) {
                $q->where('suppliers.id', $request->supplier_id);
            });
        }

        // Unidade de medida
        if ($request->filled('unit_of_measure')) {
            $query->where('unit_of_measure', $request->unit_of_measure);
        }

        //  Categoria
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Validade
        if ($request->expiration_date === 'expired') {
            $query->whereDate('expiration_date', '<', now());
        }

        if ($request->expiration_date === 'valid') {
            $query->whereDate('expiration_date', '>=', now());
        }

        if ($request->expiration_date === 'na') {
            $query->whereNull('expiration_date');
        }

        // PAGINAR SÓ NO FINAL
        $products = $query->paginate(10);

        $suppliers = Supplier::all();

        return view('cadastro.produtos.index', compact('products', 'suppliers'));
    }

    public function create(){
        $suppliers = Supplier::all(); // busca fornecedores
            return view('cadastro.produtos.create', compact('suppliers'));
    }

    public function edit(Product $product)
    {
        $suppliers = Supplier::all();

        return view('cadastro.produtos.edit', compact('product', 'suppliers'));
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

        return redirect()
            ->route('products.suppliers.index', $product->id)
            ->with('success', 'Produto cadastrado. Agora vincule um fornecedor.');
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
        $product->save();
        }

        return redirect()
            ->route('products.index')
            ->with('success', 'Produto atualizado com sucesso!');
    }

     public function destroy(Product $product){
        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produto deletado com sucesso!');
    }

    /**
     * Show printable list of products
     */
    public function print(Request $request)
    {
        $query = Product::query();

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('supplier_id')) {
            $query->whereHas('suppliers', function ($q) use ($request) {
                $q->where('suppliers.id', $request->supplier_id);
            });
        }

        $products = $query->get();

        return view('produtos.print', compact('products'));
    }
}
