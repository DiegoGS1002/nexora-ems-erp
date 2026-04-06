<?php

namespace App\Livewire\Suporte;

use App\Enums\PrioridadeTicketSuporte;
use App\Enums\StatusTicketSuporte;
use App\Livewire\Forms\NovoTicketForm;
use App\Models\MensagemSuporte;
use App\Models\TicketSuporte;
use App\Services\SuporteService;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Chat de Suporte')]
class Chat extends Component
{
    public NovoTicketForm $novoTicket;

    public ?string $ticketSelecionadoId = null;

    public string $busca = '';
    public string $filtroStatus = '';

    public bool $showNovoTicket = false;

    #[Validate('required|min:2')]
    public string $novaMensagemTexto = '';

    protected SuporteService $suporteService;

    public function boot(SuporteService $suporteService): void
    {
        $this->suporteService = $suporteService;
    }

    public function mount(): void
    {
        $this->novoTicket->prioridade = PrioridadeTicketSuporte::Media->value;

        $primeiro = $this->tickets->first();
        if ($primeiro) {
            $this->ticketSelecionadoId = $primeiro->id;
            $this->suporteService->marcarMensagensComoLidas($primeiro->id, auth()->user()->is_admin);
        }
    }

    #[Computed]
    public function tickets()
    {
        return TicketSuporte::with(['user', 'mensagens' => fn ($q) => $q->latest()->limit(1)])
            ->when(! auth()->user()->is_admin, fn ($q) => $q->where('user_id', auth()->id()))
            ->when($this->busca !== '', fn ($q) => $q->where('assunto', 'like', "%{$this->busca}%"))
            ->when($this->filtroStatus !== '', fn ($q) => $q->where('status', $this->filtroStatus))
            ->latest()
            ->get();
    }

    #[Computed]
    public function ticketAtivo(): ?TicketSuporte
    {
        if (! $this->ticketSelecionadoId) {
            return null;
        }

        return TicketSuporte::with('user')->find($this->ticketSelecionadoId);
    }

    #[Computed]
    public function mensagensAtivas()
    {
        if (! $this->ticketSelecionadoId) {
            return collect();
        }

        return MensagemSuporte::with('user')
            ->where('ticket_id', $this->ticketSelecionadoId)
            ->oldest()
            ->get();
    }

    #[Computed]
    public function statusOpcoes(): array
    {
        return StatusTicketSuporte::cases();
    }

    #[Computed]
    public function prioridadeOpcoes(): array
    {
        return PrioridadeTicketSuporte::cases();
    }

    public function updatedFiltroStatus(): void
    {
        $this->resetSelecaoSeNecessario();
    }

    public function updatedBusca(): void
    {
        $this->resetSelecaoSeNecessario();
    }

    private function resetSelecaoSeNecessario(): void
    {
        if (! $this->ticketSelecionadoId) {
            return;
        }

        if (! in_array($this->ticketSelecionadoId, $this->tickets->pluck('id')->all(), true)) {
            $this->ticketSelecionadoId = null;
            unset($this->ticketAtivo, $this->mensagensAtivas);
        }
    }

    public function selecionarTicket(string $id): void
    {
        $this->ticketSelecionadoId = $id;
        $this->novaMensagemTexto = '';
        $this->suporteService->marcarMensagensComoLidas($id, auth()->user()->is_admin);
        unset($this->mensagensAtivas, $this->ticketAtivo);
    }

    public function enviarMensagem(): void
    {
        $this->validateOnly('novaMensagemTexto');

        if (! $this->ticketSelecionadoId) {
            return;
        }

        $ticket = TicketSuporte::find($this->ticketSelecionadoId);

        if (! $ticket || ! $ticket->isAberto()) {
            return;
        }

        $this->suporteService->enviarMensagem($ticket, auth()->id(), $this->novaMensagemTexto, auth()->user()->is_admin);

        $this->novaMensagemTexto = '';
        unset($this->mensagensAtivas, $this->tickets);

        $this->dispatch('mensagem-enviada');
    }

    public function abrirNovoTicket(): void
    {
        $this->showNovoTicket = true;
        $this->novoTicket->resetComPadrao();
    }

    public function fecharNovoTicket(): void
    {
        $this->showNovoTicket = false;
        $this->novoTicket->resetValidation();
    }

    public function criarTicket(): void
    {
        $this->novoTicket->validate();

        $ticket = $this->suporteService->criarTicket(auth()->id(), $this->novoTicket);

        $this->showNovoTicket = false;
        $this->ticketSelecionadoId = $ticket->id;
        $this->novoTicket->resetComPadrao();
        unset($this->tickets, $this->mensagensAtivas, $this->ticketAtivo);

        session()->flash('success', 'Ticket de suporte criado com sucesso!');
    }

    public function atualizarStatus(string $status): void
    {
        if (! auth()->user()->is_admin || ! $this->ticketSelecionadoId) {
            return;
        }

        $ticket = TicketSuporte::find($this->ticketSelecionadoId);

        if (! $ticket) {
            return;
        }

        $this->suporteService->atualizarStatus($ticket, $status);

        unset($this->ticketAtivo, $this->tickets);
    }

    public function atualizarChat(): void
    {
        unset($this->mensagensAtivas, $this->tickets);

        if ($this->ticketSelecionadoId) {
            $this->suporteService->marcarMensagensComoLidas($this->ticketSelecionadoId, auth()->user()->is_admin);
        }
    }

    public function render()
    {
        return view('livewire.suporte.chat')
            ->title('Chat de Suporte');
    }
}

