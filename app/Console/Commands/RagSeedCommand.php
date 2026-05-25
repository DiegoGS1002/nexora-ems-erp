<?php

namespace App\Console\Commands;

use App\Ai\RagService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Popula a base vetorial RAG com documentos da base de conhecimento do Nexora ERP.
 *
 * Uso:
 *   php artisan rag:seed               # indexa tudo
 *   php artisan rag:seed --fonte=faq   # apenas FAQs
 *   php artisan rag:seed --fresh       # limpa antes de indexar
 */
class RagSeedCommand extends Command
{
    protected $signature = 'rag:seed
                            {--fonte= : Filtra por fonte específica (manual|faq|modulo|produto)}
                            {--fresh  : Remove documentos existentes antes de indexar}';

    protected $description = 'Popula a base vetorial RAG com a base de conhecimento do Nexora ERP';

    public function __construct(private RagService $rag)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        if (! config('openai.api_key')) {
            $this->error('OPENAI_API_KEY não configurada. Configure no .env para usar o RAG.');
            return self::FAILURE;
        }

        $fonte = $this->option('fonte');

        if ($this->option('fresh')) {
            $this->warn('Removendo documentos existentes...');
            $deleted = $fonte
                ? $this->rag->deleteByFonte($fonte)
                : \App\Models\RagDocument::query()->delete();
            $this->line("  → {$deleted} documento(s) removido(s).");
        }

        $this->info('Iniciando indexação da base de conhecimento do Nexora ERP...');
        $this->newLine();

        $total = 0;

        if (! $fonte || $fonte === 'modulo') {
            $total += $this->indexPrompts();
        }

        if (! $fonte || $fonte === 'faq') {
            $total += $this->indexFaqs();
        }

        if (! $fonte || $fonte === 'regras') {
            $total += $this->indexBusinessRules();
        }

        if (! $fonte || $fonte === 'sefaz') {
            $total += $this->indexSefazRejections();
        }

        $this->newLine();
        $this->info("✅ Indexação concluída. {$total} documento(s) armazenado(s).");

        return self::SUCCESS;
    }

    // -------------------------------------------------------------------------
    // Fontes de conhecimento
    // -------------------------------------------------------------------------

    private function indexPrompts(): int
    {
        $promptsPath = base_path('prompts');

        if (! File::isDirectory($promptsPath)) {
            $this->warn("Diretório /prompts não encontrado — pulando.");
            return 0;
        }

        $files = File::files($promptsPath);
        $count = 0;

        $this->line('<fg=cyan>Indexando prompts/documentação...</>');

        foreach ($files as $file) {
            if ($file->getExtension() !== 'md') {
                continue;
            }

            $titulo   = $this->titleFromFilename($file->getFilenameWithoutExtension());
            $conteudo = File::get($file->getPathname());

            if (mb_strlen($conteudo) < 50) {
                continue;
            }

            $this->line("  → {$titulo}");

            $docs = $this->rag->storeSplit(
                titulo:    $titulo,
                conteudo:  $conteudo,
                fonte:     'modulo',
                categoria: $this->categoriaFromFilename($file->getFilenameWithoutExtension()),
                metadados: ['arquivo' => $file->getFilename()],
            );

            $count += count($docs);
        }

        $this->line("  <fg=green>{$count} chunk(s) indexado(s) de prompts.</>");
        return $count;
    }

    private function indexFaqs(): int
    {
        $this->line('<fg=cyan>Indexando FAQs dos módulos...</>');

        $faqs  = $this->getFaqs();
        $count = 0;

        foreach ($faqs as $faq) {
            $this->line("  → {$faq['titulo']}");

            $doc = $this->rag->store(
                titulo:    $faq['titulo'],
                conteudo:  $faq['conteudo'],
                fonte:     'faq',
                categoria: $faq['categoria'] ?? null,
                metadados: $faq['meta'] ?? [],
            );

            if ($doc) {
                $count++;
            }
        }

        $this->line("  <fg=green>{$count} FAQ(s) indexado(s).</>");
        return $count;
    }

    private function indexBusinessRules(): int
    {
        $this->line('<fg=cyan>Indexando regras de negócio...</>');

        $rules = $this->getBusinessRules();
        $count = 0;

        foreach ($rules as $rule) {
            $this->line("  → {$rule['titulo']}");

            $docs = $this->rag->storeSplit(
                titulo:    $rule['titulo'],
                conteudo:  $rule['conteudo'],
                fonte:     'regras',
                categoria: $rule['categoria'] ?? null,
                metadados: $rule['meta'] ?? [],
            );

            $count += count($docs);
        }

        $this->line("  <fg=green>{$count} chunk(s) de regras indexado(s).</>");
        return $count;
    }

    private function indexSefazRejections(): int
    {
        $this->line('<fg=cyan>Indexando rejeições SEFAZ...</>');

        $rejections = $this->getSefazRejections();
        $count      = 0;

        foreach ($rejections as $rej) {
            $this->line("  → Rejeição {$rej['codigo']}");

            $doc = $this->rag->store(
                titulo:    "Rejeição SEFAZ {$rej['codigo']} — {$rej['titulo']}",
                conteudo:  $this->formatRejection($rej),
                fonte:     'sefaz',
                categoria: 'fiscal',
                metadados: ['codigo' => $rej['codigo'], 'tipo' => 'rejeicao_sefaz'],
            );

            if ($doc) {
                $count++;
            }
        }

        $this->line("  <fg=green>{$count} rejeição(ões) SEFAZ indexada(s).</>");
        return $count;
    }

    // -------------------------------------------------------------------------
    // FAQs completas por módulo
    // -------------------------------------------------------------------------

    private function getFaqs(): array
    {
        return [
            // ── FISCAL ──────────────────────────────────────────────────────
            [
                'titulo'    => 'Como emitir uma NF-e no Nexora ERP',
                'categoria' => 'fiscal',
                'conteudo'  => <<<TEXT
Para emitir uma NF-e no Nexora ERP, siga o passo a passo:
1. Acesse Fiscal > NF-e e clique em "Nova Nota Fiscal".
2. Selecione o cliente — certifique-se que CPF/CNPJ e endereço completo com CEP estão preenchidos.
3. Selecione o Tipo de Operação fiscal (CFOP correto: 5xxx para saídas dentro do estado, 6xxx para interestaduais).
4. Adicione os produtos com NCM (8 dígitos), CFOP, CST, alíquotas de ICMS, IPI, PIS e COFINS.
5. Revise os totais de impostos e clique em "Autorizar".
6. O status mudará para "authorized" após aprovação da SEFAZ.

Pré-requisitos obrigatórios:
- Certificado digital A1 (.pfx) válido em Fiscal > Configurações
- Cliente com CPF/CNPJ válido + endereço com CEP
- Produto com NCM de 8 dígitos válido na tabela TIPI vigente
- Empresa com CNPJ e Inscrição Estadual cadastrados em Administração > Empresas
- Ambiente definido como "Produção" (não Homologação) para notas válidas

Caso a nota seja rejeitada, verifique o campo "Mensagem SEFAZ" para o código de rejeição.
TEXT,
            ],
            [
                'titulo'    => 'Como cancelar uma NF-e autorizada',
                'categoria' => 'fiscal',
                'conteudo'  => <<<TEXT
Para cancelar uma NF-e autorizada no Nexora ERP:
1. Acesse Fiscal > NF-e e localize a nota com status "authorized".
2. Clique em "Cancelar" e informe a justificativa (mínimo 15 caracteres obrigatório pela SEFAZ).
3. O sistema enviará o evento de cancelamento à SEFAZ automaticamente.
4. O prazo máximo para cancelamento é 24 horas após a autorização (regra SEFAZ para NF-e).
5. Para NFC-e (nota do consumidor) o prazo é de 30 minutos.

Notas com status "rejected", "draft" ou já "cancelled" não podem ser canceladas.
Notas com mais de 24 horas podem ser inutilizadas ou corrigidas por Carta de Correção (CC-e), dependendo do tipo de correção necessária.

Carta de Correção: Para corrigir dados que não alteram valores ou destinatário, acesse a nota e clique em "Carta de Correção".
TEXT,
            ],
            [
                'titulo'    => 'Como importar XML de NF-e de entrada',
                'categoria' => 'fiscal',
                'conteudo'  => <<<TEXT
Para registrar a entrada de uma NF-e via XML no Nexora ERP:
1. Acesse Fiscal > NF-e Entrada > Importar XML.
2. Selecione o arquivo XML da nota (formato .xml da SEFAZ ou NF-e).
3. O sistema fará a leitura automática dos dados: fornecedor, produtos, impostos, valores.
4. Revise as informações importadas, especialmente os produtos (vinculação com cadastro interno).
5. Clique em "Confirmar Entrada".
6. O estoque dos produtos será atualizado automaticamente.
7. Um título de Contas a Pagar será gerado automaticamente para o fornecedor.

Para baixar XMLs da SEFAZ: acesse o portal NFe da SEFAZ do seu estado com o certificado digital.
TEXT,
            ],
            [
                'titulo'    => 'Como configurar o certificado digital para emissão de NF-e',
                'categoria' => 'fiscal',
                'conteudo'  => <<<TEXT
Para configurar o certificado digital no Nexora ERP:
1. Acesse Fiscal > Configurações > aba Certificado Digital.
2. Clique em "Carregar Certificado" e selecione o arquivo .pfx (certificado A1).
3. Informe a senha do certificado.
4. Clique em "Salvar e Validar".
5. O sistema verificará a validade e exibirá a data de vencimento.

Verificações importantes:
- O CNPJ do certificado DEVE ser o mesmo da empresa emitente cadastrada em Administração > Empresas
- O certificado deve estar dentro do prazo de validade
- Tipo A1: arquivo .pfx (armazenado no sistema) — tipo mais comum
- Tipo A3: token USB ou smartcard — requer driver instalado no servidor

Após configurar, defina o Ambiente: "Homologação" para testes ou "Produção" para notas fiscais válidas.
Nunca emita notas reais em ambiente de Homologação — elas não têm validade jurídica.
TEXT,
            ],
            [
                'titulo'    => 'Configurar Tipos de Operação Fiscal (CFOP)',
                'categoria' => 'fiscal',
                'conteudo'  => <<<TEXT
Os Tipos de Operação definem o CFOP e as regras tributárias para cada tipo de venda/compra.

Para criar ou editar um Tipo de Operação:
1. Acesse Fiscal > Tipos de Operação > Novo Tipo.
2. Informe: Nome, CFOP, Natureza da Operação, Finalidade.
3. Configure os impostos padrão: ICMS (CST/CSOSN, alíquota), IPI, PIS, COFINS.
4. Salve.

CFOPs mais usados para saídas:
- 5102: Venda de mercadoria dentro do estado (Simples Nacional)
- 5101: Venda de produção própria dentro do estado
- 6102: Venda de mercadoria para outro estado
- 5411/6411: Venda com substituição tributária (ICMS ST)
- 5201/6201: Devolução de compra

CFOPs mais usados para entradas:
- 1102/2102: Compra para comercialização
- 1202/2202: Devolução de venda
- 1556/2556: Compra de material de uso e consumo
TEXT,
            ],
            // ── FINANCEIRO ──────────────────────────────────────────────────
            [
                'titulo'    => 'Como registrar um pagamento de conta a pagar',
                'categoria' => 'financeiro',
                'conteudo'  => <<<TEXT
Para registrar pagamento de uma conta a pagar no Nexora ERP:
1. Acesse Financeiro > Contas a Pagar.
2. Localize a conta pelo fornecedor, vencimento ou número do documento.
3. Clique no título e depois em "Registrar Pagamento".
4. Informe: Data do pagamento, Valor pago, Conta bancária de débito, Forma de pagamento.
5. Clique em "Confirmar Pagamento".
6. O status mudará para "paid" e o Fluxo de Caixa será atualizado automaticamente.

Para pagamento parcial: informe um valor menor que o total — o sistema criará um saldo residual.
Para pagamento com desconto: informe o valor com desconto e registre a diferença como "Desconto obtido".
Para juros/multa: informe o valor total com acréscimos.
TEXT,
            ],
            [
                'titulo'    => 'Como registrar recebimento em Contas a Receber',
                'categoria' => 'financeiro',
                'conteudo'  => <<<TEXT
Para registrar o recebimento de uma conta:
1. Acesse Financeiro > Contas a Receber.
2. Localize o título pelo cliente, vencimento ou número.
3. Clique em "Registrar Recebimento".
4. Informe: Data, Valor recebido, Conta bancária de crédito, Forma de pagamento.
5. Confirme. O status muda para "received" e o Fluxo de Caixa é atualizado.

Títulos vencidos: o sistema calcula juros/multa automaticamente se configurado.
Recebimento parcial: o sistema mantém o saldo em aberto.
Baixa automática: pedidos de venda aprovados geram título automaticamente.
TEXT,
            ],
            [
                'titulo'    => 'Como criar e estruturar o Plano de Contas',
                'categoria' => 'financeiro',
                'conteudo'  => <<<TEXT
O Plano de Contas estrutura todas as contas contábeis da empresa.

Para criar uma conta:
1. Acesse Financeiro > Plano de Contas > Nova Conta.
2. Defina: Código, Nome, Tipo (Receita, Despesa, Ativo, Passivo, etc.), Conta pai (para hierarquia).
3. Marque se é conta analítica (lança valores) ou sintética (agrupador).

Estrutura padrão sugerida:
1. ATIVO (contas de ativo)
2. PASSIVO (contas de passivo)
3. PATRIMÔNIO LÍQUIDO
4. RECEITAS
  4.1 Receita de Vendas
  4.2 Outras Receitas
5. CUSTOS
  5.1 Custo de Mercadorias Vendidas (CMV)
6. DESPESAS
  6.1 Despesas Operacionais
  6.2 Despesas Administrativas
  6.3 Despesas Financeiras

Contas analíticas são usadas nos lançamentos de Contas a Pagar/Receber para classificação contábil.
TEXT,
            ],
            // ── RH ──────────────────────────────────────────────────────────
            [
                'titulo'    => 'Como fechar a folha de pagamento',
                'categoria' => 'rh',
                'conteudo'  => <<<TEXT
Para fechar a folha de pagamento no Nexora ERP:
1. Acesse RH > Folha de Pagamento > Nova Folha.
2. Selecione a competência (mês/ano) e os funcionários ativos.
3. O sistema calcula automaticamente:
   - Salário base + adicionais configurados
   - INSS (tabela vigente por faixa salarial)
   - IRRF (tabela progressiva menos deduções legais)
   - FGTS (8% sobre remuneração bruta)
   - Horas extras (50% ou 100% conforme jornada)
   - Faltas e descontos
4. Revise os cálculos individuais de cada funcionário.
5. Clique em "Fechar Folha" — após isso não é possível editar.
6. Os holerites ficam disponíveis em RH > Holerite para download em PDF.

Importante: verifique o registro de ponto em RH > Controle de Ponto antes de fechar a folha para garantir que as horas extras e faltas estejam corretas.
TEXT,
            ],
            [
                'titulo'    => 'Como registrar e corrigir batidas de ponto',
                'categoria' => 'rh',
                'conteudo'  => <<<TEXT
Para o controle de ponto no Nexora ERP:

Registro manual de ponto:
1. Acesse RH > Controle de Ponto > Novo Registro.
2. Selecione o funcionário, data, hora de entrada e saída.
3. Informe o tipo: Normal, Extra, Falta, Afastamento.

Correção de batida:
1. Localize o registro em RH > Controle de Ponto.
2. Clique em "Editar" e ajuste os horários.
3. Salve com justificativa (obrigatória para auditoria).

Cálculo automático:
- Horas trabalhadas acima da jornada = horas extras
- Horas abaixo da jornada = atraso/falta proporcional
- A jornada de referência é configurada em RH > Jornada de Trabalho por funcionário

Banco de horas:
- Se habilitado em RH > Configurações, as horas extras vão para o banco em vez de pagamento
- O saldo pode ser consultado no perfil do funcionário
TEXT,
            ],
            [
                'titulo'    => 'Como calcular e gerar o holerite',
                'categoria' => 'rh',
                'conteudo'  => <<<TEXT
O holerite é gerado automaticamente ao fechar a folha de pagamento.

Para visualizar e baixar holerites:
1. Acesse RH > Holerite.
2. Filtre por competência (mês/ano) e funcionário.
3. Clique em "Baixar PDF" para impressão ou envio ao funcionário.

Composição do holerite (proventos e descontos):
Proventos: Salário Base, Horas Extras, Adicional Noturno, Comissões, 13º Salário (se competência), Férias
Descontos: INSS, IRRF, Vale Transporte (6% do salário), Vale Alimentação, Faltas, Pensão Alimentícia

Tabela INSS 2025 (referência):
- Até R$ 1.518,00: 7,5%
- De R$ 1.518,01 a R$ 2.793,88: 9%
- De R$ 2.793,89 a R$ 4.190,83: 12%
- De R$ 4.190,84 a R$ 8.157,41: 14%
TEXT,
            ],
            // ── ESTOQUE ─────────────────────────────────────────────────────
            [
                'titulo'    => 'Como fazer um lançamento de entrada no estoque',
                'categoria' => 'estoque',
                'conteudo'  => <<<TEXT
Para lançar uma entrada de estoque manualmente:
1. Acesse Estoque > Movimentação > Nova Movimentação.
2. Selecione o tipo "Entrada".
3. Informe o produto, quantidade, custo unitário.
4. Selecione o motivo: Compra, Devolução de Cliente, Ajuste de Inventário, Produção.
5. Salve — o saldo do produto é atualizado imediatamente.

Para entradas vinculadas a NF-e de compra: use Fiscal > NF-e Entrada (o estoque é atualizado automaticamente ao confirmar a entrada).

Para conferir o saldo: acesse Cadastro > Produtos e veja o campo "Saldo em Estoque" ou acesse Estoque > Movimentação e filtre por produto.
TEXT,
            ],
            [
                'titulo'    => 'Como realizar inventário de estoque',
                'categoria' => 'estoque',
                'conteudo'  => <<<TEXT
Para realizar o inventário no Nexora ERP:
1. Acesse Estoque > Inventário > Novo Inventário.
2. O sistema lista todos os produtos com o saldo atual do sistema.
3. Preencha a coluna "Quantidade Física" com a contagem real.
4. O sistema calcula automaticamente a diferença (sobra ou falta).
5. Clique em "Aplicar Ajustes" para atualizar os saldos.
6. O ajuste gera uma movimentação de "Ajuste de Inventário" para rastreabilidade.

Boas práticas:
- Realize inventário com a loja/operação pausada (sem vendas ou compras simultâneas)
- Faça inventário parcial por categoria (curva ABC) para agilidade
- Produtos com estoque zerado ou negativo devem ser verificados primeiro
TEXT,
            ],
            // ── VENDAS ──────────────────────────────────────────────────────
            [
                'titulo'    => 'Como criar e gerenciar pedidos de venda',
                'categoria' => 'vendas',
                'conteudo'  => <<<TEXT
Para criar um pedido de venda no Nexora ERP:
1. Acesse Vendas > Pedidos > Novo Pedido.
2. Selecione o cliente (deve ter CPF/CNPJ cadastrado).
3. Adicione os produtos, informando quantidade e verificando o preço (tabela de preços ou manual).
4. Defina: desconto (se autorizado), condição de pagamento, endereço de entrega.
5. Clique em "Salvar Rascunho" ou "Aprovar Pedido".

Fluxo de status:
Rascunho → Aprovado → Faturado → Em Separação → Entregue
ou
Rascunho → Cancelado (a qualquer momento antes de faturar)

Ao Aprovar:
- Estoque é reservado automaticamente
- Título de Contas a Receber é gerado automaticamente (se configurado)

Ao Faturar:
- Geração automática de NF-e (se módulo fiscal habilitado)
- Estoque é baixado definitivamente

Desconto máximo: configurável por perfil de acesso em Administração > Configurações > Vendas.
TEXT,
            ],
            [
                'titulo'    => 'Como configurar tabelas de precificação',
                'categoria' => 'vendas',
                'conteudo'  => <<<TEXT
As tabelas de precificação permitem preços diferenciados por cliente ou grupo de clientes.

Para criar uma tabela de preços:
1. Acesse Vendas > Precificação > Nova Tabela.
2. Informe o nome (ex: "Tabela Atacado", "Tabela VIP").
3. Adicione os produtos com o preço específico ou percentual de desconto sobre o preço padrão.
4. Vincule a tabela ao cliente em Cadastro > Clientes (campo "Tabela de Preço").

Regras de precificação:
- Markup: preço de custo × (1 + margem). Ex: custo R$100 com 40% margem = R$140 de venda
- Desconto máximo por usuário: definido em Administração > Usuários > Permissões (campo "Desconto Máximo %")
- Tabela padrão é usada quando o cliente não tem tabela específica
TEXT,
            ],
            // ── COMPRAS ─────────────────────────────────────────────────────
            [
                'titulo'    => 'Fluxo completo de compras no Nexora ERP',
                'categoria' => 'compras',
                'conteudo'  => <<<TEXT
O fluxo de compras no Nexora ERP:

1. Solicitação de Compra: Acesse Compras > Solicitações > Nova Solicitação.
   - Informe o produto, quantidade necessária, data necessária
   - Aguarde aprovação do responsável

2. Cotação: Após aprovação, acesse Compras > Cotações > Nova Cotação.
   - Vincule a solicitação aprovada
   - Adicione os fornecedores a cotar
   - Informe os preços recebidos de cada fornecedor
   - O sistema compara e indica a melhor oferta

3. Pedido de Compra: Aprovada a cotação, acesse Compras > Pedidos > Novo Pedido.
   - Vincule a cotação aprovada ou preencha manualmente
   - Informe condição de pagamento e prazo de entrega

4. Recebimento: Quando a mercadoria chegar, acesse o pedido e clique em "Receber".
   - O estoque é atualizado automaticamente
   - Um título de Contas a Pagar é gerado para o fornecedor

5. Vincule a NF-e do fornecedor: importe o XML em Fiscal > NF-e Entrada para escrituração fiscal.
TEXT,
            ],
            // ── PRODUÇÃO ────────────────────────────────────────────────────
            [
                'titulo'    => 'Como criar e gerenciar Ordens de Produção',
                'categoria' => 'producao',
                'conteudo'  => <<<TEXT
Para criar uma Ordem de Produção (OP) no Nexora ERP:
1. Acesse Produção > Ordens de Produção > Nova OP.
2. Selecione o produto a ser produzido e a quantidade.
3. O sistema carrega automaticamente a Ficha Técnica (componentes necessários).
4. Verifique a disponibilidade de matéria-prima no estoque.
5. Clique em "Abrir Ordem".

Apontamento de Produção:
1. Abra a OP e clique em "Apontar Produção".
2. Informe a quantidade produzida e a data.
3. O sistema baixa automaticamente a matéria-prima do estoque conforme a ficha técnica.
4. Insira o produto acabado no estoque automaticamente.

Ficha Técnica:
- Acesse Produção > Fichas Técnicas > Nova Ficha
- Vincule ao produto acabado
- Adicione os componentes (matéria-prima) com as quantidades necessárias por unidade produzida

Status da OP: Aberta → Em Produção → Concluída → Cancelada
TEXT,
            ],
            // ── LOGÍSTICA ───────────────────────────────────────────────────
            [
                'titulo'    => 'Como criar roteiros e agendar entregas',
                'categoria' => 'logistica',
                'conteudo'  => <<<TEXT
Para agendar e roteirizar entregas no Nexora ERP:

Agendamento de Entrega:
1. Acesse Logística > Agendamento de Entregas > Novo Agendamento.
2. Selecione os pedidos de venda aprovados/faturados.
3. Defina o veículo, motorista e data da entrega.
4. Salve o agendamento.

Roteirização (otimização de rotas):
1. Acesse Logística > Roteirização.
2. Selecione os agendamentos do dia.
3. Clique em "Roteirizar" — o sistema usa o endereço dos clientes para otimizar a rota.
4. A rota sugerida pode ser ajustada manualmente.
5. Imprima o Romaneio para o motorista.

Romaneio:
1. Acesse Logística > Romaneio.
2. Selecione o veículo e data.
3. O romaneio lista todos os pedidos/volumes a entregar.
4. Download em PDF para o motorista.

Monitoramento de Entregas: acompanhe o status em Logística > Monitoramento de Entregas.
TEXT,
            ],
            // ── ADMINISTRAÇÃO ───────────────────────────────────────────────
            [
                'titulo'    => 'Como criar usuários e configurar permissões',
                'categoria' => 'administracao',
                'conteudo'  => <<<TEXT
Para criar um usuário no Nexora ERP:
1. Acesse Administração > Usuários > Novo Usuário.
2. Preencha: Nome, E-mail, Senha temporária.
3. Selecione o Perfil de Acesso (role): define quais menus e ações o usuário pode executar.
4. Em "Módulos Habilitados", marque apenas os módulos contratados/permitidos.
5. Salve — o usuário receberá e-mail de boas-vindas (se e-mail configurado).

Perfis de Acesso (Roles):
- Acesse Administração > Perfis > Novo Perfil.
- Defina o nome do perfil (ex: "Vendedor", "Financeiro", "Supervisor").
- Marque as permissões por módulo e ação (visualizar, criar, editar, excluir).
- Vincule o perfil aos usuários.

Permissões especiais:
- Desconto máximo em vendas: campo "Desconto Máximo %" no usuário
- Acesso ao sistema de forma administrativa: role com flag "is_admin"
- Módulos contratados: controlados por empresa (multi-tenant)

Para redefinir senha: Administração > Usuários > Editar > "Redefinir Senha".
Para bloquear acesso: Administração > Usuários > Desativar usuário.
TEXT,
            ],
            [
                'titulo'    => 'Como cadastrar e configurar uma empresa no sistema',
                'categoria' => 'administracao',
                'conteudo'  => <<<TEXT
Para cadastrar a empresa no Nexora ERP (necessário antes de emitir NF-e):
1. Acesse Administração > Empresas > Editar (ou Nova Empresa).
2. Preencha os dados obrigatórios:
   - CNPJ: apenas números, sem pontuação
   - Razão Social
   - Inscrição Estadual (IE)
   - Endereço completo com CEP
   - Regime Tributário: Simples Nacional, Lucro Presumido ou Lucro Real
   - Atividade Principal (CNAE)
3. Salve.

Após cadastrar a empresa:
- Configure o Certificado Digital em Fiscal > Configurações
- Defina o Ambiente (Homologação ou Produção)
- Configure a série e número inicial da NF-e

Múltiplas empresas: o Nexora ERP suporta multi-empresa. Cada empresa tem seus próprios dados fiscais, estoque e financeiro segregados.
TEXT,
            ],
            // ── CADASTROS ───────────────────────────────────────────────────
            [
                'titulo'    => 'Como cadastrar produtos corretamente',
                'categoria' => 'cadastro',
                'conteudo'  => <<<TEXT
Para cadastrar um produto no Nexora ERP:
1. Acesse Cadastro > Produtos > Novo Produto.
2. Preencha os campos obrigatórios:
   - Nome/Descrição
   - Código interno ou código de barras (EAN/GTIN)
   - Unidade de Medida (UN, KG, MT, CX, etc.)
   - Categoria
   - NCM: 8 dígitos obrigatório para NF-e (consulte a tabela TIPI em gov.br)
   - CFOP padrão para vendas
   - CST/CSOSN (tributação)
3. Defina os preços:
   - Preço de custo
   - Preço de venda (pode ser calculado por markup)
   - Margem de lucro %
4. Configure o estoque:
   - Estoque mínimo (alerta de reposição)
   - Estoque máximo

Para NF-e, os campos críticos são: NCM (8 dígitos), CFOP, CST, alíquotas de ICMS/IPI/PIS/COFINS.
NCM incorreto causa Rejeição SEFAZ 702 ou 778.
TEXT,
            ],
            [
                'titulo'    => 'Como cadastrar clientes para emissão de NF-e',
                'categoria' => 'cadastro',
                'conteudo'  => <<<TEXT
Para cadastrar um cliente com dados completos para NF-e:
1. Acesse Cadastro > Clientes > Novo Cliente.
2. Preencha:
   - Tipo: Pessoa Física (CPF) ou Pessoa Jurídica (CNPJ)
   - CPF ou CNPJ válido (é validado automaticamente pelo sistema)
   - Nome/Razão Social
   - Inscrição Estadual (para PJ contribuinte de ICMS)
   - Endereço completo: rua, número, bairro, cidade, estado, CEP
   - Contato: telefone, e-mail
3. Selecione a Tabela de Preço (opcional)
4. Salve.

Campos obrigatórios para NF-e:
- CPF ou CNPJ válido
- Nome/Razão Social
- Endereço completo com CEP

Sem esses dados, a nota será rejeitada pela SEFAZ (código 215 ou 539).

Validação de CPF/CNPJ: o sistema valida automaticamente os dígitos verificadores.
TEXT,
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // Regras de negócio detalhadas
    // -------------------------------------------------------------------------

    private function getBusinessRules(): array
    {
        return [
            [
                'titulo'    => 'Regras de negócio: Controle de Estoque no Nexora ERP',
                'categoria' => 'estoque',
                'conteudo'  => <<<TEXT
REGRAS DE CONTROLE DE ESTOQUE NO NEXORA ERP:

1. ESTOQUE NEGATIVO:
   - Por padrão, o sistema BLOQUEIA a venda se o saldo for insuficiente
   - Para habilitar estoque negativo: Administração > Configurações > Vendas > "Permitir Estoque Negativo"
   - Mesmo com estoque negativo habilitado, o sistema exibe alerta ao vendedor

2. ATUALIZAÇÃO AUTOMÁTICA:
   - NF-e de Entrada confirmada: entrada automática no estoque
   - Pedido de Venda aprovado: reserva do estoque (não baixa definitiva)
   - Pedido de Venda faturado: baixa definitiva do estoque
   - Pedido de Venda cancelado: estoque reservado é liberado automaticamente
   - Ordem de Produção concluída: consome matéria-prima e adiciona produto acabado

3. MÉTODO DE CUSTEIO:
   - O sistema utiliza Custo Médio Ponderado por padrão
   - Cada entrada atualiza o custo médio do produto
   - Fórmula: (Qtd atual × Custo atual + Qtd entrada × Custo entrada) / (Qtd atual + Qtd entrada)

4. ESTOQUE MÍNIMO E MÁXIMO:
   - Estoque mínimo: ponto de pedido para reposição
   - O Dashboard exibe alertas de produtos abaixo do mínimo
   - Configurado em Cadastro > Produtos > campo "Estoque Mínimo"
TEXT,
            ],
            [
                'titulo'    => 'Regras de negócio: Ciclo Financeiro no Nexora ERP',
                'categoria' => 'financeiro',
                'conteudo'  => <<<TEXT
CICLO FINANCEIRO NO NEXORA ERP:

1. GERAÇÃO AUTOMÁTICA DE TÍTULOS:
   - Pedido de Venda aprovado → Título em Contas a Receber (CR)
   - Pedido de Compra recebido → Título em Contas a Pagar (CP)
   - NF-e de Entrada confirmada → Título em Contas a Pagar

2. FLUXO DE CAIXA:
   - Atualizado em tempo real com cada pagamento/recebimento
   - Projeção futura baseada em vencimentos de CP e CR
   - Pode ser visualizado por período em Financeiro > Fluxo de Caixa

3. CONCILIAÇÃO BANCÁRIA:
   - Importe o extrato bancário em Financeiro > Contas Bancárias > Importar Extrato
   - O sistema vincula automaticamente os lançamentos por valor e data
   - Lançamentos não vinculados ficam pendentes para conciliação manual

4. PARCELAMENTO:
   - Títulos podem ser criados parcelados
   - Cada parcela é um título independente com data de vencimento própria
   - Pagamento parcial é registrado sem baixa total do título

5. JUROS E MULTA:
   - Configurável por título ou globalmente em Administração > Configurações > Financeiro
   - Calculado automaticamente ao registrar pagamento em atraso
TEXT,
            ],
            [
                'titulo'    => 'Regras de negócio: Tributação NF-e por Regime Tributário',
                'categoria' => 'fiscal',
                'conteudo'  => <<<TEXT
TRIBUTAÇÃO DE NF-e POR REGIME TRIBUTÁRIO NO NEXORA ERP:

SIMPLES NACIONAL:
- Usa CSOSN (Código de Situação da Operação do Simples Nacional) em vez de CST
- CSOs comuns: 102 (sem permissão de crédito), 400 (sem ST), 500 (ICMS cobrado anteriormente)
- ICMS: calculado dentro do DAS, não destacado separadamente na nota
- PIS/COFINS: geralmente CSOSN 07 (não sujeito ao ICMS)
- IPI: destacado normalmente se o produto for industrializado

LUCRO PRESUMIDO / LUCRO REAL:
- Usa CST (Código de Situação Tributária) de 3 dígitos
- CST mais comuns: 00 (tributado integralmente), 10 (ST), 20 (redução base), 40 (isento), 60 (ICMS cobrado), 70 (redução com ST)
- ICMS destacado na nota
- PIS/COFINS destacados na nota (CST 01 = operação tributável)

SUBSTITUIÇÃO TRIBUTÁRIA (ICMS ST):
- O remetente recolhe o ICMS por toda a cadeia
- Requer configuração de MVA (Margem de Valor Agregado) por produto e estado destino
- CFOP específico: 5401/5402 (com ST) ou 5411 (devolução com ST)
- Configurado em Fiscal > Grupos Tributários > ICMS ST

Para configurar a tributação: Fiscal > Grupos Tributários > selecione o regime > defina CST/CSOSN e alíquotas por tipo de produto.
TEXT,
            ],
            [
                'titulo'    => 'Regras de negócio: Controle de Acesso e Permissões',
                'categoria' => 'administracao',
                'conteudo'  => <<<TEXT
CONTROLE DE ACESSO NO NEXORA ERP:

ESTRUTURA:
- Usuário → tem um ou mais Perfis (Roles)
- Perfil → tem permissões específicas por módulo e ação
- Módulos contratados → controlam quais seções aparecem para a empresa

TIPOS DE PERMISSÃO POR AÇÃO:
- visualizar: ver listagens e registros
- criar: criar novos registros
- editar: alterar registros existentes
- excluir: remover registros
- aprovar: aprovar pedidos/solicitações (ex: pedido de compra, folha de pagamento)
- cancelar: cancelar documentos (ex: NF-e, pedidos)

PERMISSÕES ESPECIAIS:
- is_admin: acesso total ao sistema, sem restrições
- desconto_maximo: percentual máximo de desconto que o usuário pode aplicar em vendas
- ver_custos: visualizar preço de custo dos produtos

MULTI-TENANT:
- Cada empresa tem seus dados isolados (estoque, financeiro, NF-e)
- Usuário pode ter acesso a múltiplas empresas com permissões diferentes
- Alternância de empresa pelo menu superior do sistema

Para configurar: Administração > Perfis > selecione ou crie um perfil > marque as permissões.
TEXT,
            ],
            [
                'titulo'    => 'Regras de negócio: Integração entre módulos',
                'categoria' => 'geral',
                'conteudo'  => <<<TEXT
INTEGRAÇÕES AUTOMÁTICAS ENTRE MÓDULOS NO NEXORA ERP:

VENDAS → ESTOQUE:
- Pedido aprovado: reserva estoque
- Pedido faturado: baixa estoque definitivamente
- Pedido cancelado: libera reserva de estoque

VENDAS → FISCAL:
- Faturar pedido pode gerar NF-e automaticamente (se configurado)
- NF-e cancelada não reverte automaticamente o pedido (faturar novamente)

COMPRAS → ESTOQUE:
- Pedido de compra recebido: entrada automática no estoque pelo custo do pedido

COMPRAS → FINANCEIRO:
- Pedido de compra recebido: gera título em Contas a Pagar automaticamente

FISCAL (NF-e Entrada) → ESTOQUE:
- Entrada de NF-e confirmada: atualiza estoque e custo médio do produto

FISCAL (NF-e Entrada) → FINANCEIRO:
- Entrada de NF-e confirmada: gera título em Contas a Pagar para o fornecedor

RH → FINANCEIRO:
- Fechamento de folha: gera lançamento de despesa com folha em Contas a Pagar

PRODUÇÃO → ESTOQUE:
- OP concluída: baixa matéria-prima, adiciona produto acabado
TEXT,
            ],
        ];
    }

    // -------------------------------------------------------------------------
    // Rejeições SEFAZ
    // -------------------------------------------------------------------------

    private function getSefazRejections(): array
    {
        return [
            ['codigo' => '101', 'titulo' => 'NF-e não encontrada na base da SEFAZ', 'causa' => 'A chave de acesso informada não existe na base da SEFAZ.', 'solucao' => 'Verifique se a chave de acesso está correta. Acesse Fiscal > NF-e e confira os dados da nota. Se for uma nota recém-emitida, aguarde alguns minutos para a sincronização.', 'prevencao' => 'Verifique a chave antes de consultar o status.'],
            ['codigo' => '110', 'titulo' => 'Uso denegado — NF-e denegada', 'causa' => 'A NF-e foi denegada pela SEFAZ do destinatário. O destinatário pode ter IE irregular ou estar em situação cadastral irregular.', 'solucao' => 'Verifique a situação cadastral do destinatário na Receita Federal (https://www.receita.fazenda.gov.br). Uma nota denegada não tem validade jurídica e não pode ser reativada. Verifique a situação e emita uma nova nota após regularização.', 'prevencao' => 'Antes de emitir NF-e interestadual, consulte a situação cadastral do destinatário.'],
            ['codigo' => '203', 'titulo' => 'Emissor não habilitado para emissão no ambiente de produção', 'causa' => 'O CNPJ do emitente não está habilitado para emissão de NF-e em produção na SEFAZ do estado.', 'solucao' => "1. Acesse Fiscal > Configurações\n2. Verifique se o certificado digital está configurado e válido\n3. Confirme se o ambiente está como 'Produção'\n4. Verifique se a empresa está credenciada na SEFAZ do seu estado\n5. Contacte a SEFAZ do estado para solicitar habilitação se necessário", 'prevencao' => 'Homologar o sistema completamente antes de ativar o ambiente de produção.'],
            ['codigo' => '204', 'titulo' => 'Duplicidade de NF-e — número/série já utilizado', 'causa' => 'Já existe uma NF-e autorizada com o mesmo número e série. O contador de NF-e pode estar incorreto no sistema.', 'solucao' => "1. Acesse Fiscal > Configurações\n2. Localize o campo 'Próximo Número da NF-e'\n3. Atualize para o próximo número disponível (maior que o último autorizado)\n4. Salve e tente emitir novamente", 'prevencao' => 'Nunca altere o contador de NF-e manualmente, a menos que seja necessário para correção.'],
            ['codigo' => '207', 'titulo' => 'CNPJ do emitente inválido', 'causa' => 'O CNPJ do emitente tem dígitos verificadores incorretos ou formato inválido.', 'solucao' => "1. Acesse Administração > Empresas\n2. Localize a empresa emitente\n3. Corrija o CNPJ (somente números, sem pontuação)\n4. Salve e tente emitir novamente", 'prevencao' => 'O sistema valida o CNPJ ao salvar — certifique-se de usar o CNPJ completo correto.'],
            ['codigo' => '208', 'titulo' => 'CNPJ do emitente difere da assinatura digital', 'causa' => 'O CNPJ do certificado digital não é o mesmo do CNPJ da empresa emitente cadastrada.', 'solucao' => "1. Acesse Fiscal > Configurações > Certificado Digital\n2. Verifique o CNPJ exibido do certificado atual\n3. Compare com o CNPJ em Administração > Empresas\n4. Se diferentes, carregue o certificado correto para a empresa", 'prevencao' => 'Ao configurar o certificado, verifique sempre se o CNPJ bate com a empresa.'],
            ['codigo' => '214', 'titulo' => 'CNPJ do emitente não cadastrado na SEFAZ', 'causa' => 'O CNPJ do emitente não está na base da SEFAZ ou está com situação irregular.', 'solucao' => "1. Consulte a situação cadastral em https://www.receita.fazenda.gov.br\n2. Verifique o CNPJ em Administração > Empresas\n3. Se o CNPJ estiver correto e ativo, contate a SEFAZ do estado para verificar o credenciamento", 'prevencao' => 'Verifique regularmente a situação cadastral da empresa na Receita Federal.'],
            ['codigo' => '215', 'titulo' => 'CPF ou CNPJ do destinatário inválido', 'causa' => 'O CPF ou CNPJ do cliente destinatário está com formato ou dígitos verificadores inválidos.', 'solucao' => "1. Acesse Cadastro > Clientes\n2. Localize o cliente destinatário\n3. Corrija o CPF (11 dígitos) ou CNPJ (14 dígitos) sem pontuação\n4. Salve e tente emitir a nota novamente", 'prevencao' => 'O sistema valida CPF/CNPJ ao salvar o cliente — sempre valide antes de emitir NF-e.'],
            ['codigo' => '216', 'titulo' => 'Inscrição Estadual do emitente inválida', 'causa' => 'A IE do emitente está em formato inválido para o estado informado.', 'solucao' => "1. Acesse Administração > Empresas\n2. Verifique a Inscrição Estadual\n3. Corrija conforme o padrão do seu estado (número de dígitos varia por UF)\n4. Salve e tente novamente", 'prevencao' => 'Consulte a SEFAZ do estado para confirmar o formato correto da IE.'],
            ['codigo' => '539', 'titulo' => 'CNPJ destinatário não encontrado na base de dados da SEFAZ', 'causa' => 'O CNPJ do cliente destinatário não existe ou está inativo na base da Receita Federal.', 'solucao' => "1. Verifique o CNPJ em https://www.receita.fazenda.gov.br\n2. Se o CNPJ estiver incorreto: acesse Cadastro > Clientes e corrija\n3. Se o CNPJ estiver inapto/baixado: a nota não pode ser emitida para esse destinatário", 'prevencao' => 'Consulte a situação do CNPJ do cliente antes de emitir a nota.'],
            ['codigo' => '702', 'titulo' => 'NCM inválido — código não existe na tabela NCM/TIPI', 'causa' => 'O código NCM de um ou mais produtos está inválido, inexistente ou desatualizado na tabela TIPI vigente.', 'solucao' => "1. Acesse Cadastro > Produtos\n2. Localize o produto com NCM inválido\n3. Consulte o NCM correto na tabela TIPI: https://www.gov.br/receitafederal/pt-br/assuntos/aduana-e-comercio-exterior/classificacao-fiscal-de-mercadorias/tipi\n4. Corrija o NCM (deve ter exatamente 8 dígitos)\n5. Salve e emita a nota novamente", 'prevencao' => 'Verifique periodicamente se os NCMs dos produtos ainda são válidos — a TIPI é atualizada por decreto.'],
            ['codigo' => '778', 'titulo' => 'NCM inexistente na tabela atual da TIPI', 'causa' => 'O NCM do produto foi extinto ou reclassificado na última atualização da TIPI. O produto com problema é identificado por [itemN] na mensagem (ex: [item3] = 3º produto da nota).', 'solucao' => "1. Identifique qual produto é o item indicado na mensagem de erro (ex: [item8] = 8º produto)\n2. Acesse Cadastro > Produtos\n3. Abra o produto e corrija o campo NCM\n4. Consulte o NCM atualizado na tabela TIPI oficial\n5. Salve e reemita a nota em Fiscal > NF-e", 'prevencao' => 'Mantenha os NCMs atualizados. A TIPI é revisada com frequência pelo governo.'],
            ['codigo' => '741', 'titulo' => 'CFOP inválido ou inexistente', 'causa' => 'O CFOP informado não existe ou não é compatível com o tipo de operação (entrada/saída, dentro/fora do estado).', 'solucao' => "1. Acesse Fiscal > Tipos de Operação\n2. Verifique o CFOP configurado\n3. Saídas dentro do estado: CFOP começa com 5\n4. Saídas interestaduais: CFOP começa com 6\n5. Entradas: CFOP começa com 1 (intraestadual) ou 2 (interestadual)\n6. Corrija e reemita a nota", 'prevencao' => 'Configure os Tipos de Operação corretamente antes de usar em produção.'],
            ['codigo' => '938', 'titulo' => 'CFOP incompatível com operação informada', 'causa' => 'O CFOP está em conflito com outros dados da nota (ex: operação de devolução com CFOP de venda, ou CFOP interestadual com destinatário do mesmo estado).', 'solucao' => "1. Verifique se o CFOP é compatível com:\n   - Tipo de operação (venda, devolução, transferência, etc.)\n   - UF do emitente vs UF do destinatário (mesmo estado = 5xxx, outro estado = 6xxx)\n2. Acesse Fiscal > Tipos de Operação e corrija o CFOP\n3. Reemita a nota", 'prevencao' => 'Use um Tipo de Operação para cada tipo de negociação (venda, devolução, transferência, etc.).'],
            ['codigo' => '805', 'titulo' => 'Schema XML da NF-e inválido', 'causa' => 'O XML gerado pelo sistema não está em conformidade com o leiaute exigido pela SEFAZ (versão do schema). Geralmente indica um bug ou atualização pendente do sistema.', 'solucao' => 'Este erro indica um problema técnico no sistema. Entre em contato com o suporte: WhatsApp (32) 98450-2345', 'prevencao' => 'Mantenha o sistema atualizado para garantir compatibilidade com as notas técnicas da SEFAZ.'],
            ['codigo' => '999', 'titulo' => 'Erro interno da SEFAZ', 'causa' => 'Erro interno nos servidores da SEFAZ. Não é causado pelo sistema ou pelos dados da nota.', 'solucao' => "1. Aguarde alguns minutos e tente enviar a nota novamente\n2. Verifique o status dos serviços da SEFAZ\n3. Se persistir por mais de 1 hora, entre em contato com o suporte: (32) 98450-2345", 'prevencao' => 'Nenhuma ação preventiva necessária — é um problema dos servidores externos da SEFAZ.'],
        ];
    }

    private function formatRejection(array $rej): string
    {
        return <<<TEXT
Rejeição SEFAZ Código {$rej['codigo']}: {$rej['titulo']}

CAUSA DO ERRO:
{$rej['causa']}

COMO CORRIGIR NO NEXORA ERP:
{$rej['solucao']}

COMO PREVENIR:
{$rej['prevencao']}

Módulo afetado: Fiscal > NF-e
Para suporte urgente: WhatsApp (32) 98450-2345
TEXT;
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    private function titleFromFilename(string $name): string
    {
        $name = str_replace(['-', '_'], ' ', $name);
        $name = preg_replace('/^prompt /i', '', $name);
        return ucwords($name);
    }

    private function categoriaFromFilename(string $name): string
    {
        $map = [
            'nf'         => 'fiscal',
            'fiscal'     => 'fiscal',
            'folha'      => 'rh',
            'rh'         => 'rh',
            'ponto'      => 'rh',
            'holerite'   => 'rh',
            'jornada'    => 'rh',
            'employee'   => 'rh',
            'financeiro' => 'financeiro',
            'conta'      => 'financeiro',
            'fluxo'      => 'financeiro',
            'plano'      => 'financeiro',
            'produto'    => 'estoque',
            'estoque'    => 'estoque',
            'pedido'     => 'vendas',
            'venda'      => 'vendas',
            'compra'     => 'compras',
            'cotacao'    => 'compras',
            'rota'       => 'logistica',
            'vehicle'    => 'logistica',
            'suporte'    => 'suporte',
            'usuario'    => 'administracao',
            'role'       => 'administracao',
            'empresa'    => 'administracao',
            'config'     => 'administracao',
            'op'         => 'producao',
            'producao'   => 'producao',
        ];

        $lower = strtolower($name);

        foreach ($map as $keyword => $categoria) {
            if (str_contains($lower, $keyword)) {
                return $categoria;
            }
        }

        return 'geral';
    }
}

