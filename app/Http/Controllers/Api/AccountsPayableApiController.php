<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccountsPayable;
use Illuminate\Http\Request;

class AccountsPayableApiController extends Controller
{
    /**
     * Lista todas as contas a pagar
     */
    public function index()
    {
        return AccountsPayable::orderBy('due_date', 'asc')->get();
    }

    /**
     * Exibe uma conta a pagar específica
     */
    public function show(AccountsPayable $accountPayable)
    {
        return response()->json($accountPayable);
    }

    /**
     * Cria uma nova conta a pagar
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value' => 'required|numeric|min:0',
            'due_date' => 'required|date',
        ]);

        $accountPayable = AccountsPayable::create($request->only([
            'name',
            'description',
            'value',
            'due_date',
        ]));

        return response()->json([
            'message' => 'Conta a pagar criada com sucesso',
            'data' => $accountPayable
        ], 201);
    }

    /**
     * Atualiza uma conta a pagar
     */
    public function update(Request $request, AccountsPayable $accountPayable)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'value' => 'required|numeric|min:0',
            'due_date' => 'required|date',
        ]);

        $accountPayable->update($request->only([
            'name',
            'description',
            'value',
            'due_date',
        ]));

        return response()->json([
            'message' => 'Conta a pagar atualizada com sucesso',
            'data' => $accountPayable
        ]);
    }

    /**
     * Remove uma conta a pagar
     */
    public function destroy(AccountsPayable $accountPayable)
    {
        $accountPayable->delete();

        return response()->json([
            'message' => 'Conta a pagar removida com sucesso'
        ]);
    }
}



