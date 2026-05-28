# Nexora EMS ERP
Sistema ERP modular desenvolvido em **Laravel 13**, cobrindo os principais domínios de uma empresa: cadastros, vendas, compras, fiscal, financeiro, RH, produção, logística e estoque. O front-end utiliza **Blade + Tailwind CSS 4 + Bootstrap 5** com design system próprio, e conta com painel administrativo via **Filament 5**.
**Status:** desenvolvimento ativo — stack: Laravel 13.8 · Filament 5.6.2 · Livewire 4.3 · PHP 8.4
---
## Índice
- [Módulos](#módulos)
- [Stack e Dependências](#stack-e-dependências)
- [Estrutura de Pastas](#estrutura-de-pastas)
- [Instalação e Execução](#instalação-e-execução)
- [Configuração de Banco de Dados](#configuração-de-banco-de-dados)
- [Autenticação](#autenticação)
- [Dashboard](#dashboard)
- [Suporte](#suporte-suportechat)
- [Logs de Auditoria](#logs-de-auditoria-logs)
- [Notificações](#notificações)
- [Perfil do Usuário](#perfil-do-usuário-perfil)
- [Financeiro — Plano de Contas](#financeiro--plano-de-contas)
- [Financeiro — Contas Bancárias](#financeiro--contas-bancárias)
- [Financeiro — Contas a Pagar](#financeiro--contas-a-pagar)
- [Financeiro — Contas a Receber](#financeiro--contas-a-receber)
- [Financeiro — Fluxo de Caixa](#financeiro--fluxo-de-caixa)
- [RH — Jornada de Trabalho / Ponto](#rh--jornada-de-trabalho--ponto)
- [RH — Folha de Pagamento](#rh--folha-de-pagamento)
- [RH — Batida de Ponto](#rh--batida-de-ponto)
- [RH — Holerite](#rh--holerite)
- [Estoque — Movimentações](#estoque--movimentações)
- [Compras — Solicitações](#compras--solicitações-de-compra)
- [Compras — Pedidos](#compras--pedidos-de-compra)
- [Compras — Cotações](#compras--cotações)
- [Fiscal — Notas Fiscais](#fiscal--notas-fiscais)
- [Fiscal — NF-e de Entrada](#fiscal--nf-e-de-entrada)
- [Fiscal — Tipos de Operação](#fiscal--tipos-de-operação)
- [Fiscal — Grupos Tributários](#fiscal--grupos-tributários)
- [Integração SEFAZ](#integração-sefaz)
- [Logística — Agendamento de Entregas](#logística--agendamento-de-entregas)
- [Vendas — Pedidos de Venda](#vendas--pedidos-de-venda)
- [Vendas — Tabelas de Precificação](#vendas--tabelas-de-precificação)
- [Produção — Ordens de Produção](#produção--ordens-de-produção)
- [Cadastro — Categorias de Produto](#cadastro--categorias-de-produto)
- [Cadastro — Unidades de Medida](#cadastro--unidades-de-medida)
- [Administração — Empresas](#administração--empresas)
- [Configurações do Sistema](#configurações-do-sistema)
- [Controle de Usuários](#controle-de-usuários)
- [Assistente IA](#assistente-ia)
- [Fluxo de Dados entre Módulos](#fluxo-de-dados-entre-módulos)
- [API REST](#api-rest)
- [Middleware](#middleware)
- [Painel Administrativo (Filament)](#painel-administrativo-filament)
- [Design System](#design-system)
- [Testes](#testes)
- [Comandos Úteis](#comandos-úteis)
- [Diretrizes de Desenvolvimento](#diretrizes-de-desenvolvimento)
---
## Módulos
| Módulo | Status |
|---|---|
| Cadastro (Clientes, Fornecedores, Produtos, Funcionários, Funções, Veículos) | ✅ Ativo |
| Cadastro — Categorias de Produto (`/product-categories`) | ✅ Implementado |
| Cadastro — Unidades de Medida (`/unit-of-measures`) | ✅ Implementado |
| Dashboard — Visão Geral + KPIs (`/dashboard`) | ✅ Implementado (dados reais) |
| Controle de Usuários (`/users`) | ✅ Ativo (CRUD + status + licença + módulos) |
| Configurações do Sistema (`/configuracoes`) | ✅ Ativo (9 seções) |
| Suporte (`/suporte/chat`) | ✅ Ativo (tickets + chat em tempo real) |
| Logs de Auditoria (`/logs`) | ✅ Ativo (somente admin) |
| Notificações (`/notificacoes`) | ✅ Ativo (dropdown + central) |
| Perfil do Usuário (`/perfil`) | ✅ Ativo (info, senha, avatar) |
| Financeiro — Plano de Contas (`/plans_of_accounts`) | ✅ Implementado (tree view hierárquico) |
| Financeiro — Contas Bancárias (`/contas-bancarias`) | ✅ Implementado (cards + transferência + conciliação) |
| Financeiro — Contas a Pagar (`/accounts_payable`) | ✅ Implementado (CRUD + baixa + reagendamento + KPIs) |
| Financeiro — Contas a Receber (`/accounts_receivable`) | ✅ Implementado (CRUD + baixa + reagendamento + KPIs) |
| Financeiro — Fluxo de Caixa (`/cash_flow`) | ✅ Implementado (regime caixa/competência + gráfico) |
| RH — Jornada / Ponto (`/working_day`) | ✅ Implementado (turnos + registros + KPIs) |
| RH — Batida de Ponto (`/stitch_beat`) | ✅ Implementado (registro sequencial + geolocalização) |
| RH — Holerite (`/holerite`) | ✅ Implementado (visualização, verbas, fechar, pagar) |
| RH — Folha de Pagamento (`/payroll`) | ✅ Implementado (geração + fechamento + pagamento) |
| Estoque — Movimentações | ✅ Implementado (componente Livewire) |
| Produção — Ordens de Produção (`/production_orders`) | ✅ Implementado (CRUD + BOM + multi-produto + progresso) |
| Vendas — Pedidos de Venda (`/vendas/pedidos`) | ✅ Implementado (CRUD + itens + parcelas + entrega + log) |
| Vendas — Tabelas de Precificação (`/vendas/precificacao`) | ✅ Implementado (CRUD + calculadora markup) |
| Compras — Solicitações (`/compras/solicitacoes`) | ✅ Implementado (CRUD + aprovação + conversão) |
| Compras — Pedidos (`/compras/pedidos`) | ✅ Implementado (multi-abas + aprovação + frete) |
| Compras — Cotações (`/compras/cotacoes`) | ✅ Implementado (respostas por fornecedor + conversão) |
| Fiscal — Notas Fiscais (`/fiscal/notas-fiscais`) | ✅ Implementado (CRUD NF-e + emissão + cancelamento) |
| Fiscal — Tipos de Operação (`/fiscal/tipos-operacao`) | ✅ Implementado |
| Fiscal — Grupos Tributários (`/fiscal/grupos-tributarios`) | ✅ Implementado |
| Logística — Agendamento de Entregas (`/logistica/agendamento-entregas`) | ✅ Implementado (CRUD + janelas de tempo + reagendamento) |
| Empresas (`/empresas`) | ✅ Implementado (somente admin) |
| Fiscal — NF-e de Entrada | 🚧 Em breve |
| Painel Administrativo Filament (`/admin`) | ✅ Ativo |
| Assistente IA (chat bubble global) | ✅ Implementado (OpenAI + Gemini + RAG + PlaybookEngine) |
---
## Requisitos
- PHP 8.4+
- Composer 2+
- Node.js 20+ e npm 10+
- MySQL 8.x / 9.x (padrão) — ou SQLite/PostgreSQL
---
## Stack e Dependências
| Camada | Tecnologia | Versão |
|---|---|---|
| Backend | Laravel | ^13.0 (13.8.0) |
| Componentes reativos | Livewire | ^4.1 (4.3.0) |
| Admin Panel | Filament | ^5.0 (5.6.2) |
| Build | Vite | ^7.0 (7.3.1) |
| CSS Framework | Tailwind CSS | ^4.0 |
| CSS Componentes | Bootstrap | ^5.3.8 |
| Gráficos | ApexCharts | CDN |
| PDF | barryvdh/laravel-dompdf | ^3.1 (3.1.2) |
| IA — Google Gemini | google-gemini-php/laravel | ^2.0 (2.0.4) |
| IA — OpenAI | openai-php/laravel | ^0.19.1 |
| Testes | Pest + Plugin Laravel | ^4.4 / ^4.1 (4.7.0) |
| PHP mínimo | — | 8.4 |
### Dependências de Desenvolvimento
| Pacote | Versão |
|---|---|
| `fakerphp/faker` | ^1.23 |
| `laravel/pail` | ^1.2.2 |
| `laravel/pint` | ^1.24 |
| `laravel/sail` | ^1.41 |
| `nunomaduro/collision` | ^8.6 |
| `pestphp/pest` | ^4.4 |
| `pestphp/pest-plugin-laravel` | ^4.1 |
---
## Estrutura de Pastas
```text
app/
  Ai/
    AgenteService.php           # Loop de function calling (OpenAI → Gemini → Fallback)
    ContextBuilder.php          # Histórico de mensagens e contexto do ticket
    FallbackResponder.php       # Resposta quando todos os provedores falham
    PlaybookEngine.php          # Raciocínio estruturado por domínio
    RagService.php              # Busca híbrida lexical + vetorial (MySQL 8/9)
    ToolRegistry.php            # Registro das ferramentas de function calling
    Tools/
      BaseTool.php
      AnalisarXmlNfeTool.php · BuscarCodigoFonteTool.php · BuscarPedidoTool.php
      ConsultarClienteTool.php · ConsultarDiagnosticoSefazTool.php
      ConsultarEstoqueTool.php · ConsultarFinanceiroTool.php
      ConsultarTicketTool.php · InspecionarLogsTool.php · VerificarNfeTool.php
  Console/Commands/
    RagSeedCommand.php    # php artisan rag:seed
    RagSearchCommand.php  # php artisan rag:search
  Enums/                  # Enums de domínio (cast nos modelos)
  Http/
    Controllers/
      Api/                # Controllers da API REST
      Auth/SessionController.php
      (demais controllers por domínio)
    Middleware/
      DetectAiModule.php · EnforceMidnightSession.php
      EnsureUserIsAdmin.php · MaintenanceERP.php
  Livewire/               # Componentes por módulo
    Administracao/ · Cadastro/ · Compras/ · Dashboard/
    Estoque/ · Financeiro/ · Fiscal/ · Logistica/
    Producao/ · Rh/ · Suporte/ · Vendas/
    AiChatBubble.php · NotificationDropdown.php
  Models/                 # Modelos Eloquent
  Observers/
    SalesOrderObserver.php
  Services/
    AiAssistantService.php · BrasilAPIService.php
    ContasPagarService.php · ContasReceberService.php
    JornadaService.php · LogService.php · PayrollService.php
    PontoService.php · PricingService.php · RoleService.php
    SalesOrderService.php · SuporteService.php
    Dashboard/DashboardMetricsService.php
  Support/
    PerPage.php           # Seletor de itens por página (10/20/50/100/200)
config/
  ai_contexts.php · gemini.php · openai.php
database/
  migrations/
  seeders/
    AdminUserSeeder.php · ClientSeeder.php · DatabaseSeeder.php
    ProductSeeder.php · RagKnowledgeSeeder.php · SefazDiagnosticsSeeder.php
    SettingsSeeder.php · SupplierSeeder.php · SystemLogSeeder.php · UserSeeder.php
resources/
  css/
    app.css · _base.css · _layout.css · _components.css · _tables.css
  views/
    auth/login.blade.php · layouts/app.blade.php
    system/desenvolvimento.blade.php  # Tela "Em Breve"
    (demais views por módulo)
routes/
  web.php                 # Inclui todos os arquivos de domínio
  administracao.php · cadastro.php · compras.php · estoque.php
  financeiro.php · fiscal.php · logistica.php · perfil.php
  producao.php · rh.php · vendas.php · api.php
tests/
  Feature/ · Unit/
```
---
## Instalação e Execução
### 1. Clonar o projeto
```bash
git clone https://github.com/DiegoGS1002/nexora-ems-erp.git
cd nexora-ems-erp
```
### 2. Setup rápido (recomendado)
```bash
composer run setup
```
Executa em sequência: `composer install` → criação do `.env` → `key:generate` → `migrate` → `npm install` → `npm run build`.
### 3. Ambiente de desenvolvimento
```bash
composer run dev
```
Sobe em paralelo: servidor Laravel + listener de filas + Vite com HMR.
### 4. Instalação manual
```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run dev
php artisan serve
```
---
## Configuração de Banco de Dados
```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nexora
DB_USERNAME=root
DB_PASSWORD=
```
```bash
php artisan migrate           # Migrar
php artisan migrate:fresh --seed  # Recriar do zero
```
### Seeders
| Seeder | Objetivo |
|---|---|
| `AdminUserSeeder` | Usuário administrador padrão |
| `ClientSeeder` | Carga inicial de clientes |
| `ProductSeeder` | Carga inicial de produtos |
| `SupplierSeeder` | Carga inicial de fornecedores |
| `SettingsSeeder` | Valores padrão das 9 seções de configuração |
| `SystemLogSeeder` | Logs de exemplo para desenvolvimento |
| `UserSeeder` | Usuários de exemplo para desenvolvimento |
| `RagKnowledgeSeeder` | 47 documentos RAG (executar + `ai:vectorize` para embeddings) |
| `SefazDiagnosticsSeeder` | 21 diagnósticos de rejeição SEFAZ |
```bash
php artisan db:seed --no-interaction
php artisan db:seed --class=SettingsSeeder --no-interaction
```
> Os seeders são idempotentes (`updateOrCreate` / `firstOrCreate`).
### Docker
```dotenv
DOCKER_DB_HOST=db
DOCKER_DB_DATABASE=nexora
DOCKER_DB_USERNAME=nexora
DOCKER_DB_PASSWORD=nexora
DOCKER_DB_EXPOSED_PORT=3307
```
```bash
docker compose up -d --build
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed --no-interaction
```
**Troubleshooting:**
- `getaddrinfo for db failed`: rode os comandos dentro do container ou ajuste `DB_HOST=127.0.0.1` no `.env` local.
- Permissão em `storage/`: `sudo chown -R "$USER":"$USER" storage bootstrap/cache && chmod -R ug+rw storage bootstrap/cache`
---
## Autenticação
| Método | URI | Nome | Descrição |
|---|---|---|---|
| `GET` | `/login` | `login` | Tela de login |
| `POST` | `/login` | `login.store` | Processa o login |
| `POST` | `/logout` | `logout` | Encerra a sessão |
Registra `last_login_at` no usuário. Usuários com `is_active = false` têm acesso bloqueado.
---
## Dashboard
| URL | Nome | Componente |
|---|---|---|
| `/dashboard` | `dashboard.index` | `App\Livewire\Dashboard\Overview` |
| `/dashboard/kpi` | `dashboard.kpi` | `App\Livewire\Dashboard\KpiReport` |
**Visão Geral:** 4 cards KPI, gráfico de linha mensal, gráfico donut por categoria, pedidos recentes e movimentações. Auto-refresh a cada 10 s.
**KPI Report:** gráfico interativo com filtro por clique, barras Meta × Realizado, comparativos mensais e tabela detalhada com busca.
---
## Suporte (`/suporte/chat`)
- Criação de tickets com assunto, prioridade e categoria
- Chat em tempo real (Livewire polling)
- Gestão de status: Aberto → Em Andamento → Resolvido → Fechado
- Administradores veem todos os tickets; usuários comuns apenas os seus
| Modelo | Tabela |
|---|---|
| `TicketSuporte` | `tickets_suporte` |
| `MensagemSuporte` | `mensagens_suporte` |
---
## Logs de Auditoria (`/logs`)
Acessível apenas por administradores. Componente Livewire full-page.
```php
LogService::success('LOGIN', 'Usuário realizou login.', 'Segurança');
LogService::warning('ACESSO_NEGADO', 'Tentativa sem permissão.', 'Segurança');
LogService::error('ERRO_API', 'Falha na BrasilAPI.', 'Integrações', ['status' => 500]);
```
---
## Notificações
**Dropdown no topbar** (`NotificationDropdown`): ícone com badge de não lidas, painel com até 10 notificações recentes, marcar lida individual ou em lote.
**Central (`/notificacoes`)**: KPIs, filtros por tipo/status/data, ações de exclusão em lote. Usa o sistema nativo de notificações do Laravel.
---
## Perfil do Usuário (`/perfil`)
- Informações pessoais: nome, telefone, cargo, departamento, bio
- Troca de senha com validação da senha atual
- Avatar: upload (jpg/jpeg/png/webp, máx. 2 MB) em `storage/app/public/avatars/`
| Método | URI | Nome |
|---|---|---|
| `GET` | `/perfil` | `profile.index` |
| `PATCH` | `/perfil/info` | `profile.updateInfo` |
| `PATCH` | `/perfil/senha` | `profile.updatePassword` |
| `POST` | `/perfil/avatar` | `profile.uploadAvatar` |
| `DELETE` | `/perfil/avatar` | `profile.removeAvatar` |
---
## Financeiro — Plano de Contas (`/plans_of_accounts`)
Componente Livewire full-page (`App\Livewire\Financeiro\PlanoContas`).
- Tree view hierárquico (até 5 níveis), criar conta raiz ou subconta
- Editar/excluir (bloqueado se possuir subcontas)
- Busca em tempo real, filtro por tipo, KPIs
### Modelo `PlanOfAccount`
| Campo | Tipo | Descrição |
|---|---|---|
| `parent_id` | bigint (nullable) | FK para hierarquia |
| `code` | string(30) | Ex.: `1.01.002` |
| `name` | string | |
| `type` | enum | `receita`, `despesa`, `ativo`, `passivo` |
| `is_selectable` | boolean | `false` = conta sintética |
| `is_active` | boolean | |
---
## Financeiro — Contas Bancárias (`/contas-bancarias`)
Componente Livewire full-page (`App\Livewire\Financeiro\ContaBancaria`).
- Cards por banco com gradiente por instituição
- CRUD via modal, transferência entre contas, conciliação
- KPIs: saldo total, previsto, ativas, conciliadas
### Modelo `BaccaratAccount`
| Campo | Tipo | Descrição |
|---|---|---|
| `name` | string | Apelido |
| `bank_name` | string | Nome do banco |
| `type` | enum | `corrente`, `poupanca`, `caixa_interno`, `digital` |
| `balance` | decimal:2 | Saldo atual |
| `predicted_balance` | decimal:2 | Saldo previsto |
| `chart_of_account_id` | bigint (nullable) | FK plano de contas |
| `is_reconciled` | boolean | |
| `last_reconciled_at` | date (nullable) | |
---
## Financeiro — Contas a Pagar (`/accounts_payable`)
Componente Livewire full-page (`App\Livewire\Financeiro\ContasPagar`).
- CRUD, baixa de pagamento, reagendamento, cancelamento, recorrência
- Sincronização automática de vencidos
- KPIs: a pagar hoje, na semana, total pendente, pago no mês
### Modelo `AccountPayable`
| Campo | Tipo |
|---|---|
| `description_title` | string |
| `supplier_id` | bigint (nullable) |
| `chart_of_account_id` | bigint (nullable) |
| `amount` / `paid_amount` | decimal:2 |
| `due_date_at` | date |
| `payment_date` | date (nullable) |
| `status` | enum `PayableStatus` |
| `is_recurring` | boolean |
---
## Financeiro — Contas a Receber (`/accounts_receivable`)
Componente Livewire full-page (`App\Livewire\Financeiro\ContasReceber`).
- CRUD, baixa de recebimento, reagendamento, cancelamento
- Filtros por status, mês e forma de pagamento
- KPIs: a receber hoje, na semana, total pendente, recebido no mês
### Modelo `AccountReceivable`
| Campo | Tipo |
|---|---|
| `description_title` | string |
| `client_id` | UUID (nullable) |
| `amount` / `received_amount` | decimal:2 |
| `due_date_at` | date |
| `received_at` | date (nullable) |
| `payment_method` | string (nullable) |
| `installment_number` | integer |
| `status` | enum `ReceivableStatus` |
---
## Financeiro — Fluxo de Caixa (`/cash_flow`)
Componente Livewire full-page (`App\Livewire\Financeiro\FluxoCaixa`).
- Regime **Caixa** (valores efetivos) ou **Competência** (todos os lançamentos)
- Períodos: semana, mês, trimestre, ano
- KPIs: saldo inicial, entradas, saídas, saldo final, projetado
- Gráfico de barras diário e tabela com saldo acumulado
---
## RH — Jornada de Trabalho / Ponto (`/working_day`)
Componente Livewire full-page (`App\Livewire\Rh\JornadaTrabalho`).
- Gestão de Turnos (`WorkShift`), registros de ponto, grade de presença
- Timeline cronológica do dia, filtro por data e status
- KPIs: total, presentes, em pausa, ausentes, banco de horas
| Modelo | Tabela |
|---|---|
| `WorkShift` | `work_shifts` |
| `TimeRecord` | `time_records` |
---
## RH — Folha de Pagamento (`/payroll`)
Componente Livewire full-page (`App\Livewire\Rh\FolhaPagamento`).
- Geração individual ou em lote (idempotente)
- Edição de proventos e descontos com recalculação automática
- Fluxo: Rascunho → Fechada → Paga
- KPIs: proventos, descontos, líquido, por status
| Modelo | Tabela |
|---|---|
| `Payroll` | `payrolls` |
| `PayrollItem` | `payroll_items` |
---
## RH — Batida de Ponto (`/stitch_beat`)
Componente Livewire full-page (`App\Livewire\Rh\BatidaPonto`).
- Registro sequencial: Entrada → Pausa → Retorno → Saída
- Geolocalização via API do browser
- Horário previsto de saída calculado pelo turno
- Funcionário identificado pelo e-mail do usuário logado
---
## RH — Holerite (`/holerite`)
Página dedicada com painel lateral de seleção (`App\Livewire\Rh\Holerite`).
- Visualização completa (proventos, descontos, líquido, dados da empresa)
- Edição de verbas com recalculação automática
- Fechar (Rascunho → Fechada) e Pagar (Fechada → Paga)
- Impressão via `HoleriteController@print`
---
## Estoque — Movimentações
Componente Livewire (`App\Livewire\Estoque\Movimentacao`).
- Entrada, saída, ajuste ou transferência (atualiza `stock` do produto)
- Editar/excluir (exclusão reverte o estoque)
- Filtros por produto, tipo e período
- KPIs: total, entradas, saídas, ajustes
### Modelo `StockMovement`
| Campo | Tipo |
|---|---|
| `product_id` | UUID |
| `user_id` | bigint |
| `quantity` | decimal:3 |
| `type` | `input`, `output`, `adjustment`, `transfer` |
| `origin` | string |
| `unit_cost` | decimal:2 (nullable) |
---
## Compras — Solicitações de Compra (`/compras/solicitacoes`)
Componente Livewire full-page (`App\Livewire\Compras\SolicitacoesCompra`).
- CRUD via modal multi-abas (Geral + Itens)
- Fluxo: Rascunho → Aguardando → Aprovada / Rejeitada
- Conversão para Pedido de Compra ou Cotação
- KPIs: total, aguardando, aprovadas, rejeitadas, convertidas
---
## Compras — Pedidos de Compra (`/compras/pedidos`)
Componente Livewire full-page (`App\Livewire\Compras\PedidosCompra`).
- CRUD via modal com 6 abas (Geral, Itens, Pagamento, Totais, Logística, Observações)
- Cálculo automático de totais, fluxo de aprovação
- Tipos de frete (CIF, FOB, terceiros, próprio) com vínculo em `Carrier`
---
## Compras — Cotações (`/compras/cotacoes`)
Componente Livewire full-page (`App\Livewire\Compras\Cotacoes`).
- Respostas por fornecedor com preço e prazo de entrega
- Seleção do melhor preço por item
- Conversão para Pedido de Compra
---
## Fiscal — Notas Fiscais (`/fiscal/notas-fiscais`)
Componente Livewire full-page (`App\Livewire\Fiscal\NotaFiscal`).
- CRUD (criar, visualizar, cancelar, excluir)
- Filtros por status, tipo (NF-e/NFC-e) e ambiente
- KPIs: total, emitidas, canceladas, rascunhos
### Modelo `FiscalNote`
| Campo | Tipo |
|---|---|
| `chave_acesso` | string(44) (nullable) |
| `numero` | integer (nullable) |
| `tipo` | `nfe`, `nfce`, `nfs` |
| `ambiente` | `homologacao`, `producao` |
| `status` | enum `FiscalNoteStatus` |
| `client_id` | UUID (nullable) |
| `sales_order_id` | UUID (nullable) |
| `total_value` | decimal:2 |
---
## Fiscal — NF-e de Entrada
> 🚧 **Em breve** — modelos e migration criados. Interface Livewire ainda não implementada.
Modelos: `FiscalInvoiceIn` (chave, fornecedor, data, valor) e `FiscalInvoiceItemIn` (produto, quantidade, preços).
---
## Fiscal — Tipos de Operação (`/fiscal/tipos-operacao`)
- CRUD com Index paginado e Form dedicado
- Vínculo com CFOP, natureza da operação e tipo de movimento
| Rota | Nome |
|---|---|
| `GET /fiscal/tipos-operacao` | `fiscal.tipo-operacao.index` |
| `GET /fiscal/tipos-operacao/create` | `fiscal.tipo-operacao.create` |
| `GET /fiscal/tipos-operacao/{operacao}/edit` | `fiscal.tipo-operacao.edit` |
---
## Fiscal — Grupos Tributários (`/fiscal/grupos-tributarios`)
- CRUD com Index paginado e Form dedicado
- Configuração de ICMS, IPI, PIS/COFINS por regime tributário
- Aplicado a produtos para cálculo automático de impostos
| Rota | Nome |
|---|---|
| `GET /fiscal/grupos-tributarios` | `fiscal.grupo-tributario.index` |
| `GET /fiscal/grupos-tributarios/create` | `fiscal.grupo-tributario.create` |
| `GET /fiscal/grupos-tributarios/{grupo}/edit` | `fiscal.grupo-tributario.edit` |
---
## Integração SEFAZ
Integração completa para emissão de NF-e (modelo 55) e NFC-e (modelo 65).
**Recursos:**
- ✅ Emissão e transmissão · ✅ Cancelamento · ✅ Consulta de status
- ✅ Geração de DANFE em PDF · ✅ Armazenamento de XMLs
**Configuração:**
1. Configure as variáveis `NFE_*` no `.env`
2. Coloque o certificado A1 em `storage/app/nfe/certificado.pfx`
3. Execute `php artisan migrate`
**Bibliotecas:** `nfephp-org/sped-nfe` v5.2+ · `nfephp-org/sped-da` v1.1+
**Documentação completa:** [`docs/NF-e/IMPLEMENTACAO_API_NFE.md`](docs/NF-e/IMPLEMENTACAO_API_NFE.md)
### API NF-e — prefixo `/api/nfe`, middleware `auth`
| Método | URI | Nome |
|---|---|---|
| `POST` | `/api/nfe/{note}/transmitir` | `api.nfe.transmitir` |
| `POST` | `/api/nfe/{note}/cancelar` | `api.nfe.cancelar` |
| `GET` | `/api/nfe/status` | `api.nfe.status` |
| `POST` | `/api/nfe/consultar` | `api.nfe.consultar` |
| `GET` | `/api/nfe/{note}/danfe` | `api.nfe.danfe` |
| `GET` | `/api/nfe/{note}/xml` | `api.nfe.xml` |
---
## Logística — Agendamento de Entregas (`/logistica/agendamento-entregas`)
Componente Livewire full-page (`App\Livewire\Logistica\AgendamentoEntregas`).
- CRUD via modal, reagendamento sem recriar o registro
- Janelas de tempo (`DeliveryTimeWindow`) pré-cadastradas
- Vínculo com Pedido de Venda e Veículo
- KPIs: agendados, em rota, entregues, cancelados
---
## Vendas — Pedidos de Venda (`/vendas/pedidos`)
Componente Livewire full-page (`App\Livewire\Vendas\PedidosVenda`).
- CRUD via modal multi-abas (Cabeçalho, Itens, Entrega, Pagamento, Observações)
- Tabela de preços, tipo de operação fiscal, canal de venda
- Fluxo: novo → aprovado → em separação → enviado → entregue / cancelado
- Log automático, anexos, parcelas
**Service:** `SalesOrderService` — `createOrder()`, `updateOrder()`, aprovação, cancelamento.
**Observer:** `SalesOrderObserver` — reage a `created`, `updated`, `deleted` para log e integrações inter-módulos.
---
## Vendas — Tabelas de Precificação (`/vendas/precificacao`)
Componente Livewire full-page (`App\Livewire\Vendas\TabelasPrecificacao`).
- CRUD de tabelas (nome, código, vigência, is_default)
- Calculadora de markup: custo + percentuais → preço final, markup divisor, margem
**Service:** `PricingService::calculateFinalPrice(array $params): array`
---
## Produção — Ordens de Produção (`/production_orders`)
Componente Livewire (`App\Livewire\Producao\OrdemProducao`).
- CRUD via modal, multi-produto por OP, BOM de matérias-primas
- Modal de progresso: lançar quantidades, pausar/retomar
- Fluxo: Pendente → Em Produção → Pausada → Concluída / Cancelada
---
## Cadastro — Categorias de Produto (`/product-categories`)
- CRUD com slug automático e cor associada, soft delete
- Relacionamento com `Product` via `product_category_id`
| Campo | Tipo |
|---|---|
| `id` | UUID |
| `name` | string |
| `slug` | string (auto-gerado) |
| `color` | string (nullable) |
| `is_active` | boolean |
---
## Cadastro — Unidades de Medida (`/unit-of-measures`)
- Abreviação normalizada para maiúsculas, soft delete
- Relacionamento com `Product` via `unit_of_measure_id`
| Campo | Tipo |
|---|---|
| `id` | UUID |
| `name` | string |
| `abbreviation` | string (maiúsculas) |
| `is_active` | boolean |
---
## Administração — Empresas (`/empresas`)
Acessível somente por administradores. Componentes Livewire full-page.
- Listagem com busca e filtro por status
- CRUD: razão social, CNPJ, endereço, regime tributário, logo
| Rota | Nome |
|---|---|
| `GET /empresas` | `companies.index` |
| `GET /empresas/create` | `companies.create` |
| `GET /empresas/{company}/edit` | `companies.edit` |
---
## Configurações do Sistema (`/configuracoes`)
Restrito a administradores. Tabela `settings` com padrão key-value por grupo e cache automático.
```php
Setting::get('system_name', 'Nexora ERP');
Setting::set('system_name', 'Minha Empresa');
Setting::group('general');
```
### Seções (9 abas)
| Aba | Grupo | Exemplos |
|---|---|---|
| **Geral** | `general` | Nome do sistema, fuso horário, idioma |
| **Empresa** | `company` | Razão social, CNPJ, endereço |
| **Financeiro** | `financial` | Moeda, separadores, alíquota |
| **Notificações** | `notifications` | Alertas de estoque, e-mail boas-vindas |
| **Aparência** | `appearance` | Tema, cor primária, sidebar |
| **Segurança** | `security` | Sessão, senha forte, modo manutenção |
| **Regras de Estoque** | `stock` | Venda sem estoque, reserva, alerta crítico |
| **Regras Fiscais** | `fiscal` | CFOP padrão, ambiente SEFAZ |
| **Regras de Venda** | `sales` | Tipo de venda, desconto máximo |
---
## Controle de Usuários (`/users`)
Acessível somente por administradores.
| Campo | Descrição |
|---|---|
| `is_admin` | Acesso total sem restrições |
| `is_active` | Inativo = login bloqueado |
| `has_license` | Sem licença = modal de aviso a cada 15 s |
| `modules` | JSON — módulos contratados |
| `last_login_at` | Registrado pelo `SessionController` |
---
## Assistente IA
Balão de chat global no canto inferior direito, disponível em qualquer página.
### Arquitetura
```
Usuário → AiChatBubble (Livewire) → AiAssistantService → [OpenAI | Gemini | FallbackResponder]
                                           ↕
                                     AgenteService (function calling)
                                           ↕
                                     ToolRegistry → Tools
```
### Ferramentas disponíveis
| Ferramenta | Descrição |
|---|---|
| `buscar_pedido` | Pedidos de venda por número ou cliente |
| `consultar_cliente` | Dados cadastrais de clientes |
| `consultar_estoque` | Saldo em estoque por produto |
| `consultar_financeiro` | Contas a pagar/receber e fluxo de caixa |
| `consultar_ticket` | Dados do ticket de suporte |
| `verificar_nfe` | Status de Notas Fiscais |
| `analisar_xml_nfe` | Validação de campos críticos de XML NF-e |
| `buscar_codigo_fonte` | Pesquisa no código-fonte do ERP |
| `consultar_diagnostico_sefaz` | Diagnóstico determinístico de rejeições SEFAZ |
| `inspecionar_logs` | Logs de auditoria para diagnóstico |
### Configuração
```dotenv
OPENAI_API_KEY=sk-...
OPENAI_MODEL=gpt-4o-mini
GEMINI_API_KEY=AI...
GEMINI_MODEL=gemini-1.5-flash
```
Contextos por módulo em `config/ai_contexts.php`.
### Fallback Chain
```
1. OpenAI  → function calling nativo
2. Gemini  → function calling via Google AI
3. FallbackResponder → resposta genérica sem IA
```
### RAG (`RagService`)
| MySQL | Coluna `embedding` | Método |
|---|---|---|
| 9.x | `VECTOR(1536)` nativo | `VECTOR_TO_STRING()` + cosseno PHP |
| 8.x | `LONGTEXT` (JSON) | cosseno calculado em PHP |
**Hierarquia de busca:**
```
1. SefazDiagnostics (SQL estruturado) → diagnóstico determinístico
2. RagService (busca vetorial)         → documentos semânticos
3. LLM                                 → geração contextualizada
```
```bash
php artisan rag:seed
php artisan rag:search "rejeição 539 NF-e"
```
### PlaybookEngine
Detecta o domínio do problema e retorna playbook estruturado para guiar o agente:
| Domínio | Escopo |
|---|---|
| Fiscal | CFOP/NCM/CST, SEFAZ, certificado, XML |
| Financeiro | contas, fluxo de caixa, reconciliação |
| Estoque | saldo, movimentações |
| RH | ponto, folha, holerite |
| Vendas | pedido, precificação, parcelas |
| Compras | solicitação, cotação, aprovação |
| Acesso | login, permissão, licença |
---
## Fluxo de Dados entre Módulos
```
CADASTRO → Base compartilhada (Clientes, Produtos, Fornecedores, Funcionários)
COMPRAS
  Solicitação ──► Cotação ──► Pedido
  Pedido [RecebidoTotal]
      ├──► ESTOQUE: StockMovement(input) + Product.stock++
      └──► FINANCEIRO: AccountPayable criada
VENDAS
  Pedido [EmSeparacao]  ──► ESTOQUE: StockMovement(output) + Product.stock--
  Pedido [Invoiced]     ──► FINANCEIRO: AccountReceivable + FISCAL: FiscalNote(draft)
  Pedido [Delivered]    ──► marcado pela LOGÍSTICA
PRODUÇÃO
  OP [Completed]
      ├──► ESTOQUE: StockMovement(output) por insumo (BOM)
      └──► ESTOQUE: StockMovement(input) por produto acabado
RH
  Folha [Paid] ──► FINANCEIRO: AccountPayable quitada
LOGÍSTICA
  Entrega [Entregue] ──► VENDAS: SalesOrder.status → Delivered
```
| Evento | Responsável | Ação |
|---|---|---|
| Compra → `RecebidoTotal` | `PedidosCompra::changeStatus()` | StockMovement(input) + AccountPayable |
| Venda → `EmSeparacao` | `SalesOrderObserver::onSeparation()` | StockMovement(output) |
| Venda → `Invoiced` | `SalesOrderObserver::onInvoiced()` | AccountReceivable + FiscalNote(draft) |
| Venda → `Delivered` | `AgendamentoEntregas::changeStatus()` | SalesOrder.status atualizado |
| OP → `Completed` | `OrdemProducao::changeStatus()` | StockMovement(out insumos + in acabados) |
| Folha → `Paid` | `PayrollService::markAsPaid()` | AccountPayable quitada |
---
## API REST
Todos os endpoints ficam sob o prefixo `/api`.
### Proxy BrasilAPI — middleware `auth`
| Método | URI |
|---|---|
| `GET` | `/api/proxy/cnpj/{cnpj}` |
| `GET` | `/api/proxy/cep/{cep}` |
### Recursos CRUD — middleware `api`
| Recurso | Prefixo |
|---|---|
| Produtos | `/api/products` |
| Fornecedores | `/api/suppliers` |
| Clientes | `/api/clients` |
| Movimentações de Estoque | `/api/stock-movements` |
| Contas a Pagar | `/api/accounts-payable` |
| Contas a Receber | `/api/accounts-receivable` |
Cada recurso suporta: `GET` (listar), `POST` (criar), `GET /{id}` (detalhar), `PUT/PATCH /{id}` (atualizar), `DELETE /{id}` (remover).
Relacionamento produto × fornecedor:
- `GET /api/products/{product}/suppliers`
- `POST /api/products/{product}/suppliers`
- `DELETE /api/products/{product}/suppliers/{supplier}`
### Pedidos de Venda — prefixo `/api/sales-orders`
| Método | URI | Descrição |
|---|---|---|
| `GET / POST` | `/api/sales-orders` | Listar / Criar |
| `GET` | `/api/sales-orders/statistics` | Estatísticas |
| `POST` | `/api/sales-orders/calculate` | Simulação |
| `GET / PUT / PATCH / DELETE` | `/api/sales-orders/{order}` | CRUD |
| `POST` | `/api/sales-orders/{order}/approve` | Aprovar |
| `POST` | `/api/sales-orders/{order}/cancel` | Cancelar |
| `GET` | `/api/sales-orders/{order}/logs` | Histórico |
| `GET` | `/api/sales-orders/{order}/attachments` | Anexos |
---
## Middleware
### `MaintenanceERP`
Rotas fora do whitelist exibem a view `system.desenvolvimento` ("Em Breve").
> Ao criar uma nova rota acessível, adicionar o padrão `rotaNova.*` em `MaintenanceERP::handle()`.
### `DetectAiModule`
Detecta o módulo de contexto pelo primeiro segmento da URL e compartilha `$aiModule` com todas as views.
### `EnforceMidnightSession`
Encerra a sessão se a data do login for anterior ao dia atual, redirecionando para `/login`.
### `EnsureUserIsAdmin`
Bloqueia não-administradores nas rotas de users, permissions e logs.
---
## Painel Administrativo (Filament)
Acesse em `/admin`. Cor primária: Amber.
```bash
php artisan make:filament-user
```
---
## Design System
| Arquivo | Conteúdo |
|---|---|
| `_base.css` | Reset, tipografia, utilitários |
| `_layout.css` | Sidebar, topbar, variáveis CSS |
| `_components.css` | Cards, KPIs, badges, modal de licença, settings |
| `_tables.css` | Tabelas dos módulos |
> Após editar qualquer arquivo em `resources/css/`, rode `npm run build`.
**Modal de Licença:** exibido para usuários sem licença. Abre 1,5 s após o carregamento, fecha após 8 s, reabre a cada 15 s.
---
## Testes
```bash
composer test
# ou
php artisan test
```
O projeto usa **Pest 4** com o plugin Laravel. Toda nova funcionalidade deve incluir testes Pest.
---
## Comandos Úteis
```bash
php artisan route:list
php artisan migrate:fresh --seed
php artisan db:seed --no-interaction
php artisan db:seed --class=SettingsSeeder --no-interaction
php artisan rag:seed
php artisan rag:search "termo de busca"
php artisan optimize:clear
npm run build
php artisan about
# Docker
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed --no-interaction
```
---
## Diretrizes de Desenvolvimento
- **Controllers:** slim — lógica de negócio em `app/Services/`.
- **Paginação:** `->paginate(PerPage::resolve())` em todos os componentes Livewire.
- **Livewire:** componentes full-page usam `#[Layout('layouts.app')]` e `#[Title('...')]`.
- **CSS:** editar apenas os parciais em `resources/css/`; rodar `npm run build` em produção.
- **Enums:** em `app/Enums/`, com cast no modelo.
- **Testes:** obrigatórios para novas funcionalidades (Pest).
- **Migrations:** nunca encadear com `&&`; rodar um por vez.
- **Observers:** registrar via `#[ObservedBy([...])]` no modelo.
- **Novas rotas:** adicionar padrão `rotaNova.*` ao whitelist em `MaintenanceERP.php`.
- **Configurações:** usar `Setting::get()` / `Setting::set()` — nunca acessar `settings` diretamente.
- **Logs:** usar `LogService::success/warning/error()`.
- **BrasilAPI:** usar `BrasilAPIService` ou o proxy `/api/proxy/...`.
- **Contas a Pagar/Receber:** usar os respectivos services; `syncOverdueStatus()` é chamado automaticamente.
- **Folha de Pagamento:** folhas `Closed` e `Paid` são imutáveis; usar `Payroll::recalculate()`.
- **Movimentações de Estoque:** ao criar/excluir `StockMovement`, atualizar `stock` do produto manualmente.
- **Pedidos de Venda:** usar `SalesOrderService`; nunca manipular `SalesOrderLog` diretamente.
- **NF-e:** ambiente `homologacao` por padrão; alterar em Configurações → Regras Fiscais.
- **Assistente IA:** adicionar módulo em `config/ai_contexts.php`; novas tools devem estender `BaseTool` e ser registradas em `ToolRegistry`.
- **RAG:** campo `embedding` gerenciado exclusivamente via raw SQL pelo `RagService`.
- **SefazDiagnostics:** têm prioridade sobre RAG e LLM; adicionar códigos via `SefazDiagnosticsSeeder`.
- **Sail:** se usando Laravel Sail, prefixar com `./vendor/bin/sail artisan ...`.
---
## 🤝 Contribuição
Projeto interno **Nexora**.
## 📜 Licença
Propriedade da **Nexora**. Todos os direitos reservados © 2026.
---
<div align="center">
<sub>Desenvolvido com ❤️ pela equipe Nexora</sub>
</div>
