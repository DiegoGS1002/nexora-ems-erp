<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ClientApiController extends Controller
{
    public function index()
    {
        return Client::all();
    }

    public function show(Client $client)
    {
        return response()->json($client);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'social_name' => 'nullable|string|max:255',
            'taxNumber' => 'required|unique:clients,taxNumber',
            'email' => 'required|email|unique:clients,email',
            'phone_number' => 'required',
            'address' => 'required',
        ]);

        $client = Client::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'social_name' => $request->social_name,
            'taxNumber' => $request->taxNumber,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
        ]);

        return response()->json([
            'message' => 'Cliente criado com sucesso',
            'data' => $client
        ], 201);
    }

    public function update(Request $request, Client $client)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'social_name' => 'nullable|string|max:255',
            'taxNumber' => [
                'required',
                Rule::unique('clients', 'taxNumber')->ignore($client->id),
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('clients', 'email')->ignore($client->id),
            ],
            'phone_number' => 'required',
            'address' => 'required',
        ]);

        $client->update($request->all());

        return response()->json([
            'message' => 'Cliente atualizado com sucesso',
            'data' => $client
        ]);
    }

    public function destroy(Client $client)
    {
        $client->delete();

        return response()->json([
            'message' => 'Cliente deletado com sucesso'
        ]);
    }
}
