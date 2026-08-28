<?php

namespace App\Services;

use App\Models\FiscalNote;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class FiscalDocumentDeliveryService
{
    public function __construct(protected DanfeService $danfeService) {}

    public function send(FiscalNote $note, string $email): void
    {
        if (! filter_var($email, FILTER_VALIDATE_EMAIL) || ! $note->xml_path) {
            throw new \InvalidArgumentException('E-mail ou XML autorizado inválido.');
        }

        $xml = Storage::disk(config('nfe.xml_disk'))->get($note->xml_path);
        $pdf = $this->danfeService->getContent($note);
        Mail::raw("Seguem XML e DANFE da NF-e {$note->invoice_number}.", function ($message) use ($note, $email, $xml, $pdf): void {
            $message->to($email)
                ->subject("NF-e {$note->invoice_number}")
                ->attachData($xml, "NFe-{$note->invoice_number}.xml", ['mime' => 'application/xml'])
                ->attachData($pdf, "DANFE-{$note->invoice_number}.pdf", ['mime' => 'application/pdf']);
        });
    }
}
