<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SupplierApiController extends Controller
{
    public function index()
    {
        return Supplier::all();
    }

    public function show(Supplier $supplier)
    {
        return response()->json($supplier->load('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
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

        $supplier = Supplier::create($request->all());

        return response()->json([
            'message' => 'Fornecedor criado com sucesso',
            'data' => $supplier
        ], 201);
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
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
            'phone_number' => 'required',
            'address_zip_code' => 'required',
            'address_street' => 'required',
            'address_number' => 'required',
            'address_complement' => 'nullable',
            'address_district' => 'required',
            'address_city' => 'required',
            'address_state' => 'required',
        ]);

        $supplier->update($request->all());

        return response()->json([
            'message' => 'Fornecedor atualizado com sucesso',
            'data' => $supplier
        ]);
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();

        return response()->json([
            'message' => 'Fornecedor deletado com sucesso'
        ]);
    }
}
