<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AccountsReceivableController extends Controller
{
    public function index()
    {
        $accountsReceivable = AccountsReceivable::all(); // ou paginate(10)

        return view('cadastro.accounts_receivable.index', compact('accountsReceivable'));
    }

    public function create(){
        return view('cadastro.accounts_receivable.create');
    }

   public function store(Request $request)
    {
        $request->validate([

        ]);

        AccountsReceivable::create([
            'name' => $request->name,
            'value' => $request->value,
            'due_date' => $request->due_date,
        ]);

        return redirect()
            ->route('accounts_receivable.index')
            ->with('success', 'Conta a receber salva com sucesso!');
    }
}
