<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AccountsPayable;

class AccountsPayableController extends Controller
{
    public function index()
    {
        $accountsPayable = AccountsPayable::all(); // ou paginate(10)

        return view('cadastro.accounts_payable.index', compact('accountsPayable'));
    }

    public function create(){
        return view('cadastro.accounts_payable.create');
    }

   public function store(Request $request)
    {
        $request->validate([

        ]);

        AccountsPayable::create([
            'name' => $request->name,
            'value' => $request->value,
            'due_date' => $request->due_date,
        ]);

        return redirect()
            ->route('accounts_payable.index')
            ->with('success', 'Conta a pagar salva com sucesso!');
    }
}
