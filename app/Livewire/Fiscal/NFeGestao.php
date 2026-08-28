<?php

namespace App\Livewire\Fiscal;

use App\Enums\FiscalNoteEventType;
use App\Enums\NFeTransmissionState;
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
            'events' => fn ($q) => $q->latest(),
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

            if ($result['state'] === NFeTransmissionState::Authorized) {
                // Registra evento
                FiscalNoteEvent::create([
                    'fiscal_note_id' => $this->fiscalNote->id,
                    'event_type' => FiscalNoteEventType::Authorized,
                    'description' => 'NF-e autorizada pela SEFAZ',
                    'protocol' => $result['protocol'],
                    'created_by' => auth()->id(),
                ]);

                session()->flash('success', '✅ NF-e autorizada! Protocolo: '.$result['protocol']);
            } elseif (in_array($result['state'], [
                NFeTransmissionState::Rejected,
                NFeTransmissionState::Denied,
            ], true)) {
                $eventType = $result['state'] === NFeTransmissionState::Denied
                    ? FiscalNoteEventType::Denied
                    : FiscalNoteEventType::Rejected;
                $label = $result['state'] === NFeTransmissionState::Denied ? 'Denegação' : 'Rejeição';

                FiscalNoteEvent::create([
                    'fiscal_note_id' => $this->fiscalNote->id,
                    'event_type' => $eventType,
                    'description' => $label.': ['.$result['code'].'] '.$result['message'],
                    'response_data' => $result,
                    'created_by' => auth()->id(),
                ]);

                session()->flash('error', '❌ '.$label.': ['.$result['code'].'] '.$result['message']);
            } elseif (in_array($result['state'], [
                NFeTransmissionState::Processing,
                NFeTransmissionState::Indeterminate,
            ], true)) {
                session()->flash('success', 'NF-e recebida pela SEFAZ e aguardando processamento.');
            } else {
                session()->flash('error', 'Falha no envio: ['.$result['code'].'] '.$result['message']);
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erro ao transmitir: '.$e->getMessage());
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
                session()->flash('success', '✅ NF-e cancelada com sucesso!');
                $this->closeCancelModal();
                $this->loadNote();

                return;
            } else {
                session()->flash('error', '❌ Erro ao cancelar: '.($result['message'] ?? 'Erro desconhecido'));
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erro: '.$e->getMessage());
        }

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
                session()->flash('success', '✅ Carta de Correção enviada!');
                $this->closeCceModal();
                $this->loadNote();

                return;
            } else {
                session()->flash('error', '❌ Erro: '.($result['message'] ?? 'Erro desconhecido'));
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Erro: '.$e->getMessage());
        }
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
