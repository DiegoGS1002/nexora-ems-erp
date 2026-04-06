<?php

namespace App\Http\Controllers;

use App\Services\BrasilAPIService;
use Illuminate\Http\JsonResponse;

class ExternalApiProxyController extends Controller
{
    public function __construct(
        protected BrasilAPIService $brasilApiService
    ) {}

    public function getCnpj(string $cnpj): JsonResponse
    {
        $dados = $this->brasilApiService->consultarCnpj($cnpj);

        if (!$dados) {
            return response()->json(['error' => 'CNPJ não encontrado ou instabilidade no serviço'], 404);
        }

        return response()->json($dados);
    }

    public function getCep(string $cep): JsonResponse
    {
        $dados = $this->brasilApiService->consultarCep($cep);

        if (!$dados) {
            return response()->json(['error' => 'CEP não encontrado'], 404);
        }

        return response()->json($dados);
    }
}

