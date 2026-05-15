<?php

namespace App\Livewire\Fiscal;

use App\Enums\FiscalNoteStatus;
use App\Enums\SalesOrderStatus;
use App\Models\FiscalNote;
use App\Models\FiscalNoteEvent;
use App\Services\NFeService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Gestão de NF-e')]
class NFeGestao extends Component
{
    public int $noteId;
    public ?FiscalNote $fiscalNote = null;

    /* ─── Estados ─── */
    public bool $transmitting = false;
    public bool $showEventsModal = false;
    public bool $showCancelModal = false;
    public bool $showCceModal = false;

    /* ─── Formulários ─── */
    public string $cancelReason = '';
    public string $cceText = '';

    /* ─────────────────────────────────────────
       LIFECYCLE
    ───────────────────────────────────────── */
    public function mount(int $noteId): void
    {
        $this->noteId = $noteId;
        $this->loadNote();
    }

    public function loadNote(): void
    {
        $this->fiscalNote = FiscalNote::with([
            'client',
            'salesOrder',
            'items',
            'events' => fn($q) => $q->latest()
        ])->findOrFail($this->noteId);
    }

    /* ─────────────────────────────────────────
       TRANSMITIR NF-e
    ───────────────────────────────────────── */
    public function transmitir(): void
    {
        $this->transmitting = true;

        try {
            $nfeService = app(NFeService::class);
            $result = $nfeService->transmitir($this->fiscalNote->fresh(['items', 'client']), sincrono: true);

            if ($result['authorized']) {
                // Atualiza NF-e
                $this->fiscalNote->update([
                    'status'       => FiscalNoteStatus::Authorized->value,
                    'access_key'   => $result['access_key'],
                    'protocol'     => $result['protocol'],
                    'authorized_at'=> now(),
                ]);

                // Atualiza pedido
                if ($this->fiscalNote->salesOrder) {
                    $this->fiscalNote->salesOrder->update([
                        'status'        => SalesOrderStatus::NfTransmitida->value,
                        'nfe_key'       => $result['access_key'],
                        'nfe_protocol'  => $result['protocol'],
                        'nfe_status'    => 'authorized',
                        'nfe_issued_at' => now(),
                    ]);
                }

                // Registra evento
                FiscalNoteEvent::create([
                    'fiscal_note_id' => $this->fiscalNote->id,
                    'event_type'     => 'authorized',
                    'description'    => 'NF-e autorizada pela SEFAZ',
                    'protocol'       => $result['protocol'],
                    'created_by'     => auth()->id(),
                ]);

                session()->flash('success', '✅ NF-e autorizada! Protocolo: ' . $result['protocol']);
            } else {
                $this->fiscalNote->update([
                    'status' => FiscalNoteStatus::Rejected->value,
                ]);

                // Registra evento de rejeição
                FiscalNoteEvent::create([
                    'fiscal_note_id' => $this->fiscalNote->id,
                    'event_type'     => 'rejected',
                    'description'    => 'Rejeição: [' . $result['code'] . '] ' . $result['message'],
                    'response_data'  => json_encode($result),
                    'created_by'     => auth()->id(),
                ]);

                session()->flash('error', '❌ NF-e rejeitada: [' . $result['code'] . '] ' . $result['message']);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao transmitir: ' . $e->getMessage());
        }

        $this->transmitting = false;
        $this->loadNote();
    }

    /* ─────────────────────────────────────────
       CANCELAR NF-e
    ───────────────────────────────────────── */
    public function openCancelModal(): void
    {
        $this->showCancelModal = true;
    }

    public function closeCancelModal(): void
    {
        $this->showCancelModal = false;
        $this->cancelReason = '';
        $this->resetErrorBag();
    }

    public function cancelar(): void
    {
        $this->validate([
            'cancelReason' => 'required|min:15|max:255',
        ], [
            'cancelReason.required' => 'Informe o motivo do cancelamento.',
            'cancelReason.min' => 'O motivo deve ter no mínimo 15 caracteres.',
        ]);

        try {
            $nfeService = app(NFeService::class);
            $result = $nfeService->cancelar($this->fiscalNote, $this->cancelReason);

            if ($result['cancelled']) {
                $this->fiscalNote->update([
                    'status'       => FiscalNoteStatus::Cancelled->value,
                    'cancelled_at' => now(),
                ]);

                // Registra evento
                FiscalNoteEvent::create([
                    'fiscal_note_id' => $this->fiscalNote->id,
                    'event_type'     => 'cancelled',
                    'description'    => 'NF-e cancelada: ' . $this->cancelReason,
                    'protocol'       => $result['protocol'] ?? null,
                    'created_by'     => auth()->id(),
                ]);

                session()->flash('success', '✅ NF-e cancelada com sucesso!');
            } else {
                session()->flash('error', '❌ Erro ao cancelar: ' . ($result['message'] ?? 'Erro desconhecido'));
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erro: ' . $e->getMessage());
        }

        $this->closeCancelModal();
        $this->loadNote();
    }

    /* ─────────────────────────────────────────
       CARTA DE CORREÇÃO (CC-e)
    ───────────────────────────────────────── */
    public function openCceModal(): void
    {
        $this->showCceModal = true;
    }

    public function closeCceModal(): void
    {
        $this->showCceModal = false;
        $this->cceText = '';
        $this->resetErrorBag();
    }

    public function enviarCce(): void
    {
        $this->validate([
            'cceText' => 'required|min:15|max:1000',
        ], [
            'cceText.required' => 'Informe a correção.',
            'cceText.min' => 'A correção deve ter no mínimo 15 caracteres.',
        ]);

        try {
            $nfeService = app(NFeService::class);
            $result = $nfeService->cartaCorrecao($this->fiscalNote, $this->cceText);

            if ($result['success']) {
                // Registra evento
                FiscalNoteEvent::create([
                    'fiscal_note_id' => $this->fiscalNote->id,
                    'event_type'     => 'cce',
                    'description'    => 'CC-e enviada: ' . $this->cceText,
                    'protocol'       => $result['protocol'] ?? null,
                    'created_by'     => auth()->id(),
                ]);

                session()->flash('success', '✅ Carta de Correção enviada!');
            } else {
                session()->flash('error', '❌ Erro: ' . ($result['message'] ?? 'Erro desconhecido'));
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erro: ' . $e->getMessage());
        }

        $this->closeCceModal();
        $this->loadNote();
    }

    /* ─────────────────────────────────────────
       EVENTOS
    ───────────────────────────────────────── */
    public function openEventsModal(): void
    {
        $this->showEventsModal = true;
    }

    public function closeEventsModal(): void
    {
        $this->showEventsModal = false;
    }

    /* ─────────────────────────────────────────
       RENDER
    ───────────────────────────────────────── */
    public function render(): View
    {
        return view('livewire.fiscal.nfe-gestao');
    }
}

