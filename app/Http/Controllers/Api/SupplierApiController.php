<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSupplierRequest;
use App\Http\Requests\Api\UpdateSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\Request;

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

    public function store(StoreSupplierRequest $request)
    {
        $supplier = Supplier::create($request->validated());

        return response()->json([
            'message' => 'Fornecedor criado com sucesso',
            'data' => $supplier
        ], 201);
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier)
    {
        $supplier->update($request->validated());

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
