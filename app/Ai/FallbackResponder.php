<?php

namespace App\Ai;

use App\Models\TicketSuporte;

class FallbackResponder
{
    /**
     * Mapeamento de rejeições SEFAZ → resposta acionável.
     * Fonte: Manual de Orientação do Contribuinte (MOC) - SEFAZ
     */
    private const REJEICOES_SEFAZ = [
        '101' => ['titulo' => 'NF-e não encontrada', 'causa' => 'A NF-e informada não existe na base da SEFAZ.', 'solucao' => 'Verifique se a chave de acesso está correta. Acesse **Fiscal > NF-e** e confira os dados da nota.'],
        '102' => ['titulo' => 'NF-e cancelada', 'causa' => 'A NF-e já foi cancelada anteriormente.', 'solucao' => 'Esta nota não pode ser reativada. Emita uma nova NF-e em **Fiscal > NF-e > Nova Nota**.'],
        '110' => ['titulo' => 'Uso denegado', 'causa' => 'A NF-e foi denegada pela SEFAZ do destinatário.', 'solucao' => 'A nota não tem validade jurídica. Verifique a situação cadastral do destinatário na Receita Federal.'],
        '202' => ['titulo' => 'Rejeição não autorizada', 'causa' => 'Falha de comunicação com a SEFAZ.', 'solucao' => 'Tente reenviar a nota em **Fiscal > NF-e**. Se persistir, verifique a conexão e o certificado digital em **Fiscal > Configurações**.'],
        '203' => ['titulo' => 'Emissor não habilitado', 'causa' => 'O CNPJ do emitente não está habilitado para emissão no ambiente de produção.', 'solucao' => "1. Acesse **Fiscal > Configurações**\n2. Verifique se o certificado digital está válido\n3. Confirme se a empresa está habilitada na SEFAZ do seu estado\n4. Se estiver em homologação, mude para produção apenas quando estiver pronto"],
        '204' => ['titulo' => 'Número/Série duplicado', 'causa' => 'Já existe uma NF-e autorizada com o mesmo número e série.', 'solucao' => "1. Acesse **Fiscal > Configurações**\n2. Atualize o número da próxima NF-e para o próximo número disponível\n3. Tente emitir novamente"],
        '205' => ['titulo' => 'NF-e já autorizada', 'causa' => 'Esta NF-e já foi autorizada anteriormente.', 'solucao' => 'A nota já está válida. Acesse **Fiscal > NF-e** para visualizá-la e baixar o XML/DANFE.'],
        '207' => ['titulo' => 'CNPJ emitente inválido', 'causa' => 'O CNPJ do emitente está inválido ou com dígitos verificadores incorretos.', 'solucao' => "1. Acesse **Administração > Empresas**\n2. Corrija o CNPJ da empresa emitente\n3. Salve e tente emitir novamente"],
        '208' => ['titulo' => 'CNPJ do emitente difere da assinatura', 'causa' => 'O CNPJ do certificado digital não corresponde ao CNPJ da empresa emitente.', 'solucao' => "1. Acesse **Fiscal > Configurações**\n2. Verifique se o certificado digital pertence ao CNPJ correto da empresa\n3. Substitua o certificado se necessário"],
        '214' => ['titulo' => 'CNPJ do emitente não cadastrado na SEFAZ', 'causa' => 'O CNPJ do emitente não está na base de dados da SEFAZ.', 'solucao' => "1. Verifique a situação cadastral no site da Receita Federal\n2. Acesse **Administração > Empresas** e confirme o CNPJ\n3. Contate a SEFAZ do seu estado para habilitação"],
        '215' => ['titulo' => 'CPF/CNPJ destinatário inválido', 'causa' => 'O CPF ou CNPJ do destinatário está com formato ou dígitos verificadores inválidos.', 'solucao' => "1. Acesse **Cadastro > Clientes**\n2. Localize o cliente da nota\n3. Corrija o CPF/CNPJ\n4. Salve e tente emitir novamente"],
        '216' => ['titulo' => 'IE do emitente inválida', 'causa' => 'A Inscrição Estadual do emitente está inválida para o estado informado.', 'solucao' => "1. Acesse **Administração > Empresas**\n2. Verifique a Inscrição Estadual\n3. Corrija conforme o padrão do seu estado e salve"],
        '238' => ['titulo' => 'Número máximo da série atingido', 'causa' => 'O número de NF-e na série atual atingiu o limite (999999999).', 'solucao' => "1. Acesse **Fiscal > Configurações**\n2. Mude para a próxima série disponível (ex: série 2)\n3. Recomece a numeração a partir de 1"],
        '539' => ['titulo' => 'CNPJ destinatário inválido ou não encontrado', 'causa' => 'O CNPJ do destinatário não existe na base da Receita Federal.', 'solucao' => "1. Acesse **Cadastro > Clientes**\n2. Verifique o CNPJ do cliente\n3. Consulte a situação cadastral em https://www.receita.fazenda.gov.br\n4. Corrija e tente emitir novamente"],
        '702' => ['titulo' => 'NCM inválido', 'causa' => 'O código NCM (Nomenclatura Comum do Mercosul) de um ou mais produtos está inválido ou desatualizado.', 'solucao' => "1. Acesse **Cadastro > Produtos**\n2. Localize o(s) produto(s) da nota\n3. Corrija o NCM de cada produto com o código correto de 8 dígitos\n4. Consulte a tabela NCM atualizada em: https://www.mdic.gov.br/nomenclatura-comum-do-mercosul\n5. Salve e tente emitir novamente"],
        '741' => ['titulo' => 'CFOP inválido', 'causa' => 'O CFOP (Código Fiscal de Operações e Prestações) informado é inválido ou incompatível com a operação.', 'solucao' => "1. Acesse **Fiscal > Tipos de Operação**\n2. Revise o CFOP configurado\n3. Use CFOP iniciado em 5/6 para saídas e 1/2 para entradas\n4. Consulte a tabela CFOP vigente para confirmar o código correto"],
        '778' => ['titulo' => 'NCM inexistente', 'causa' => 'O código NCM informado não existe na tabela oficial da TIPI/NCM vigente. O produto com NCM inválido é identificado pelo número do item entre colchetes — ex: [item8] = 8º produto da nota.', 'solucao' => "**Como corrigir:**\n\n1. Identifique qual produto é o item indicado (ex: [item8] = 8º produto na nota)\n2. Acesse **Cadastro > Produtos**\n3. Abra o produto e localize o campo **NCM**\n4. Consulte o NCM correto na tabela TIPI atualizada:\n   - Site oficial: https://www.gov.br/receitafederal/pt-br/assuntos/aduana-e-comercio-exterior/classificacao-fiscal-de-mercadorias/tipi\n5. Atualize o NCM com o código de **8 dígitos** correto\n6. Salve e emita a nota novamente em **Fiscal > NF-e**\n\n**Dica:** NCMs inválidos geralmente ocorrem quando um código foi **extinção/reclassificado** na última atualização da TIPI. Verifique se o NCM não foi alterado recentemente."],
        '784' => ['titulo' => 'Duplicidade de NF-e (nota em contingência)', 'causa' => 'NF-e já autorizada em contingência.', 'solucao' => 'A nota já foi autorizada. Acesse **Fiscal > NF-e** para localizar a nota autorizada e baixar o XML.'],
        '805' => ['titulo' => 'Falha no schema XML', 'causa' => 'O XML da NF-e não está no formato correto exigido pela SEFAZ.', 'solucao' => "Este é um erro interno do sistema. Entre em contato com o suporte técnico via WhatsApp: **(32) 98450-2345**"],
        '999' => ['titulo' => 'Erro não catalogado', 'causa' => 'Erro interno da SEFAZ.', 'solucao' => "Aguarde alguns minutos e tente novamente. Se persistir, entre em contato com o suporte: **(32) 98450-2345**"],
    ];

    /**
     * Gera resposta de fallback baseada em padrões no ticket quando a IA não está disponível.
     */
    public function gerarResposta(TicketSuporte $ticket, string $mensagem): string
    {
        $textoCompleto = $ticket->assunto . ' ' . $mensagem;

        // 1. Verifica rejeições SEFAZ pelo código (ex: "Rejeição 778", "Rejeição: 702", "rej 539")
        $respostaSefaz = $this->detectarRejeicaoSefaz($textoCompleto);
        if ($respostaSefaz) {
            return $this->formatarResposta($respostaSefaz, isSefaz: true);
        }

        // 2. Padrões gerais de problemas
        $padroes = [
            'nota fiscal|nf-e|nfe|emitir nota|danfe|xml nota' => $this->respostaNFe(),
            'pedido|venda|order' => $this->respostaPedido(),
            'cliente|cadastro cliente' => $this->respostaCliente(),
            'estoque|produto|saldo' => $this->respostaEstoque(),
            'financeiro|boleto|pagamento|vencido' => $this->respostaFinanceiro(),
            'login|acesso|senha|usuário' => $this->respostaLogin(),
            'ncm|nomenclatura|tipi' => $this->respostaNcm(),
            'certificado|a1|a3|pfx' => $this->respostaCertificado(),
            'erro|bug|problema|não funciona' => $this->respostaErroGenerico(),
        ];

        $assunto = strtolower($textoCompleto);
        foreach ($padroes as $pattern => $resposta) {
            if (preg_match("/$pattern/i", $assunto)) {
                return $this->formatarResposta($resposta);
            }
        }

        return $this->respostaGenerica();
    }

    /**
     * Detecta código de rejeição SEFAZ na mensagem.
     */
    private function detectarRejeicaoSefaz(string $texto): ?string
    {
        // Padrões: "Rejeição 778", "Rejeição: 778", "Rej. 778", "778 -", "código 778"
        if (preg_match('/rejei[çc][aã]o[\s:.]*(\d{3})/iu', $texto, $m) ||
            preg_match('/\brej\.?\s*(\d{3})\b/iu', $texto, $m) ||
            preg_match('/c[oó]digo[\s:]*(\d{3})\b/iu', $texto, $m) ||
            preg_match('/\b(\d{3})\s*[-:]\s*[A-Z]/i', $texto, $m)) {
            $codigo = $m[1];

            if (isset(self::REJEICOES_SEFAZ[$codigo])) {
                $r = self::REJEICOES_SEFAZ[$codigo];

                // Extrai item referenciado se presente (ex: [item8])
                $itemInfo = '';
                if (preg_match('/\[item(\d+)]/i', $texto, $mi)) {
                    $itemInfo = "\n\n> ⚠️ **Item com problema:** item **#{$mi[1]}** da nota (verifique o {$mi[1]}º produto adicionado).";
                }

                return "### ❌ Rejeição SEFAZ {$codigo} — {$r['titulo']}\n\n"
                    . "**Causa:** {$r['causa']}{$itemInfo}\n\n"
                    . "**Solução:**\n\n{$r['solucao']}";
            }

            // Código não mapeado mas reconhecido
            return "### ❌ Rejeição SEFAZ {$codigo}\n\n"
                . "Este código de rejeição não está no nosso banco de diagnósticos locais.\n\n"
                . "**Próximos passos:**\n"
                . "1. Consulte o Manual de Orientação do Contribuinte (MOC) da SEFAZ\n"
                . "2. Verifique a mensagem completa retornada pelo sistema\n"
                . "3. Entre em contato com o suporte: **(32) 98450-2345**";
        }

        return null;
    }

    private function respostaNFe(): string
    {
        return <<<RESPOSTA
**Como emitir Nota Fiscal Eletrônica:**

1. Acesse **Fiscal > NF-e > Nova Nota**
2. Selecione o cliente e tipo de operação (CFOP)
3. Adicione os produtos com NCM, CST e alíquotas corretos
4. Verifique os impostos (ICMS, IPI, PIS/COFINS)
5. Clique em **Gerar NF-e**

**Pré-requisitos obrigatórios:**
- ✅ Certificado digital válido em **Fiscal > Configurações**
- ✅ Cliente com CPF/CNPJ preenchido
- ✅ Produto com NCM válido de 8 dígitos
- ✅ Empresa emitente com CNPJ e IE cadastrados

**Problemas comuns:**
- ❌ Certificado digital vencido → **Fiscal > Configurações**
- ❌ Cliente sem CPF/CNPJ → **Cadastro > Clientes**
- ❌ Produto sem NCM ou NCM inválido → **Cadastro > Produtos**
RESPOSTA;
    }

    private function respostaNcm(): string
    {
        return <<<RESPOSTA
**Como corrigir o NCM de um produto:**

1. Acesse **Cadastro > Produtos**
2. Abra o produto com NCM inválido
3. Localize o campo **NCM** e atualize com o código correto de **8 dígitos**
4. Consulte a tabela oficial: https://www.gov.br/receitafederal

**O que é NCM?**
NCM (Nomenclatura Comum do Mercosul) é um código de 8 dígitos obrigatório em NF-e para classificar cada produto. Produtos com NCM desatualizado causam rejeição na SEFAZ.

**Dica:** A TIPI é atualizada periodicamente por decreto. Verifique se o NCM do produto não foi alterado/extinto recentemente.
RESPOSTA;
    }

    private function respostaCertificado(): string
    {
        return <<<RESPOSTA
**Como configurar o Certificado Digital:**

1. Acesse **Fiscal > Configurações**
2. Na aba **Certificado Digital**, carregue o arquivo `.pfx` (A1)
3. Informe a senha do certificado
4. Clique em **Salvar**

**Verificações:**
- ✅ O certificado deve estar dentro do prazo de validade
- ✅ O CNPJ do certificado deve corresponder ao CNPJ da empresa emitente
- ✅ Para tipo A3 (token/cartão), o driver deve estar instalado no servidor

**Vencimento:** Renove o certificado com a Autoridade Certificadora (AC) antes do vencimento.
RESPOSTA;
    }

    private function respostaPedido(): string
    {
        return <<<RESPOSTA
**Gestão de Pedidos de Venda:**

📋 **Criar pedido**: **Vendas > Pedidos > Novo Pedido**
🔍 **Consultar**: Acesse **Vendas > Pedidos** e use os filtros
✅ **Alterar status**: Abra o pedido e clique em **Alterar Status**
RESPOSTA;
    }

    private function respostaCliente(): string
    {
        return <<<RESPOSTA
**Cadastro de Clientes:**

➕ **Novo cliente**: **Cadastro > Clientes > Novo Cliente**

**Campos obrigatórios para emissão de NF-e:**
- Nome/Razão Social
- CPF ou CNPJ válido
- Endereço completo com CEP
- Inscrição Estadual (para pessoas jurídicas contribuintes de ICMS)
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
RESPOSTA;
    }

    private function respostaFinanceiro(): string
    {
        return <<<RESPOSTA
**Gestão Financeira:**

💰 **Contas a Pagar**: **Financeiro > Contas a Pagar**
💵 **Contas a Receber**: **Financeiro > Contas a Receber**
📊 **Fluxo de Caixa**: **Financeiro > Fluxo de Caixa**
RESPOSTA;
    }

    private function respostaLogin(): string
    {
        return <<<RESPOSTA
**Problemas de Acesso:**

🔐 **Esqueci minha senha**: Use o link "Esqueci minha senha" na tela de login

🚫 **Conta bloqueada ou sem permissão**:
- Entre em contato com o **administrador do sistema**
- Somente administradores podem criar usuários e definir permissões → **Administração > Usuários**
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
4. ✅ O código de rejeição da SEFAZ (se for NF-e)

Para suporte urgente → WhatsApp: **(32) 98450-2345**
RESPOSTA;
    }

    private function respostaGenerica(): string
    {
        return <<<RESPOSTA
**Como posso ajudar?**

No momento estou operando sem acesso à IA online, mas posso orientar sobre:

📋 **Vendas**: Pedidos, orçamentos, precificação
📦 **Estoque**: Movimentações, saldos
💰 **Financeiro**: Contas a pagar/receber, fluxo de caixa
🧾 **Fiscal**: Emissão de NF-e, rejeições SEFAZ
👥 **Cadastros**: Clientes, produtos, fornecedores
⚙️ **Configurações**: Certificado digital, parâmetros do sistema

**Para rejeições SEFAZ**: Informe o **código da rejeição** (ex: "Rejeição 702") na sua mensagem para receber orientação específica.

Ou entre em contato: **WhatsApp (32) 98450-2345**
RESPOSTA;
    }

    private function formatarResposta(string $resposta, bool $isSefaz = false): string
    {
        if ($isSefaz) {
            $header = "⚠️ *Modo Offline* — Respondendo com base no Manual SEFAZ (MOC) sem consulta ao banco de dados em tempo real.\n\n";
            $footer = "\n\n---\n🤖 Para diagnóstico com dados reais da sua nota, entre em contato via WhatsApp: **(32) 98450-2345**";
        } else {
            $header = "⚠️ *Modo Offline* — Respondendo sem acesso à IA online no momento.\n\n";
            $footer = "\n\n---\n🤖 Para respostas com consulta aos seus dados reais, entre em contato via WhatsApp: **(32) 98450-2345**";
        }

        return $header . trim($resposta) . $footer;
    }
}

