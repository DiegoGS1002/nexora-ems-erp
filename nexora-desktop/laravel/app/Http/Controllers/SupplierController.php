<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Supplier;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::all();
        return view('cadastro.fornecedores.index', compact('suppliers'));

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
    }

    public function create()
    {
        return view('cadastro.fornecedores.create');
    }

    public function edit(Supplier $supplier)
    {
        return view('cadastro.fornecedores.edit', compact('supplier'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'social_name' => 'required|string|max:255',
            'taxNumber' => 'required|unique:suppliers,taxNumber|max:14',
            'email' => 'required|email|unique:suppliers,email',
            'phone_number' => 'required',
            'address_zip_code' => 'required',
            'address_street' => 'required',
            'address_number' => 'required',
            'address_complement' => 'nullable',
            'address_district' => 'required',
            'address_city' => 'required',
            'address_state' => 'required',
        ], [
            'taxNumber.unique' => 'Este CNPJ já está cadastrado.',
        ]);

        Supplier::create($validated);

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Fornecedor cadastrado com sucesso!');

    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'social_name' => 'required|string|max:255',
            'taxNumber' => [
                'required',
                Rule::unique('suppliers', 'taxNumber')->ignore($supplier->id),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('suppliers', 'email')->ignore($supplier->id),
            ],
        ]);

        $supplier->update($request->only([
            'name',
            'social_name',
            'taxNumber',
            'email',
            'phone_number',
            'address_zip_code',
            'address_street',
            'address_number',
            'address_complement',
            'address_district',
            'address_city',
            'address_state',
        ]));

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Fornecedor atualizado com sucesso!');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Fornecedor deletado com sucesso!');
    }

    /**
     * Show printable list of suppliers
     */
    public function print()
    {
        $suppliers = Supplier::all();
        return view('cadastro.fornecedores.print', compact('suppliers'));
    }
}
