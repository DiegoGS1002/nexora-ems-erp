<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Company;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsersController extends Controller
{
    public function index(Request $request)
    {
        $query = User::orderBy('name');

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('perfil')) {
            $query->where('is_admin', $request->input('perfil') === 'admin');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'ativo');
        }

        $users = $query->paginate(15)->withQueryString();

        $stats = [
            'total'    => User::count(),
            'admins'   => User::where('is_admin', true)->count(),
            'inativos' => User::where('is_active', false)->count(),
            'licencas' => User::where('has_license', true)->count(),
        ];

        return view('admin.users.index', compact('users', 'stats'));
    }

    public function create()
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        return view('admin.users.create', compact('companies'));
    }

    public function store(StoreUserRequest $request)
    {
        $validated = $request->validated();

        User::create([
            'name'        => $validated['name'],
            'email'       => $validated['email'],
            'password'    => Hash::make($validated['password']),
            'is_admin'    => (bool) $validated['is_admin'],
            'is_active'   => (bool) $validated['is_active'],
            'has_license' => (bool) $validated['has_license'],
            'modules'     => $validated['modules'] ?? [],
            'company_id'  => $validated['company_id'] ?? null,
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuário salvo com sucesso!');
    }

    public function edit(User $user)
    {
        $companies = Company::where('is_active', true)->orderBy('name')->get();
        return view('admin.users.edit', compact('user', 'companies'));
    }

    public function update(UpdateUserRequest $request, User $user)
    {
        $validated = $request->validated();

        $user->name        = $validated['name'];
        $user->email       = $validated['email'];
        $user->is_admin    = (bool) $validated['is_admin'];
        $user->is_active   = (bool) $validated['is_active'];
        $user->has_license = (bool) $validated['has_license'];
        $user->modules     = $validated['modules'] ?? [];
        $user->company_id  = $validated['company_id'] ?? null;

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuário atualizado com sucesso!');
    }

    public function destroy(User $user)
    {
        if ($user->id === Auth::id()) {
            return redirect()
                ->route('users.index')
                ->with('error', 'Você não pode excluir a própria conta.');
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'Usuário excluído com sucesso!');
    }
}
