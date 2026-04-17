<?php
namespace App\Services;
use Gemini\Laravel\Facades\Gemini;
use Gemini\Data\Content;
use Gemini\Enums\Role;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use OpenAI\Laravel\Facades\OpenAI;
use Exception;
class AiAssistantService
{
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
        'notificacoes'               => 'administracao',
        'suporte'                    => 'suporte',
        'perfil'                     => 'suporte',
    ];
    protected array $pageNames = [
        'plans_of_accounts'              => 'Plano de Contas',
        'contas-bancarias'               => 'Contas Bancarias',
        'accounts_payable'               => 'Contas a Pagar',
        'accounts_receivable'            => 'Contas a Receber',
        'cash_flow'                      => 'Fluxo de Caixa',
        'financial_reports'              => 'Relatorios Financeiros',
        'payroll'                        => 'Folha de Pagamento',
        'holerite'                       => 'Holerite',
        'working_day'                    => 'Jornada de Trabalho',
        'stitch_beat'                    => 'Controle de Ponto',
        'employee_management'            => 'Gestao de Funcionarios',
        'clients'                        => 'Clientes',
        'products'                       => 'Produtos',
        'suppliers'                      => 'Fornecedores',
        'employees'                      => 'Funcionarios',
        'roles'                          => 'Funcoes e Cargos',
        'vehicles'                       => 'Veiculos',
        'product-categories'             => 'Categorias de Produtos',
        'unit-of-measures'               => 'Unidades de Medida',
        'compras/solicitacoes'           => 'Solicitacoes de Compra',
        'compras/pedidos'                => 'Pedidos de Compra',
        'compras/cotacoes'               => 'Cotacoes',
        'vendas/pedidos'                 => 'Pedidos de Venda',
        'vendas/precificacao'            => 'Tabelas de Precificacao',
        'requests'                       => 'Pedidos de Venda',
        'fiscal/notas-fiscais'           => 'Notas Fiscais Eletronicas',
        'fiscal/tipos-operacao'          => 'Tipos de Operacao Fiscal',
        'fiscal/grupos-tributarios'      => 'Grupos Tributarios',
        'fiscal/entrada'                 => 'NF de Entrada',
        'fiscal/saida'                   => 'NF de Saida',
        'stock'                          => 'Estoque',
        'route_management'               => 'Gestao de Rotas',
        'routing'                        => 'Roteirizacao',
        'monitoring_of_deliveries'       => 'Monitoramento de Entregas',
        'driver_management'              => 'Gestao de Motoristas',
        'romaneio'                       => 'Romaneio',
        'vehicle_tracking'               => 'Rastreamento de Veiculos',
        'vehicle_maintenance'            => 'Manutencao de Veiculos',
        'transport_report'               => 'Relatorio de Transporte',
        'logistica/agendamento-entregas' => 'Agendamento de Entregas',
        'production_orders'              => 'Ordens de Producao',
        'employee_management'            => 'Gestao de Funcionarios (RH)',
        'rh_reports'                     => 'Relatorios de RH',
        'financial_reports'              => 'Relatorios Financeiros',
        'sales_report'                   => 'Relatorio de Vendas',
        'requests'                       => 'Pedidos de Venda',
        'visits'                         => 'Visitas Comerciais',
        'baccarat_accounts'              => 'Conciliacao Bancaria',
        'dashboard'                      => 'Dashboard',
        'dashboard/kpi'                  => 'Relatorio KPI',
        'configuracoes'                  => 'Configuracoes do Sistema',
        'empresas'                       => 'Gestao de Empresas',
        'suporte/chat'                   => 'Chat de Suporte',
    ];
    public function detectModuleFromPath(string $path): string
    {
        $path = trim($path, '/');

        // Root path → home
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
        try {
            return $this->getGeminiResponse($module, $pageName, $userMessage, $history);
        } catch (Exception $e) {
            $errorMsg = $e->getMessage();
            $isQuotaOrModel = str_contains($errorMsg, 'quota') ||
                              str_contains($errorMsg, 'RESOURCE_EXHAUSTED') ||
                              str_contains($errorMsg, 'not found') ||
                              str_contains($errorMsg, 'not supported');

            if ($isQuotaOrModel) {
                Log::warning('Gemini fallback to OpenAI', ['reason' => substr($errorMsg, 0, 200)]);
                try {
                    return $this->getOpenAiResponse($module, $pageName, $userMessage, $history);
                } catch (Exception $e2) {
                    Log::error('OpenAI fallback also failed', ['error' => $e2->getMessage()]);
                }
            } else {
                Log::error('AiAssistantService Error', [
                    'module'  => $module,
                    'page'    => $pageName,
                    'message' => $userMessage,
                    'error'   => $errorMsg,
                ]);
            }

            return 'Desculpe, ocorreu um erro ao processar sua pergunta. Tente novamente.';
        }
    }

    protected function getGeminiResponse(string $module, string $pageName, string $userMessage, array $history = []): string
    {
        $systemInstruction = Content::parse(part: $this->buildSystemInstruction($module, $pageName), role: Role::USER);
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

    protected function getOpenAiResponse(string $module, string $pageName, string $userMessage, array $history = []): string
    {
        $systemPrompt = $this->buildSystemInstruction($module, $pageName);
        $messages = [['role' => 'system', 'content' => $systemPrompt]];

        foreach (array_slice($history, -10) as $msg) {
            if (empty($msg['content'])) continue;
            $role = $msg['role'] === 'user' ? 'user' : 'assistant';
            $messages[] = ['role' => $role, 'content' => $msg['content']];
        }
        $messages[] = ['role' => 'user', 'content' => $userMessage];

        $response = OpenAI::chat()->create([
            'model'       => 'gpt-4o-mini',
            'messages'    => $messages,
            'max_tokens'  => (int) config('gemini.max_tokens', 2048),
            'temperature' => (float) config('gemini.temperature', 0.7),
        ]);

        return $response->choices[0]->message->content ?? 'Sem resposta.';
    }
    protected function buildSystemInstruction(string $module, string $pageName): string
    {
        $base = config("ai_contexts.{$module}", config('ai_contexts.suporte'));
        if ($pageName) {
            $base .= "\n\n**Contexto atual:** O usuario esta na pagina \"{$pageName}\" do sistema Nexora ERP. Foque suas respostas nessa tela e no que o usuario pode estar fazendo nela. Seja direto e pratico.";
        }
        return $base;
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
            'Plano de Contas'           => ['Como criar uma conta contabil?', 'Qual a estrutura de um plano de contas?', 'Como classificar despesas operacionais?'],
            'Contas a Pagar'            => ['Como registrar um novo titulo a pagar?', 'Como fazer um pagamento em lote?', 'Como visualizar titulos vencidos?'],
            'Contas a Receber'          => ['Como lancar um recebimento?', 'Como gerenciar inadimplentes?', 'Como baixar um titulo recebido?'],
            'Fluxo de Caixa'            => ['Como interpretar o fluxo de caixa?', 'O que sao entradas e saidas previstas?', 'Como melhorar o fluxo de caixa?'],
            'Folha de Pagamento'        => ['Como calcular o 13 salario?', 'Como lancar horas extras?', 'Como gerar o arquivo para banco?'],
            'Controle de Ponto'         => ['Como registrar uma batida de ponto?', 'Como corrigir uma batida incorreta?', 'Como calcular horas trabalhadas?'],
            'Notas Fiscais Eletronicas' => ['Como emitir uma NF-e?', 'O que e CFOP e como escolher?', 'Como cancelar uma nota fiscal?'],
            'NF de Entrada'             => ['Como fazer a entrada de uma nota fiscal?', 'Como importar XML de NF-e?'],
            'Ordens de Producao'        => ['Como criar uma ordem de producao?', 'Como apontar producao?', 'Como verificar o andamento da OP?'],
            'Estoque'                   => ['Como fazer um inventario?', 'O que e estoque minimo?', 'Como registrar uma movimentacao?'],
            'Pedidos de Compra'         => ['Como criar um pedido de compra?', 'Como aprovar um pedido?', 'Como receber um pedido?'],
            'Cotacoes'                  => ['Como criar uma cotacao?', 'Como comparar fornecedores?', 'Como aprovar uma cotacao?'],
            'Pedidos de Venda'          => ['Como criar um pedido de venda?', 'Como verificar o status do pedido?'],
            'Agendamento de Entregas'   => ['Como agendar uma entrega?', 'Como otimizar rotas?', 'Como confirmar uma entrega?'],
            'Dashboard'                 => ['O que mostram esses graficos?', 'Como melhorar meus indicadores?', 'Como filtrar por periodo?'],
        ];
        if ($pageName && isset($byPage[$pageName])) {
            return $byPage[$pageName];
        }
        $byModule = [
            'financeiro'    => ['Como analisar o DRE?', 'Como fazer conciliacao bancaria?', 'Quais sao os principais indicadores financeiros?'],
            'rh'            => ['Como calcular ferias?', 'Como lancar afastamento?', 'Quais sao os encargos sobre a folha?'],
            'producao'      => ['Como calcular o OEE?', 'O que e Lead Time?', 'Como reduzir refugo na producao?'],
            'estoque'       => ['O que e PEPS e UEPS?', 'Como calcular estoque minimo?', 'O que e curva ABC?'],
            'compras'       => ['Como negociar com fornecedores?', 'O que e lead time de compra?', 'Como reduzir custos de compras?'],
            'vendas'        => ['Como aumentar a taxa de conversao?', 'Como calcular margem de contribuicao?', 'Como gerenciar inadimplencia?'],
            'fiscal'        => ['O que e CFOP?', 'Como calcular ICMS ST?', 'Quais obrigacoes acessorias preciso enviar?'],
            'logistica'     => ['Como otimizar rotas de entrega?', 'O que e o romaneio?', 'Como calcular custo de frete?'],
            'cadastro'      => ['Como cadastrar um produto corretamente?', 'Quais dados sao obrigatorios para fornecedor?', 'Como classificar por NCM?'],
            'administracao' => ['Como criar um usuario?', 'Como configurar permissoes?', 'Como fazer backup do sistema?'],
            'suporte'       => ['Como abrir um chamado?', 'Como acompanhar meu ticket?', 'Qual o prazo de atendimento?'],
            'home'          => ['O que posso fazer neste sistema?', 'Como navegar entre os modulos?', 'Como configurar meu perfil?'],
        ];
        return $byModule[$module] ?? ['Como posso te ajudar?'];
    }
    public function getModuleName(string $module): string
    {
        return [
            'financeiro'    => 'Financeiro',
            'rh'            => 'Recursos Humanos',
            'producao'      => 'Producao',
            'estoque'       => 'Estoque',
            'compras'       => 'Compras',
            'vendas'        => 'Vendas',
            'logistica'     => 'Logistica',
            'fiscal'        => 'Fiscal',
            'administracao' => 'Administracao',
            'cadastro'      => 'Cadastros',
            'suporte'       => 'Suporte',
            'home'          => 'Inicio',
        ][$module] ?? 'Assistente';
    }
}
