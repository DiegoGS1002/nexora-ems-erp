<?php

namespace App\Jobs;

use App\Enums\FiscalNoteStatus;
use App\Models\FiscalNote;
use App\Services\NFeService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class TransmitFiscalNote implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    public int $tries = 5;

    public array $backoff = [60, 300, 900, 1800];

    public function __construct(public int $noteId) {}

    public function uniqueId(): string
    {
        return (string) $this->noteId;
    }

    public function handle(NFeService $service): void
    {
        $note = FiscalNote::findOrFail($this->noteId);
        if ($note->status === FiscalNoteStatus::Draft) {
            $service->transmitir($note);
        } elseif ($note->status === FiscalNoteStatus::Sent && $note->receipt_number) {
            $service->consultarRecibo($note);
        }
    }
}
