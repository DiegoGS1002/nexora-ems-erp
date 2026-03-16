<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Users;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    public function index()
    {
        $users = Users::all(); // ou paginate(10)

        return view('cadastro.usuarios.index', compact('users'));
    }

    public function create(){
        return view('cadastro.usuarios.create');
    }

   public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'identification_number' => 'required|string|max:20|unique:employees',
            'role' => 'required|string|max:50',
            'email' => 'required|email|unique:employees,email',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
        ]);

        Users::create([
            'name' => $request->name,
            'identification_number' => $request->identification_number,
            'role' => $request->role,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
            'password' => bcrypt(Str::random(10)), // Gerar senha aleatória
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuário salvo com sucesso!');
    }
}
