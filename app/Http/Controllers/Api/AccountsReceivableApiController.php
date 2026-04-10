<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AccountReceivable;
use Illuminate\Http\Request;

class AccountsReceivableApiController extends Controller
{
    /**
     * Lista todas as contas a receber
     */
    public function index()
    {
        return AccountReceivable::orderBy('created_at', 'desc')->get();
    }

    /**
     * Exibe uma conta a receber específica
     */
    public function show(AccountReceivable $accountReceivable)
    {
        return response()->json($accountReceivable);
    }

    /**
     * Cria uma nova conta a receber
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $accountReceivable = AccountReceivable::create($request->only([
            'name',
            'description',
        ]));

        return response()->json([
            'message' => 'Conta a receber criada com sucesso',
            'data' => $accountReceivable
        ], 201);
    }

    /**
     * Atualiza uma conta a receber
     */
    public function update(Request $request, AccountReceivable $accountReceivable)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $accountReceivable->update($request->only([
            'name',
            'description',
        ]));

        return response()->json([
            'message' => 'Conta a receber atualizada com sucesso',
            'data' => $accountReceivable
        ]);
    }

    /**
     * Remove uma conta a receber
     */
    public function destroy(AccountReceivable $accountReceivable)
    {
        $accountReceivable->delete();

        return response()->json([
            'message' => 'Conta a receber removida com sucesso'
        ]);
    }
}

