<?php

namespace App\Console\Commands;

use App\Enums\StatusTicketSuporte;
use App\Models\MensagemSuporte;
use App\Models\TicketSuporte;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class FecharTicketsInativos extends Command
{
    protected $signature = 'tickets:fechar-inativos {--minutos=30 : Minutos de inatividade antes de fechar}';
    protected $description = 'Fecha automaticamente tickets de suporte sem atividade do usuário';

    public function handle()
    {
        $minutosInatividade = (int) $this->option('minutos');
        $tempoLimite = Carbon::now()->subMinutes($minutosInatividade);

        $this->info("Buscando tickets inativos há mais de {$minutosInatividade} minutos...");

        $ticketsAbertos = TicketSuporte::whereIn('status', [
            StatusTicketSuporte::Aberto->value,
            StatusTicketSuporte::EmAndamento->value,
        ])
        ->where('updated_at', '<=', $tempoLimite)
        ->with(['mensagens' => fn($q) => $q->latest()->limit(1)])
        ->get();

        $this->info("Encontrados {$ticketsAbertos->count()} tickets para analisar.");

        $fechados = 0;
        $adminUser = User::where('is_admin', true)->first();

        if (!$adminUser) {
            $this->error('Nenhum usuário administrador encontrado!');
            return 1;
        }

        foreach ($ticketsAbertos as $ticket) {
            $ultimaMensagem = $ticket->mensagens->first();

            if (!$ultimaMensagem) {
                continue;
            }

            // Se última mensagem foi do usuário e está inativa
            if (!$ultimaMensagem->is_suporte &&
                !$ultimaMensagem->is_ia &&
                $ultimaMensagem->created_at <= $tempoLimite) {

                MensagemSuporte::create([
                    'ticket_id'  => $ticket->id,
                    'user_id'    => $adminUser->id,
                    'conteudo'   => $this->getMensagemEncerramento(),
                    'is_suporte' => true,
                    'is_ia'      => true,
                ]);

                $ticket->update([
                    'status'     => StatusTicketSuporte::Fechado->value,
                    'fechado_em' => now(),
                ]);

                $this->line("✓ Ticket #{$ticket->id} ({$ticket->assunto}) fechado por inatividade.");
                $fechados++;
            }
            // Se última mensagem foi da IA/suporte, verificar se usuário respondeu
            elseif (($ultimaMensagem->is_suporte || $ultimaMensagem->is_ia) &&
                    $ultimaMensagem->created_at <= $tempoLimite) {

                $respostaUsuario = MensagemSuporte::where('ticket_id', $ticket->id)
                    ->where('is_suporte', false)
                    ->where('is_ia', false)
                    ->where('created_at', '>', $ultimaMensagem->created_at)
                    ->exists();

                if (!$respostaUsuario) {
                    MensagemSuporte::create([
                        'ticket_id'  => $ticket->id,
                        'user_id'    => $adminUser->id,
                        'conteudo'   => $this->getMensagemEncerramento(),
                        'is_suporte' => true,
                        'is_ia'      => true,
                    ]);

                    $ticket->update([
                        'status'     => StatusTicketSuporte::Fechado->value,
                        'fechado_em' => now(),
                    ]);

                    $this->line("✓ Ticket #{$ticket->id} ({$ticket->assunto}) fechado por inatividade (sem resposta do usuário).");
                    $fechados++;
                }
            }
        }

        if ($fechados > 0) {
            $this->info("\n✅ Total de tickets fechados: {$fechados}");
        } else {
            $this->info("\n✓ Nenhum ticket inativo encontrado.");
        }

        return 0;
    }

    private function getMensagemEncerramento(): string
    {
        return <<<MENSAGEM
🕒 **Ticket fechado automaticamente por inatividade**

Este ticket foi encerrado devido à falta de atividade nos últimos 30 minutos.

**Precisa de mais ajuda?**
- Você pode abrir um novo ticket a qualquer momento
- Para suporte urgente: **WhatsApp (32) 98450-2345**

Obrigado por utilizar o Nexora ERP! 🚀
MENSAGEM;
    }
}

