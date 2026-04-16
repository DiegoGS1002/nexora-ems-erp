<?php

namespace App\Ai;

use App\Models\TicketSuporte;

class FallbackResponder
{
    /**
     * Gera resposta de fallback baseada em padrões no ticket quando a IA não está disponível.
     */
    public function gerarResposta(TicketSuporte $ticket, string $mensagem): string
    {
        $assunto = strtolower($ticket->assunto . ' ' . $mensagem);

        // Padrões de problemas comuns
        $padroes = [
            'nota fiscal|nf-e|nfe|emitir nota' => $this->respostaNFe(),
            'pedido|venda|order' => $this->respostaPedido(),
            'cliente|cadastro cliente' => $this->respostaCliente(),
            'estoque|produto|saldo' => $this->respostaEstoque(),
            'financeiro|boleto|pagamento|vencido' => $this->respostaFinanceiro(),
            'login|acesso|senha|usuário' => $this->respostaLogin(),
            'erro|bug|problema|não funciona' => $this->respostaErroGenerico(),
        ];

        foreach ($padroes as $pattern => $resposta) {
            if (preg_match("/$pattern/i", $assunto)) {
                return $this->formatarResposta($resposta);
            }
        }

        return $this->respostaGenerica();
    }

    private function respostaNFe(): string
    {
        return <<<RESPOSTA
**Como emitir Nota Fiscal Eletrônica:**

1. Acesse **Fiscal > NF-e > Nova Nota**
2. Selecione o cliente e tipo de operação (CFOP)
3. Adicione os produtos
4. Verifique os impostos (ICMS, IPI, PIS/COFINS)
5. Clique em **Gerar NF-e**

**Problemas comuns:**
- ❌ Certificado digital vencido → Atualize em **Fiscal > Configurações**
- ❌ Cliente sem CPF/CNPJ → Complete o cadastro
- ❌ Produto sem NCM → Configure em **Cadastro > Produtos**
RESPOSTA;
    }

    private function respostaPedido(): string
    {
        return <<<RESPOSTA
**Gestão de Pedidos de Venda:**

📋 **Criar pedido**: **Vendas > Pedidos > Novo Pedido**
🔍 **Consultar**: Acesse **Vendas > Pedidos** e use os filtros
✅ **Alterar status**: Abra o pedido e clique em **Alterar Status**

**Precisa de informações específicas?**
Para consultar pedidos, clientes ou estoque, por favor reformule sua pergunta quando a IA estiver disponível, ou entre em contato via WhatsApp.
RESPOSTA;
    }

    private function respostaCliente(): string
    {
        return <<<RESPOSTA
**Cadastro de Clientes:**

➕ **Novo cliente**: **Cadastro > Clientes > Novo Cliente**

**Campos obrigatórios:**
- Nome/Razão Social
- CPF ou CNPJ
- Endereço completo
- Email e telefone

💡 **Dica**: Para emitir NF-e, todos os dados fiscais devem estar completos.
RESPOSTA;
    }

    private function respostaEstoque(): string
    {
        return <<<RESPOSTA
**Gestão de Estoque:**

📦 **Consultar saldo**: **Estoque > Movimentação** ou **Cadastro > Produtos**
📥 **Entrada**: **Estoque > Movimentação > Nova Entrada**
📤 **Saída**: **Estoque > Movimentação > Nova Saída**
🔄 **Transferência**: **Estoque > Transferência**

**Para consultar saldos específicos**, aguarde a IA voltar ou use o menu Estoque.
RESPOSTA;
    }

    private function respostaFinanceiro(): string
    {
        return <<<RESPOSTA
**Gestão Financeira:**

💰 **Contas a Pagar**: **Financeiro > Contas a Pagar**
💵 **Contas a Receber**: **Financeiro > Contas a Receber**
📊 **Fluxo de Caixa**: **Financeiro > Fluxo de Caixa**

**Vencimentos**: Use os filtros de data para ver contas vencidas.
**Relatórios**: Acesse **Financeiro > Relatórios / DRE**
RESPOSTA;
    }

    private function respostaLogin(): string
    {
        return <<<RESPOSTA
**Problemas de Acesso:**

🔐 **Esqueci minha senha**: Use o link "Esqueci minha senha" na tela de login

🚫 **Conta bloqueada ou sem permissão**:
- Entre em contato com o **administrador do sistema**
- Somente administradores podem criar usuários e definir permissões

**Módulo Administração** (apenas para admin):
**Administração > Usuários**
RESPOSTA;
    }

    private function respostaErroGenerico(): string
    {
        return <<<RESPOSTA
**Reportando Erros:**

Para ajudar a resolver o problema, informe:
1. ✅ Qual tela/módulo você estava usando
2. ✅ O que você tentou fazer
3. ✅ Qual erro apareceu (se possível, tire um print)
4. ✅ Em que momento ocorreu

📸 **Dica**: Prints de tela ajudam muito!

Para suporte urgente, entre em contato via WhatsApp.
RESPOSTA;
    }

    private function respostaGenerica(): string
    {
        return <<<RESPOSTA
**Olá! Como posso ajudar?**

No momento, estou operando em modo simplificado. Posso orientá-lo sobre:

📋 **Vendas**: Pedidos, orçamentos, precificação
📦 **Estoque**: Movimentações, saldos
💰 **Financeiro**: Contas a pagar/receber, fluxo de caixa
🧾 **Fiscal**: Emissão de NF-e
👥 **Cadastros**: Clientes, produtos, fornecedores
⚙️ **Configurações**: Parâmetros do sistema

**Navegue pelos menus** ou aguarde alguns minutos para ter acesso à IA completa com consultas ao banco de dados.
RESPOSTA;
    }

    private function formatarResposta(string $resposta): string
    {
        $header = "⚠️ *Modo Simplificado* — Minha IA está temporariamente indisponível devido a limitações da API.\n\n";
        $footer = "\n\n---\n\n🤖 Para respostas mais detalhadas com consulta aos seus dados reais, aguarde alguns minutos ou **entre em contato via WhatsApp: (32) 98450-2345**";

        return $header . trim($resposta) . $footer;
    }
}

