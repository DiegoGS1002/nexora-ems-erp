<?php

namespace App\Ai;

use App\Models\TicketSuporte;

/**
 * PlaybookEngine — Raciocínio estruturado por domínio.
 *
 * Detecta automaticamente o domínio do problema e retorna o playbook
 * de diagnóstico correspondente para guiar o agente no chain-of-thought.
 *
 * Baseado no Master Guide: arquitetura_agente_suporte_senior_rag_md.md
 */
class PlaybookEngine
{
    /**
     * Detecta o domínio do problema e retorna o playbook de diagnóstico.
     */
    public function detectarPlaybook(TicketSuporte $ticket, string $mensagem): ?array
    {
        $texto = strtolower($ticket->assunto . ' ' . $mensagem);

        // Prioridade: fiscal > financeiro > estoque > rh > vendas > compras > acesso
        if ($this->isFiscal($texto)) {
            return $this->playbookFiscal($texto);
        }

        if ($this->isFinanceiro($texto)) {
            return $this->playbookFinanceiro();
        }

        if ($this->isEstoque($texto)) {
            return $this->playbookEstoque();
        }

        if ($this->isRh($texto)) {
            return $this->playbookRh();
        }

        if ($this->isVendas($texto)) {
            return $this->playbookVendas();
        }

        if ($this->isAcesso($texto)) {
            return $this->playbookAcesso();
        }

        return null;
    }

    /**
     * Retorna o bloco de instruções de raciocínio para injetar no prompt.
     */
    public function buildPlaybookPrompt(TicketSuporte $ticket, string $mensagem): string
    {
        $playbook = $this->detectarPlaybook($ticket, $mensagem);

        if (! $playbook) {
            return '';
        }

        $prompt  = "\n\n## 🎯 Playbook de Diagnóstico — {$playbook['dominio']}\n\n";
        $prompt .= "Siga OBRIGATORIAMENTE esta sequência de raciocínio antes de responder:\n\n";

        foreach ($playbook['etapas'] as $i => $etapa) {
            $n       = $i + 1;
            $prompt .= "**Etapa {$n}: {$etapa['titulo']}**\n";
            $prompt .= $etapa['instrucao'] . "\n\n";
        }

        if (! empty($playbook['ferramentas'])) {
            $prompt .= "**Ferramentas recomendadas neste playbook:** " . implode(', ', $playbook['ferramentas']) . "\n\n";
        }

        if (! empty($playbook['formato_resposta'])) {
            $prompt .= "**Formato esperado da resposta:**\n" . $playbook['formato_resposta'] . "\n";
        }

        return $prompt;
    }

    // -------------------------------------------------------------------------
    // Detecção de domínio
    // -------------------------------------------------------------------------

    private function isFiscal(string $texto): bool
    {
        return (bool) preg_match('/nf.?e|nota fiscal|nfc.?e|ct.?e|mdf.?e|sefaz|rejei|xml|danfe|cfop|ncm|cst|icms|ipi|pis|cofins|emissão|emitir|cancelar nota|protocolo|chave acesso|certif/i', $texto);
    }

    private function isFinanceiro(string $texto): bool
    {
        return (bool) preg_match('/financeiro|conta.a.pagar|conta.a.receber|boleto|vencimento|pagamento|recebimento|fluxo.de.caixa|dre|plano.de.conta|banco|concilia/i', $texto);
    }

    private function isEstoque(string $texto): bool
    {
        return (bool) preg_match('/estoque|produto|saldo|movimenta|inventário|entrada.*produto|saída.*produto|transferência/i', $texto);
    }

    private function isRh(string $texto): bool
    {
        return (bool) preg_match('/folha|salário|holerite|ponto|jornada|funcionário|admissão|demissão|férias|inss|irrf|fgts/i', $texto);
    }

    private function isVendas(string $texto): bool
    {
        return (bool) preg_match('/pedido|venda|cliente|orçamento|cotação|entrega|frete|comissão/i', $texto);
    }

    private function isAcesso(string $texto): bool
    {
        return (bool) preg_match('/login|senha|acesso|permissão|bloqueado|usuário|perfil/i', $texto);
    }

    // -------------------------------------------------------------------------
    // Playbooks
    // -------------------------------------------------------------------------

    private function playbookFiscal(string $texto): array
    {
        // Detecta se há código de rejeição
        $temRejeicao = (bool) preg_match('/rejei[çc][aã]o[\s:.]*\d{3}|\brej\.?\s*\d{3}|\b\d{3}\s*[-:]/i', $texto);

        if ($temRejeicao) {
            return [
                'dominio' => 'Fiscal — Rejeição SEFAZ',
                'etapas'  => [
                    ['titulo' => 'Identificar o código', 'instrucao' => 'Extraia o código numérico da rejeição da mensagem do usuário.'],
                    ['titulo' => 'Consultar diagnóstico estruturado', 'instrucao' => 'Use a ferramenta `consultar_diagnostico_sefaz` com o código extraído. Esta é sua fonte primária — não invente.'],
                    ['titulo' => 'Verificar a NF-e real', 'instrucao' => 'Use `verificar_nfe` para confirmar a mensagem SEFAZ real da nota do usuário. Cruzar com o diagnóstico estruturado.'],
                    ['titulo' => 'Diagnosticar causa raiz', 'instrucao' => 'Cite: qual produto/campo está errado, o valor atual vs. o correto, e qual item da nota (se [itemN] presente).'],
                    ['titulo' => 'Orientar correção', 'instrucao' => 'Diga o caminho EXATO no Nexora ERP para corrigir (Menu > Submenu > Campo). Não generalize.'],
                ],
                'ferramentas' => ['consultar_diagnostico_sefaz', 'verificar_nfe', 'analisar_xml_nfe'],
                'formato_resposta' => "**Rejeição [código] — [título]**\n**Causa:** [causa específica com dado real]\n**Correção:** [passo a passo no Nexora]\n**Prevenção:** [como evitar no futuro]",
            ];
        }

        return [
            'dominio' => 'Fiscal — Emissão/NF-e',
            'etapas'  => [
                ['titulo' => 'Identificar o problema', 'instrucao' => 'Determine se é: erro de emissão, rejeição, dúvida de configuração ou cancelamento.'],
                ['titulo' => 'Consultar notas do usuário', 'instrucao' => 'Use `verificar_nfe` para ver as últimas notas e status. Verifique `sefaz_message` nas rejeitadas.'],
                ['titulo' => 'Verificar pré-requisitos', 'instrucao' => 'Certificado digital válido? Cliente com CPF/CNPJ? Produtos com NCM válido? Empresa com IE?'],
                ['titulo' => 'Diagnosticar e orientar', 'instrucao' => 'Cite dados reais encontrados nas ferramentas. Nunca suponha sem consultar.'],
            ],
            'ferramentas' => ['verificar_nfe', 'consultar_diagnostico_sefaz', 'consultar_cliente'],
            'formato_resposta' => "**Diagnóstico:** [problema específico]\n**Dados encontrados:** [dados reais do sistema]\n**Solução:** [passo a passo]",
        ];
    }

    private function playbookFinanceiro(): array
    {
        return [
            'dominio' => 'Financeiro',
            'etapas'  => [
                ['titulo' => 'Identificar natureza', 'instrucao' => 'É conta a pagar, a receber, fluxo de caixa ou relatório financeiro?'],
                ['titulo' => 'Consultar dados financeiros', 'instrucao' => 'Use `consultar_financeiro` para ver contas vencidas, saldos e lançamentos.'],
                ['titulo' => 'Verificar regras de negócio', 'instrucao' => 'Verifique se o problema é de lançamento, baixa, conciliação ou configuração de plano de contas.'],
                ['titulo' => 'Orientar solução', 'instrucao' => 'Indique o caminho no módulo Financeiro do Nexora ERP com os campos corretos a preencher.'],
            ],
            'ferramentas' => ['consultar_financeiro'],
        ];
    }

    private function playbookEstoque(): array
    {
        return [
            'dominio' => 'Estoque',
            'etapas'  => [
                ['titulo' => 'Identificar o produto/problema', 'instrucao' => 'Qual produto? Qual tipo de movimentação (entrada, saída, transferência)?'],
                ['titulo' => 'Consultar saldo atual', 'instrucao' => 'Use `consultar_estoque` para verificar o saldo real do produto.'],
                ['titulo' => 'Verificar movimentações', 'instrucao' => 'Há inconsistências entre pedidos de venda, compra e o saldo físico?'],
                ['titulo' => 'Orientar correção', 'instrucao' => 'Indique se é ajuste manual, lançamento de NF-e de entrada ou erro de movimentação.'],
            ],
            'ferramentas' => ['consultar_estoque'],
        ];
    }

    private function playbookRh(): array
    {
        return [
            'dominio' => 'RH / Folha de Pagamento',
            'etapas'  => [
                ['titulo' => 'Identificar o problema', 'instrucao' => 'É cálculo de folha, ponto eletrônico, holerite ou jornada de trabalho?'],
                ['titulo' => 'Verificar período', 'instrucao' => 'Qual o mês/ano da competência? A folha já foi fechada?'],
                ['titulo' => 'Diagnosticar', 'instrucao' => 'Verifique configurações de jornada, banco de horas e rubricas salariais.'],
                ['titulo' => 'Orientar', 'instrucao' => 'Indique o caminho em RH > Folha ou RH > Ponto para a correção.'],
            ],
            'ferramentas' => ['inspecionar_logs'],
        ];
    }

    private function playbookVendas(): array
    {
        return [
            'dominio' => 'Vendas / Pedidos',
            'etapas'  => [
                ['titulo' => 'Consultar o pedido', 'instrucao' => 'Use `buscar_pedido` para verificar status, itens, valores e cliente do pedido.'],
                ['titulo' => 'Verificar o cliente', 'instrucao' => 'Use `consultar_cliente` se houver problemas cadastrais que bloqueiem o pedido.'],
                ['titulo' => 'Verificar estoque', 'instrucao' => 'Se o problema for de reserva ou falta de produto, use `consultar_estoque`.'],
                ['titulo' => 'Orientar próxima ação', 'instrucao' => 'Indique o status correto e o caminho em Vendas > Pedidos.'],
            ],
            'ferramentas' => ['buscar_pedido', 'consultar_cliente', 'consultar_estoque'],
        ];
    }

    private function playbookAcesso(): array
    {
        return [
            'dominio' => 'Controle de Acesso',
            'etapas'  => [
                ['titulo' => 'Identificar tipo de problema', 'instrucao' => 'É senha esquecida, conta bloqueada, permissão insuficiente ou módulo não contratado?'],
                ['titulo' => 'Verificar histórico', 'instrucao' => 'Use `inspecionar_logs` com módulo Segurança para ver tentativas de login falhas.'],
                ['titulo' => 'Orientar solução', 'instrucao' => 'Para permissões: Administração > Usuários > Perfis. Para senha: link "Esqueci minha senha" na tela de login.'],
            ],
            'ferramentas' => ['inspecionar_logs'],
        ];
    }
}

