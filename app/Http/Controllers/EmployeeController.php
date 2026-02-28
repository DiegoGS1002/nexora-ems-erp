<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    public function index()
    {
        $employees = Employee::all(); // ou paginate(10)

        return view('cadastro.funcionarios.index', compact('employees'));
    }

    public function create(){
        return view('cadastro.funcionarios.create');
    }

   public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'identification_number' => 'required|string|max:11|unique:employees,identification_number',
            'role' => 'required|unique:employees,role',
            'email' => 'required|email|unique:employees,email',
            'phone_number' => 'required',
            'address' => 'required',
        ]);

        Employee::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'identification_number' => $request->identification_number,
            'role' => $request->role,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'address' => $request->address,
        ]);

        return redirect()
            ->route('employees.index')
            ->with('success', 'Funcionário salvo com sucesso!');
    }

    public function update(Request $request, Employee $employee){
           $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:employees,email',
            'phone-number' => 'required',
            'identification_number' => 'required|string|max:11|unique:employees,identification_number',
            'address' => 'required',
            'role' => 'required|unique:employees,role',
        ]);

        Employee::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'identification_number' => $request->identification_number,
            'role' => $request->role,
            'address' => $request->address,
            'taxNumber' => $request->taxNumber,
        ]);
        return redirect()
            ->route('employees.index')
            ->with('success', 'Funcionário atualizado com sucesso!');
    }

    public function destroy(Employee $employee){
        $employee->delete();

        return redirect()
            ->route('employees.index')
            ->with('success', 'Funcionário deletado com sucesso!');
    }

    /**
     * Show printable list of employees
     */
    public function print()
    {
        $employees = Employee::all();
        return view('cadastro.funcionarios.print', compact('employees'));
    }
}
