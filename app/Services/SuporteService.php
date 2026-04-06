<?php

namespace App\Services;

use App\Enums\StatusTicketSuporte;
use App\Livewire\Forms\NovoTicketForm;
use App\Models\MensagemSuporte;
use App\Models\TicketSuporte;

class SuporteService
{
    public function criarTicket(int $userId, NovoTicketForm $form): TicketSuporte
    {
        $ticket = TicketSuporte::create([
            'user_id'    => $userId,
            'assunto'    => $form->assunto,
            'prioridade' => $form->prioridade,
            'categoria'  => $form->categoria ?: null,
            'status'     => StatusTicketSuporte::Aberto->value,
        ]);

        MensagemSuporte::create([
            'ticket_id'  => $ticket->id,
            'user_id'    => $userId,
            'conteudo'   => $form->mensagem,
            'is_suporte' => false,
        ]);

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

        if ($isAdmin && $ticket->status === StatusTicketSuporte::Aberto) {
            $ticket->update(['status' => StatusTicketSuporte::EmAndamento->value]);
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

