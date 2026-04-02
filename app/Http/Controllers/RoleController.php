<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        return view('system.desenvolvimento', [
            'title'       => 'Funções / Cargos',
            'description' => 'Cadastro de cargos e funções dos colaboradores',
            'color'       => '#8B5CF6',
            'icon'        => '<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="12" cy="8" r="5"/><path d="M20 21a8 8 0 1 0-16 0"/></svg>',
            'moduleSlug'  => 'cadastro',
            'moduleName'  => 'Cadastro',
        ]);
    }

    public function create()  { return $this->index(); }
    public function store(Request $request) { return redirect()->route('role.index'); }
    public function show($id) { return $this->index(); }
    public function edit($id) { return $this->index(); }
    public function update(Request $request, $id) { return redirect()->route('role.index'); }
    public function destroy($id) { return redirect()->route('role.index'); }
}
