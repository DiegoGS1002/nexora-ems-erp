<?php
namespace App\Services;

use App\Ai\RagService;
use App\Ai\ToolRegistry;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Data\Content;
use Gemini\Enums\Role;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use Exception;

class AiAssistantService
{
    private const MAX_TOOL_ITERATIONS = 6;

    protected array $routeModuleMap = [
        '/'                          => 'home',
        'dashboard'                  => 'home',
        'plans_of_accounts'          => 'financeiro',
        'contas-bancarias'           => 'financeiro',
        'accounts_payable'           => 'financeiro',
        'accounts_receivable'        => 'financeiro',
        'cash_flow'                  => 'financeiro',
        'baccarat_accounts'          => 'financeiro',
        'financial_reports'          => 'financeiro',
        'payroll'                    => 'rh',
        'holerite'                   => 'rh',
        'working_day'                => 'rh',
        'stitch_beat'                => 'rh',
        'employee_management'        => 'rh',
        'rh_reports'                 => 'rh',
        'clients'                    => 'cadastro',
        'products'                   => 'cadastro',
        'suppliers'                  => 'cadastro',
        'employees'                  => 'cadastro',
        'roles'                      => 'cadastro',
        'vehicles'                   => 'cadastro',
        'product-categories'         => 'cadastro',
        'unit-of-measures'           => 'cadastro',
        'compras'                    => 'compras',
        'vendas'                     => 'vendas',
        'requests'                   => 'vendas',
        'visits'                     => 'vendas',
        'sales_report'               => 'vendas',
        'fiscal'                     => 'fiscal',
        'stock'                      => 'estoque',
        'route_management'           => 'logistica',
        'routing'                    => 'logistica',
        'logistica'                  => 'logistica',
        'monitoring_of_deliveries'   => 'logistica',
        'driver_management'          => 'logistica',
        'romaneio'                   => 'logistica',
        'vehicle_tracking'           => 'logistica',
        'vehicle_maintenance'        => 'logistica',
        'transport_report'           => 'logistica',
        'production_orders'          => 'producao',
        'configuracoes'              => 'administracao',
        'empresas'                   => 'administracao',
        'companies'                  => 'administracao',
        'notificacoes'               => 'administracao',
        'suporte'                    => 'suporte',
        'perfil'                     => 'administracao',
    ];

    protected array $pageNames = [
        'plans_of_accounts'              => 'Plano de Contas',
        'contas-bancarias'               => 'Contas Bancárias',
        'accounts_payable'               => 'Contas a Pagar',
        'accounts_receivable'            => 'Contas a Receber',
        'cash_flow'                      => 'Fluxo de Caixa',
        'financial_reports'              => 'Relatórios Financeiros',
        'payroll'                        => 'Folha de Pagamento',
        'holerite'                       => 'Holerite',
        'working_day'                    => 'Jornada de Trabalho',
        'stitch_beat'                    => 'Controle de Ponto',
        'employee_management'            => 'Gestão de Funcionários',
        'clients'                        => 'Clientes',
        'products'                       => 'Produtos',
        'suppliers'                      => 'Fornecedores',
        'employees'                      => 'Funcionários',
        'roles'                          => 'Funções e Cargos',
        'vehicles'                       => 'Veículos',
        'product-categories'             => 'Categorias de Produtos',
        'unit-of-measures'               => 'Unidades de Medida',
        'compras/solicitacoes'           => 'Solicitações de Compra',
        'compras/pedidos'                => 'Pedidos de Compra',
        'compras/cotacoes'               => 'Cotações',
        'vendas/pedidos'                 => 'Pedidos de Venda',
        'vendas/precificacao'            => 'Tabelas de Precificação',
        'requests'                       => 'Pedidos de Venda',
        'fiscal/notas-fiscais'           => 'Notas Fiscais Eletrônicas',
        'fiscal/tipos-operacao'          => 'Tipos de Operação Fiscal',
        'fiscal/grupos-tributarios'      => 'Grupos Tributários',
        'fiscal/entrada'                 => 'NF de Entrada',
        'fiscal/saida'                   => 'NF de Saída',
        'stock'                          => 'Estoque',
        'route_management'               => 'Gestão de Rotas',
        'routing'                        => 'Roteirização',
        'monitoring_of_deliveries'       => 'Monitoramento de Entregas',
        'driver_management'              => 'Gestão de Motoristas',
        'romaneio'                       => 'Romaneio',
        'vehicle_tracking'               => 'Rastreamento de Veículos',
        'vehicle_maintenance'            => 'Manutenção de Veículos',
        'transport_report'               => 'Relatório de Transporte',
        'logistica/agendamento-entregas' => 'Agendamento de Entregas',
        'production_orders'              => 'Ordens de Produção',
        'rh_reports'                     => 'Relatórios de RH',
        'sales_report'                   => 'Relatório de Vendas',
        'visits'                         => 'Visitas Comerciais',
        'baccarat_accounts'              => 'Conciliação Bancária',
        'dashboard'                      => 'Dashboard',
        'dashboard/kpi'                  => 'Relatório KPI',
        'configuracoes'                  => 'Configurações do Sistema',
        'empresas'                       => 'Gestão de Empresas',
        'suporte/chat'                   => 'Chat de Suporte',
    ];

    public function __construct(
        private RagService $rag,
        private ToolRegistry $tools
    ) {}

    public function detectModuleFromPath(string $path): string
    {
        $path = trim($path, '/');

        if ($path === '' || $path === '/') {
            return 'home';
        }

        if (isset($this->routeModuleMap[$path])) {
            return $this->routeModuleMap[$path];
        }

        foreach ($this->routeModuleMap as $route => $module) {
            $route = trim($route, '/');
            if ($route !== '' && str_starts_with($path, $route)) {
                return $module;
            }
        }

        $first = explode('/', $path)[0];
        return $this->routeModuleMap[$first] ?? 'suporte';
    }

    public function getPageName(string $path): string
    {
        $path = trim($path, '/');

        if ($path === '' || $path === '/') {
            return 'Dashboard';
        }

        if (isset($this->pageNames[$path])) {
            return $this->pageNames[$path];
        }

        foreach ($this->pageNames as $route => $name) {
            $route = trim($route, '/');
            if ($route !== '' && str_starts_with($path, $route)) {
                return $name;
            }
        }

        return ucwords(str_replace(['-', '_', '/'], ' ', $path)) ?: 'Dashboard';
    }

    public function getResponse(string $module, string $pageName, string $userMessage, array $history = []): string
    {
        // Contexto RAG da base de conhecimento indexada
        $ragContext = $this->rag->buildContext($userMessage, 6);

        // 1. OpenAI com function calling + RAG
        if (config('openai.api_key')) {
            try {
                $result = $this->getOpenAiResponseWithTools($module, $pageName, $userMessage, $history, $ragContext);
                if ($result) {
                    return $result;
                }
            } catch (Exception $e) {
                Log::warning('AiAssistantService: OpenAI falhou', ['error' => substr($e->getMessage(), 0, 300)]);
            }
        }

        // 2. Gemini com RAG como contexto
        try {
            return $this->getGeminiResponse($module, $pageName, $userMessage, $history, $ragContext);
        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
            Log::error('AiAssistantService Error', [
                'module'  => $module,
                'page'    => $pageName,
                'message' => $userMessage,
                'error'   => $errorMsg,
            ]);
        }

        return 'Desculpe, ocorreu um erro ao processar sua pergunta. Tente novamente em instantes.';
    }

    /**
     * OpenAI com function calling (tools) + contexto RAG.
     */
    protected function getOpenAiResponseWithTools(
        string $module,
        string $pageName,
        string $userMessage,
        array $history,
        string $ragContext = ''
    ): ?string {
        $systemPrompt = $this->buildSystemInstruction($module, $pageName, $ragContext);

        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        foreach (array_slice($history, -10) as $msg) {
            if (empty($msg['content'])) continue;
            $role       = $msg['role'] === 'user' ? 'user' : 'assistant';
            $messages[] = ['role' => $role, 'content' => $msg['content']];
        }

        $messages[] = ['role' => 'user', 'content' => $userMessage];

        $toolDefs  = $this->tools->definitions();
        $userId    = auth()->id() ?? 0;
        $iteration = 0;

        while ($iteration < self::MAX_TOOL_ITERATIONS) {
            $iteration++;

            $response = OpenAI::chat()->create([
                'model'       => config('openai.model', 'gpt-4o-mini'),
                'messages'    => $messages,
                'tools'       => $toolDefs,
                'tool_choice' => 'auto',
                'max_tokens'  => 2048,
                'temperature' => 0.3,
            ]);

            $choice      = $response->choices[0] ?? null;
            if (! $choice) break;

            $finishReason = $choice->finishReason ?? '';
            $message      = $choice->message;

            $messages[] = [
                'role'       => 'assistant',
                'content'    => $message->content ?? null,
                'tool_calls' => $message->toolCalls ?? null,
            ];

            if ($finishReason === 'tool_calls' && ! empty($message->toolCalls)) {
                foreach ($message->toolCalls as $toolCall) {
                    $functionName = $toolCall->function->name;
                    $arguments    = json_decode($toolCall->function->arguments, true) ?? [];
                    $toolResult   = $this->tools->execute($functionName, $arguments, $userId);

                    $messages[] = [
                        'role'         => 'tool',
                        'tool_call_id' => $toolCall->id,
                        'content'      => json_encode($toolResult, JSON_UNESCAPED_UNICODE),
                    ];
                }
                continue;
            }

            if ($finishReason === 'stop' && $message->content) {
                return $message->content;
            }

            break;
        }

        return null;
    }

    protected function getGeminiResponse(
        string $module,
        string $pageName,
        string $userMessage,
        array $history = [],
        string $ragContext = ''
    ): string {
        $systemInstruction = Content::parse(
            part: $this->buildSystemInstruction($module, $pageName, $ragContext),
            role: Role::USER
        );

        $contents = [];
        foreach (array_slice($history, -10) as $msg) {
            if (empty($msg['content'])) continue;
            $contents[] = Content::parse(
                part: $msg['content'],
                role: $msg['role'] === 'user' ? Role::USER : Role::MODEL
            );
        }
        $contents[] = Content::parse(part: $userMessage, role: Role::USER);

        $result = Gemini::generativeModel(model: config('gemini.model', 'gemini-2.0-flash'))
            ->withSystemInstruction($systemInstruction)
            ->generateContent(...$contents);

        return $result->text();
    }

    protected function buildSystemInstruction(string $module, string $pageName, string $ragContext = ''): string
    {
        $moduleContext = config("ai_contexts.{$module}", config('ai_contexts.suporte'));

        $prompt = <<<PROMPT
# IDENTIDADE — Nexora IA

Você é a **Nexora IA**, assistente de suporte inteligente do **Nexora ERP** — um sistema ERP completo para gestão empresarial brasileira.

Você NÃO é um chatbot genérico. Você é um **Analista Sênior Virtual** com conhecimento profundo de:
- Todos os módulos do Nexora ERP (Fiscal, Financeiro, RH, Estoque, Vendas, Compras, Logística, Produção, Cadastros, Administração)
- Regras de negócio reais do sistema
- Legislação fiscal brasileira (NF-e, NFC-e, SEFAZ, MOC, TIPI)
- CLT, eSocial, tributação empresarial
- Diagnóstico de causa raiz (RCA)

---

# MISSÃO

Resolver COMPLETAMENTE o problema do usuário com orientações precisas e concretas.

**REGRAS ABSOLUTAS:**
1. SEMPRE dê uma resposta concreta — nunca diga apenas "verifique o sistema"
2. SEMPRE indique o caminho exato no sistema: **Menu > Submenu > Campo**
3. SEMPRE use as ferramentas disponíveis para consultar dados reais antes de responder
4. NUNCA invente dados — se não souber, diga o que precisa de informação
5. Use **markdown** com listas, negrito e tabelas para respostas claras

---

# MÓDULOS DO NEXORA ERP

## Administração
- **Empresas**: Cadastro de empresas, CNPJ, IE, Regime Tributário, Certificado Digital
- **Usuários**: Criação, permissões, perfis de acesso (roles), módulos contratados
- **Configurações**: Parâmetros do sistema, chaves de API, e-mail, integrações
- **Logs**: Auditoria de ações do sistema

## Dashboard
- KPIs em tempo real: vendas, estoque crítico, contas vencidas, NF-e pendentes
- Gráficos de faturamento, comparativo mensal, indicadores financeiros

## Cadastros
- **Produtos**: Nome, NCM (8 dígitos obrigatório), CFOP padrão, CST, preço de custo/venda, estoque mínimo, unidade de medida, categoria, código de barras
- **Clientes**: Razão Social, CPF/CNPJ, IE, endereço completo (CEP obrigatório para NF-e), contatos
- **Fornecedores**: CNPJ, contato, condições de pagamento
- **Funcionários**: Dados pessoais, cargo, salário, jornada, banco
- **Veículos**: Placa, modelo, capacidade, motorista vinculado
- **Unidades de Medida** e **Categorias de Produtos**

## Produção
- **Ordens de Produção (OP)**: Criação com ficha técnica, apontamento de produção, baixa de matéria-prima no estoque, status (Aberta/Em Produção/Concluída/Cancelada)
- **Ficha Técnica**: Componentes e quantidades necessárias por produto acabado

## Estoque
- **Movimentação**: Entrada manual, saída, transferência entre depósitos
- **Inventário**: Ajuste de saldo físico x sistema
- **Saldo por produto**: consulta em tempo real, estoque mínimo/máximo
- Integrado com NF-e de entrada (baixa automática) e pedidos de venda

## Vendas
- **Pedidos de Venda**: Criação, itens, desconto, tabela de preço, status (Rascunho/Aprovado/Faturado/Entregue/Cancelado)
- **Precificação**: Tabelas de preço por cliente/grupo, markup, margem
- **Visitas Comerciais**: Registro de visitas e oportunidades

## Compras
- **Solicitações de Compra**: Requisição interna, aprovação, vinculação com pedido
- **Cotações**: Comparação de fornecedores, aprovação do melhor preço
- **Pedidos de Compra**: Emissão, recebimento (baixa automática no estoque)

## Fiscal
- **NF-e (Saída)**: Emissão, autorização SEFAZ, cancelamento, carta de correção, contingência
- **NF-e (Entrada)**: Importação de XML, escrituração, baixa de estoque
- **Tipos de Operação**: CFOP, natureza da operação, impostos padrão
- **Grupos Tributários**: CST/CSOSN, alíquotas de ICMS, IPI, PIS, COFINS por regime tributário
- **Configurações Fiscais**: Certificado digital (.pfx), série/número da nota, ambiente (homologação/produção), regime tributário

## Financeiro
- **Plano de Contas**: Estrutura contábil, centros de custo
- **Contas a Pagar**: Lançamento, pagamento, parcelamento, vencimento
- **Contas a Receber**: Lançamento, recebimento, cobrança, inadimplência
- **Fluxo de Caixa**: Projeção de entradas e saídas, saldo previsto
- **Contas Bancárias**: Extrato, conciliação bancária, saldo

## RH
- **Jornada de Trabalho**: Escala, horários, turno
- **Controle de Ponto**: Registro de entrada/saída, horas extras, banco de horas
- **Folha de Pagamento**: Cálculo automático de salário, INSS, IRRF, FGTS, 13º, férias
- **Holerite**: Geração e download em PDF por competência

## Logística/Transporte
- **Roteirização**: Otimização de rotas de entrega por algoritmo
- **Agendamento de Entregas**: Vinculação com pedidos, datas, motoristas
- **Romaneio**: Manifesto de cargas por veículo
- **Rastreamento de Veículos**: GPS e status em tempo real
- **Manutenção de Veículos**: Agenda preventiva/corretiva

---

# REGRAS DE NEGÓCIO CRÍTICAS

## Emissão de NF-e — Pré-requisitos obrigatórios:
- Certificado digital A1 (.pfx) válido instalado em **Fiscal > Configurações**
- Empresa com CNPJ e Inscrição Estadual corretos em **Administração > Empresas**
- Produto com NCM de 8 dígitos válido (tabela TIPI/NCM vigente)
- Cliente com CPF ou CNPJ válido + endereço completo com CEP
- Tipo de Operação configurado com CFOP correto (5xxx para saídas internas, 6xxx para interestaduais)
- Ambiente configurado: Homologação (testes) ou Produção (notas válidas)

## Rejeições SEFAZ mais comuns:
| Código | Causa | Correção |
|--------|-------|----------|
| 203 | Emissor não habilitado em produção | Fiscal > Configurações (verificar certificado/ambiente) |
| 204 | Número/série duplicado | Fiscal > Configurações (atualizar próximo número) |
| 214 | CNPJ emitente inválido | Administração > Empresas |
| 215/539 | CPF/CNPJ destinatário inválido | Cadastro > Clientes |
| 702/778 | NCM inválido/inexistente | Cadastro > Produtos (corrigir NCM 8 dígitos) |
| 741/938 | CFOP incompatível | Fiscal > Tipos de Operação |
| 805 | Schema XML inválido | Erro interno — contato suporte |

## Estoque:
- Movimentações de NF-e de entrada baixam automaticamente o estoque
- Pedidos de venda reservam estoque ao aprovar
- Ordens de Produção consomem matéria-prima ao concluir (se ficha técnica configurada)
- Estoque negativo pode ser habilitado/bloqueado em Configurações > Vendas

## Financeiro:
- Pedidos de venda aprovados geram automaticamente título em Contas a Receber
- Pedidos de compra recebidos geram automaticamente título em Contas a Pagar
- Pagamentos e recebimentos atualizam o Fluxo de Caixa em tempo real

## RH:
- Folha de pagamento bloqueada após fechamento (não editável)
- Ponto eletrônico integrado com jornada para cálculo automático de extras/faltas
- holerite gerado automaticamente ao fechar folha

---

# FERRAMENTAS DISPONÍVEIS

Use as ferramentas abaixo para consultar dados reais do sistema antes de responder:

| Ferramenta | Quando usar |
|------------|------------|
| `consultar_diagnostico_sefaz` | Código de rejeição SEFAZ (PRIMEIRO para fiscal) |
| `verificar_nfe` | Problemas com notas fiscais específicas |
| `analisar_xml_nfe` | Quando usuário colar XML de NF-e |
| `inspecionar_logs` | Erros recentes, rastreamento de causa raiz |
| `buscar_pedido` | Status e detalhes de pedidos de venda |
| `consultar_cliente` | Dados cadastrais, CPF/CNPJ, campos faltantes |
| `consultar_estoque` | Saldo atual de produtos |
| `consultar_financeiro` | Contas a pagar/receber, vencidos, saldos |
| `consultar_ticket` | Histórico de chamados do usuário |
| `buscar_codigo_fonte` | Localizar onde erro é gerado no código |

**NUNCA responda sobre dados do sistema sem consultar pelo menos 1 ferramenta relevante.**

---

# FORMATO DE RESPOSTA

Para problemas técnicos:
```
**Diagnóstico**
[causa raiz com dados reais]

**Dados encontrados**
[o que as ferramentas retornaram]

**Solução passo a passo**
1. Acesse Menu > Submenu
2. [ação específica]
3. [resultado esperado]

**Prevenção**
[como evitar no futuro]
```

Para dúvidas funcionais: resposta direta e concisa com caminho no sistema.

---

# CONTATO DE SUPORTE HUMANO
Para problemas urgentes não resolvidos: **WhatsApp (32) 98450-2345**

PROMPT;

        // Contexto do módulo específico
        $prompt .= "\n\n---\n## Contexto do Módulo Atual\n{$moduleContext}";

        // Contexto da página atual
        if ($pageName) {
            $prompt .= "\n\n**Página atual do usuário:** \"{$pageName}\" — foque suas respostas nesta tela e no que o usuário pode estar fazendo.";
        }

        // Contexto RAG
        if ($ragContext) {
            $prompt .= "\n\n" . $ragContext;
        }

        return $prompt;
    }

    public function saveHistory(int $userId, string $module, array $messages): void
    {
        Cache::put("ai_history_{$userId}_{$module}", array_slice($messages, -20), now()->addHours(24));
    }

    public function getHistory(int $userId, string $module): array
    {
        return Cache::get("ai_history_{$userId}_{$module}", []);
    }

    public function clearHistory(int $userId, string $module): void
    {
        Cache::forget("ai_history_{$userId}_{$module}");
    }

    public function getSuggestedQuestions(string $module, string $pageName = ''): array
    {
        $byPage = [
            'Plano de Contas'              => ['Como criar uma conta contábil?', 'Qual a estrutura de um plano de contas?', 'Como classificar despesas operacionais?'],
            'Contas a Pagar'               => ['Como registrar um novo título a pagar?', 'Como fazer um pagamento em lote?', 'Como visualizar títulos vencidos?'],
            'Contas a Receber'             => ['Como lançar um recebimento?', 'Como gerenciar inadimplentes?', 'Como baixar um título recebido?'],
            'Fluxo de Caixa'               => ['Como interpretar o fluxo de caixa?', 'O que são entradas e saídas previstas?', 'Como melhorar o fluxo de caixa?'],
            'Folha de Pagamento'           => ['Como calcular o 13º salário?', 'Como lançar horas extras?', 'Como gerar o arquivo para banco?'],
            'Controle de Ponto'            => ['Como registrar uma batida de ponto?', 'Como corrigir uma batida incorreta?', 'Como calcular horas trabalhadas?'],
            'Notas Fiscais Eletrônicas'    => ['Como emitir uma NF-e?', 'O que é CFOP e como escolher?', 'Como cancelar uma nota fiscal?'],
            'NF de Entrada'                => ['Como fazer a entrada de uma nota fiscal?', 'Como importar XML de NF-e?'],
            'Ordens de Produção'           => ['Como criar uma ordem de produção?', 'Como apontar produção?', 'Como verificar o andamento da OP?'],
            'Estoque'                      => ['Como fazer um inventário?', 'O que é estoque mínimo?', 'Como registrar uma movimentação?'],
            'Pedidos de Compra'            => ['Como criar um pedido de compra?', 'Como aprovar um pedido?', 'Como receber um pedido?'],
            'Cotações'                     => ['Como criar uma cotação?', 'Como comparar fornecedores?', 'Como aprovar uma cotação?'],
            'Pedidos de Venda'             => ['Como criar um pedido de venda?', 'Como verificar o status do pedido?', 'Como faturar um pedido?'],
            'Agendamento de Entregas'      => ['Como agendar uma entrega?', 'Como otimizar rotas?', 'Como confirmar uma entrega?'],
            'Dashboard'                    => ['O que mostram esses gráficos?', 'Como melhorar meus indicadores?', 'Como filtrar por período?'],
            'Grupos Tributários'           => ['Como criar um grupo tributário?', 'Qual CST usar para Simples Nacional?', 'Como configurar ICMS ST?'],
            'Tipos de Operação Fiscal'     => ['O que é CFOP?', 'Como configurar operação de venda interestadual?', 'Qual CFOP usar para devolução?'],
            'Configurações do Sistema'     => ['Como configurar o certificado digital?', 'Como mudar para ambiente de produção?', 'Como configurar e-mail no sistema?'],
            'Gestão de Empresas'           => ['Como cadastrar uma nova empresa?', 'Como atualizar o CNPJ da empresa?', 'Como configurar o regime tributário?'],
            'Holerite'                     => ['Como gerar holerite em PDF?', 'Como visualizar o desconto do INSS?', 'Como corrigir um holerite?'],
            'Produtos'                     => ['Como cadastrar um produto com NCM?', 'Como definir preço de venda?', 'Como configurar estoque mínimo?'],
            'Clientes'                     => ['Como cadastrar um cliente PJ?', 'Quais dados são obrigatórios para NF-e?', 'Como atualizar o endereço de um cliente?'],
        ];

        if ($pageName && isset($byPage[$pageName])) {
            return $byPage[$pageName];
        }

        $byModule = [
            'financeiro'    => ['Como analisar o DRE?', 'Como fazer conciliação bancária?', 'Quais são os principais indicadores financeiros?'],
            'rh'            => ['Como calcular férias?', 'Como lançar afastamento?', 'Quais são os encargos sobre a folha?'],
            'producao'      => ['Como calcular o OEE?', 'O que é Lead Time?', 'Como reduzir refugo na produção?'],
            'estoque'       => ['O que é PEPS e UEPS?', 'Como calcular estoque mínimo?', 'O que é curva ABC?'],
            'compras'       => ['Como negociar com fornecedores?', 'O que é lead time de compra?', 'Como reduzir custos de compras?'],
            'vendas'        => ['Como aumentar a taxa de conversão?', 'Como calcular margem de contribuição?', 'Como gerenciar inadimplência?'],
            'fiscal'        => ['O que é CFOP?', 'Como calcular ICMS ST?', 'Quais obrigações acessórias preciso enviar?'],
            'logistica'     => ['Como otimizar rotas de entrega?', 'O que é o romaneio?', 'Como calcular custo de frete?'],
            'cadastro'      => ['Como cadastrar um produto corretamente?', 'Quais dados são obrigatórios para fornecedor?', 'Como classificar por NCM?'],
            'administracao' => ['Como criar um usuário?', 'Como configurar permissões?', 'Como configurar o certificado digital?'],
            'suporte'       => ['Como abrir um chamado?', 'Como acompanhar meu ticket?', 'Qual o prazo de atendimento?'],
            'home'          => ['O que posso fazer neste sistema?', 'Como navegar entre os módulos?', 'Como configurar meu perfil?'],
        ];

        return $byModule[$module] ?? ['Como posso te ajudar?'];
    }

    public function getModuleName(string $module): string
    {
        return [
            'financeiro'    => 'Financeiro',
            'rh'            => 'Recursos Humanos',
            'producao'      => 'Produção',
            'estoque'       => 'Estoque',
            'compras'       => 'Compras',
            'vendas'        => 'Vendas',
            'logistica'     => 'Logística',
            'fiscal'        => 'Fiscal',
            'administracao' => 'Administração',
            'cadastro'      => 'Cadastros',
            'suporte'       => 'Suporte',
            'home'          => 'Início',
        ][$module] ?? 'Assistente';
    }
}
