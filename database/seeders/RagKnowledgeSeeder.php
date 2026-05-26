<?php

namespace Database\Seeders;

use App\Ai\RagService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Log;

/**
 * Popula a base de conhecimento RAG do Nexora ERP com documentação
 * de todos os módulos, regras de negócio e fluxos operacionais.
 *
 * Execução: php artisan db:seed --class=RagKnowledgeSeeder
 * Ou via comando: php artisan ai:index-knowledge
 */
class RagKnowledgeSeeder extends Seeder
{
    private RagService $rag;
    private int $total = 0;
    private int $errors = 0;
    private ?\Illuminate\Console\Command $cmd = null;

    /**
     * Permite injetar o Command para usar com output quando chamado via Artisan.
     */
    public function setCommand(\Illuminate\Console\Command $command): void
    {
        $this->cmd = $command;
    }

    private function log(string $message): void
    {
        if ($this->cmd) {
            $this->cmd->line($message);
        } elseif (isset($this->command)) {
            $this->log($message);
        }
    }

    private function info(string $message): void
    {
        if ($this->cmd) {
            $this->cmd->info($message);
        } elseif (isset($this->command)) {
            $this->info($message);
        }
    }

    private function warn(string $message): void
    {
        if ($this->cmd) {
            $this->cmd->warn($message);
        } elseif (isset($this->command)) {
            $this->warn($message);
        }
    }

    public function run(): void
    {
        $this->rag = app(RagService::class);

        $this->info('🧠 Iniciando indexação da base de conhecimento RAG do Nexora ERP...');
        $this->info('ℹ️  Documentos existentes NÃO serão duplicados se já indexados.');

        // Limpa base existente e reindexa
        \App\Models\RagDocument::truncate();
        $this->warn('⚠️  Base RAG limpa. Reindexando...');

        $this->indexarFiscal();
        $this->indexarFinanceiro();
        $this->indexarVendas();
        $this->indexarCompras();
        $this->indexarEstoque();
        $this->indexarRh();
        $this->indexarTransporte();
        $this->indexarProducao();
        $this->indexarCadastros();
        $this->indexarAdministracao();
        $this->indexarRegrasNegocio();
        $this->indexarFluxosOperacionais();
        $this->indexarErrosComuns();

        $this->info("✅ Indexação concluída: {$this->total} documentos indexados | {$this->errors} erros.");

        if ($this->errors > 0) {
            $this->warn('⚠️  Alguns documentos falharam ao gerar embedding (verifique OPENAI_API_KEY). A busca lexical ainda funciona.');
        }
    }

    // =========================================================================
    // MÓDULO FISCAL / NF-e
    // =========================================================================

    private function indexarFiscal(): void
    {
        $this->log('📋 Indexando: Fiscal / NF-e...');

        $docs = [
            [
                'titulo'   => 'Como emitir NF-e no Nexora ERP',
                'conteudo' => <<<TEXT
Para emitir uma Nota Fiscal Eletrônica (NF-e) no Nexora ERP:
1. Acesse o menu Fiscal > NF-e > Nova Nota
2. Selecione o cliente (deve ter CPF/CNPJ válido cadastrado)
3. Escolha o Tipo de Operação (CFOP será preenchido automaticamente)
4. Adicione os produtos: cada produto deve ter NCM de 8 dígitos, CFOP, CST, unidade de medida
5. Verifique os impostos calculados: ICMS, IPI, PIS, COFINS
6. Clique em Gerar NF-e para enviar à SEFAZ
7. Acompanhe o status: draft (rascunho), sent (enviada), authorized (autorizada), rejected (rejeitada)

Pré-requisitos obrigatórios:
- Certificado digital válido configurado em Fiscal > Configurações > Certificado Digital
- Empresa com CNPJ e Inscrição Estadual cadastrados em Administração > Empresas
- Cliente com CPF ou CNPJ válido em Cadastro > Clientes
- Produtos com NCM de 8 dígitos em Cadastro > Produtos
- Tipo de Operação fiscal configurado em Fiscal > Tipos de Operação
TEXT,
            ],
            [
                'titulo'   => 'Status de NF-e e significados',
                'conteudo' => <<<TEXT
Status possíveis de uma NF-e no Nexora ERP:
- draft (Rascunho): nota criada mas ainda não enviada à SEFAZ
- sent (Enviada): XML enviado à SEFAZ, aguardando resposta
- authorized (Autorizada): NF-e aprovada pela SEFAZ, tem validade jurídica
- rejected (Rejeitada): SEFAZ recusou a nota por erro. Verificar sefaz_message para o código de rejeição
- cancelled (Cancelada): NF-e autorizada e depois cancelada pelo emitente
- denied (Denegada): SEFAZ negou a nota por irregularidade fiscal do destinatário. Não tem solução - emita nova nota para outro destinatário

Para notas rejeitadas, o campo sefaz_message contém a mensagem exata da SEFAZ com o código de rejeição (ex: "Rejeição 778 - NCM inexistente").
TEXT,
            ],
            [
                'titulo'   => 'Rejeição SEFAZ 778 - NCM inexistente',
                'conteudo' => <<<TEXT
Rejeição 778 significa que o NCM (Nomenclatura Comum do Mercosul) de um produto na nota não existe na tabela TIPI vigente.

Causa: O NCM foi extinto ou reclassificado numa atualização da TIPI (Tabela de Incidência do IPI).

Como identificar o produto com NCM inválido:
- A mensagem SEFAZ geralmente indica [item8] = 8º produto da nota
- Use a ferramenta verificar_nfe para ver a sefaz_message completa

Como corrigir:
1. Acesse Cadastro > Produtos
2. Localize o produto indicado (item N da nota)
3. Corrija o campo NCM com o código de 8 dígitos correto
4. Consulte a tabela TIPI atualizada: https://www.gov.br/receitafederal
5. Salve e emita a nota novamente em Fiscal > NF-e

Prevenção: Configure alertas de NCM desatualizado em Cadastro > Produtos. Revise NCMs após decreto federal de atualização da TIPI.
TEXT,
            ],
            [
                'titulo'   => 'Rejeição SEFAZ 702 - NCM inválido',
                'conteudo' => <<<TEXT
Rejeição 702: O código NCM informado está com formato inválido ou dígitos incorretos.

Diferença entre 702 e 778:
- 702: NCM com formato errado (menos de 8 dígitos, caracteres inválidos)
- 778: NCM com 8 dígitos corretos mas não existe na tabela TIPI

Correção:
1. Acesse Cadastro > Produtos
2. Localize o produto da nota (ver [itemN] na mensagem SEFAZ)
3. No campo NCM, insira exatamente 8 dígitos numéricos
4. Remova pontos, traços ou qualquer caractere não numérico
5. Salve e reemita em Fiscal > NF-e
TEXT,
            ],
            [
                'titulo'   => 'Rejeição SEFAZ 203 - Emissor não habilitado',
                'conteudo' => <<<TEXT
Rejeição 203: A empresa emitente não está habilitada para emissão de NF-e no ambiente de produção.

Causas comuns:
1. Empresa em ambiente de homologação tentando emitir em produção
2. Empresa não cadastrada na SEFAZ estadual
3. Certificado digital inválido ou vencido

Soluções:
1. Verifique o ambiente em Fiscal > Configurações > Ambiente (deve ser "Produção")
2. Confirme que o certificado digital é válido e pertence ao CNPJ correto
3. Verifique a habilitação da empresa no portal da SEFAZ do seu estado
4. Se necessário, solicite credenciamento à SEFAZ estadual
TEXT,
            ],
            [
                'titulo'   => 'Rejeição SEFAZ 204 - Número/Série duplicado',
                'conteudo' => <<<TEXT
Rejeição 204: Já existe uma NF-e autorizada com o mesmo número e série.

Causa: Tentativa de emitir nota com numeração já utilizada.

Solução:
1. Acesse Fiscal > Configurações
2. Localize o campo "Próximo número NF-e"
3. Atualize para o próximo número disponível (número atual + 1)
4. Salve e tente emitir novamente

Prevenção: Nunca altere manualmente a numeração retroativamente. O sistema incrementa automaticamente após cada nota autorizada.
TEXT,
            ],
            [
                'titulo'   => 'Rejeição SEFAZ 215/539 - CPF/CNPJ destinatário inválido',
                'conteudo' => <<<TEXT
Rejeição 215 ou 539: CPF ou CNPJ do destinatário (cliente) está inválido ou não existe na base da Receita Federal.

Correção:
1. Acesse Cadastro > Clientes
2. Localize o cliente da nota rejeitada
3. Verifique e corrija o CPF ou CNPJ
4. Para CNPJ: consulte situação em https://www.receita.fazenda.gov.br
5. Salve e reemita em Fiscal > NF-e

Atenção: CNPJ de empresa baixada, inapta ou suspensa também causa rejeição 539.
TEXT,
            ],
            [
                'titulo'   => 'Certificado digital NF-e - configuração',
                'conteudo' => <<<TEXT
Como configurar o certificado digital para emissão de NF-e no Nexora ERP:

1. Acesse Fiscal > Configurações > Certificado Digital
2. Tipo A1 (arquivo .pfx): faça upload do arquivo e informe a senha
3. Tipo A3 (token/cartão): o driver deve estar instalado no servidor
4. Clique em Salvar

Verificações:
- O CNPJ no certificado deve ser exatamente o CNPJ da empresa emitente
- Verifique a data de validade (certificados vencem em 1 ou 3 anos)
- Para certificado vencido: renove com a Autoridade Certificadora (Serpro, Certisign, etc.)

Rejeições relacionadas ao certificado:
- 208: CNPJ do certificado diferente do CNPJ da empresa
- 203: Certificado inválido ou empresa não habilitada
TEXT,
            ],
            [
                'titulo'   => 'Tipos de Operação Fiscal (CFOP) - configuração',
                'conteudo' => <<<TEXT
Os Tipos de Operação Fiscal no Nexora ERP determinam o CFOP, CST e tributação de cada operação.

Acesso: Fiscal > Tipos de Operação

Regras de CFOP:
- Iniciados em 1 ou 2: entradas (1=estadual, 2=interestadual)
- Iniciados em 5 ou 6: saídas (5=estadual, 6=interestadual)
- Iniciados em 7: exportação

CFOPs mais comuns:
- 5102/6102: Venda de mercadoria adquirida de terceiros
- 5101/6101: Venda de produção do estabelecimento
- 5403: Venda de mercadoria sujeita ao Simples Nacional
- 1102/2102: Compra de mercadoria para revenda
- 5949/6949: Outra saída não especificada

Rejeição 741 (CFOP inválido): Verifique em Fiscal > Tipos de Operação se o CFOP está correto para a natureza da operação.
TEXT,
            ],
            [
                'titulo'   => 'NF-e de entrada - importação de XML',
                'conteudo' => <<<TEXT
Para registrar NF-e de entrada (compra) no Nexora ERP:

1. Acesse Fiscal > NF-e Entrada
2. Clique em Importar XML
3. Faça upload do arquivo XML da nota do fornecedor
4. O sistema validará o XML automaticamente
5. Vincule os produtos da nota aos produtos cadastrados no estoque
6. Confirme os valores de ICMS, IPI, PIS/COFINS para aproveitamento de crédito
7. Clique em Confirmar para registrar a entrada no estoque

Para buscar XMLs da SEFAZ diretamente:
1. Acesse Fiscal > NF-e Entrada > Buscar na SEFAZ
2. Informe o CNPJ e período
3. O sistema buscará as notas disponíveis no portal da SEFAZ
TEXT,
            ],
            [
                'titulo'   => 'Cancelamento de NF-e',
                'conteudo' => <<<TEXT
Como cancelar uma NF-e autorizada no Nexora ERP:

Prazo: O cancelamento só é permitido em até 24 horas após a autorização (em alguns estados, até 168 horas).

Passos:
1. Acesse Fiscal > NF-e
2. Localize a nota autorizada que deseja cancelar
3. Clique em Ações > Cancelar NF-e
4. Informe a justificativa do cancelamento (mínimo 15 caracteres)
5. Confirme. O sistema enviará o evento de cancelamento à SEFAZ.

Após o cancelamento:
- A nota fica com status "cancelled"
- O estoque é estornado automaticamente (se configurado)
- O conteúdo da nota pode ser reemitido com um novo número

Atenção: Após o prazo de cancelamento, a NF-e não pode ser cancelada. Emita uma NF-e de devolução.
TEXT,
            ],
        ];

        foreach ($docs as $doc) {
            $this->store($doc['titulo'], $doc['conteudo'], 'modulo', 'fiscal');
        }
    }

    // =========================================================================
    // MÓDULO FINANCEIRO
    // =========================================================================

    private function indexarFinanceiro(): void
    {
        $this->log('💰 Indexando: Financeiro...');

        $docs = [
            [
                'titulo'   => 'Contas a Pagar - como lançar e baixar',
                'conteudo' => <<<TEXT
Como gerenciar Contas a Pagar no Nexora ERP:

Lançar nova conta:
1. Acesse Financeiro > Contas a Pagar > Nova Conta
2. Informe: descrição, fornecedor, valor, data de vencimento, plano de contas
3. Clique em Salvar

Baixar (registrar pagamento):
1. Acesse Financeiro > Contas a Pagar
2. Localize a conta e clique em Baixar
3. Informe: data do pagamento, valor pago, conta bancária debitada
4. Confirme

Filtros disponíveis:
- Por status: Pendente, Vencido, Pago
- Por período de vencimento
- Por fornecedor
- Por plano de contas

Atenção: Contas vencidas aparecem destacadas em vermelho. Acesse Financeiro > Contas a Pagar > Filtrar por "Vencido" para ver todas em atraso.
TEXT,
            ],
            [
                'titulo'   => 'Contas a Receber - gestão e baixa',
                'conteudo' => <<<TEXT
Como gerenciar Contas a Receber no Nexora ERP:

Lançar recebimento:
1. Acesse Financeiro > Contas a Receber > Nova Conta
2. Informe: cliente, descrição, valor, data de vencimento
3. Para pedidos de venda: o título é criado automaticamente ao faturar

Baixar recebimento:
1. Acesse Financeiro > Contas a Receber
2. Localize o título e clique em Baixar
3. Informe: data recebimento, valor, forma de pagamento, conta bancária

Títulos em atraso:
- Filtrar por status "Vencido"
- O sistema calcula automaticamente juros e multa se configurado

Relatório de inadimplência: Financeiro > Relatórios > Inadimplência
TEXT,
            ],
            [
                'titulo'   => 'Fluxo de Caixa - visão geral',
                'conteudo' => <<<TEXT
O Fluxo de Caixa no Nexora ERP consolida todas as movimentações financeiras.

Acesso: Financeiro > Fluxo de Caixa

O fluxo apresenta:
- Saldo inicial do período
- Entradas (recebimentos de contas a receber, outras receitas)
- Saídas (pagamentos de contas a pagar, despesas)
- Saldo final projetado

Filtros:
- Por período (diário, semanal, mensal)
- Por conta bancária
- Por plano de contas (centro de custo)

Para projeção futura: ative a visualização de títulos em aberto para ver comprometimento futuro do caixa.
TEXT,
            ],
            [
                'titulo'   => 'Plano de Contas - estrutura e configuração',
                'conteudo' => <<<TEXT
O Plano de Contas organiza as categorias financeiras no Nexora ERP.

Acesso: Financeiro > Plano de Contas

Estrutura hierárquica:
- Nível 1: Grupo (Receitas, Despesas, Patrimônio)
- Nível 2: Subgrupo (Receitas Operacionais, Despesas Administrativas)
- Nível 3: Conta analítica (onde os lançamentos são vinculados)

Para criar nova conta:
1. Financeiro > Plano de Contas > Nova Conta
2. Selecione o grupo pai
3. Informe código e descrição
4. Defina como Analítica (aceita lançamentos) ou Sintética (agrupamento)

Os lançamentos de Contas a Pagar e Receber devem sempre vincular a uma conta analítica do Plano de Contas.
TEXT,
            ],
            [
                'titulo'   => 'Contas Bancárias - cadastro e conciliação',
                'conteudo' => <<<TEXT
Gerenciamento de Contas Bancárias no Nexora ERP:

Cadastrar conta:
1. Financeiro > Contas Bancárias > Nova Conta
2. Informe: banco, agência, conta, titular, saldo inicial
3. Vincule ao Plano de Contas

Movimentações:
- Toda baixa de CP/CR deve vincular a uma conta bancária
- O saldo da conta é atualizado automaticamente

Conciliação bancária:
1. Financeiro > Contas Bancárias > Conciliação
2. Importe o extrato bancário (OFX ou manual)
3. O sistema compara com os lançamentos do Nexora
4. Marque os lançamentos conciliados
TEXT,
            ],
        ];

        foreach ($docs as $doc) {
            $this->store($doc['titulo'], $doc['conteudo'], 'modulo', 'financeiro');
        }
    }

    // =========================================================================
    // MÓDULO VENDAS
    // =========================================================================

    private function indexarVendas(): void
    {
        $this->log('🛒 Indexando: Vendas...');

        $docs = [
            [
                'titulo'   => 'Pedidos de Venda - fluxo completo',
                'conteudo' => <<<TEXT
Fluxo completo de um Pedido de Venda no Nexora ERP:

1. Criação: Vendas > Pedidos > Novo Pedido
   - Selecione o cliente
   - Adicione produtos com quantidade e preço
   - Informe condição de pagamento e prazo de entrega

2. Aprovação: Pedido passa por fluxo de aprovação (se configurado)
   - Status: Rascunho → Aprovado

3. Faturamento: Gera NF-e automaticamente
   - Vendas > Pedidos > Faturar
   - O sistema pré-preenche a NF-e com dados do pedido

4. Expedição: Separa itens do estoque
   - Estoque é debitado

5. Entrega: Marcação da entrega realizada

Status do pedido:
- draft: Rascunho
- approved: Aprovado
- invoiced: Faturado
- shipped: Expedido
- delivered: Entregue
- cancelled: Cancelado
TEXT,
            ],
            [
                'titulo'   => 'Tabela de Preços - configuração',
                'conteudo' => <<<TEXT
As Tabelas de Preços no Nexora ERP permitem diferentes preços por cliente/grupo.

Acesso: Vendas > Tabela de Preços

Como criar:
1. Clique em Nova Tabela
2. Defina nome e descrição
3. Adicione produtos com preços específicos
4. Defina vigência (data início e fim)

Vincular tabela ao cliente:
1. Cadastro > Clientes > [cliente] > Aba Comercial
2. Selecione a Tabela de Preços padrão do cliente

Na criação do pedido, o sistema aplica automaticamente a tabela do cliente.
Caso não haja tabela, usa o preço de venda padrão do produto.
TEXT,
            ],
            [
                'titulo'   => 'Precificação de produtos',
                'conteudo' => <<<TEXT
Como configurar preços de produtos no Nexora ERP:

Preço de venda padrão: Cadastro > Produtos > [produto] > Campo "Preço de Venda"

Precificação automática:
1. Acesse Vendas > Precificação
2. Defina margem de lucro por categoria ou produto
3. O sistema calcula preço baseado no custo de aquisição + margem

Tipos de preço:
- Preço de Custo: valor de aquisição do produto
- Preço de Venda: preço padrão ao cliente
- Preço Mínimo: limite mínimo para vendas (sem perda)
- Tabela de Preços: preços específicos por cliente/grupo

Atualização em lote:
Vendas > Precificação > Atualização em Lote (reajuste percentual por categoria)
TEXT,
            ],
        ];

        foreach ($docs as $doc) {
            $this->store($doc['titulo'], $doc['conteudo'], 'modulo', 'vendas');
        }
    }

    // =========================================================================
    // MÓDULO COMPRAS
    // =========================================================================

    private function indexarCompras(): void
    {
        $this->log('🏪 Indexando: Compras...');

        $docs = [
            [
                'titulo'   => 'Pedido de Compra - fluxo completo',
                'conteudo' => <<<TEXT
Fluxo de um Pedido de Compra no Nexora ERP:

1. Requisição: Compras > Requisições > Nova Requisição
   - Informe produto, quantidade necessária e justificativa

2. Cotação: Compras > Cotações
   - Envie a cotação para múltiplos fornecedores
   - Compare propostas recebidas
   - Selecione o melhor fornecedor

3. Pedido de Compra: Compras > Pedidos > Novo Pedido
   - Pode ser criado manualmente ou a partir de uma cotação aprovada
   - Informe fornecedor, produtos, preços, prazo de entrega

4. Recebimento: Após chegada da mercadoria
   - Compras > Pedidos > Receber
   - Confira itens e quantidades
   - O sistema registra entrada no estoque automaticamente
   - Vincula à NF-e de entrada (Fiscal > NF-e Entrada)

Status do Pedido de Compra:
- pending: Pendente/Aguardando
- approved: Aprovado
- partially_received: Recebido Parcialmente
- received: Recebido/Concluído
- cancelled: Cancelado
TEXT,
            ],
            [
                'titulo'   => 'Fornecedores - cadastro e gestão',
                'conteudo' => <<<TEXT
Cadastro e gestão de Fornecedores no Nexora ERP:

Cadastrar fornecedor:
1. Cadastro > Fornecedores > Novo Fornecedor
2. Informe: CNPJ ou CPF, Razão Social, Endereço, Contatos
3. Na aba Financeiro: defina condições de pagamento padrão

Dados obrigatórios para emissão de NF-e:
- CNPJ válido (ou CPF para pessoa física)
- Razão Social
- Endereço completo com CEP

Histórico de compras:
Cadastro > Fornecedores > [fornecedor] > Aba Pedidos

Avaliação de fornecedores:
Compras > Fornecedores > Avaliação (qualidade, prazo, preço)
TEXT,
            ],
        ];

        foreach ($docs as $doc) {
            $this->store($doc['titulo'], $doc['conteudo'], 'modulo', 'compras');
        }
    }

    // =========================================================================
    // MÓDULO ESTOQUE
    // =========================================================================

    private function indexarEstoque(): void
    {
        $this->log('📦 Indexando: Estoque...');

        $docs = [
            [
                'titulo'   => 'Estoque - movimentação e consulta de saldo',
                'conteudo' => <<<TEXT
Gestão de Estoque no Nexora ERP:

Consultar saldo:
1. Estoque > Movimentação (saldo atual de todos os produtos)
2. Cadastro > Produtos > [produto] > Aba Estoque

Registrar entrada manual:
1. Estoque > Movimentação > Nova Entrada
2. Selecione produto, quantidade, custo unitário
3. Informe motivo: Compra, Devolução de Venda, Ajuste de Inventário

Registrar saída manual:
1. Estoque > Movimentação > Nova Saída
2. Selecione produto e quantidade
3. Informe motivo: Venda, Devolução para Fornecedor, Perda

Transferência entre depósitos/filiais:
1. Estoque > Transferência > Nova Transferência
2. Selecione depósito origem e destino
3. Informe produtos e quantidades

Atenção: Movimentações automáticas ocorrem ao faturar pedidos de venda (saída) e ao confirmar recebimento de compras (entrada).
TEXT,
            ],
            [
                'titulo'   => 'Inventário - contagem e ajuste de estoque',
                'conteudo' => <<<TEXT
Como realizar Inventário no Nexora ERP:

1. Acesse Estoque > Inventário > Novo Inventário
2. Selecione os produtos ou categorias a inventariar
3. Imprima a lista de contagem ou use a versão mobile
4. Informe as quantidades contadas
5. O sistema calcula as diferenças (positivas e negativas)
6. Confirme o ajuste para atualizar o saldo

Tipos de inventário:
- Geral: todos os produtos
- Parcial: por categoria ou localização
- Rotativo: grupos de produtos em rodízio

Após a confirmação, o sistema gera automaticamente os lançamentos de ajuste de estoque (entrada para positivos, saída para negativos) com justificativa "Ajuste de Inventário".
TEXT,
            ],
            [
                'titulo'   => 'Produto sem saldo - diagnóstico',
                'conteudo' => <<<TEXT
Diagnóstico quando produto aparece sem saldo no Nexora ERP:

Verificar movimentações:
1. Estoque > Movimentação > Filtrar pelo produto
2. Verifique todas as entradas e saídas do período

Causas comuns de saldo zerado inesperadamente:
- Pedido de venda faturado debitou mais do que deveria (verifique quantidades)
- Saída manual lançada incorretamente
- Inventário ajustou saldo para zero (verifique histórico de inventários)
- Produto transferido para outro depósito sem registro na origem

Solução para saldo errado:
1. Identifique a movimentação incorreta em Estoque > Movimentação
2. Se possível, cancele a movimentação incorreta
3. Se não puder cancelar, realize um ajuste manual em Estoque > Movimentação > Novo Ajuste
4. Justifique o ajuste como "Correção de Saldo"
TEXT,
            ],
        ];

        foreach ($docs as $doc) {
            $this->store($doc['titulo'], $doc['conteudo'], 'modulo', 'estoque');
        }
    }

    // =========================================================================
    // MÓDULO RH
    // =========================================================================

    private function indexarRh(): void
    {
        $this->log('👥 Indexando: RH / Folha...');

        $docs = [
            [
                'titulo'   => 'Folha de Pagamento - processamento mensal',
                'conteudo' => <<<TEXT
Como processar a Folha de Pagamento no Nexora ERP:

1. Acesse RH > Folha de Pagamento > Nova Folha
2. Selecione a competência (mês/ano)
3. Clique em Calcular para processar todos os funcionários
4. O sistema aplica automaticamente:
   - Salário base da jornada de trabalho
   - Descontos de INSS (tabela progressiva)
   - Desconto de IRRF (tabela progressiva com deduções)
   - FGTS (8% sobre remuneração bruta)
   - Horas extras, faltas e outros eventos da jornada
5. Revise os valores calculados
6. Clique em Fechar Folha para finalizar
7. Gere os holerites em RH > Holerite

Tabela INSS 2026 (por faixa):
- Até R$ 1.518,00: 7,5%
- R$ 1.518,01 a R$ 2.793,88: 9%
- R$ 2.793,89 a R$ 4.190,83: 12%
- R$ 4.190,84 a R$ 8.157,41: 14%
TEXT,
            ],
            [
                'titulo'   => 'Ponto Eletrônico - registro e gestão',
                'conteudo' => <<<TEXT
Gestão de Ponto Eletrônico no Nexora ERP:

Registro de ponto:
1. RH > Ponto > Registros de Ponto
2. Os registros são importados do dispositivo de ponto ou lançados manualmente
3. Para lançamento manual: clique em Novo Registro, informe funcionário, data, hora entrada/saída

Apuração do ponto:
1. RH > Ponto > Apuração
2. Selecione o período e funcionários
3. O sistema calcula: horas trabalhadas, horas extras, faltas, atrasos
4. Banco de horas: créditos e débitos de horas extras

Justificativas de ausência:
1. RH > Ponto > Justificativas
2. Informe: funcionário, data, tipo (falta justificada, atestado, folga compensatória)

Relatórios:
- RH > Ponto > Relatório de Frequência
- RH > Ponto > Banco de Horas
TEXT,
            ],
            [
                'titulo'   => 'Holerite - geração e acesso',
                'conteudo' => <<<TEXT
Como gerar e disponibilizar Holerites no Nexora ERP:

Gerar holerites:
1. Após fechar a folha, acesse RH > Holerite
2. Selecione competência e funcionários (ou todos)
3. Clique em Gerar Holerites
4. Os holerites ficam disponíveis em PDF

Acesso pelo funcionário:
- O funcionário pode acessar o próprio holerite no Portal do Funcionário
- Acesso via login no sistema com permissão "RH - Visualizar próprio holerite"

Componentes do holerite:
- Proventos: salário base, horas extras, adicional noturno, comissões, férias
- Descontos: INSS, IRRF, faltas, empréstimos, plano de saúde
- FGTS (informativo, não é desconto)
- Salário líquido = Total Proventos - Total Descontos
TEXT,
            ],
            [
                'titulo'   => 'Jornada de Trabalho - configuração',
                'conteudo' => <<<TEXT
Configuração de Jornadas de Trabalho no Nexora ERP:

Criar jornada:
1. RH > Jornada > Nova Jornada
2. Informe: nome, horário de entrada, saída e intervalo
3. Defina tolerâncias (em minutos) para atrasos e saídas antecipadas
4. Configure os dias da semana trabalhados
5. Salve

Vincular funcionário à jornada:
1. Cadastro > Funcionários > [funcionário] > Aba Contrato
2. Selecione a Jornada de Trabalho

Jornadas especiais:
- 12x36: trabalho de 12h seguido por 36h de descanso
- Escala: configuração de turnos rotativos
- Flexível: banco de horas sem horário fixo
TEXT,
            ],
            [
                'titulo'   => 'Funcionários - admissão e gestão',
                'conteudo' => <<<TEXT
Cadastro e admissão de funcionários no Nexora ERP:

Novo funcionário:
1. Cadastro > Funcionários > Novo Funcionário
2. Aba Pessoal: nome, CPF, RG, data nascimento, endereço
3. Aba Contrato: data admissão, cargo, salário, jornada, departamento
4. Aba Bancária: banco, agência, conta para depósito do salário

Demissão:
1. Cadastro > Funcionários > [funcionário] > Desligar
2. Informe: data desligamento, tipo (sem justa causa, justa causa, pedido de demissão)
3. O sistema calcula verbas rescisórias automaticamente

Férias:
1. RH > Férias > Nova Programação
2. Selecione funcionário e período de gozo
3. Após aprovação, inclui automaticamente na folha do mês
TEXT,
            ],
        ];

        foreach ($docs as $doc) {
            $this->store($doc['titulo'], $doc['conteudo'], 'modulo', 'rh');
        }
    }

    // =========================================================================
    // MÓDULO TRANSPORTE
    // =========================================================================

    private function indexarTransporte(): void
    {
        $this->log('🚚 Indexando: Transporte / Logística...');

        $docs = [
            [
                'titulo'   => 'Entregas - agendamento e monitoramento',
                'conteudo' => <<<TEXT
Gestão de Entregas no Nexora ERP:

Agendar entrega:
1. Logística > Entregas > Nova Entrega
2. Vincule ao Pedido de Venda ou NF-e
3. Informe: data prevista, endereço de entrega, veículo, motorista
4. Salve

Status da entrega:
- pending: Aguardando
- scheduled: Agendada
- in_route: Em rota
- delivered: Entregue
- failed: Falha na entrega
- cancelled: Cancelada

Monitoramento em tempo real:
1. Logística > Monitoramento
2. Visualize localização e status das entregas ativas
3. Filtre por motorista, veículo ou período

Romaneio de entrega:
1. Logística > Entregas > Gerar Romaneio
2. Agrupa múltiplas entregas do mesmo veículo
3. Imprima o romaneio para o motorista
TEXT,
            ],
            [
                'titulo'   => 'Roteirização - otimização de rotas',
                'conteudo' => <<<TEXT
Roteirização de Entregas no Nexora ERP:

A roteirização organiza a sequência ótima de entregas para minimizar distância e tempo.

Como roteirizar:
1. Logística > Roteirização > Nova Rota
2. Selecione as entregas do dia
3. Clique em Otimizar Rota
4. O sistema organiza a sequência por proximidade geográfica
5. Atribua um veículo e motorista
6. Confirme para criar a rota

Rotas cadastradas:
1. Logística > Rotas
2. Cadastre rotas fixas (percursos que se repetem)
3. Associe veículo padrão e motorista responsável

Janelas de tempo:
1. Logística > Entregas > [entrega] > Janela de Entrega
2. Defina horário mínimo e máximo para a entrega
TEXT,
            ],
            [
                'titulo'   => 'Veículos - cadastro e manutenção',
                'conteudo' => <<<TEXT
Gestão de Frota no Nexora ERP:

Cadastrar veículo:
1. Logística > Veículos > Novo Veículo
2. Informe: placa, modelo, marca, ano, tipo (caminhão, van, moto)
3. Informe capacidade de carga (kg e m³)
4. Vincule ao motorista responsável

Manutenção preventiva:
1. Logística > Veículos > [veículo] > Manutenções
2. Cadastre revisões programadas por km ou data
3. O sistema alertará quando o veículo se aproximar do prazo

Controle de combustível:
1. Logística > Veículos > Abastecimentos
2. Registre cada abastecimento com km, litros e valor
3. Relatório de consumo médio: km/litro por veículo

Veículo inativo: altere o status para "Em manutenção" para não aparecer na atribuição de rotas.
TEXT,
            ],
        ];

        foreach ($docs as $doc) {
            $this->store($doc['titulo'], $doc['conteudo'], 'modulo', 'transporte');
        }
    }

    // =========================================================================
    // MÓDULO PRODUÇÃO
    // =========================================================================

    private function indexarProducao(): void
    {
        $this->log('🏭 Indexando: Produção...');

        $docs = [
            [
                'titulo'   => 'Ordem de Produção - criação e controle',
                'conteudo' => <<<TEXT
Gestão de Ordens de Produção (OP) no Nexora ERP:

Criar Ordem de Produção:
1. Produção > Ordens de Produção > Nova OP
2. Selecione o produto a ser fabricado
3. Informe quantidade e data de entrega prevista
4. O sistema carrega automaticamente a ficha técnica (insumos necessários)
5. Verifique disponibilidade de insumos no estoque
6. Confirme para abrir a OP

Fluxo da OP:
- Aberta: OP criada, aguardando início
- Em Andamento: produção iniciada
- Pausada: produção interrompida temporariamente
- Finalizada: produto acabado registrado no estoque
- Cancelada: OP encerrada sem produção

Ao finalizar a OP:
1. Informe a quantidade produzida real
2. O sistema debita os insumos do estoque (conforme ficha técnica)
3. Credita o produto acabado no estoque
4. Registra variações de consumo (real vs. planejado)
TEXT,
            ],
            [
                'titulo'   => 'Ficha Técnica de Produto - estrutura',
                'conteudo' => <<<TEXT
A Ficha Técnica (ou BOM - Bill of Materials) define os insumos necessários para produzir uma unidade do produto acabado.

Cadastrar ficha técnica:
1. Cadastro > Produtos > [produto acabado] > Aba Ficha Técnica
2. Clique em Adicionar Insumo
3. Selecione o produto/matéria-prima
4. Informe a quantidade necessária por unidade produzida
5. Defina a unidade de medida
6. Repita para todos os insumos
7. Salve

Exemplo: Produto "Mesa de Madeira" precisa de:
- 2,5 m² de madeira compensada
- 4 parafusos M8x50
- 0,2 L de verniz

Ao abrir uma OP para 10 mesas, o sistema reservará:
- 25 m² de madeira
- 40 parafusos
- 2 L de verniz
TEXT,
            ],
        ];

        foreach ($docs as $doc) {
            $this->store($doc['titulo'], $doc['conteudo'], 'modulo', 'producao');
        }
    }

    // =========================================================================
    // CADASTROS MESTRES
    // =========================================================================

    private function indexarCadastros(): void
    {
        $this->log('📁 Indexando: Cadastros...');

        $docs = [
            [
                'titulo'   => 'Clientes - cadastro completo',
                'conteudo' => <<<TEXT
Cadastro de Clientes no Nexora ERP:

Novo cliente:
1. Cadastro > Clientes > Novo Cliente
2. Tipo: Pessoa Física (CPF) ou Pessoa Jurídica (CNPJ)
3. Dados obrigatórios para emissão de NF-e:
   - Nome/Razão Social
   - CPF ou CNPJ válido
   - Endereço completo: CEP, Rua, Número, Bairro, Cidade, UF
4. Dados opcionais: IE (Inscrição Estadual), e-mail, telefone

Aba Comercial:
- Condição de pagamento padrão
- Tabela de preços aplicada
- Limite de crédito
- Vendedor responsável

Consultar CNPJvia Brasil API:
- No campo CNPJ, clique em "Consultar CNPJ"
- O sistema preenche automaticamente Razão Social e Endereço

Clientes inativos: altere o status para "Inativo" para não aparecer nas consultas de venda.
TEXT,
            ],
            [
                'titulo'   => 'Produtos - cadastro e configuração',
                'conteudo' => <<<TEXT
Cadastro de Produtos no Nexora ERP:

Novo produto:
1. Cadastro > Produtos > Novo Produto
2. Aba Principal:
   - Código (gerado automaticamente ou manual)
   - Descrição completa
   - Categoria
   - Unidade de Medida
   - NCM (8 dígitos - obrigatório para NF-e)
   - CFOP padrão

3. Aba Fiscal:
   - CST de ICMS (tabela A para Lucro Real/Presumido, tabela B para Simples)
   - Alíquota ICMS
   - CST PIS e COFINS
   - Alíquota IPI

4. Aba Preços:
   - Preço de Custo
   - Preço de Venda
   - Margem de lucro

5. Aba Estoque:
   - Estoque mínimo (para alertas de reposição)
   - Estoque máximo
   - Localização no depósito

NCM inválido causa Rejeição 702 ou 778 ao emitir NF-e. Sempre use 8 dígitos numéricos.
TEXT,
            ],
        ];

        foreach ($docs as $doc) {
            $this->store($doc['titulo'], $doc['conteudo'], 'modulo', 'cadastros');
        }
    }

    // =========================================================================
    // ADMINISTRAÇÃO
    // =========================================================================

    private function indexarAdministracao(): void
    {
        $this->log('⚙️ Indexando: Administração...');

        $docs = [
            [
                'titulo'   => 'Usuários e Permissões - controle de acesso',
                'conteudo' => <<<TEXT
Controle de Acesso no Nexora ERP:

Criar usuário:
1. Administração > Usuários > Novo Usuário
2. Informe nome, e-mail, senha
3. Atribua um Perfil (conjunto de permissões)

Perfis de acesso (Administração > Usuários > Perfis):
- Administrador: acesso total a todos os módulos
- Financeiro: apenas módulos financeiros
- Vendas: apenas módulos de vendas
- Estoque: apenas módulos de estoque
- Visualizador: somente leitura

Permissões granulares:
- Cada perfil pode ter permissões específicas por módulo
- Ex: "Financeiro - Contas a Pagar" pode ter: Visualizar, Criar, Editar, Excluir, Baixar

Usuário sem acesso a um menu:
1. Administração > Usuários > [usuário] > Permissões
2. Verifique o perfil atribuído
3. Adicione as permissões faltantes ou altere o perfil

Senha esquecida:
- O usuário usa o link "Esqueci minha senha" na tela de login
- Um link de redefinição é enviado para o e-mail cadastrado
TEXT,
            ],
            [
                'titulo'   => 'Empresas - configuração multi-empresa',
                'conteudo' => <<<TEXT
Configuração de Empresas no Nexora ERP:

Dados da empresa (Administração > Empresas):
- Razão Social e Nome Fantasia
- CNPJ (obrigatório, deve ser válido)
- Inscrição Estadual
- Regime Tributário: Simples Nacional, Lucro Presumido ou Lucro Real
- Endereço completo

Configurações fiscais da empresa:
- Série da NF-e (padrão: 1)
- Ambiente: Homologação (testes) ou Produção
- Certificado Digital

Multi-empresa:
- O Nexora ERP suporta múltiplas empresas na mesma instalação
- Cada usuário pode ser vinculado a uma ou mais empresas
- Os dados são segregados por empresa (multi-tenant)

CNPJ inválido causa Rejeição 207 ou 214 ao emitir NF-e.
TEXT,
            ],
            [
                'titulo'   => 'Logs do sistema - auditoria',
                'conteudo' => <<<TEXT
Logs de Auditoria no Nexora ERP:

Acesso: Administração > Logs

Os logs registram todas as ações dos usuários:
- Data e hora da ação
- Usuário que realizou
- Módulo afetado
- Tipo de ação (criar, editar, excluir, login, logout)
- IP de acesso
- Descrição da operação

Filtros disponíveis:
- Por usuário
- Por módulo (Fiscal, Financeiro, Vendas, etc.)
- Por nível (sucesso, aviso, erro)
- Por período

Para diagnóstico de problemas:
1. Filtre logs de "erro" no período do problema
2. Verifique as ações do usuário afetado
3. Identifique qual operação gerou o erro
TEXT,
            ],
        ];

        foreach ($docs as $doc) {
            $this->store($doc['titulo'], $doc['conteudo'], 'modulo', 'administracao');
        }
    }

    // =========================================================================
    // REGRAS DE NEGÓCIO
    // =========================================================================

    private function indexarRegrasNegocio(): void
    {
        $this->log('📜 Indexando: Regras de Negócio...');

        $docs = [
            [
                'titulo'   => 'Regras de negócio - Simples Nacional vs Lucro Presumido',
                'conteudo' => <<<TEXT
Diferenças de configuração fiscal no Nexora ERP:

Simples Nacional:
- CST de ICMS: tabela B (400 a 500)
- CST 400: tributado pelo Simples (sem destaque de ICMS na NF-e)
- CST 500: ICMS cobrado por substituição tributária
- PIS/COFINS: CST 07 (sem alíquota - recolhido no Simples)
- Não destaca IPI na maioria dos casos

Lucro Presumido e Lucro Real:
- CST de ICMS: tabela A (00, 10, 20, 30, 40, 41, 50, 51, 60, 70, 90)
- CST 00: tributação normal, destaca ICMS
- CST 40/41: isento/não tributado
- CST 60: ICMS por substituição tributária
- PIS/COFINS: CST 01 (alíquota 0,65% PIS, 3% COFINS - Lucro Presumido)

Erros comuns:
- Usar CST da tabela B numa empresa de Lucro Presumido causa rejeição
- Alíquotas de ICMS incorretas para o estado causam Rejeição 541
TEXT,
            ],
            [
                'titulo'   => 'Grupos Tributários - configuração de impostos',
                'conteudo' => <<<TEXT
Os Grupos Tributários no Nexora ERP definem as regras de tributação para cada operação.

Acesso: Fiscal > Grupos Tributários

Componentes de um Grupo Tributário:
1. ICMS: CST, alíquota, base de cálculo, MVA (para ST)
2. IPI: CST, alíquota
3. PIS: CST, alíquota
4. COFINS: CST, alíquota

Como vincular ao produto:
Cadastro > Produtos > [produto] > Aba Fiscal > Grupo Tributário

Como vincular ao Tipo de Operação:
Fiscal > Tipos de Operação > [operação] > Grupo Tributário

A tributação final da NF-e usa a combinação de Tipo de Operação + Produto.
Se houver conflito, o Tipo de Operação tem precedência.
TEXT,
            ],
        ];

        foreach ($docs as $doc) {
            $this->store($doc['titulo'], $doc['conteudo'], 'faq', 'fiscal');
        }
    }

    // =========================================================================
    // FLUXOS OPERACIONAIS
    // =========================================================================

    private function indexarFluxosOperacionais(): void
    {
        $this->log('🔄 Indexando: Fluxos Operacionais...');

        $docs = [
            [
                'titulo'   => 'Fluxo completo Venda → NF-e → Estoque → Financeiro',
                'conteudo' => <<<TEXT
Fluxo integrado de uma venda no Nexora ERP:

1. PEDIDO DE VENDA (Vendas > Pedidos > Novo Pedido)
   - Cliente selecionado, produtos adicionados, preços definidos
   - Status: Rascunho → Aprovado

2. FATURAMENTO (Vendas > Pedidos > Faturar)
   - Gera NF-e automaticamente (Fiscal > NF-e)
   - Pré-preenche cliente, produtos, CFOP, impostos
   - Ao autorizar a NF-e: estoque é debitado automaticamente

3. FINANCEIRO (automático)
   - Cria título em Contas a Receber com vencimento conforme condição de pagamento
   - Aparece em Financeiro > Contas a Receber

4. ENTREGA (Logística > Entregas)
   - Agendamento e monitoramento da entrega

5. RECEBIMENTO FINANCEIRO (Financeiro > Contas a Receber > Baixar)
   - Registra o pagamento do cliente

Integração: qualquer problema em uma etapa pode bloquear as próximas. Ex: cliente sem CNPJ impede a emissão da NF-e.
TEXT,
            ],
            [
                'titulo'   => 'Fluxo Compra → Recebimento → Estoque → Financeiro',
                'conteudo' => <<<TEXT
Fluxo integrado de uma compra no Nexora ERP:

1. PEDIDO DE COMPRA (Compras > Pedidos > Novo Pedido)
   - Fornecedor, produtos, preços, prazo de entrega

2. RECEBIMENTO (Compras > Pedidos > Receber)
   - Confere mercadorias fisicamente
   - Cria entrada de estoque automaticamente
   - Vincula NF-e de entrada: Fiscal > NF-e Entrada > Importar XML

3. FISCAL (Fiscal > NF-e Entrada)
   - Importa XML do fornecedor
   - Verifica tributos para aproveitamento de crédito (ICMS, IPI, PIS/COFINS)

4. FINANCEIRO (automático)
   - Cria título em Contas a Pagar com vencimento conforme condição com fornecedor
   - Aparece em Financeiro > Contas a Pagar

5. PAGAMENTO (Financeiro > Contas a Pagar > Baixar)
   - Registra o pagamento ao fornecedor
TEXT,
            ],
        ];

        foreach ($docs as $doc) {
            $this->store($doc['titulo'], $doc['conteudo'], 'faq', null);
        }
    }

    // =========================================================================
    // ERROS COMUNS
    // =========================================================================

    private function indexarErrosComuns(): void
    {
        $this->log('🐛 Indexando: Erros Comuns...');

        $docs = [
            [
                'titulo'   => 'Erro ao salvar pedido - campos obrigatórios',
                'conteudo' => <<<TEXT
Erros comuns ao salvar Pedido de Venda no Nexora ERP:

"Cliente não informado":
- Solução: Selecione o cliente no campo obrigatório

"Produto sem preço":
- Solução: Cadastro > Produtos > [produto] > Defina o Preço de Venda

"Estoque insuficiente":
- Solução: Verifique o saldo em Estoque > Movimentação antes de criar o pedido
- Se necessário, registre uma entrada de estoque primeiro

"Condição de pagamento não informada":
- Solução: Selecione a condição de pagamento no pedido ou configure o padrão do cliente

"CFOP não configurado":
- Solução: Fiscal > Tipos de Operação > Configure o CFOP para a operação
TEXT,
            ],
            [
                'titulo'   => 'Erro de login - acesso ao sistema',
                'conteudo' => <<<TEXT
Problemas de login no Nexora ERP:

Senha incorreta:
- Use o link "Esqueci minha senha" na tela de login
- Um e-mail com link de redefinição será enviado

Conta bloqueada:
- Após 5 tentativas falhas, a conta é bloqueada temporariamente
- Aguarde 15 minutos ou contate o administrador
- Admin: Administração > Usuários > [usuário] > Desbloquear

Sem permissão para acessar módulo:
- Contate o administrador do sistema
- Admin: Administração > Usuários > [usuário] > Permissões

E-mail não encontrado:
- Verifique se está usando o e-mail correto cadastrado
- Admin pode verificar em Administração > Usuários

Licença expirada:
- O sistema exibirá aviso de licença vencida
- Contate o suporte comercial Nexora: (32) 98450-2345
TEXT,
            ],
            [
                'titulo'   => 'Nota fiscal rejeitada - diagnóstico geral',
                'conteudo' => <<<TEXT
Diagnóstico geral para NF-e rejeitada no Nexora ERP:

Passos para diagnosticar qualquer rejeição:

1. Acesse Fiscal > NF-e
2. Localize a nota rejeitada (status "rejected")
3. Clique na nota para ver detalhes
4. Verifique o campo "Mensagem SEFAZ" - contém o código e descrição da rejeição
5. Anote o código numérico (ex: 702, 778, 203)

Códigos mais frequentes e solução rápida:
- 203: Empresa não habilitada → Fiscal > Configurações
- 204: Número duplicado → Fiscal > Configurações > Numeração
- 215/539: CPF/CNPJ inválido → Cadastro > Clientes
- 702/778: NCM inválido → Cadastro > Produtos > NCM
- 741: CFOP inválido → Fiscal > Tipos de Operação
- 805: Erro de XML (erro interno) → Contate o suporte

Após corrigir a causa:
1. Retorne à nota rejeitada em Fiscal > NF-e
2. Clique em "Editar" para corrigir os dados
3. Clique em "Reenviar" para submeter novamente à SEFAZ
TEXT,
            ],
            [
                'titulo'   => 'Suporte Nexora - canais de atendimento',
                'conteudo' => <<<TEXT
Canais de Suporte do Nexora ERP:

Chat IA (este canal):
- Disponível 24h para diagnósticos automáticos
- Acesse Suporte > Chat IA para iniciar conversa

WhatsApp (suporte humano urgente):
- Número: (32) 98450-2345
- Horário: Seg-Sex 08h-18h

Abertura de ticket:
- Acesse Suporte > Tickets > Novo Ticket
- Descreva o problema com detalhes: módulo, ação, erro exato
- Acompanhe o status do ticket em Suporte > Tickets

Para problemas fiscais urgentes (NF-e rejeitada), use o WhatsApp para atendimento prioritário.

Informações para agilizar o atendimento:
- Código de rejeição SEFAZ (se fiscal)
- Print da tela com o erro
- Número do pedido, nota ou registro afetado
- Módulo onde ocorreu o problema
TEXT,
            ],
        ];

        foreach ($docs as $doc) {
            $this->store($doc['titulo'], $doc['conteudo'], 'faq', null);
        }
    }

    // =========================================================================
    // Helper
    // =========================================================================

    private function store(string $titulo, string $conteudo, string $fonte, ?string $categoria): void
    {
        try {
            // Usa storeTextOnly() para indexação rápida sem chamar OpenAI.
            // Os embeddings vetoriais podem ser gerados depois via: php artisan ai:vectorize
            $doc = $this->rag->storeTextOnly($titulo, $conteudo, $fonte, $categoria);
            if ($doc) {
                $this->total++;
            } else {
                $this->errors++;
            }
        } catch (\Throwable $e) {
            $this->errors++;
            Log::error("RagKnowledgeSeeder: falha ao indexar '{$titulo}'", ['error' => $e->getMessage()]);
        }
    }
}





