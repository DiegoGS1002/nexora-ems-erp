<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreStockMovementRequest;
use App\Http\Requests\Api\UpdateStockMovementRequest;
use App\Models\Entrance;
use Illuminate\Http\Request;

class StockMovementApiController extends Controller
{
    /**
     * Lista todas as movimentações de estoque (entradas)
     */
    public function index()
    {
        return Entrance::orderBy('created_at', 'desc')->get();
    }

    /**
     * Exibe uma movimentação específica
     */
    public function show(Entrance $stockMovement)
    {
        return response()->json($stockMovement);
    }

    /**
     * Cria uma nova movimentação de estoque
     */
    public function store(StoreStockMovementRequest $request)
    {
        $stockMovement = Entrance::create($request->validated());

        return response()->json([
            'message' => 'Movimentação de estoque criada com sucesso',
            'data' => $stockMovement
        ], 201);
    }

    /**
     * Atualiza uma movimentação de estoque
     */
    public function update(UpdateStockMovementRequest $request, Entrance $stockMovement)
    {
        $stockMovement->update($request->validated());

        return response()->json([
            'message' => 'Movimentação de estoque atualizada com sucesso',
            'data' => $stockMovement
        ]);
    }

    /**
     * Remove uma movimentação de estoque
     */
    public function destroy(Entrance $stockMovement)
    {
        $stockMovement->delete();

        return response()->json([
            'message' => 'Movimentação de estoque removida com sucesso'
        ]);
    }
}

