<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FiscalNote;
use App\Services\DanfeService;
use App\Services\NFeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NFeController extends Controller
{
    public function __construct(
        protected NFeService $nfeService,
        protected DanfeService $danfeService
    ) {}

    /**
     * Transmite NF-e para SEFAZ
     */
    public function transmitir(Request $request, FiscalNote $note): JsonResponse
    {
        try {
            $result = $this->nfeService->transmitir($note, sincrono: true);

            return response()->json([
                'success' => $result['authorized'],
                'message' => $result['message'],
                'data'    => $result,
            ], $result['authorized'] ? 200 : 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Cancela NF-e
     */
    public function cancelar(Request $request, FiscalNote $note): JsonResponse
    {
        $request->validate([
            'justificativa' => 'required|string|min:15|max:255',
        ]);

        try {
            $result = $this->nfeService->cancelar($note, $request->justificativa);

            return response()->json([
                'success' => $result['canceled'],
                'message' => $result['message'],
                'data'    => $result,
            ], $result['canceled'] ? 200 : 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Consulta status do serviço SEFAZ
     */
    public function status(): JsonResponse
    {
        try {
            $result = $this->nfeService->consultarStatus();

            return response()->json([
                'success' => $result['online'],
                'message' => $result['message'],
                'data'    => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Consulta NF-e pela chave
     */
    public function consultar(Request $request): JsonResponse
    {
        $request->validate([
            'chave' => 'required|string|size:44',
        ]);

        try {
            $result = $this->nfeService->consultarChave($request->chave);

            return response()->json([
                'success' => $result['found'],
                'message' => $result['message'],
                'data'    => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download DANFE em PDF
     */
    public function danfe(FiscalNote $note)
    {
        try {
            return $this->danfeService->download($note);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Visualizar DANFE inline
     */
    public function visualizarDanfe(FiscalNote $note)
    {
        try {
            $pdf = $this->danfeService->getContent($note);

            return response($pdf, 200)
                ->header('Content-Type', 'application/pdf')
                ->header('Content-Disposition', 'inline; filename="DANFE_' . $note->invoice_number . '.pdf"');
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download XML da NF-e
     */
    public function xml(FiscalNote $note)
    {
        if (!$note->xml_path) {
            return response()->json([
                'success' => false,
                'message' => 'XML não disponível para esta nota.',
            ], 404);
        }

        $disk = config('nfe.xml_disk');

        return \Storage::disk($disk)->download(
            $note->xml_path,
            'NFe_' . $note->invoice_number . '.xml'
        );
    }
}

