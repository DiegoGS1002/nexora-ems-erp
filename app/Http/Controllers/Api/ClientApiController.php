<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_pessoa'              => ['required', Rule::in(['PF', 'PJ'])],
            'name'                     => 'required|string|max:255',
            'social_name'              => 'nullable|string|max:255',
            'taxNumber'                => 'required|unique:clients,taxNumber',
            'inscricao_estadual'       => 'nullable|string|max:50',
            'email'                    => 'required|email|unique:clients,email',
            'phone_number'             => 'required|string|max:20',
            'address'                  => 'nullable|string|max:500',
            'address_zip_code'         => 'nullable|string|max:10',
            'address_street'           => 'nullable|string|max:255',
            'address_number'           => 'nullable|string|max:20',
            'address_complement'       => 'nullable|string|max:100',
            'address_district'         => 'nullable|string|max:100',
            'address_city'             => 'nullable|string|max:100',
            'address_state'            => 'nullable|string|max:2',
            'credit_limit'             => 'nullable|numeric|min:0',
            'payment_condition_default'=> 'nullable|string|max:100',
            'situation'                => 'nullable|string|in:active,inactive,defaulter',
            'price_table_id'           => 'nullable|exists:price_tables,id',
            'discount_limit'           => 'nullable|numeric|min:0|max:100',
        ]);

        $client = Client::create($validated);

        return response()->json([
            'message' => 'Cliente criado com sucesso',
            'data'    => $client->load('priceTable'),
        ], 201);
    }

    public function update(Request $request, Client $client)
    {
        $validated = $request->validate([
            'tipo_pessoa'              => ['nullable', Rule::in(['PF', 'PJ'])],
            'name'                     => 'required|string|max:255',
            'social_name'              => 'nullable|string|max:255',
            'taxNumber'                => ['required', Rule::unique('clients', 'taxNumber')->ignore($client->id)],
            'inscricao_estadual'       => 'nullable|string|max:50',
            'email'                    => ['required', 'email', Rule::unique('clients', 'email')->ignore($client->id)],
            'phone_number'             => 'required|string|max:20',
            'address'                  => 'nullable|string|max:500',
            'address_zip_code'         => 'nullable|string|max:10',
            'address_street'           => 'nullable|string|max:255',
            'address_number'           => 'nullable|string|max:20',
            'address_complement'       => 'nullable|string|max:100',
            'address_district'         => 'nullable|string|max:100',
            'address_city'             => 'nullable|string|max:100',
            'address_state'            => 'nullable|string|max:2',
            'credit_limit'             => 'nullable|numeric|min:0',
            'payment_condition_default'=> 'nullable|string|max:100',
            'situation'                => 'nullable|string|in:active,inactive,defaulter',
            'price_table_id'           => 'nullable|exists:price_tables,id',
            'discount_limit'           => 'nullable|numeric|min:0|max:100',
        ]);

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
