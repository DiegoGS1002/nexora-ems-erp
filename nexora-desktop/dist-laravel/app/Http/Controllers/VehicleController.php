<?php

namespace App\Http\Controllers;

use App\Models\vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::all(); // ou paginate(10)

        return view('cadastro.veiculos.index', compact('vehicles'));
    }

    public function create(){
        return view('cadastro.veiculos.create');
    }

   public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'plate' => 'required|string|max:255|unique:vehicles',
            'renavam' => 'required|string|max:255|unique:vehicles',
            'chassis' => 'required|string|max:255|unique:vehicles',
            'fuel_type' => 'required|string|max:255',
            'year' => 'required|string|max:255',
            'color' => 'required|string|max:255',
        ]);

        Vehicle::create([
            'id' => Str::uuid(),
            'name' => $request->name,
            'model' => $request->model,
            'brand' => $request->brand,
            'plate' => $request->plate,
            'renavam' => $request->renavam,
            'chassis' => $request->chassis,
            'fuel_type' => $request->fuel_type,
            'year' => $request->year,
            'color' => $request->color,

        ]);

        return redirect()
            ->route('vehicles.index')
            ->with('success', 'Veículo salvo com sucesso!');
    }

    public function update(Request $request, Vehicle $vehicle){
           $request->validate([
            'name' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'brand' => 'required|string|max:255',
            'plate' => 'required|string|max:255|unique:vehicles,plate,' . $vehicle->id,
            'renavam' => 'required|string|max:255|unique:vehicles,renavam,' . $vehicle->id,
            'chassis' => 'required|string|max:255|unique:vehicles,chassis,' . $vehicle->id,
            'fuel_type' => 'required|string|max:255',
            'year' => 'required|string|max:255',
            'color' => 'required|string|max:255',

        ]);

        // update the existing vehicle rather than creating a new one
        $vehicle->update([
            'name' => $request->name,
            'model' => $request->model,
            'brand' => $request->brand,
            'plate' => $request->plate,
            'renavam' => $request->renavam,
            'chassis' => $request->chassis,
            'fuel_type' => $request->fuel_type,
            'year' => $request->year,
            'color' => $request->color,
        ]);
        return redirect()
            ->route('vehicles.index')
            ->with('success', 'Veículo atualizado com sucesso!');
    }

    public function destroy(Vehicle $vehicle){
        $vehicle->delete();

        return redirect()
            ->route('vehicles.index')
            ->with('success', 'Veículo deletado com sucesso!');
    }

    /**
     * Show printable list of vehicles
     */
    public function print()
    {
        $vehicles = Vehicle::all();
        return view('cadastro.veiculos.print', compact('vehicles'));
    }
}
