<?php

namespace App\Jobs;

use App\Enums\FiscalNoteStatus;
use App\Models\FiscalNote;
use App\Models\FiscalOperationLog;
use App\Services\NFeService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ReconcileFiscalNoteStatus implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $noteId) {}

    public function handle(NFeService $service): void
    {
        $note = FiscalNote::findOrFail($this->noteId);
        if (! $note->access_key) {
            return;
        }
        $result = $service->consultarChave($note->access_key);
        $status = match ($result['code']) {
            '100' => FiscalNoteStatus::Authorized,
            '101', '151' => FiscalNoteStatus::Cancelled,
            '110', '301', '302' => FiscalNoteStatus::Denied,
            default => null,
        };
        if ($status && $note->status !== $status) {
            FiscalOperationLog::create([
                'company_id' => $note->company_id,
                'fiscal_note_id' => $note->id,
                'operation' => 'status_reconciliation',
                'payload' => ['previous' => $note->status->value],
                'result' => ['current' => $status->value, 'sefaz_code' => $result['code']],
                'occurred_at' => now(),
            ]);
            $note->update(['status' => $status, 'sefaz_message' => $result['message']]);
        }
    }
}
