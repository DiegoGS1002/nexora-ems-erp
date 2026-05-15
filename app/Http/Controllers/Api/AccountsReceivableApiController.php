<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreAccountReceivableRequest;
use App\Http\Requests\Api\UpdateAccountReceivableRequest;
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
    public function store(StoreAccountReceivableRequest $request)
    {
        $accountReceivable = AccountReceivable::create($request->validated());

        return response()->json([
            'message' => 'Conta a receber criada com sucesso',
            'data' => $accountReceivable
        ], 201);
    }

    /**
     * Atualiza uma conta a receber
     */
    public function update(UpdateAccountReceivableRequest $request, AccountReceivable $accountReceivable)
    {
        $accountReceivable->update($request->validated());

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

