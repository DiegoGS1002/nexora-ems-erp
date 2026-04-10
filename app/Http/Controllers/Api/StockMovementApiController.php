<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $stockMovement = Entrance::create($request->only([
            'name',
            'description',
        ]));

        return response()->json([
            'message' => 'Movimentação de estoque criada com sucesso',
            'data' => $stockMovement
        ], 201);
    }

    /**
     * Atualiza uma movimentação de estoque
     */
    public function update(Request $request, Entrance $stockMovement)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $stockMovement->update($request->only([
            'name',
            'description',
        ]));

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

