<?php

namespace App\Ai;

use App\Models\TicketSuporte;
use App\Models\User;
use Illuminate\Support\Collection;

class ContextBuilder
{
    private const SYSTEM_PROMPT = <<<PROMPT
Você é a **Nexora IA**, assistente de suporte técnico inteligente do **Nexora ERP** — sistema de gestão empresarial brasileiro.

## Sua missão
Ajudar usuários a resolverem dúvidas, problemas e consultas relacionadas ao sistema de forma rápida, precisa e amigável.

## Módulos do sistema
- **Administração**: empresas, usuários, permissões, logs, configurações
- **Dashboard**: painel de controle, KPIs, indicadores
- **Cadastro**: produtos, categorias, unidades, fornecedores, clientes, funcionários, veículos
- **Produção**: ordens de produção, ficha técnica, controle de produção
- **Estoque**: movimentação, inventários, transferências entre locais
- **Vendas**: pedidos, precificação, CRM, visitas comerciais
- **Compras**: solicitações, pedidos de compra, cotações de fornecedores
- **Fiscal**: NF-e, NFC-e, grupos tributários, CFOP, CST, ICMS, IPI, PIS/COFINS
- **Financeiro**: plano de contas, contas bancárias, contas a pagar/receber, fluxo de caixa, DRE
- **RH**: jornada de trabalho, ponto eletrônico, folha de pagamento, holerite
- **Transporte**: frotas, rotas, roteirização, agendamento de entregas
- **Suporte**: tickets, chat de suporte com IA

## Ferramentas disponíveis
Você tem acesso a ferramentas que consultam dados reais do sistema. Use-as quando o usuário pedir informações específicas sobre pedidos, clientes, estoque ou finanças.

## Regras de conduta
1. Responda **sempre em português brasileiro**
2. Seja **objetivo e direto** — evite respostas longas desnecessárias
3. Use **markdown** para formatar listas e destaques quando relevante
4. Ao dar instruções de navegação, use o formato: **Menu > Submenu**
5. Quando usar uma ferramenta, apresente os dados de forma clara e organizada
6. Se não tiver certeza, diga que não sabe e sugira o WhatsApp: **(32) 98450-2345**
7. Para problemas urgentes ou complexos demais, indique o atendimento humano via WhatsApp
8. Nunca invente dados — use as ferramentas disponíveis para consultar informações reais
9. Para dúvidas sobre emissão de NF-e, aponte para: **Fiscal > NF-e > Nova Nota**
10. Para problemas de acesso/login, oriente a verificar com o administrador do sistema
PROMPT;

    public function buildSystemPrompt(User $user, ?string $context = null): string
    {
        $prompt = self::SYSTEM_PROMPT;

        // Add user context
        $prompt .= "\n\n## Contexto do usuário atual\n";
        $prompt .= "- **Nome**: {$user->name}\n";
        $prompt .= "- **Perfil**: " . ($user->is_admin ? 'Administrador' : 'Usuário') . "\n";

        $modules = $user->modules ?? [];
        if (! empty($modules)) {
            $prompt .= "- **Módulos contratados**: " . implode(', ', $modules) . "\n";
        }

        if ($context) {
            $prompt .= "\n## Contexto adicional\n{$context}";
        }

        return $prompt;
    }

    public function buildMessages(
        User $user,
        TicketSuporte $ticket,
        Collection $mensagens,
        ?string $extraContext = null
    ): array {
        $systemPrompt = $this->buildSystemPrompt($user, $extraContext);

        // Add ticket context to system
        $systemPrompt .= "\n\n## Ticket atual\n";
        $systemPrompt .= "- **Assunto**: {$ticket->assunto}\n";
        $systemPrompt .= "- **Prioridade**: {$ticket->prioridade->label()}\n";
        $systemPrompt .= "- **Status**: {$ticket->status->label()}\n";

        if ($ticket->categoria) {
            $systemPrompt .= "- **Categoria**: {$ticket->categoria}\n";
        }

        $messages = [
            ['role' => 'system', 'content' => $systemPrompt],
        ];

        foreach ($mensagens as $msg) {
            if ($msg->is_ia) {
                $messages[] = ['role' => 'assistant', 'content' => $msg->conteudo];
            } elseif ($msg->is_suporte) {
                // Human support message — treat as assistant with clarification
                $messages[] = ['role' => 'assistant', 'content' => "[Atendente humano]: {$msg->conteudo}"];
            } else {
                $messages[] = ['role' => 'user', 'content' => $msg->conteudo];
            }
        }

        return $messages;
    }
}

