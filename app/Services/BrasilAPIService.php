<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BrasilAPIService
{
    protected string $baseUrl = 'https://brasilapi.com.br/api';

    public function consultarCnpj(string $cnpj): ?array
    {
        $cnpj = preg_replace('/\D/', '', $cnpj);

        try {
            $response = Http::timeout(10)->get("{$this->baseUrl}/cnpj/v1/{$cnpj}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error("Nexora BrasilAPI — erro ao consultar CNPJ: " . $e->getMessage());
            return null;
        }
    }

    public function consultarCep(string $cep): ?array
    {
        $cep = preg_replace('/\D/', '', $cep);

        try {
            $response = Http::timeout(5)->get("{$this->baseUrl}/cep/v2/{$cep}");

            if ($response->successful()) {
                return $response->json();
            }

            return null;
        } catch (\Exception $e) {
            Log::error("Nexora BrasilAPI — erro ao consultar CEP: " . $e->getMessage());
            return null;
        }
    }
}

