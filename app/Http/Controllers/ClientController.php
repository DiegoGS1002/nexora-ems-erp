<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\ResolvePdfLogo;
use App\Models\Client;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    use ResolvePdfLogo;

    /**
     * Exporta a listagem de clientes em PDF.
     */
    public function print(Request $request)
    {
        $clients = Client::query()
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = (string) $request->string('search');

                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('social_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('taxNumber', 'like', "%{$search}%")
                        ->orWhere('address_city', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get();

        $pdf = Pdf::loadView('cadastro.clientes.print', array_merge([
            'clients'    => $clients,
            'printedAt'  => now(),
            'search'     => $request->string('search')->toString(),
        ], $this->resolvePdfLogo()))->setPaper('a4', 'landscape');

        return $pdf->download('clientes-' . now()->format('Y-m-d-His') . '.pdf');
    }
}
