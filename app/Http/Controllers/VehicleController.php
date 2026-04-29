<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvePdfLogo;
use App\Models\Vehicle;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VehicleController extends Controller
{
    use ResolvePdfLogo;

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
     * Exporta a listagem de veiculos em PDF.
     */
    public function print(Request $request)
    {
        $vehicles = Vehicle::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = (string) $request->string('search');

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('plate', 'like', "%{$search}%")
                        ->orWhere('brand', 'like', "%{$search}%")
                        ->orWhere('model', 'like', "%{$search}%")
                        ->orWhere('renavam', 'like', "%{$search}%")
                        ->orWhere('chassis', 'like', "%{$search}%")
                        ->orWhere('responsible_driver', 'like', "%{$search}%");
                });
            })
            ->orderBy('brand')
            ->orderBy('model')
            ->get();

        $pdf = Pdf::loadView('cadastro.veiculos.print', array_merge([
            'vehicles'   => $vehicles,
            'printedAt'  => now(),
            'search'     => $request->string('search')->toString(),
        ], $this->resolvePdfLogo()))->setPaper('a4', 'landscape');

        return $pdf->download('veiculos-' . now()->format('Y-m-d-His') . '.pdf');
    }
}
