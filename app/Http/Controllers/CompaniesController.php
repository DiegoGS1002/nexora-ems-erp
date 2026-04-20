<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CompaniesController extends Controller
{
    public function index()
    {
        return view('system.desenvolvimento');
    }

    public function create()
    {
        return view('system.desenvolvimento');
    }

    public function store(Request $request)
    {
        return redirect()->route('companies.index');
    }

    public function show(string $id)
    {
        return view('system.desenvolvimento');
    }

    public function edit(string $id)
    {
        return view('system.desenvolvimento');
    }

    public function update(Request $request, string $id)
    {
        return redirect()->route('companies.index');
    }

    public function destroy(string $id)
    {
        return redirect()->route('companies.index');
    }
}
