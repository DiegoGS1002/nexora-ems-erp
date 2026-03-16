<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClientController extends Controller
{
    public function index()
    {
        $clients = Client::all(); // ou paginate(10)

        return view('cadastro.clientes.index', compact('clients'));
    }

    public function create(){
        return view('cadastro.clientes.create');
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

        Client::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'social_name' => $request->social_name,
            'taxNumber' => $request->taxNumber,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
        ]);

        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente salvo com sucesso!');
    }

    public function update(Request $request, Client $client){
           $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email',
            'phone-number' => 'required',
            'social_name' => 'nullable|string|max:255',
            'address' => 'required',
            'taxNumber' => 'required|unique:clients,taxNumber',
        ]);

        Client::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'email' => $request->email,
            'phone-number' => $request->phone_number,
            'social_name' => $request->social_name,
            'address' => $request->address,
            'taxNumber' => $request->taxNumber,
        ]);
        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente atualizado com sucesso!');
    }

    public function destroy(Client $client){
        $client->delete();

        return redirect()
            ->route('clients.index')
            ->with('success', 'Cliente deletado com sucesso!');
    }

    /**
     * Show printable list of clients
     */
    public function print()
    {
        $clients = Client::all();
        return view('cadastro.clientes.print', compact('clients'));
    }
}
