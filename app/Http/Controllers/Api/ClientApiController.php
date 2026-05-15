<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreClientRequest;
use App\Http\Requests\Api\UpdateClientRequest;
use App\Models\Client;
use Illuminate\Http\Request;

class ClientApiController extends Controller
{
    public function index(Request $request)
    {
        $query = Client::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('social_name', 'like', "%{$search}%")
                  ->orWhere('taxNumber', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tipo_pessoa')) {
            $query->where('tipo_pessoa', $request->tipo_pessoa);
        }

        if ($request->filled('situation')) {
            $query->where('situation', $request->situation);
        }

        $perPage = $request->get('per_page', 50);

        return $request->boolean('paginate', true)
            ? $query->paginate($perPage)
            : $query->get();
    }

    public function show(Client $client)
    {
        return response()->json($client->load('priceTable'));
    }

    public function store(StoreClientRequest $request)
    {
        $validated = $request->validated();

        $client = Client::create($validated);

        return response()->json([
            'message' => 'Cliente criado com sucesso',
            'data'    => $client->load('priceTable'),
        ], 201);
    }

    public function update(UpdateClientRequest $request, Client $client)
    {
        $validated = $request->validated();

        $client->update($validated);

        return response()->json([
            'message' => 'Cliente atualizado com sucesso',
            'data'    => $client->fresh()->load('priceTable'),
        ]);
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return response()->json([
            'message' => 'Cliente deletado com sucesso',
        ]);
    }
}
