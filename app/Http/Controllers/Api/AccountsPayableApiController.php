<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreAccountPayableRequest;
use App\Http\Requests\Api\UpdateAccountPayableRequest;
use App\Models\AccountPayable;

class AccountsPayableApiController extends Controller
{
    /**
     * Lista todas as contas a pagar
     */
    public function index()
    {
        return AccountPayable::orderBy('due_date', 'asc')->get();
    }

    /**
     * Exibe uma conta a pagar específica
     */
    public function show(AccountPayable $accountPayable)
    {
        return response()->json($accountPayable);
    }

    /**
     * Cria uma nova conta a pagar
     */
    public function store(StoreAccountPayableRequest $request)
    {
        $accountPayable = AccountPayable::create($request->validated());

        return response()->json([
            'message' => 'Conta a pagar criada com sucesso',
            'data' => $accountPayable
        ], 201);
    }

    /**
     * Atualiza uma conta a pagar
     */
    public function update(UpdateAccountPayableRequest $request, AccountPayable $accountPayable)
    {
        $accountPayable->update($request->validated());

        return response()->json([
            'message' => 'Conta a pagar atualizada com sucesso',
            'data' => $accountPayable
        ]);
    }

    /**
     * Remove uma conta a pagar
     */
    public function destroy(AccountPayable $accountPayable)
    {
        $accountPayable->delete();

        return response()->json([
            'message' => 'Conta a pagar removida com sucesso'
        ]);
    }
}


