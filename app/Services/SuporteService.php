<?php

namespace App\Services;

use App\Ai\AgenteService;
use App\Enums\StatusTicketSuporte;
use App\Livewire\Forms\NovoTicketForm;
use App\Models\MensagemSuporte;
use App\Models\TicketSuporte;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class SuporteService
{
    public function __construct(private AgenteService $agenteService) {}

    public function criarTicket(int $userId, NovoTicketForm $form): TicketSuporte
    {
        $ticket = TicketSuporte::create([
            'user_id'    => $userId,
            'assunto'    => $form->assunto,
            'prioridade' => $form->prioridade,
            'categoria'  => $form->categoria ?: null,
            'status'     => StatusTicketSuporte::EmAndamento->value,
        ]);

        MensagemSuporte::create([
            'ticket_id'  => $ticket->id,
            'user_id'    => $userId,
            'conteudo'   => $form->mensagem,
            'is_suporte' => false,
        ]);

        $this->responderComIA($ticket);

        return $ticket;
    }

    public function enviarMensagem(TicketSuporte $ticket, int $userId, string $conteudo, bool $isAdmin): void
    {
        MensagemSuporte::create([
            'ticket_id'  => $ticket->id,
            'user_id'    => $userId,
            'conteudo'   => $conteudo,
            'is_suporte' => $isAdmin,
        ]);

        if (! $isAdmin) {
            $this->responderComIA($ticket);
        }
    }

    private function responderComIA(TicketSuporte $ticket): void
    {
        $user = User::find($ticket->user_id);
        if (! $user) {
            Log::warning('SuporteService: user not found for ticket', ['ticket_id' => $ticket->id]);
            return;
        }

        $mensagens = MensagemSuporte::where('ticket_id', $ticket->id)->oldest()->get();

        $adminUser = User::where('is_admin', true)->first();
        if (! $adminUser) {
            Log::warning('SuporteService: no admin user found');
            return;
        }

        $resposta = $this->agenteService->gerarResposta($user, $ticket, $mensagens);

        if ($resposta) {
            MensagemSuporte::create([
                'ticket_id'  => $ticket->id,
                'user_id'    => $adminUser->id,
                'conteudo'   => $resposta,
                'is_suporte' => true,
                'is_ia'      => true,
            ]);
        }
    }

    public function atualizarStatus(TicketSuporte $ticket, string $status): void
    {
        $novoStatus = StatusTicketSuporte::from($status);

        $ticket->update([
            'status'     => $novoStatus->value,
            'fechado_em' => in_array($novoStatus, [StatusTicketSuporte::Resolvido, StatusTicketSuporte::Fechado])
                ? now()
                : null,
        ]);
    }

    public function marcarMensagensComoLidas(string $ticketId, bool $isAdmin): void
    {
        MensagemSuporte::where('ticket_id', $ticketId)
            ->where('is_suporte', ! $isAdmin)
            ->where('lida', false)
            ->update(['lida' => true]);
    }
}

