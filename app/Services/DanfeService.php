<?php

namespace App\Services;

use App\Models\FiscalNote;
use Illuminate\Support\Facades\Storage;
use NFePHP\DA\NFe\Danfe;

class DanfeService
{
    /**
     * Gera DANFE em PDF a partir de uma nota fiscal
     */
    public function generate(FiscalNote $note, ?string $logoPath = null): string
    {
        if (!$note->xml_path) {
            throw new \InvalidArgumentException('Nota fiscal não possui XML gerado.');
        }

        $xmlDisk = config('nfe.xml_disk');
        $xmlContent = Storage::disk($xmlDisk)->get($note->xml_path);

        $danfe = new Danfe($xmlContent);

        // Logo (opcional)
        if ($logoPath && Storage::disk('public')->exists($logoPath)) {
            $logoContent = Storage::disk('public')->get($logoPath);
            $danfe->logoParameters($logoContent, 'C');
        }

        // Configurações
        $danfe->debugMode(false);
        $danfe->setDefaultFont('times');

        // Gera PDF
        $pdf = $danfe->render();

        // Salva
        $danfePath = $this->savePdf($note, $pdf);

        return $danfePath;
    }

    /**
     * Salva PDF do DANFE no storage
     */
    protected function savePdf(FiscalNote $note, string $pdfContent): string
    {
        $disk = config('nfe.xml_disk');
        $path = config('nfe.danfe_path') . '/danfe_' . $note->invoice_number . '.pdf';

        Storage::disk($disk)->put($path, $pdfContent);

        return $path;
    }

    /**
     * Retorna o PDF como download
     */
    public function download(FiscalNote $note): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $pdfPath = $this->generate($note);
        $disk = config('nfe.xml_disk');

        return Storage::disk($disk)->download($pdfPath, 'DANFE_' . $note->invoice_number . '.pdf');
    }

    /**
     * Retorna conteúdo do PDF como string (para exibição inline)
     */
    public function getContent(FiscalNote $note): string
    {
        if (!$note->xml_path) {
            throw new \InvalidArgumentException('Nota fiscal não possui XML gerado.');
        }

        $xmlDisk = config('nfe.xml_disk');
        $xmlContent = Storage::disk($xmlDisk)->get($note->xml_path);

        $danfe = new Danfe($xmlContent);
        $danfe->debugMode(false);

        return $danfe->render();
    }
}

