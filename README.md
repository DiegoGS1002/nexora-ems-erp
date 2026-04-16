# Nexora EMS ERP

Sistema ERP modular desenvolvido em Laravel 12, cobrindo os principais domínios de uma empresa: cadastros, produção, vendas, compras, fiscal, financeiro, RH, logística e estoque. O front-end utiliza Blade + Tailwind CSS 4 + Bootstrap 5 com um design system próprio, e existe um painel administrativo via Filament 4.5.

---

## Visão Geral

A aplicação expõe uma página inicial (`/`) com todos os módulos disponíveis. Cada módulo possui uma página de detalhes com suas funcionalidades. As rotas ativas redirecionam para seus respectivos CRUDs; as funcionalidades ainda em construção exibem a tela **Em Breve** via middleware `MaintenanceERP`.

**Status:** desenvolvimento ativo.

**Última atualização da documentação:** 2026-04-14 (README revisado).

## Índice Rápido

- [Módulos](#módulos)
- [Instalação e Execução](#instalação-e-execução)
- [Configuração de Banco de Dados](#configuração-de-banco-de-dados)
- [Configurações do Sistema](#configurações-do-sistema-configuracoes)
- [Middleware](#middleware)
- [Detalhamento de Rotas](#detalhamento-de-rotas)
- [Compras — Solicitações](#compras--solicitações-de-compra-comprassolicitacoes)
- [Compras — Pedidos](#compras--pedidos-de-compra-compraspedidos)
- [Compras — Cotações](#compras--cotações-de-compra-comprascotacoes)
- [Fiscal — Notas Fiscais](#fiscal--notas-fiscais-fiscalnotas-fiscais)
- [Fiscal — Tipos de Operação](#fiscal--tipos-de-operação-fiscaltipos-operacao)
- [Fiscal — Grupos Tributários](#fiscal--grupos-tributários-fiscalgrupos-tributarios)
- [Logística — Agendamento de Entregas](#logística--agendamento-de-entregas-logisticaagendamento-entregas)
- [Vendas — Pedidos de Venda](#vendas--pedidos-de-venda-vendaspedidos)
- [Vendas — Tabelas de Precificação](#vendas--tabelas-de-precificação-vendasprecificacao)
- [RH — Batida de Ponto](#rh--batida-de-ponto-stitch_beat)
- [RH — Holerite](#rh--holerite-holerite)
- [Produção — Ordens de Produção](#produção--ordens-de-produção-production_orders)
- [API REST](#api-rest-routesapiphp)
- [Diretrizes de Desenvolvimento](#diretrizes-de-desenvolvimento)


### Módulos

| Módulo | Status                                                                        |
|---|-------------------------------------------------------------------------------|
| Cadastro (Clientes, Fornecedores, Produtos, Funcionários, Funções, Veículos) | ✅ Ativo                                                                       |
| Cadastro — Categorias de Produto (`/product-categories`) | ✅ Implementado (CRUD Livewire)                                                |
| Cadastro — Unidades de Medida (`/unit-of-measures`) | ✅ Implementado (CRUD Livewire)                                                |
| Dashboard — Visão Geral (`/dashboard`) | ✅ Implementado (dados reais)                                                 |
| Dashboard — Indicadores KPI (`/dashboard/kpi`) | ✅ Implementado (dados reais)                                                 |
| Controle de Usuários (`/users`) | ✅ Ativo (CRUD + status + licença + módulos)                                   |
| Configurações do Sistema (`/configuracoes`) | ✅ Ativo (9 seções)                                                            |
| Suporte (`/suporte/chat`) | ✅ Ativo (tickets + chat em tempo real)                                        |
| Logs de Auditoria (`/logs`) | ✅ Ativo (somente admin)                                                       |
| Notificações (`/notificacoes`) | ✅ Ativo (dropdown no topbar + central de notificações)                        |
| Perfil do Usuário (`/perfil`) | ✅ Ativo (info, senha, avatar)                                                 |
| Financeiro — Plano de Contas (`/plans_of_accounts`) | ✅ Implementado (tree view hierárquico)                                        |
| Financeiro — Contas Bancárias (`/contas-bancarias`) | ✅ Implementado (cards + transferência + conciliação)                          |
| Financeiro — Contas a Pagar (`/accounts_payable`) | ✅ Implementado (CRUD + baixa + reagendamento + KPIs)                          |
| Financeiro — Contas a Receber (`/accounts_receivable`) | ✅ Implementado (CRUD + baixa + reagendamento + KPIs)                          |
| Financeiro — Fluxo de Caixa (`/cash_flow`) | ✅ Implementado (regime caixa/competência + gráfico diário)                    |
| RH — Jornada / Ponto (`/working_day`) | ✅ Implementado (turnos + registros de ponto + KPIs)                           |
| RH — Batida de Ponto (`/stitch_beat`) | ✅ Implementado (registro automático por ação sequencial + geolocalização)     |
| RH — Holerite (`/holerite`) | ✅ Implementado (visualização, edição de verbas, fechar, pagar)                |
| RH — Folha de Pagamento (`/payroll`) | ✅ Implementado (geração + holerite + fechamento + pagamento)                  |
| Estoque — Movimentações | ✅ Implementado (componente Livewire, rota em breve)                           |
| Produção — Ordens de Produção (`/production_orders`) | ✅ Implementado (CRUD Livewire + BOM + multi-produto + progresso)              |
| Vendas — Pedidos de Venda (`/vendas/pedidos`) | ✅ Implementado (CRUD Livewire + itens + parcelas + entrega + log)             |
| Vendas — Tabelas de Precificação (`/vendas/precificacao`) | ✅ Implementado (CRUD Livewire + calculadora markup)                           |
| Compras — Solicitações de Compra (`/compras/solicitacoes`) | ✅ Implementado (CRUD + aprovação/rejeição + conversão para Pedido ou Cotação) |
| Compras — Pedidos de Compra (`/compras/pedidos`) | ✅ Implementado (CRUD multi-abas + aprovação + frete/pagamento)                |
| Compras — Cotações de Compra (`/compras/cotacoes`) | ✅ Implementado (CRUD + respostas por fornecedor + conversão para Pedido)      |
| Fiscal — Notas Fiscais (`/fiscal/notas-fiscais`) | ✅ Implementado (CRUD NF-e + emissão + cancelamento)                           |
| Fiscal — Tipos de Operação (`/fiscal/tipos-operacao`) | ✅ Implementado (CRUD Livewire)                                                |
| Fiscal — Grupos Tributários (`/fiscal/grupos-tributarios`) | ✅ Implementado (CRUD Livewire)                                                |
| Logística — Agendamento de Entregas (`/logistica/agendamento-entregas`) | ✅ Implementado (CRUD Livewire + janelas de tempo + reagendamento)             |
| Painel Administrativo Filament (`/admin`) | ✅ Ativo                                                                       |

## Mudanças Recentes no README

- **2026-04-14:** Documentação dos módulos implementados: Compras (Solicitações, Pedidos, Cotações), Fiscal (NF-e, Tipos de Operação, Grupos Tributários), Logística (Agendamento de Entregas), Vendas (Pedidos de Venda, Tabelas de Precificação), RH (Batida de Ponto, Holerite dedicado), Produção (Ordens de Produção).
- **2026-04-14:** Atualização do middleware `MaintenanceERP` — remoção da nota desatualizada; whitelist agora inclui `compras.*` e `fiscal.*`.
- **2026-04-14:** Novos serviços documentados: `SalesOrderService`, `PricingService`, `PontoService`.
- **2026-04-14:** Novos enums documentados: `CanalVenda`, `CotacaoStatus`, `DeliveryPriority`, `DeliveryScheduleStatus`, `FiscalNoteStatus`, `IcmsModalidadeBC`, `IpiModalidade`, `OrigemPedido`, `ProductionOrderStatus`, `PurchaseOrderOrigin`, `PurchaseOrderStatus`, `RegimeTributario`, `SalesOrderStatus`, `SolicitacaoCompraPrioridade`, `SolicitacaoCompraStatus`, `StatusSeparacao`, `TipoFrete`, `TipoMovimentoFiscal`, `TipoOperacaoVenda`.
- Correção da seção de API REST para incluir endpoints já implementados em `routes/api.php`.
- Correção da whitelist documentada do middleware `MaintenanceERP` conforme `app/Http/Middleware/MaintenanceERP.php`.
- Inclusão do heading da seção de Configurações do Sistema para melhorar navegação e âncoras.

---

## Requisitos

- PHP 8.2+
- Composer 2+
- Node.js 20+ e npm 10+
- MySQL (padrão no `.env.example`) — ou SQLite/PostgreSQL ajustando `DB_*` no `.env`

---

## Stack e Dependências Principais

| Camada | Tecnologia | Versão |
|---|---|---|
| Backend | Laravel | ^12.0 |
| Componentes reativos | Livewire | ^3.7 |
| Admin Panel | Filament | ^4.5 |
| Templating | Blade | — |
| Build | Vite | ^7.0 |
| CSS Framework | Tailwind CSS | ^4.0 |
| CSS Componentes | Bootstrap | ^5.3 |
| Gráficos | ApexCharts | CDN (latest) |
| Testes | Pest + Plugin Laravel | ^3.8 / ^3.2 |
| PHP (mínimo) | — | 8.2 |

### Dependências de Desenvolvimento

| Pacote | Versão |
|---|---|
| `fakerphp/faker` | ^1.23 |
| `laravel/pail` | ^1.2 |
| `laravel/pint` | ^1.24 |
| `laravel/sail` | ^1.41 |
| `nunomaduro/collision` | ^8.6 |

---

## Estrutura de Pastas

```text
app/
  Enums/                # Enums de domínio (Pascal case)
    CanalVenda.php
    CategoriaVeiculo.php
    CombustivelVeiculo.php
    CotacaoStatus.php
    DeliveryPriority.php
    DeliveryScheduleStatus.php
    EspecieVeiculo.php
    FiscalNoteStatus.php
    IcmsModalidadeBC.php
    IpiModalidade.php
    NaturezaProduto.php
    OrigemPedido.php
    PayableStatus.php         # Contas a Pagar: pending, paid, overdue, cancelled
    PayrollStatus.php         # Folha de Pagamento: draft, closed, paid
    PrioridadeTicketSuporte.php
    ProductionOrderStatus.php
    PurchaseOrderOrigin.php
    PurchaseOrderStatus.php
    ReceivableStatus.php      # Contas a Receber: pending, received, overdue, partial, cancelled
    RegimeTributario.php
    SalesOrderStatus.php
    SolicitacaoCompraPrioridade.php
    SolicitacaoCompraStatus.php
    StatusSeparacao.php
    StatusTicketSuporte.php
    TimeRecordStatus.php      # Ponto: active, break, absent, completed
    TipoFrete.php
    TipoMovimentoFiscal.php
    TipoOperacaoVenda.php
    TipoPessoa.php
    TipoProduto.php
    TipoVeiculo.php
  Http/
    Controllers/        # Slim controllers por domínio
      Api/              # Controllers da API REST
        ClientApiController.php
        ProductApiController.php
        ProductSupplierApiController.php
        SupplierApiController.php
      Auth/
        SessionController.php     # Login / logout
      ConfigurationController.php   # Configurações do sistema (GET/POST /configuracoes)
      ExternalApiProxyController.php # Proxy autenticado para BrasilAPI (CNPJ/CEP)
      UsersController.php           # Controle de usuários com status, licença e módulos
    Middleware/
      EnforceMidnightSession.php    # Encerra sessão se a data de login for anterior ao dia atual
      EnsureUserIsAdmin.php         # Bloqueia acesso de não-administradores
      MaintenanceERP.php            # Whitelist de rotas liberadas
  Livewire/             # Componentes Livewire
    Administracao/
      Logs/             # Index — listagem de logs de auditoria (somente admin)
      Notifications/    # Index — central de notificações (paginada, com filtros)
    Cadastro/
      CategoriaProduto/ # Index + Form (Categorias de Produto)
      Clientes/         # Index + Form (full-page)
      Fornecedores/     # Index + Form
      Funcionarios/     # Index + Form
      Funcoes/          # Index + Form (Funções/Cargos)
      Produtos/         # Index + Form
      UnidadeMedida/    # Index + Form (Unidades de Medida)
      Veiculos/         # Index + Form
    Compras/
      SolicitacoesCompra.php  # Solicitações de Compra (CRUD + aprovação + conversão)
      PedidosCompra.php       # Pedidos de Compra (CRUD multi-abas + aprovação + frete)
      Cotacoes.php            # Cotações de Compra (CRUD + respostas por fornecedor)
    Dashboard/
      Overview.php      # Visão Geral do Dashboard
      KpiReport.php     # Indicadores KPI com drill-down
    Estoque/
      Movimentacao.php  # Movimentações de estoque (entrada, saída, ajuste, transferência)
    Financeiro/
      PlanoContas.php   # Plano de Contas (tree view hierárquico, full-page)
      ContaBancaria.php # Contas Bancárias (cards + transferência + conciliação, full-page)
      ContasPagar.php   # Contas a Pagar (CRUD + baixa + reagendamento + KPIs)
      ContasReceber.php # Contas a Receber (CRUD + baixa + reagendamento + KPIs)
      FluxoCaixa.php    # Fluxo de Caixa (regime caixa/competência + gráfico diário)
    Fiscal/
      NotaFiscal.php        # Notas Fiscais Eletrônicas (CRUD + emissão + cancelamento)
      TipoOperacao/         # Index + Form (Tipos de Operação Fiscal)
      GrupoTributario/      # Index + Form (Grupos Tributários)
    Forms/
      NovoTicketForm.php  # Livewire Form Object para criação de tickets
    Logistica/
      AgendamentoEntregas.php  # Agendamento de Entregas (CRUD + janelas de tempo + reagendamento)
    Producao/
      OrdemProducao.php   # Ordens de Produção (CRUD + BOM + multi-produto + progresso)
    Suporte/
      Chat.php          # Chat de suporte com tickets em tempo real
    Rh/
      JornadaTrabalho.php # Jornada/Ponto (turnos + registros de ponto + KPIs)
      FolhaPagamento.php  # Folha de Pagamento (geração + holerite + fechamento + pagamento)
      Holerite.php        # Holerite dedicado (visualização + edição de verbas + impressão)
      BatidaPonto.php     # Batida de Ponto (registro automático por ação sequencial + geolocalização)
    Vendas/
      PedidosVenda.php        # Pedidos de Venda (CRUD + itens + parcelas + entrega + log)
      TabelasPrecificacao.php # Tabelas de Precificação (CRUD + calculadora markup)
    NotificationDropdown.php  # Dropdown de notificações no topbar
  Models/               # Modelos Eloquent
    AccountPayable.php    # Contas a Pagar (status, vencimento, recorrência, vínculo com Plano de Contas)
    AccountReceivable.php # Contas a Receber (status, parcelas, forma de pagamento, vínculo com Plano de Contas)
    BaccaratAccount.php   # Conta bancária (saldo, conciliação, vínculo com Plano de Contas)
    Carrier.php           # Transportadora (nome, CNPJ, contato, is_active)
    Cotacao.php           # Cotação de compra (título, status, prazo)
    CotacaoItem.php       # Item de cotação (produto, descrição, quantidade)
    CotacaoResposta.php   # Resposta de fornecedor a uma cotação (preço unitário, prazo de entrega)
    DeliveryTimeWindow.php # Janela de tempo para entrega (horário início/fim, label)
    DriverManagement.php  # Gestão de motoristas
    EmployeeManagement.php # Histórico de gestão de funcionários
    Employees.php         # Funcionários (dados pessoais, contrato, salário)
    Entrance.php          # Entrada fiscal (NF-e de entrada)
    ExitRecord.php        # Saída fiscal (NF-e de saída)
    FinancialReport.php   # Relatório financeiro
    FiscalNote.php        # Nota Fiscal Eletrônica (NF-e/NFC-e — chave, status, ambiente)
    GrupoTributario.php   # Grupo tributário (ICMS, IPI, PIS/COFINS, CST)
    MensagemSuporte.php   # Mensagens de tickets de suporte
    MonitoringOfDeliveries.php # Monitoramento de entregas em tempo real
    Payroll.php           # Folha de pagamento (salário base, proventos, descontos, líquido)
    PayrollItem.php       # Itens da folha (provento/desconto)
    PlanOfAccount.php     # Plano de contas hierárquico (parent-child)
    PriceTable.php        # Tabela de preços (nome, vigência, is_default)
    PriceTableItem.php    # Item de tabela de preços (produto, preço calculado, margem)
    ProductCategory.php   # Categorias de produto (UUID, slug, cor)
    ProductionItem.php    # Insumo/BOM de uma ordem de produção
    ProductionOrder.php   # Ordem de produção (status, lote, custo estimado)
    ProductionOrderProduct.php # Produto(s) a produzir em uma OP (quantidade, produzido)
    PurchaseOrder.php     # Pedido de compra (fornecedor, totais, frete, aprovação)
    PurchaseOrderItem.php # Item de pedido de compra (quantidade, preço, desconto, total)
    PurchaseRequisition.php # Solicitação de compra (título, status, aprovação, conversão)
    PurchaseRequisitionItem.php # Item de solicitação (produto, quantidade, preço estimado)
    RhReport.php          # Relatório de RH
    Romaneio.php          # Romaneio de entrega (lista de volumes/pedidos)
    RouteManagement.php   # Gestão de rotas de entrega
    Routing.php           # Roteirização de entregas
    SalesOrder.php        # Pedido de venda (cliente, status, canal, operação fiscal)
    SalesOrderAddress.php # Endereço de entrega do pedido de venda
    SalesOrderAttachment.php # Anexos do pedido de venda
    SalesOrderInstallment.php # Parcelas do pedido de venda
    SalesOrderItem.php    # Item do pedido de venda (produto, quantidade, preço, desconto)
    SalesOrderLog.php     # Log de alterações do pedido de venda
    SalesOrderPayment.php # Pagamento do pedido de venda
    SalesReport.php       # Relatório de vendas
    SchedulingOfDeliveries.php # Agendamento de entregas (data, veículo, janela, prioridade)
    Setting.php           # Configurações do sistema (key-value com cache)
    Stock.php             # Estoque (saldo por produto/localização)
    StockMovement.php     # Movimentações de estoque (entrada, saída, ajuste, transferência)
    SystemLog.php         # Registros de auditoria do sistema
    TicketSuporte.php     # Tickets de suporte
    TimeRecord.php        # Registros de ponto (clock_in/out, pausa, horas trabalhadas)
    TipoOperacaoFiscal.php # Tipo de operação fiscal (CFOP, natureza, fins tributários)
    TransportReport.php   # Relatório de transporte
    UnitOfMeasure.php     # Unidades de medida (UUID, abreviação padronizada)
    VehicleMaintenance.php # Manutenção de veículos (tipo, data, custo, status)
    VehicleTracking.php   # Rastreamento de veículos (posição, velocidade, status)
    Visit.php             # Visitas comerciais
    WorkShift.php         # Turnos de trabalho (horário início/fim, duração de pausa)
    WorkingDay.php        # Jornada de trabalho (model auxiliar)
  Providers/
    Filament/           # AdminPanelProvider (Filament)
  Services/             # Service classes com lógica de negócio
    BrasilAPIService.php        # Integração com BrasilAPI (CNPJ e CEP)
    ContasPagarService.php      # CRUD, baixa, reagendamento, cancelamento e KPIs de contas a pagar
    ContasReceberService.php    # CRUD, baixa de recebimento, reagendamento, cancelamento e KPIs de contas a receber
    Dashboard/
      DashboardMetricsService.php
    JornadaService.php          # KPIs, grade de presença, timeline e gerenciamento de turnos (WorkShift)
    LogService.php              # Registro centralizado de logs de auditoria
    PayrollService.php          # Geração de folha, itens (proventos/descontos), fechamento e pagamento
    PontoService.php            # Batida de ponto: registro sequencial de entrada/pausa/saída por funcionário
    PricingService.php          # Cálculo de preço por markup divisor (custo + percentuais sobre venda)
    RoleService.php             # Lógica de negócio de funções/cargos
    SalesOrderService.php       # CRUD de pedidos de venda, aprovação, cancelamento, estatísticas
    SuporteService.php          # Criação e gestão de tickets de suporte
config/
database/
  migrations/           # Migrations em ordem cronológica
  seeders/
    AdminUserSeeder.php   # Cria usuário administrador padrão
    ClientSeeder.php      # Carga inicial de clientes
    DatabaseSeeder.php    # Orquestrador principal
    ProductSeeder.php     # Carga inicial de produtos
    SettingsSeeder.php    # Valores padrão das 9 seções de configuração
    SupplierSeeder.php    # Carga inicial de fornecedores
    SystemLogSeeder.php   # Logs de exemplo para desenvolvimento
    UserSeeder.php        # Usuários de exemplo para desenvolvimento
  factories/
resources/
  css/
    app.css             # Ponto de entrada (importa os partials abaixo)
    _base.css           # Reset, tipografia, utilitários globais
    _layout.css         # Sidebar, topbar, wrapper, variáveis CSS (:root)
    _components.css     # Cards, KPIs, badges, botões, gráficos, modal de licença, settings
    _tables.css         # Estilos de tabelas dos módulos
  js/
  views/
    admin/
      users/            # Controle de usuários (index, create, edit)
      settings/
        index.blade.php # Página de configurações (9 abas)
    cadastro/           # Views CRUD: clientes, fornecedores, produtos, funcionários, funções, veículos
    administrativo/     # Views de permissões
    auth/
      login.blade.php   # Tela de login
    components/
      dashboard/        # Blade components reutilizáveis do dashboard
        kpi-card.blade.php
        chart-line.blade.php
        chart-donut.blade.php
        table.blade.php
    layouts/
      app.blade.php     # Layout principal — inclui modal de aviso de licença
      livewire/
        administracao/
          logs/           # View do componente de logs de auditoria
          notifications/  # View da central de notificações
        cadastro/         # Views Livewire de cadastro (inclui product-categories e unit-of-measures)
        dashboard/
          overview.blade.php    # View da Visão Geral
          kpi-report.blade.php  # View dos Indicadores KPI
        estoque/
          movimentacao.blade.php # View de movimentações de estoque
        financeiro/
          plano-contas/   # View do Plano de Contas (tree view)
          conta-bancaria/ # View das Contas Bancárias (cards + modal)
          contas-pagar/   # View das Contas a Pagar (tabela + modais de baixa/reagendamento)
          contas-receber/ # View das Contas a Receber (tabela + modais de baixa/reagendamento)
          fluxo-caixa/    # View do Fluxo de Caixa (gráfico + tabela diária)
        rh/
          jornada-trabalho/ # View da Jornada de Trabalho (grade presença + timeline)
          folha-pagamento/  # View da Folha de Pagamento (tabela + holerite modal)
        suporte/
          chat.blade.php  # Interface de chat de suporte (tickets)
        notification-dropdown.blade.php  # Dropdown de notificações no topbar
    modules/            # Página de detalhes do módulo (show.blade.php)
    partials/           # Partials reutilizáveis (navbar.blade.php)
    perfil/             # Views de perfil do usuário
    system/
      desenvolvimento.blade.php  # Tela "Em Breve"
routes/
  web.php               # Ponto de entrada — inclui todos os arquivos abaixo
  administracao.php     # GET/POST /configuracoes, profile
  cadastro.php
  compras.php
  estoque.php
  financeiro.php
  fiscal.php
  logistica.php
  perfil.php            # users.*, permissions.*, logs.*
  producao.php          # Inclui também as rotas do Dashboard
  rh.php
  vendas.php
  api.php               # API REST + proxy BrasilAPI
tests/
  Feature/
  Unit/
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

Esse comando executa em sequência:

1. `composer install`
2. Criação de `.env` (se ausente, a partir de `.env.example`)
3. `php artisan key:generate`
4. `php artisan migrate --force`
5. `npm install`
6. `npm run build`

### 3. Ambiente de desenvolvimento

```bash
composer run dev
```

Sobe em paralelo (via `concurrently`):
- Servidor Laravel (`php artisan serve`)
- Listener de filas (`php artisan queue:listen --tries=1`)
- Vite em modo `dev` com HMR

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

O `.env.example` usa MySQL por padrão:

```dotenv
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

Ajuste as variáveis `DB_*` no `.env` e rode:

```bash
php artisan migrate
```

Para recriar o banco do zero:

```bash
php artisan migrate:fresh --seed
```

### Seeders iniciais

O `DatabaseSeeder` registra os seguintes seeders na execução principal:

| Seeder | Objetivo |
|---|---|
| `AdminUserSeeder` | Cria o usuário administrador padrão |
| `ClientSeeder` | Carga inicial de clientes |
| `ProductSeeder` | Carga inicial de produtos |
| `SupplierSeeder` | Carga inicial de fornecedores |

Seeders adicionais (executados individualmente):

| Seeder | Objetivo |
|---|---|
| `SettingsSeeder` | Valores padrão para todas as 9 seções de configuração do sistema |
| `SystemLogSeeder` | Logs de exemplo para desenvolvimento |
| `UserSeeder` | Usuários de exemplo para desenvolvimento |

Rodar todos os seeders:

```bash
php artisan db:seed --no-interaction
```

Rodar seeders específicos:

```bash
php artisan db:seed --class=AdminUserSeeder --no-interaction
php artisan db:seed --class=ClientSeeder --no-interaction
php artisan db:seed --class=ProductSeeder --no-interaction
php artisan db:seed --class=SupplierSeeder --no-interaction
php artisan db:seed --class=SettingsSeeder --no-interaction
php artisan db:seed --class=SystemLogSeeder --no-interaction
```

> Observação: os seeders já são idempotentes (`updateOrCreate` / `firstOrCreate`) e definidos para funcionar com UUID mesmo quando eventos de modelo estiverem desabilitados durante o `db:seed`.

### Docker sem trocar o `.env`

O `docker-compose.yml` injeta as variáveis de banco no container `app` via `DOCKER_DB_*`, permitindo manter o `.env` com credenciais locais.

Defaults usados no Docker:

```dotenv
DOCKER_DB_CONNECTION=mysql
DOCKER_DB_HOST=db
DOCKER_DB_PORT=3306
DOCKER_DB_DATABASE=nexora
DOCKER_DB_USERNAME=nexora
DOCKER_DB_PASSWORD=nexora
DOCKER_DB_ROOT_PASSWORD=root
DOCKER_DB_EXPOSED_PORT=3307
```

```bash
export DOCKER_DB_PASSWORD=minha_senha_forte
docker compose up -d --build
```

Comandos de seed no container:

```bash
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed --no-interaction
docker compose exec app php artisan db:seed --class=SettingsSeeder --no-interaction
```

### Troubleshooting rápido de seed

- Erro `getaddrinfo for db failed`: ocorre ao rodar `php artisan` fora do Docker com `DB_HOST=db`.
  - Opção 1: rode os comandos via container (`docker compose exec app ...`).
  - Opção 2: ajuste `DB_HOST` no `.env` local para um host acessível (ex.: `127.0.0.1` ou `localhost`).
- Erro `storage/logs/laravel.log could not be opened in append mode`: permissões de escrita no diretório `storage/`.

```bash
sudo chown -R "$USER":"$USER" storage bootstrap/cache
chmod -R ug+rw storage bootstrap/cache
```

---

## Autenticação

As rotas de autenticação são tratadas pelo `SessionController`:

| Método | URI | Nome | Descrição |
|---|---|---|---|
| `GET` | `/login` | `login` | Exibe a tela de login (middleware `guest`) |
| `POST` | `/login` | `login.store` | Processa o login (middleware `guest`) |
| `POST` | `/logout` | `logout` | Encerra a sessão (middleware `auth`) |

O login registra `last_login_at` no usuário. Usuários com `is_active = false` têm o acesso bloqueado com mensagem de erro.

---

## Dashboard

O dashboard é composto por dois componentes Livewire full-page:

| URL | Nome da rota | Componente Livewire |
|---|---|---|
| `/dashboard` | `dashboard.index` | `App\Livewire\Dashboard\Overview` |
| `/dashboard/kpi` | `dashboard.kpi` | `App\Livewire\Dashboard\KpiReport` |

### Visão Geral (`/dashboard`)

- **4 cards KPI** lado a lado: Faturamento, Produtos, Pedidos, Despesas
- **Gráfico de linha** — evolução mensal de faturamento (ApexCharts)
- **Gráfico donut** — distribuição por categoria (ApexCharts)
- **Pedidos recentes** com badge de status (Aprovado / Pendente / Cancelado)
- **Movimentações** — entradas e saídas com dot colorido e horário
- Auto-refresh a cada 10 s via `wire:poll.10s`

### Indicadores KPI (`/dashboard/kpi`)

- **4 cards KPI** idênticos à Visão Geral
- **Barra de busca** com filtro de mês via clique no gráfico
- **Gráfico de linha** interativo — clique em um ponto para filtrar a tabela
- **Gráfico donut** — distribuição por categoria
- **Desempenho Geral** — barras de progresso Meta vs Realizado para cada KPI
- **Gráfico de barras agrupado** — Meta × Realizado por mês (ApexCharts)
- **Comparativos Mensais** — tabela com faturamento, variação % e pedidos vs mês anterior
- **Tabela detalhada** — dados por período com busca em tempo real

---

## Suporte (`/suporte/chat`)

Acessível por todos os usuários autenticados. Administradores visualizam todos os tickets; usuários comuns só veem os seus próprios.

### Funcionalidades

- **Criação de tickets** com assunto, prioridade e categoria
- **Chat em tempo real** dentro de cada ticket (Livewire polling)
- **Filtros** por status e busca por assunto
- **Gestão de status** pelo administrador (Aberto → Em Andamento → Resolvido → Fechado)
- **Marcação de leitura** automática ao selecionar um ticket

### Modelos

| Modelo | Tabela | Descrição |
|---|---|---|
| `TicketSuporte` | `tickets_suporte` | Ticket de suporte (UUID, assunto, status, prioridade, categoria) |
| `MensagemSuporte` | `mensagens_suporte` | Mensagem de ticket (conteúdo, flag `is_suporte`, flag `lida`) |

### Enums de Suporte

| Enum | Valores |
|---|---|
| `StatusTicketSuporte` | `aberto`, `em_andamento`, `resolvido`, `fechado` |
| `PrioridadeTicketSuporte` | `baixa`, `media`, `alta`, `critica` |

### Rota

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/suporte/chat` | `suporte.chat` | `App\Livewire\Suporte\Chat` |

---

## Logs de Auditoria (`/logs`)

Acessível apenas por administradores (middleware `admin`). Implementado como componente Livewire full-page (`App\Livewire\Administracao\Logs\Index`).

### Modelo `SystemLog`

| Campo | Tipo | Descrição |
|---|---|---|
| `level` | string | `success`, `warning`, `error` |
| `action` | string | Código da ação (ex.: `LOGIN`, `ACESSO_NEGADO`) |
| `module` | string | Módulo do sistema (ex.: `Segurança`, `Cadastros`) |
| `description` | string | Descrição textual do evento |
| `ip` | string | IP do requisitante |
| `user_id` | bigint | FK para `users` (nullable) |
| `user_name` | string | Nome snapshot do usuário |
| `user_email` | string | E-mail snapshot do usuário |
| `context` | JSON | Dados extras opcionais |

### `LogService` — Registro centralizado

```php
// Registrar evento de sucesso
LogService::success('LOGIN', 'Usuário realizou login.', 'Segurança');

// Registrar aviso
LogService::warning('ACESSO_NEGADO', 'Tentativa sem permissão.', 'Segurança');

// Registrar erro com contexto extra
LogService::error('ERRO_API', 'Falha ao consultar BrasilAPI.', 'Integrações', ['status' => 500]);
```

---

## Notificações

### Dropdown (`topbar`)

O componente `App\Livewire\NotificationDropdown` é incluído no layout principal e exibe:

- **Ícone de sino** com badge de contagem de não lidas
- **Painel flutuante** glassmorphism com até 10 notificações recentes
- **Ações rápidas:** marcar uma ou todas como lidas
- `toggle()`, `markAsRead(string $id)`, `markAllAsRead()`

### Central de Notificações (`/notificacoes`)

Componente Livewire full-page (`App\Livewire\Administracao\Notifications\Index`), acessível por todos os usuários autenticados.

#### Funcionalidades

- **KPIs no topo:** total, não lidas, lidas, distribuição por tipo
- **Filtros:** por tipo, por status (lida/não lida), por data
- **Paginação** configurável (padrão: 15 por página)
- **Ações:** marcar como lida, excluir individual, marcar todas como lidas, excluir todas

#### Modelo (tabela `notifications`)

Utiliza o sistema nativo de notificações do Laravel:

| Campo | Tipo | Descrição |
|---|---|---|
| `id` | UUID | Identificador único |
| `type` | string | Classe da notificação |
| `notifiable_type` / `notifiable_id` | morphs | Polimórfico — geralmente `App\Models\User` |
| `data` | JSON | Payload da notificação (inclui chave `type` para categorização) |
| `read_at` | timestamp (nullable) | Nulo = não lida |

#### Rota

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/notificacoes` | `notifications.index` | `App\Livewire\Administracao\Notifications\Index` |

---

## Perfil do Usuário (`/perfil`)

Acessível por todos os usuários autenticados via `ProfileController`.

### Funcionalidades

- **Informações pessoais:** nome, telefone, cargo (`job_title`), departamento, bio (máx. 500 chars)
- **Troca de senha:** valida senha atual + confirma nova (mín. 8 chars, letras + números)
- **Avatar:** upload (jpg/jpeg/png/webp, máx. 2 MB) armazenado em `storage/app/public/avatars/`; remoção volta ao avatar gerado por inicial

### Rotas (`routes/perfil.php`)

| Método | URI | Nome | Descrição |
|---|---|---|---|
| `GET` | `/perfil` | `profile.index` | Exibe a página de perfil |
| `PATCH` | `/perfil/info` | `profile.updateInfo` | Atualiza nome, telefone, cargo, departamento, bio |
| `PATCH` | `/perfil/senha` | `profile.updatePassword` | Altera a senha |
| `POST` | `/perfil/avatar` | `profile.uploadAvatar` | Faz upload do avatar |
| `DELETE` | `/perfil/avatar` | `profile.removeAvatar` | Remove o avatar |

---

## Financeiro — Plano de Contas (`/plans_of_accounts`)

Implementado como componente Livewire full-page (`App\Livewire\Financeiro\PlanoContas`).

### Funcionalidades

- **Tree view hierárquico** — suporta até 5 níveis de profundidade (pai → filho recursivo)
- **Criar conta raiz ou subconta** — modal com herança automática do `type` do pai
- **Editar / excluir** — exclusão bloqueada se a conta possui subcontas
- **Toggle ativo/inativo** sem recarregar a página
- **Busca em tempo real** por nome ou código (exibe lista plana com recuo)
- **Filtro por tipo** (`receita`, `despesa`, `ativo`, `passivo`)
- **KPIs:** total de contas, ativas, grupos (contas sintéticas)

### Modelo `PlanOfAccount`

| Campo | Tipo | Descrição |
|---|---|---|
| `id` | bigint | Auto-increment |
| `parent_id` | bigint (nullable) | FK para a própria tabela — define hierarquia |
| `code` | string(30) | Código da conta (ex.: `1.01.002`) |
| `name` | string | Nome da conta |
| `description` | text (nullable) | Descrição opcional |
| `type` | enum | `receita`, `despesa`, `ativo`, `passivo` |
| `is_selectable` | boolean | `false` = conta sintética (não recebe lançamentos) |
| `is_active` | boolean | |

**Relacionamentos:**

| Método | Tipo | Descrição |
|---|---|---|
| `parent()` | `BelongsTo` | Conta pai |
| `children()` | `HasMany` | Subcontas diretas (ordenadas por `code`) |
| `isSynthetic()` | método | `true` se a conta possui filhos |

**Accessors:** `type_label`, `type_color`, `type_css_class`.

### Rota

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/plans_of_accounts` | `plans_of_accounts.index` | `App\Livewire\Financeiro\PlanoContas` |

---

## Financeiro — Contas Bancárias (`/contas-bancarias`)

Implementado como componente Livewire full-page (`App\Livewire\Financeiro\ContaBancaria`).

### Funcionalidades

- **Cards de banco** no topo com gradiente por instituição (Nubank, Itaú, Bradesco, BB, Caixa, Santander, Inter, C6, Sicredi…)
- **CRUD completo** via modal (criar, editar, excluir com confirmação)
- **Transferência entre contas** — valida saldo suficiente, debita origem e credita destino
- **Toggle ativo/inativo** e **toggle conciliação** (registra `last_reconciled_at`)
- **Filtros:** busca (nome/banco/número), tipo de conta, status (ativo/inativo)
- **KPIs:** saldo total, saldo previsto total, contas ativas, total de contas, conciliadas
- **Vínculo com Plano de Contas** — filtrado por `type = 'ativo'` e `is_selectable = true`

### Modelo `BaccaratAccount`

| Campo | Tipo | Descrição |
|---|---|---|
| `id` | bigint | Auto-increment |
| `name` | string | Apelido da conta (ex.: `Itaú Principal`) |
| `bank_name` | string | Nome do banco/instituição |
| `agency` | string (nullable) | Agência |
| `number` | string (nullable) | Número da conta |
| `type` | enum | `corrente`, `poupanca`, `caixa_interno`, `digital` |
| `balance` | decimal:2 | Saldo atual |
| `predicted_balance` | decimal:2 | Saldo previsto (conciliado) |
| `color` | string(20) (nullable) | Cor hex do card; se nulo, inferida automaticamente pelo banco |
| `chart_of_account_id` | bigint (nullable) | FK para `plans_of_accounts` |
| `is_active` | boolean | |
| `is_reconciled` | boolean | Indica conciliação com extrato bancário |
| `last_reconciled_at` | date (nullable) | Data da última conciliação |
| `description` | text (nullable) | Observações |

**Relacionamentos:** `chartOfAccount()` → `BelongsTo(PlanOfAccount)`.

**Accessors:** `type_label`, `type_icon`, `card_color`, `formatted_balance`, `formatted_predicted_balance`.

### Rota

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/contas-bancarias` | `contas_bancarias.index` | `App\Livewire\Financeiro\ContaBancaria` |

---

## Financeiro — Contas a Pagar (`/accounts_payable`)

Implementado como componente Livewire full-page (`App\Livewire\Financeiro\ContasPagar`).

### Funcionalidades

- **CRUD completo** via modal (criar, editar, excluir com confirmação)
- **Baixa de pagamento** — registra data, valor pago e observação; muda status para `Pago`
- **Reagendamento** — altera a data de vencimento e redefine o status para `Pendente`
- **Cancelamento** de conta individual
- **Recorrência** — flag `is_recurring` com dia do mês para geração futura
- **Sincronização automática de vencidos** na abertura da página (`syncOverdueStatus`)
- **Filtros:** busca (título/fornecedor), por status, por mês de vencimento
- **KPIs:** total a pagar hoje, a pagar na semana, total pendente, total pago no mês, contas vencidas

### Modelo `AccountPayable`

| Campo | Tipo | Descrição |
|---|---|---|
| `id` | bigint | Auto-increment |
| `description_title` | string | Título/descrição da conta |
| `supplier_id` | bigint (nullable) | FK para `suppliers` |
| `chart_of_account_id` | bigint (nullable) | FK para `plans_of_accounts` (tipo `despesa`) |
| `amount` | decimal:2 | Valor original |
| `paid_amount` | decimal:2 | Valor pago na baixa |
| `due_date_at` | date | Data de vencimento |
| `payment_date` | date (nullable) | Data efetiva do pagamento |
| `status` | enum | Cast `PayableStatus` |
| `observation` | text (nullable) | Observações |
| `attachment_path` | string (nullable) | Caminho do comprovante |
| `is_recurring` | boolean | Conta recorrente |
| `recurrence_day` | integer (nullable) | Dia do mês de recorrência |

**Scopes:** `scopeDueToday()`, `scopeDueThisWeek()`.

**Relacionamentos:** `supplier()` → `BelongsTo(Supplier)`, `chartOfAccount()` → `BelongsTo(PlanOfAccount)`.

### Rota

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/accounts_payable` | `accounts_payable.index` | `App\Livewire\Financeiro\ContasPagar` |

---

## Financeiro — Contas a Receber (`/accounts_receivable`)

Implementado como componente Livewire full-page (`App\Livewire\Financeiro\ContasReceber`).

### Funcionalidades

- **CRUD completo** via modal
- **Baixa de recebimento** — registra data, valor recebido e observação; muda status para `Recebido`
- **Reagendamento** da data de vencimento
- **Cancelamento** de conta individual
- **Sincronização automática de vencidos** na abertura da página
- **Filtros:** busca (título/cliente), por status, por mês, por forma de pagamento
- **KPIs:** total a receber hoje, a receber na semana, total pendente, total recebido no mês, contas vencidas

### Modelo `AccountReceivable`

| Campo | Tipo | Descrição |
|---|---|---|
| `id` | bigint | Auto-increment |
| `description_title` | string | Título/descrição da conta |
| `client_id` | string/UUID (nullable) | FK para `clients` |
| `chart_of_account_id` | bigint (nullable) | FK para `plans_of_accounts` (tipo `receita`) |
| `amount` | decimal:2 | Valor original |
| `received_amount` | decimal:2 | Valor efetivamente recebido |
| `due_date_at` | date | Data de vencimento |
| `received_at` | date (nullable) | Data do recebimento |
| `payment_method` | string (nullable) | Forma de pagamento |
| `installment_number` | integer | Número da parcela (padrão: 1) |
| `status` | enum | Cast `ReceivableStatus` |
| `observation` | text (nullable) | Observações |

**Relacionamentos:** `client()` → `BelongsTo(Client)`, `chartOfAccount()` → `BelongsTo(PlanOfAccount)`.

### Rota

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/accounts_receivable` | `accounts_receivable.index` | `App\Livewire\Financeiro\ContasReceber` |

---

## Financeiro — Fluxo de Caixa (`/cash_flow`)

Implementado como componente Livewire full-page (`App\Livewire\Financeiro\FluxoCaixa`).

### Funcionalidades

- **Regime de Caixa** — considera apenas valores efetivamente pagos/recebidos
- **Regime de Competência** — considera todos os lançamentos (exceto cancelados)
- **Períodos predefinidos:** semana, mês (padrão), trimestre, ano (com datas ajustáveis)
- **KPIs:** saldo inicial (soma das contas bancárias ativas), total entradas, total saídas, saldo final, contas pendentes a receber/pagar, saldo projetado
- **Gráfico de barras diário** (ApexCharts) — entradas vs. saídas por dia
- **Tabela de fluxo diário** — somente dias com movimentação, com saldo acumulado e indicação de hoje/futuro

### Rota

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/cash_flow` | `cash_flow.index` | `App\Livewire\Financeiro\FluxoCaixa` |

---

## RH — Jornada de Trabalho / Ponto (`/working_day`)

Implementado como componente Livewire full-page (`App\Livewire\Rh\JornadaTrabalho`).

### Funcionalidades

- **Gestão de Turnos (`WorkShift`)** — criar, editar, excluir turnos com horário de início/fim e duração de pausa
- **Registro de Ponto** — criar e editar registros de entrada, início de pausa, fim de pausa e saída
- **Grade de Presença** — exibe todos os funcionários ativos com status do dia (Presente / Em Pausa / Ausente / Concluído)
- **Timeline** — lista cronológica dos registros do dia
- **Filtro por data** — default: dia atual
- **Busca e filtro por status** na grade de presença
- **KPIs:** total de funcionários, presentes, em pausa, ausentes, banco de horas

### Modelos

| Modelo | Tabela | Descrição |
|---|---|---|
| `WorkShift` | `work_shifts` | Turno (nome, início, fim, pausa em minutos) |
| `TimeRecord` | `time_records` | Registro de ponto por funcionário/dia (clock_in/out, pausa, status, localização) |

#### `WorkShift`

| Campo | Tipo | Descrição |
|---|---|---|
| `name` | string | Nome do turno (ex.: `Manhã`, `Tarde`) |
| `description` | text (nullable) | Descrição opcional |
| `start_time` | string | Horário de início (`HH:MM`) |
| `end_time` | string | Horário de encerramento (`HH:MM`) |
| `break_duration` | integer | Duração da pausa em minutos |
| `is_active` | boolean | |

**Accessor:** `work_hours` — retorna `"start_time - end_time"`.

#### `TimeRecord`

| Campo | Tipo | Descrição |
|---|---|---|
| `employee_id` | UUID | FK para `employees` |
| `work_shift_id` | bigint (nullable) | FK para `work_shifts` |
| `date` | date | Data do registro |
| `clock_in` | datetime | Entrada |
| `break_start` | datetime (nullable) | Início da pausa |
| `break_end` | datetime (nullable) | Fim da pausa |
| `clock_out` | datetime (nullable) | Saída |
| `status` | enum | Cast `TimeRecordStatus` |
| `ip_address` | string (nullable) | IP do registro |
| `latitude` / `longitude` | decimal:8 (nullable) | Geolocalização |
| `observation` | text (nullable) | Observações |

**Accessors:** `worked_minutes` (int), `worked_hours_formatted` (string `HHhMM`).

### Rota

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/working_day` | `working_day.index` | `App\Livewire\Rh\JornadaTrabalho` |

---

## RH — Folha de Pagamento (`/payroll`)

Implementado como componente Livewire full-page (`App\Livewire\Rh\FolhaPagamento`).

### Funcionalidades

- **Geração de folha** individual por funcionário ou em lote para todos os ativos (idempotente)
- **Holerite (modal)** — visualiza e edita proventos e descontos do funcionário
- **Itens da folha** — adicionar/editar/remover proventos (`earning`) e descontos (`deduction`) com recalculação automática
- **Fechar folha** — altera status de `Rascunho` → `Fechada`
- **Marcar como pago** — altera status de `Fechada` → `Paga` com data de pagamento
- **Filtro por mês de referência** (padrão: mês atual)
- **KPIs:** total de proventos, descontos, líquido, contagem por status

### Modelos

| Modelo | Tabela | Descrição |
|---|---|---|
| `Payroll` | `payrolls` | Folha de pagamento por funcionário/mês |
| `PayrollItem` | `payroll_items` | Itens da folha (provento ou desconto) |

#### `Payroll`

| Campo | Tipo | Descrição |
|---|---|---|
| `employee_id` | UUID | FK para `employees` |
| `reference_month` | date | Mês de referência (dia 01) |
| `base_salary` | decimal:2 | Salário base do funcionário |
| `total_earnings` | decimal:2 | Soma dos proventos extras |
| `total_deductions` | decimal:2 | Soma dos descontos |
| `net_salary` | decimal:2 | Salário líquido (`base + earnings - deductions`) |
| `status` | enum | Cast `PayrollStatus` |
| `payment_date` | date (nullable) | Data efetiva do pagamento |
| `observations` | text (nullable) | Observações |

**Método:** `recalculate()` — recalcula totais a partir dos itens.

#### `PayrollItem`

| Campo | Tipo | Descrição |
|---|---|---|
| `payroll_id` | bigint | FK para `payrolls` |
| `description` | string | Descrição do item |
| `type` | string | `earning` (provento) ou `deduction` (desconto) |
| `amount` | decimal:2 | Valor |

**Método:** `typeLabel()` — retorna `"Provento"` ou `"Desconto"`.

### Rota

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/payroll` | `payroll.index` | `App\Livewire\Rh\FolhaPagamento` |

---

## Estoque — Movimentações

Implementado como componente Livewire (`App\Livewire\Estoque\Movimentacao`). A rota dedicada está prevista para breve.

### Funcionalidades

- **Registrar movimentação** — entrada, saída, ajuste ou transferência; atualiza automaticamente o estoque do produto
- **Editar / excluir** — exclusão reverte o estoque do produto
- **Filtros:** busca por produto, tipo, produto específico, período (início/fim)
- **KPIs:** total de movimentações, total entradas, total saídas, total ajustes

### Modelo `StockMovement`

| Campo | Tipo | Descrição |
|---|---|---|
| `product_id` | UUID | FK para `products` |
| `user_id` | bigint | FK para `users` |
| `quantity` | decimal:3 | Quantidade movimentada |
| `type` | string | `input`, `output`, `adjustment`, `transfer` |
| `origin` | string | Origem/documento da movimentação |
| `unit_cost` | decimal:2 (nullable) | Custo unitário |
| `observation` | text (nullable) | Observações |

---

## Compras — Solicitações de Compra (`/compras/solicitacoes`)

Implementado como componente Livewire full-page (`App\Livewire\Compras\SolicitacoesCompra`).

### Funcionalidades

- **CRUD completo** via modal multi-abas (Geral + Itens)
- **Busca de produtos** em tempo real (por nome, código ou EAN) + adição de itens manuais
- **Fluxo de aprovação:** Rascunho → Aguardando Aprovação → Aprovada / Rejeitada
- **Rejeição com motivo** obrigatório
- **Conversão para Pedido de Compra** — gera `PurchaseOrder` com os itens da solicitação
- **Conversão para Cotação** — gera `Cotacao` com os itens para consulta de fornecedores
- **Cancelamento** individual
- **Filtros:** busca (número/título/departamento/solicitante), por status, por prioridade
- **KPIs:** total, aguardando aprovação, aprovadas, rejeitadas, convertidas, valor total estimado

### Modelos

| Modelo | Tabela | Descrição |
|---|---|---|
| `PurchaseRequisition` | `purchase_requisitions` | Solicitação (número, título, status, prioridade, departamento, prazo) |
| `PurchaseRequisitionItem` | `purchase_requisition_items` | Item (produto, descrição, unidade, quantidade, preço estimado) |

### Enums

| Enum | Valores |
|---|---|
| `SolicitacaoCompraStatus` | `rascunho`, `aguardando_aprovacao`, `aprovada`, `rejeitada`, `convertida`, `cancelada` |
| `SolicitacaoCompraPrioridade` | `baixa`, `normal`, `alta`, `urgente` |

### Rota

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/compras/solicitacoes` | `compras.solicitacoes` | `App\Livewire\Compras\SolicitacoesCompra` |

---

## Compras — Pedidos de Compra (`/compras/pedidos`)

Implementado como componente Livewire full-page (`App\Livewire\Compras\PedidosCompra`).

### Funcionalidades

- **CRUD completo** via modal com 6 abas (Geral, Itens, Pagamento, Totais, Logística, Observações)
- **Busca de produtos** em tempo real + adição de itens manuais
- **Cálculo automático de totais** — subtotal, desconto, frete, outras despesas
- **Fluxo de aprovação:** Rascunho → Aguardando Aprovação → Aprovado → Recebido
- **Frete:** CIF, FOB, terceiros, próprio, sem frete; vínculo com `Carrier`
- **Condição e forma de pagamento** configuráveis
- **Cancelamento** individual
- **Filtros:** busca (número/fornecedor) e por status
- **KPIs:** total, valor total, rascunhos, aguardando, aprovados, recebidos

### Modelos

| Modelo | Tabela | Descrição |
|---|---|---|
| `PurchaseOrder` | `purchase_orders` | Pedido (fornecedor, número, status, origem, totais, frete, aprovação) |
| `PurchaseOrderItem` | `purchase_order_items` | Item (produto, quantidade, preço unitário, desconto, total) |
| `Carrier` | `carriers` | Transportadora (nome, CNPJ, contato, is_active) |

### Enums

| Enum | Valores |
|---|---|
| `PurchaseOrderStatus` | `rascunho`, `aguardando_aprovacao`, `aprovado`, `recebido_parcial`, `recebido_total`, `cancelado` |
| `PurchaseOrderOrigin` | `manual`, `solicitacao`, `cotacao` |
| `TipoFrete` | `cif`, `fob`, `terceiros`, `proprio`, `sem_frete` |

### Rota

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/compras/pedidos` | `compras.pedidos` | `App\Livewire\Compras\PedidosCompra` |

---

## Compras — Cotações de Compra (`/compras/cotacoes`)

Implementado como componente Livewire full-page (`App\Livewire\Compras\Cotacoes`).

### Funcionalidades

- **CRUD completo** via modal multi-abas (Geral, Itens, Respostas)
- **Itens de cotação** com busca de produto + adição manual
- **Respostas por fornecedor** — cada fornecedor informa preço unitário e prazo de entrega
- **Seleção do melhor preço** por item
- **Conversão para Pedido de Compra** a partir da cotação fechada
- **Filtros:** busca e por status
- **KPIs:** total, abertas, em análise, finalizadas

### Modelos

| Modelo | Tabela | Descrição |
|---|---|---|
| `Cotacao` | `cotacoes` | Cotação (título, status, prazo) |
| `CotacaoItem` | `cotacao_items` | Item da cotação (produto, descrição, quantidade) |
| `CotacaoResposta` | `cotacao_respostas` | Resposta do fornecedor (preço unitário, prazo de entrega) |

### Enum

| Enum | Valores |
|---|---|
| `CotacaoStatus` | `aberta`, `em_analise`, `finalizada`, `cancelada` |

### Rota

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/compras/cotacoes` | `compras.cotacoes` | `App\Livewire\Compras\Cotacoes` |

---

## Fiscal — Notas Fiscais (`/fiscal/notas-fiscais`)

Implementado como componente Livewire full-page (`App\Livewire\Fiscal\NotaFiscal`).

### Funcionalidades

- **CRUD completo** via modal (criar, visualizar, cancelar, excluir)
- **Filtros:** busca, por status, por tipo (NF-e/NFC-e/…) e por ambiente (homologação/produção)
- **Cancelamento** com motivo obrigatório
- **KPIs:** total, emitidas, canceladas, rascunhos

### Modelo `FiscalNote`

| Campo | Tipo | Descrição |
|---|---|---|
| `id` | bigint | Auto-increment |
| `chave_acesso` | string(44) (nullable) | Chave de acesso da NF-e |
| `numero` | integer (nullable) | Número da NF-e |
| `serie` | string (nullable) | Série |
| `tipo` | string | `nfe`, `nfce`, `nfs` |
| `ambiente` | string | `homologacao`, `producao` |
| `status` | enum | Cast `FiscalNoteStatus` |
| `client_id` | UUID (nullable) | FK para `clients` |
| `total_value` | decimal:2 | Valor total |
| `issue_date` | datetime (nullable) | Data de emissão |
| `cancel_reason` | text (nullable) | Motivo do cancelamento |

### Enum

| Enum | Valores |
|---|---|
| `FiscalNoteStatus` | `rascunho`, `emitida`, `cancelada`, `denegada`, `inutilizada` |

### Rota

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/fiscal/notas-fiscais` | `fiscal.nfe.index` | `App\Livewire\Fiscal\NotaFiscal` |

---

## Fiscal — Tipos de Operação (`/fiscal/tipos-operacao`)

Implementado com componentes Livewire `App\Livewire\Fiscal\TipoOperacao\Index` e `Form`.

### Funcionalidades

- **CRUD completo** com Index paginado e Form dedicado
- Vínculo com CFOP, natureza da operação, tipo de movimento (entrada/saída)

### Modelo `TipoOperacaoFiscal`

Armazena o tipo de operação fiscal (CFOP, natureza, fins tributários). Usado nos pedidos de venda e NF-e.

### Rotas

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/fiscal/tipos-operacao` | `fiscal.tipo-operacao.index` | `App\Livewire\Fiscal\TipoOperacao\Index` |
| `GET` | `/fiscal/tipos-operacao/create` | `fiscal.tipo-operacao.create` | `App\Livewire\Fiscal\TipoOperacao\Form` |
| `GET` | `/fiscal/tipos-operacao/{operacao}/edit` | `fiscal.tipo-operacao.edit` | `App\Livewire\Fiscal\TipoOperacao\Form` |

---

## Fiscal — Grupos Tributários (`/fiscal/grupos-tributarios`)

Implementado com componentes Livewire `App\Livewire\Fiscal\GrupoTributario\Index` e `Form`.

### Funcionalidades

- **CRUD completo** com Index paginado e Form dedicado
- Configuração de ICMS (modalidade de BC, CST), IPI (modalidade, CST), PIS/COFINS (CST, alíquotas)
- Vínculo com regime tributário

### Modelo `GrupoTributario`

Armazena as regras tributárias por grupo — ICMS, IPI, PIS, COFINS. Aplicado a produtos para cálculo automático dos impostos.

### Enums

| Enum | Descrição |
|---|---|
| `RegimeTributario` | `simples_nacional`, `lucro_presumido`, `lucro_real` |
| `IcmsModalidadeBC` | Modalidade da base de cálculo do ICMS |
| `IpiModalidade` | Modalidade de cálculo do IPI |
| `TipoMovimentoFiscal` | `entrada`, `saida` |

### Rotas

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/fiscal/grupos-tributarios` | `fiscal.grupo-tributario.index` | `App\Livewire\Fiscal\GrupoTributario\Index` |
| `GET` | `/fiscal/grupos-tributarios/create` | `fiscal.grupo-tributario.create` | `App\Livewire\Fiscal\GrupoTributario\Form` |
| `GET` | `/fiscal/grupos-tributarios/{grupo}/edit` | `fiscal.grupo-tributario.edit` | `App\Livewire\Fiscal\GrupoTributario\Form` |

---

## Logística — Agendamento de Entregas (`/logistica/agendamento-entregas`)

Implementado como componente Livewire full-page (`App\Livewire\Logistica\AgendamentoEntregas`).

### Funcionalidades

- **CRUD completo** via modal
- **Reagendamento** — altera data e janela de tempo sem recriar o registro
- **Janelas de tempo** (`DeliveryTimeWindow`) — blocos de horário pré-cadastrados
- **Vínculo com Pedido de Venda** e com **Veículo**
- **Filtros:** busca, por status, por data e por prioridade
- **KPIs:** agendados, em rota, entregues, cancelados

### Modelos

| Modelo | Tabela | Descrição |
|---|---|---|
| `SchedulingOfDeliveries` | `scheduling_of_deliveries` | Agendamento (data, veículo, motorista, peso, volume, prioridade, status) |
| `DeliveryTimeWindow` | `delivery_time_windows` | Janela de tempo (label, horário início/fim) |

### Enums

| Enum | Valores |
|---|---|
| `DeliveryScheduleStatus` | `agendado`, `em_rota`, `entregue`, `cancelado`, `reagendado` |
| `DeliveryPriority` | `baixa`, `normal`, `alta`, `urgente` |

### Rota

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/logistica/agendamento-entregas` | `scheduling_of_deliveries.index` | `App\Livewire\Logistica\AgendamentoEntregas` |

---

## Vendas — Pedidos de Venda (`/vendas/pedidos`)

Implementado como componente Livewire full-page (`App\Livewire\Vendas\PedidosVenda`).

### Funcionalidades

- **CRUD completo** via modal multi-abas (Cabeçalho, Itens, Entrega/Endereço, Pagamento, Observações)
- **Busca de produtos** em tempo real; cálculo automático de totais com desconto
- **Tabela de preços** vinculável ao pedido
- **Tipo de operação fiscal** (`TipoOperacaoFiscal`) por pedido
- **Canal de venda** (balcão, televendas, e-commerce, representante)
- **Fluxo de status:** novo → aprovado → em separação → enviado → entregue / cancelado
- **Log de alterações** automático (`SalesOrderLog`)
- **Anexos** (`SalesOrderAttachment`)
- **Parcelas** (`SalesOrderInstallment`)
- **Filtros:** busca e por status
- **KPIs:** total de pedidos, valor total, aprovados, cancelados

### Modelos principais

| Modelo | Tabela | Descrição |
|---|---|---|
| `SalesOrder` | `sales_orders` | Pedido de venda (cliente, vendedor, status, canal, operação fiscal) |
| `SalesOrderItem` | `sales_order_items` | Item do pedido (produto, quantidade, preço, desconto, total) |
| `SalesOrderAddress` | `sales_order_addresses` | Endereço de entrega do pedido |
| `SalesOrderPayment` | `sales_order_payments` | Pagamento do pedido |
| `SalesOrderInstallment` | `sales_order_installments` | Parcelas do pedido |
| `SalesOrderLog` | `sales_order_logs` | Log de alterações do pedido |
| `SalesOrderAttachment` | `sales_order_attachments` | Anexos do pedido |

### Enums

| Enum | Descrição |
|---|---|
| `SalesOrderStatus` | Ciclo de vida do pedido (novo → aprovado → …) |
| `TipoOperacaoVenda` | `venda`, `bonificacao`, `consignacao`, `devolucao` |
| `CanalVenda` | `balcao`, `televendas`, `ecommerce`, `representante` |
| `OrigemPedido` | Origem/canal de entrada do pedido |

### Service

`SalesOrderService` — `createOrder()`, `updateOrder()`, aprovação, cancelamento, estatísticas, cálculo de valores.

### Rota

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/vendas/pedidos` | `vendas.pedidos` | `App\Livewire\Vendas\PedidosVenda` (via view) |

---

## Vendas — Tabelas de Precificação (`/vendas/precificacao`)

Implementado como componente Livewire full-page (`App\Livewire\Vendas\TabelasPrecificacao`).

### Funcionalidades

- **CRUD de tabelas** (nome, código, vigência, is_default, is_active)
- **Itens por tabela** — preço calculado e margem por produto
- **Calculadora de markup** — insere custo e percentuais (despesas, imposto, comissão, frete, prazo, VPC, assistência, inadimplência, lucro) e obtém preço final, markup divisor e margem de contribuição
- **Filtro** por status ativo/inativo

### Modelos

| Modelo | Tabela | Descrição |
|---|---|---|
| `PriceTable` | `price_tables` | Tabela de preços (nome, código, vigência, is_default) |
| `PriceTableItem` | `price_table_items` | Preço por produto dentro da tabela |

### Service

`PricingService::calculateFinalPrice(array $params): array` — retorna preço final, markup divisor e margem de contribuição usando a fórmula de markup divisor.

### Rota

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/vendas/precificacao` | `vendas.precificacao` | `App\Livewire\Vendas\TabelasPrecificacao` |

---

## RH — Batida de Ponto (`/stitch_beat`)

Implementado como componente Livewire full-page (`App\Livewire\Rh\BatidaPonto`).

### Funcionalidades

- **Registro automático sequencial** — o sistema detecta o próximo passo: Registrar Entrada → Iniciar Pausa → Retornar de Pausa → Registrar Saída
- **Geolocalização** — captura latitude/longitude via API do browser; exibe nome do local
- **Horário previsto de saída** calculado a partir do turno do funcionário
- **Indicador de jornada completa** ao registrar a saída
- **Vinculação por e-mail** — o funcionário é identificado pelo e-mail do usuário logado
- **Timeline do dia** — lista todos os registros do dia atual

### Dependência

Usa `PontoService`:

| Método | Descrição |
|---|---|
| `getEmployeeByEmail(string $email)` | Retorna o funcionário ativo com o e-mail informado |
| `getTodayRecord(string $employeeId)` | Retorna o `TimeRecord` do dia atual |
| `getTodayRecords(string $employeeId)` | Retorna todos os registros do dia (collection) |
| `registerClockAction(...)` | Registra a ação de ponto sequencial e retorna `['success', 'message', 'action']` |
| `getNextAction(TimeRecord $record)` | Determina o próximo passo ("Registrar Entrada", "Iniciar Pausa", etc.) |
| `calculateExpectedEndTime(TimeRecord $record)` | Calcula horário previsto de saída baseado no turno |

### Rota

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/stitch_beat` | `stitch_beat.index` | `App\Livewire\Rh\BatidaPonto` |

---

## RH — Holerite (`/holerite`)

Página dedicada ao holerite, implementada como componente Livewire full-page (`App\Livewire\Rh\Holerite`).

> Diferente do modal de holerite dentro da **Folha de Pagamento**, esta página oferece uma interface mais completa com painel lateral de seleção.

### Funcionalidades

- **Painel lateral** com lista de folhas do mês filtráveis por status e nome do funcionário
- **Visualização do holerite** — proventos, descontos, salário líquido, dados da empresa
- **Edição de verbas** — adicionar/editar/remover proventos e descontos com recalculação automática
- **Fechar folha** — Rascunho → Fechada (com confirmação)
- **Marcar como pago** — Fechada → Paga (com data de pagamento)
- **Impressão** via `HoleriteController@print` (`GET /holerite/{id}/imprimir`)
- **KPIs do mês:** total de folhas, por status, total líquido
- **Dados da empresa** lidos via `Setting::get()`

### Rotas

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/holerite` | `holerite.index` | `App\Livewire\Rh\Holerite` |
| `GET` | `/holerite/{id}/imprimir` | `holerite.print` | `HoleriteController@print` |

---

## Produção — Ordens de Produção (`/production_orders`)

Implementado como componente Livewire (`App\Livewire\Producao\OrdemProducao`).

### Funcionalidades

- **CRUD completo** via modal
- **Multi-produto** — uma OP pode produzir vários produtos com quantidades diferentes
- **BOM (Bill of Materials / Insumos)** — lista de matérias-primas consumidas (`ProductionItem`)
- **Modal de progresso** — lançar quantidades produzidas e pausar/retomar a OP
- **Fluxo de status:** Pendente → Em Produção → Pausada → Concluída / Cancelada
- **Custo estimado** e **número de lote** por OP
- **Filtros:** busca (nome, produto, lote) e por status

### Modelos

| Modelo | Tabela | Descrição |
|---|---|---|
| `ProductionOrder` | `production_orders` | Ordem de produção (nome, status, datas, custo, lote) |
| `ProductionOrderProduct` | `production_order_products` | Produto a produzir (quantidade planejada, quantidade produzida) |
| `ProductionItem` | `production_items` | Insumo/BOM (produto, quantidade a consumir) |

### Enum

| Enum | Valores |
|---|---|
| `ProductionOrderStatus` | `pendente`, `em_producao`, `pausada`, `concluida`, `cancelada` |

---

### Funcionalidades

- **CRUD completo** via componentes Livewire (`Index` + `Form`)
- **Slug automático** gerado a partir do nome
- **Cor** associada para identificação visual
- **Soft delete**
- Relacionamento `HasMany` com `Product` via `product_category_id`

### Modelo `ProductCategory`

| Campo | Tipo | Descrição |
|---|---|---|
| `id` | UUID | Gerado automaticamente |
| `name` | string | Nome da categoria |
| `slug` | string | Slug único (auto-gerado) |
| `description` | text (nullable) | Descrição opcional |
| `color` | string (nullable) | Cor hex para identificação visual |
| `is_active` | boolean | |

### Rotas

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/product-categories` | `product-categories.index` | `App\Livewire\Cadastro\CategoriaProduto\Index` |
| `GET` | `/product-categories/create` | `product-categories.create` | `App\Livewire\Cadastro\CategoriaProduto\Form` |
| `GET` | `/product-categories/{category}/edit` | `product-categories.edit` | `App\Livewire\Cadastro\CategoriaProduto\Form` |

---

## Cadastro — Unidades de Medida (`/unit-of-measures`)

### Funcionalidades

- **CRUD completo** via componentes Livewire (`Index` + `Form`)
- **Abreviação em maiúsculas** — normalizada automaticamente (ex.: `kg`, `UN` → `KG`, `UN`)
- **Soft delete**
- Relacionamento `HasMany` com `Product` via `unit_of_measure_id`

### Modelo `UnitOfMeasure`

| Campo | Tipo | Descrição |
|---|---|---|
| `id` | UUID | Gerado automaticamente |
| `name` | string | Nome completo (ex.: `Quilograma`) |
| `abbreviation` | string | Abreviação em maiúsculas (ex.: `KG`) |
| `description` | text (nullable) | Descrição opcional |
| `is_active` | boolean | |

**Accessor:** `label` — retorna `"ABBREVIATION — name"`.

### Rotas

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/unit-of-measures` | `unit-of-measures.index` | `App\Livewire\Cadastro\UnidadeMedida\Index` |
| `GET` | `/unit-of-measures/create` | `unit-of-measures.create` | `App\Livewire\Cadastro\UnidadeMedida\Form` |
| `GET` | `/unit-of-measures/{unit}/edit` | `unit-of-measures.edit` | `App\Livewire\Cadastro\UnidadeMedida\Form` |

---

## Configurações do Sistema (`/configuracoes`)

Acessível em `/configuracoes` (link no dropdown do usuário na sidebar). Restrito a administradores via middleware.

### Arquitetura

A tabela `settings` usa o padrão **key-value** com agrupamento por seção:

```sql
settings (id, key UNIQUE, value TEXT, group, created_at, updated_at)
```

O model `App\Models\Setting` expõe helpers estáticos com **cache automático**:

```php
Setting::get('system_name', 'Nexora ERP');          // lê com cache
Setting::set('system_name', 'Minha Empresa');        // grava e invalida cache
Setting::group('general');                           // array key=>value do grupo
Setting::allKeyed();                                 // todas as configurações
```

### Seções (9 abas)

| Aba | Grupo | Configurações |
|---|---|---|
| **Geral** | `general` | Nome do sistema, Slogan, Fuso horário, Idioma, Formato de data/hora |
| **Empresa** | `company` | Razão Social, CNPJ/IE, Endereço completo, E-mail, Telefone |
| **Financeiro** | `financial` | Moeda, Separadores decimal/milhar, Alíquota padrão |
| **Notificações** | `notifications` | Alertas de estoque, E-mail boas-vindas, Notif. browser, WhatsApp API |
| **Aparência** | `appearance` | Tema (claro/escuro/sistema), Cor primária (7 paletas), Densidade, Sidebar |
| **Segurança** | `security` | Tempo de sessão, Senha forte, Logs de atividade, Modo manutenção |
| **Regras de Estoque** | `stock` | Venda sem estoque (Não/Autorização/Sim), Reserva (Pedido/Nota), Alerta crítico % |
| **Regras Fiscais** | `fiscal` | CFOP padrão, Emissão automática NF-e, Ambiente (Homologação/Produção), Impostos em tempo real |
| **Regras de Venda** | `sales` | Tipo de venda (Gerencial/Fiscal/Híbrido), Tabela de preços, Validade de orçamentos, Desconto máximo, Margem negativa, CPF obrigatório |

### Rotas

| Método | URI | Nome | Descrição |
|---|---|---|---|
| `GET` | `/configuracoes` | `configuration.index` | Exibe a página de configurações |
| `POST` | `/configuracoes` | `configuration.store` | Salva todas as configurações |

---

## Controle de Usuários

Acessível em `/users` (somente administradores via middleware `admin`).

### Campos do Usuário

| Campo | Tipo | Descrição |
|---|---|---|
| `name` | string | Nome completo |
| `email` | string | E-mail único |
| `password` | string | Hash bcrypt |
| `is_admin` | boolean | Acesso total sem restrições de licença ou status |
| `is_active` | boolean | Usuário inativo não consegue fazer login |
| `has_license` | boolean | Sem licença exibe modal de aviso a cada 15 s |
| `modules` | JSON | Lista dos módulos contratados pelo usuário |
| `last_login_at` | timestamp | Data/hora do último login (registrado pelo `SessionController`) |
| `avatar` | string (nullable) | Caminho do avatar no disco `public`; gerenciado pelo `ProfileController` |
| `phone` | string(20) (nullable) | Telefone do usuário |
| `job_title` | string(100) (nullable) | Cargo / título |
| `department` | string(100) (nullable) | Departamento |
| `bio` | text (nullable) | Breve biografia |

### Regras de Negócio

| Regra | Comportamento |
|---|---|
| `is_admin = true` | Acesso livre — sem verificação de licença ou status ativo |
| `is_active = false` | Login bloqueado com mensagem: *"Usuário inativado, para mais informações entre em contato com o suporte"* |
| `has_license = false` | Modal de aviso glassmorphism exibido a cada 15 s durante o uso do sistema |
| Módulos | O usuário só acessa módulos habilitados no seu cadastro |

---

## Modal de Aviso de Licença

Exibido em `layouts/app.blade.php` para usuários **ativos**, **não-admin** e **sem licença paga**.

### Visual (Glassmorphism Dark)

- **Overlay:** `rgba(0,0,0,0.70)` + `backdrop-blur(6px)`
- **Modal:** `rgba(10,15,29,0.88)` + `backdrop-blur(28px)`, borda `rgba(255,255,255,0.10)`
- **Ícone:** círculo amber (`#FBBF24`) centralizado no topo
- **Botão primário:** gradiente `#2563EB → #06B6D4` — *"Falar com o Suporte"*
- **Botão secundário:** border sutil — *"Entendi, fechar"*
- **Botão X:** canto superior direito

### Comportamento (JavaScript)

1. Aparece **1,5 s** após o carregamento da página
2. Fecha-se automaticamente após **8 s** (barra de progresso visual)
3. Usuário pode fechar manualmente (botão X, botão "Entendi" ou clique no overlay)
4. Após fechar: contador regressivo de **15 s** visível na tela
5. Reabre automaticamente após os 15 s

---

## Design System

O CSS é gerido por arquivos parciais importados em `resources/css/app.css`:

| Arquivo | Conteúdo |
|---|---|
| `_base.css` | Reset, tipografia global, utilitários |
| `_layout.css` | Sidebar, topbar, variáveis CSS (`:root`), app wrapper |
| `_components.css` | Cards, KPIs, badges, botões, gráficos, modal de licença, página de configurações |
| `_tables.css` | Estilos de tabelas dos módulos |

### Classes principais — Dashboard

| Classe CSS | Descrição |
|---|---|
| `.nx-dashboard-kpis` | Grid `repeat(4, 1fr)` para os 4 cards KPI lado a lado |
| `.nx-kpi-card` | Card branco com borda, sombra suave e hover elevado |
| `.nx-kpi-card-trend` | Linha de variação com seta ▲/▼ e cor verde/vermelho |
| `.nx-dashboard-grid-charts` | Grid `2fr 1fr` para gráfico de linha + donut |
| `.nx-activity-row` | Grid `1fr 1fr` para os dois painéis de atividade |
| `.nx-desempenho-stats` | Grid de 3 colunas para o bloco de desempenho |

### Classes principais — Configurações

| Classe CSS | Descrição |
|---|---|
| `.nx-settings-layout` | Grid `220px 1fr` — sidebar de nav + área de conteúdo |
| `.nx-settings-nav` | Sidebar de navegação entre abas (sticky) |
| `.nx-settings-nav-item` | Botão de aba com estado `.active` |
| `.nx-settings-content` | Painel de conteúdo — visível apenas com classe `.active` |
| `.nx-settings-body` | Área de campos dentro de cada aba |
| `.nx-settings-footer` | Rodapé com botão de salvar |
| `.nx-toggle-row` | Linha com label + toggle switch |
| `.nx-switch` | Componente switch (checkbox oculto + `.nx-switch-track`) |
| `.nx-theme-cards` | Grid de 3 cards para seleção de tema visual |
| `.nx-color-swatches` | Linha de bolinhas coloridas para cor primária |

### Classes principais — Modal de Licença

| Classe CSS | Descrição |
|---|---|
| `.nx-license-overlay` | Overlay fixo fullscreen — oculto por padrão |
| `.nx-license-overlay--visible` | Exibe o overlay + anima o modal |
| `.nx-license-modal` | Janela glassmorphism centralizada |
| `.nx-license-modal-x` | Botão X no canto superior direito |
| `.nx-license-modal-btn-primary` | Botão gradiente ciano (Falar com Suporte) |
| `.nx-license-modal-progress-bar.nx-running` | Animação de drenagem da barra de progresso |

> Após editar qualquer arquivo em `resources/css/`, rode `npm run build` para gerar o bundle de produção.

---

## Middleware

### `MaintenanceERP`

Aplicado ao grupo de rotas autenticadas em `routes/web.php`. Rotas **liberadas** (renderizam normalmente):

- `home` — página inicial `/`
- `module.show` — página de detalhes do módulo
- `module.item.development` — tela de funcionalidade em desenvolvimento
- Módulos de item de módulo listados em `ModulePageController::moduleItemRouteNames()`
- `products.*`, `clients.*`, `vehicles.*`, `employees.*`, `suppliers.*` — cadastros ativos
- `roles.*` — funções/cargos (Livewire)
- `users.*` — controle de usuários (somente admin via middleware separado)
- `configuration.*` — configurações do sistema
- `profile.*` — perfil do usuário
- `permissions.*` — gerenciamento de permissões
- `logs.*` — logs do sistema
- `suporte.*` — módulo de suporte
- `route_management.*`, `routing.*`, `scheduling_of_deliveries.*` — logística liberada no middleware
- `compras.*` — telas de compras em desenvolvimento
- `fiscal.*` — rotas fiscais liberadas
- `notifications.*` — central de notificações
- `unit-of-measures.*`, `product-categories.*` — cadastros complementares

Todas as demais rotas retornam a view `system.desenvolvimento` ("Em Breve") até que o módulo esteja pronto.

> **Atenção:** ao implementar uma nova rota que deve estar acessível, adicione o padrão `rotaNova.*` no bloco `if` do método `handle()` em `MaintenanceERP.php`.
> As rotas dos módulos financeiro (`plans_of_accounts.*`, `contas_bancarias.*`, `accounts_payable.*`, `accounts_receivable.*`, `cash_flow.*`), RH (`working_day.*`, `payroll.*`, `holerite.*`, `stitch_beat.*`), dashboard (`dashboard.*`), produção (`production_orders.*`), vendas (`vendas.*`) e relatórios ainda não estão no whitelist — rotas ativas nesses módulos precisam ser adicionadas manualmente.

### `EnforceMidnightSession`

Aplicado ao grupo de rotas autenticadas. Encerra automaticamente a sessão do usuário se a data do login (`last_login_at`) for anterior ao dia atual, redirecionando para `/login` com mensagem de expiração. Garante que nenhuma sessão atravesse a meia-noite sem reautenticação.

### `EnsureUserIsAdmin`

Bloqueia o acesso de usuários não-administradores às rotas protegidas. Aplicado ao grupo de rotas em `routes/perfil.php` (users, permissions, logs).

---

## Painel Administrativo (Filament)

Acesse em: `/admin`

- Cor primária: Amber.
- Descobre automaticamente Resources, Pages e Widgets em `app/Filament/`.
- Widgets padrão: `AccountWidget` e `FilamentInfoWidget`.

Para criar o primeiro usuário administrador:

```bash
php artisan make:filament-user
```

---

## Enums

Todos os enums ficam em `app/Enums/` e são usados como cast nos modelos.

| Enum | Modelo | Descrição |
|---|---|---|
| `TipoPessoa` | `Client` | Pessoa Física / Jurídica |
| `TipoProduto` | `Product` | Tipo do produto |
| `NaturezaProduto` | `Product` | Natureza (ex.: revenda, produção própria) |
| `TipoVeiculo` | `Vehicle` | Tipo do veículo |
| `CategoriaVeiculo` | `Vehicle` | Categoria do veículo |
| `EspecieVeiculo` | `Vehicle` | Espécie do veículo |
| `CombustivelVeiculo` | `Vehicle` | Tipo de combustível |
| `StatusTicketSuporte` | `TicketSuporte` | `aberto`, `em_andamento`, `resolvido`, `fechado` |
| `PrioridadeTicketSuporte` | `TicketSuporte` | `baixa`, `media`, `alta`, `critica` |
| `PayableStatus` | `AccountPayable` | `pending`, `paid`, `overdue`, `cancelled` |
| `ReceivableStatus` | `AccountReceivable` | `pending`, `received`, `overdue`, `partial`, `cancelled` |
| `PayrollStatus` | `Payroll` | `draft`, `closed`, `paid` |
| `TimeRecordStatus` | `TimeRecord` | `active`, `break`, `absent`, `completed` |
| `SolicitacaoCompraStatus` | `PurchaseRequisition` | `rascunho`, `aguardando_aprovacao`, `aprovada`, `rejeitada`, `convertida`, `cancelada` |
| `SolicitacaoCompraPrioridade` | `PurchaseRequisition` | `baixa`, `normal`, `alta`, `urgente` |
| `PurchaseOrderStatus` | `PurchaseOrder` | `rascunho`, `aguardando_aprovacao`, `aprovado`, `recebido_parcial`, `recebido_total`, `cancelado` |
| `PurchaseOrderOrigin` | `PurchaseOrder` | `manual`, `solicitacao`, `cotacao` |
| `TipoFrete` | `PurchaseOrder`, `SalesOrder` | `cif`, `fob`, `terceiros`, `proprio`, `sem_frete` |
| `CotacaoStatus` | `Cotacao` | `aberta`, `em_analise`, `finalizada`, `cancelada` |
| `SalesOrderStatus` | `SalesOrder` | status do ciclo de vida do pedido de venda |
| `TipoOperacaoVenda` | `SalesOrder` | `venda`, `bonificacao`, `consignacao`, `devolucao` |
| `CanalVenda` | `SalesOrder` | `balcao`, `televendas`, `ecommerce`, `representante` |
| `OrigemPedido` | `SalesOrder` | origem/canal de entrada do pedido |
| `FiscalNoteStatus` | `FiscalNote` | `rascunho`, `emitida`, `cancelada`, `denegada`, `inutilizada` |
| `TipoMovimentoFiscal` | `FiscalNote` | `entrada`, `saida` |
| `RegimeTributario` | `GrupoTributario` | `simples_nacional`, `lucro_presumido`, `lucro_real` |
| `IcmsModalidadeBC` | `GrupoTributario` | modalidade de base de cálculo do ICMS |
| `IpiModalidade` | `GrupoTributario` | modalidade de cálculo do IPI |
| `ProductionOrderStatus` | `ProductionOrder` | `pendente`, `em_producao`, `pausada`, `concluida`, `cancelada` |
| `DeliveryScheduleStatus` | `SchedulingOfDeliveries` | `agendado`, `em_rota`, `entregue`, `cancelado`, `reagendado` |
| `DeliveryPriority` | `SchedulingOfDeliveries` | `baixa`, `normal`, `alta`, `urgente` |
| `StatusSeparacao` | (separação de pedidos) | `pendente`, `em_separacao`, `separado`, `cancelado` |

---

## Modelos Principais

### `Setting`

| Campo | Tipo | Observações |
|---|---|---|
| `id` | bigint | Auto-increment |
| `key` | string | Único — ex: `system_name`, `theme` |
| `value` | text | Valor serializado como string |
| `group` | string | Agrupamento — ex: `general`, `security`, `sales` |

**Helpers estáticos:**

| Método | Descrição |
|---|---|
| `Setting::get($key, $default)` | Retorna valor com cache |
| `Setting::set($key, $value, $group)` | Grava e invalida o cache da chave |
| `Setting::group($group)` | Retorna array `key => value` do grupo |
| `Setting::allKeyed()` | Retorna todas as configurações como array |

### `User`

| Campo | Tipo | Observações |
|---|---|---|
| `id` | bigint | Auto-increment |
| `name` | string | Nome completo |
| `email` | string | Único |
| `password` | string | Hash bcrypt |
| `is_admin` | boolean | Administrador — acesso total |
| `is_active` | boolean | Inativo = login bloqueado |
| `has_license` | boolean | Sem licença = modal de aviso recorrente |
| `modules` | JSON | Array de slugs dos módulos contratados |
| `last_login_at` | timestamp | Nullable — data do último acesso |
| `avatar` | string (nullable) | Caminho no disco `public` (ex.: `avatars/foto.jpg`) |
| `phone` | string(20) (nullable) | Telefone do usuário |
| `job_title` | string(100) (nullable) | Cargo / título |
| `department` | string(100) (nullable) | Departamento |
| `bio` | text (nullable) | Breve biografia (máx. 500 chars) |

### `Product`

| Campo | Tipo | Observações |
|---|---|---|
| `id` | UUID | Gerado automaticamente via `booted()` |
| `product_code` | string | Código interno (ex.: `PROD-000001`), gerado automaticamente |
| `name` | string | Nome do produto |
| `ean` | string (13) | Código de barras (nullable) |
| `description` | string | Descrição (nullable) |
| `short_description` | string | Descrição curta |
| `brand` | string | Marca |
| `product_type` | enum | Cast `TipoProduto` |
| `nature` | enum | Cast `NaturezaProduto` |
| `product_line` | string | Linha do produto |
| `unit_of_measure` | string | |
| `category` | string | |
| `sale_price` | decimal:2 | |
| `stock` | integer | |
| `expiration_date` | date | Nullable |
| `weight_net` | decimal:3 | Peso líquido (kg) |
| `weight_gross` | decimal:3 | Peso bruto (kg) |
| `height` / `width` / `depth` | decimal:2 | Dimensões (cm) |
| `full_description` | text | Descrição completa |
| `is_active` | boolean | |
| `highlights` | JSON | Destaques/características |
| `tags` | JSON | Tags de busca |
| `image` | string | Caminho no disco `public`; accessor `image_url` retorna URL completa |

- Usa `SoftDeletes`.
- Relacionamento N:N com `Supplier` via tabela `product_supplier`.

### `Supplier`

| Campo | Tipo |
|---|---|
| `id` | UUID |
| `name` | string |
| `social_name` | string |
| `taxNumber` | string (CNPJ) |
| `email` | string |
| `phone_number` | string |
| `address_zip_code` | string |
| `address_street` | string |
| `address_number` | string |
| `address_complement` | string (nullable) |
| `address_district` | string |
| `address_city` | string |
| `address_state` | string |

- Usa `SoftDeletes`.
- Método auxiliar `getFullAddress(): string`.
- Relacionamento N:N com `Product`.

### `Client`

| Campo | Tipo | Observações |
|---|---|---|
| `id` | UUID | |
| `tipo_pessoa` | enum | Cast `TipoPessoa` (Física / Jurídica) |
| `name` | string | |
| `social_name` | string (nullable) | |
| `taxNumber` | string | CPF ou CNPJ |
| `email` | string | |
| `phone_number` | string | |
| `address` | string | Endereço legado (campo único) |
| `address_zip_code` | string | CEP |
| `address_street` | string | Logradouro |
| `address_number` | string | Número |
| `address_complement` | string (nullable) | Complemento |
| `address_district` | string | Bairro |
| `address_city` | string | Cidade |
| `address_state` | string | UF |

- Método auxiliar `getFullAddress(): string`.

### `Employees`

| Campo | Tipo | Observações |
|---|---|---|
| `id` | UUID | |
| `name` | string | |
| `social_name` | string | Nome social |
| `identification_number` | string | CPF |
| `rg` | string | RG |
| `rg_issuer` | string | Órgão emissor do RG |
| `rg_date` | date | Data de emissão do RG |
| `birth_date` | date | Data de nascimento |
| `gender` | string | |
| `marital_status` | string | Estado civil |
| `nationality` | string | |
| `birthplace` | string | Naturalidade |
| `role` | string | Função/cargo (nome) |
| `email` | string | |
| `phone_number` | string | Telefone principal |
| `phone_secondary` | string | Telefone secundário |
| `address` | string | Endereço legado |
| `zip_code` / `street` / `number` / `complement` / `neighborhood` / `city` / `state` / `country` | string | Endereço detalhado |
| `emergency_contact_name` | string | |
| `emergency_contact_relationship` | string | |
| `emergency_contact_phone` | string | |
| `photo` | string | Caminho da foto |
| `access_profile` | string | Perfil de acesso ao sistema |
| `is_active` | boolean | |
| `admission_date` | date | Data de admissão |
| `work_schedule` | string | Jornada de trabalho |
| `allow_system_access` | boolean | Permite acesso ao sistema |
| `department` | string | Departamento |
| `salary` | decimal:2 | Salário |
| `internal_code` | string | Código interno |
| `observations` | text | Observações |

### `Vehicle`

| Campo | Tipo | Observações |
|---|---|---|
| `name` | string | Apelido/identificação |
| `plate` | string | Placa |
| `renavam` | string | |
| `chassis` | string | |
| `vehicle_type` | enum | Cast `TipoVeiculo` |
| `category` | enum | Cast `CategoriaVeiculo` |
| `species` | enum | Cast `EspecieVeiculo` |
| `manufacturing_year` | integer | Ano de fabricação |
| `model_year` | integer | Ano do modelo |
| `brand` | string | Marca |
| `model` | string | Modelo |
| `color` | string | |
| `fuel_type` | enum | Cast `CombustivelVeiculo` |
| `power_hp` | integer | Potência (cv) |
| `displacement_cc` | integer | Cilindrada (cc) |
| `doors` | integer | Número de portas |
| `passenger_capacity` | integer | Capacidade de passageiros |
| `transmission_type` | string | Câmbio |
| `traction_type` | string | Tração |
| `gross_weight` | decimal:2 | PBT (kg) |
| `net_weight` | decimal:2 | Tara (kg) |
| `cargo_capacity` | decimal:2 | Capacidade de carga (kg) |
| `department` | string | Departamento responsável |
| `responsible_driver` | string | Motorista responsável |
| `cost_center` | string | Centro de custo |
| `unit` | string | Unidade/filial |
| `current_location` | string | Localização atual |
| `location_note` | string | Observação de localização |
| `is_active` | boolean | |
| `acquisition_date` | date | Data de aquisição |
| `acquisition_value` | decimal:2 | Valor de aquisição |
| `photos` | JSON | Array de caminhos de fotos |
| `observations` | text | |

- Accessors: `status_label` e `display_name`.

### `Role`

| Campo | Tipo | Observações |
|---|---|---|
| `id` | bigint | Auto-increment |
| `name` | string | Nome da função/cargo |
| `department` | string | Departamento |
| `code` | string | Código interno |
| `parent_role_id` | bigint (nullable) | FK para hierarquia de cargos |
| `description` | string | |
| `is_active` | boolean | |
| `allow_assignment` | boolean | Permite atribuição a funcionários |
| `permissions` | JSON | Mapa de permissões por módulo/ação |

- Relacionamentos: `parentRole()`, `childRoles()`, `employees()`.
- Método `getPermissionForModule(string $module, string $action): bool` — verifica permissão com herança hierárquica.

### `SystemLog`

| Campo | Tipo | Observações |
|---|---|---|
| `id` | bigint | Auto-increment |
| `level` | string | `success`, `warning`, `error` |
| `action` | string | Código da ação |
| `module` | string | Módulo do sistema |
| `description` | string | Descrição do evento |
| `ip` | string | IP do requisitante |
| `user_id` | bigint (nullable) | FK para `users` |
| `user_name` | string | Nome snapshot |
| `user_email` | string | E-mail snapshot |
| `context` | JSON | Dados extras opcionais |

### `TicketSuporte`

| Campo | Tipo | Observações |
|---|---|---|
| `id` | UUID | |
| `user_id` | bigint | FK para `users` |
| `assunto` | string | |
| `status` | enum | Cast `StatusTicketSuporte` |
| `prioridade` | enum | Cast `PrioridadeTicketSuporte` |
| `categoria` | string (nullable) | |
| `fechado_em` | datetime (nullable) | Preenchido ao resolver/fechar |

### `MensagemSuporte`

| Campo | Tipo | Observações |
|---|---|---|
| `id` | bigint | Auto-increment |
| `ticket_id` | UUID | FK para `tickets_suporte` |
| `user_id` | bigint | FK para `users` |
| `conteudo` | text | |
| `is_suporte` | boolean | `true` = mensagem do time de suporte |
| `lida` | boolean | Marcada ao selecionar o ticket |

### `PlanOfAccount`

Ver documentação completa em [Financeiro — Plano de Contas](#financeiro--plano-de-contas-plansofaccounts).

| Campo | Tipo | Observações |
|---|---|---|
| `id` | bigint | Auto-increment |
| `parent_id` | bigint (nullable) | FK para a própria tabela |
| `code` | string(30) | Código (ex.: `1.01.002`) |
| `name` | string | |
| `description` | text (nullable) | |
| `type` | enum | `receita`, `despesa`, `ativo`, `passivo` |
| `is_selectable` | boolean | `false` = conta sintética |
| `is_active` | boolean | |

### `BaccaratAccount`

Ver documentação completa em [Financeiro — Contas Bancárias](#financeiro--contas-bancárias-contas-bancarias).

| Campo | Tipo | Observações |
|---|---|---|
| `id` | bigint | Auto-increment |
| `name` | string | Apelido da conta |
| `bank_name` | string | Nome do banco |
| `agency` | string (nullable) | |
| `number` | string (nullable) | |
| `type` | enum | `corrente`, `poupanca`, `caixa_interno`, `digital` |
| `balance` | decimal:2 | Saldo atual |
| `predicted_balance` | decimal:2 | Saldo previsto |
| `color` | string(20) (nullable) | Cor hex do card |
| `chart_of_account_id` | bigint (nullable) | FK para `plans_of_accounts` |
| `is_active` | boolean | |
| `is_reconciled` | boolean | |
| `last_reconciled_at` | date (nullable) | |

### `AccountPayable`

Ver documentação completa em [Financeiro — Contas a Pagar](#financeiro--contas-a-pagar-accountspayable).

### `AccountReceivable`

Ver documentação completa em [Financeiro — Contas a Receber](#financeiro--contas-a-receber-accountsreceivable).

### `Payroll`

Ver documentação completa em [RH — Folha de Pagamento](#rh--folha-de-pagamento-payroll).

### `PayrollItem`

Ver documentação completa em [RH — Folha de Pagamento](#rh--folha-de-pagamento-payroll).

### `WorkShift`

Ver documentação completa em [RH — Jornada de Trabalho / Ponto](#rh--jornada-de-trabalho--ponto-workingday).

### `TimeRecord`

Ver documentação completa em [RH — Jornada de Trabalho / Ponto](#rh--jornada-de-trabalho--ponto-workingday).

### `StockMovement`

Ver documentação completa em [Estoque — Movimentações](#estoque--movimentações).

### `ProductCategory`

Ver documentação completa em [Cadastro — Categorias de Produto](#cadastro--categorias-de-produto-product-categories).

### `UnitOfMeasure`

Ver documentação completa em [Cadastro — Unidades de Medida](#cadastro--unidades-de-medida-unit-of-measures).

---

## Services

| Service | Descrição |
|---|---|
| `BrasilAPIService` | Consulta CNPJ (`consultarCnpj`) e CEP (`consultarCep`) via BrasilAPI |
| `ContasPagarService` | CRUD, baixa (`registerPayment`), reagendamento (`reschedule`), cancelamento, KPIs e sync de vencidos |
| `ContasReceberService` | CRUD, baixa (`registerReceipt`), reagendamento (`reschedule`), cancelamento, KPIs e sync de vencidos |
| `JornadaService` | KPIs de presença, grade de presença (`getPresenceGrid`), timeline, save/delete de registros, turnos ativos |
| `LogService` | Registro centralizado de logs: `::success()`, `::warning()`, `::error()` |
| `PayrollService` | Geração por funcionário/lote, itens de folha (`saveItem`/`removeItem`), fechamento, pagamento, KPIs |
| `PontoService` | Batida de ponto: `registerClockAction` (entrada→pausa→saída sequencial), `getTodayRecord`, `getNextAction`, `calculateExpectedEndTime` |
| `PricingService` | Cálculo de preço por markup divisor (`calculateFinalPrice`) — custo + percentuais sobre venda |
| `RoleService` | Lógica de negócio de funções/cargos |
| `SalesOrderService` | CRUD de pedidos de venda (`createOrder`, `updateOrder`), aprovação, cancelamento, estatísticas |
| `SuporteService` | Criação de tickets, envio de mensagens, atualização de status e marcação de leitura |
| `DashboardMetricsService` | Métricas e dados agregados para o dashboard (usa `stock_movements`) |

---

## Detalhamento de Rotas

Todas as rotas web estão sob o middleware `auth`, `midnight.session` e `MaintenanceERP`. O arquivo `routes/web.php` inclui os arquivos de domínio.

### Rota base

| Método | URI | Nome | Descrição |
|---|---|---|---|
| `GET` | `/` | `home` | Página inicial com todos os módulos |
| `GET` | `/modulo/{module}` | `module.show` | Página de detalhes do módulo |
| `GET` | `/modulo/{module}/item/{item}` | `module.item.development` | Tela de funcionalidade em desenvolvimento |
| `GET` | `/suporte/chat` | `suporte.chat` | Chat de suporte com tickets |

### Padrão `Route::resource`

| Método | URI | Action |
|---|---|---|
| `GET` | `/recurso` | `index` |
| `GET` | `/recurso/create` | `create` |
| `POST` | `/recurso` | `store` |
| `GET` | `/recurso/{id}` | `show` |
| `GET` | `/recurso/{id}/edit` | `edit` |
| `PUT / PATCH` | `/recurso/{id}` | `update` |
| `DELETE` | `/recurso/{id}` | `destroy` |

### Rotas por módulo

#### Administração (`routes/administracao.php`)

| Método | URI | Nome | Descrição |
|---|---|---|---|
| `GET` | `/configuracoes` | `configuration.index` | Página de configurações (9 seções) |
| `POST` | `/configuracoes` | `configuration.store` | Salvar configurações |

#### Perfil / Segurança (`routes/perfil.php`)

| Recurso / Rota | Nome(s) | Middleware | Descrição |
|---|---|---|---|
| `Route::resource` `users` | `users.*` | `admin` | Controle de usuários (sem `show`) |
| `Route::resource` `permissions` | `permissions.*` | `admin` | Gerenciamento de permissões |
| `GET /logs` | `logs.index` | `admin` | Listagem de logs de auditoria (Livewire) |
| `GET /notificacoes` | `notifications.index` | `auth` | Central de notificações (todos os usuários) |
| `GET /perfil` | `profile.index` | `auth` | Página de perfil |
| `PATCH /perfil/info` | `profile.updateInfo` | `auth` | Atualizar dados pessoais |
| `PATCH /perfil/senha` | `profile.updatePassword` | `auth` | Alterar senha |
| `POST /perfil/avatar` | `profile.uploadAvatar` | `auth` | Upload de avatar |
| `DELETE /perfil/avatar` | `profile.removeAvatar` | `auth` | Remover avatar |

#### Cadastro (`routes/cadastro.php`)

Todas as rotas abaixo usam componentes **Livewire** (GET):

- `clients.index`, `clients.create`, `clients.edit`
- `products.index`, `products.create`, `products.edit`
- `suppliers.index`, `suppliers.create`, `suppliers.edit`
- `employees.index`, `employees.create`, `employees.edit`
- `roles.index`, `roles.create`, `roles.edit`
- `vehicles.index`, `vehicles.create`, `vehicles.edit`
- `product-categories.index`, `product-categories.create`, `product-categories.edit`
- `unit-of-measures.index`, `unit-of-measures.create`, `unit-of-measures.edit`

Rotas extras de impressão:
- `GET /clients/print` → `clients.print`
- `GET /products/print` → `products.print`
- `GET /suppliers/print` → `suppliers.print`
- `GET /employees/print` → `employees.print`
- `GET /vehicles/print` → `vehicles.print`

Relacionamento produto × fornecedor:
- `GET /products/{product}/suppliers` → `products.suppliers.index`
- `POST /products/{product}/suppliers` → `products.suppliers.store`
- `DELETE /products/{product}/suppliers/{supplier}` → `products.suppliers.destroy`

#### Produção + Dashboard (`routes/producao.php`)

| Método | URI | Nome | Componente / Controller |
|---|---|---|---|
| `GET` | `/dashboard` | `dashboard.index` | `Livewire\Dashboard\Overview` |
| `GET` | `/dashboard/kpi` | `dashboard.kpi` | `Livewire\Dashboard\KpiReport` |
| `Route::resource` | `production_orders` | `production_orders.*` | `ProductionOrdersController` |

> O componente Livewire `App\Livewire\Producao\OrdemProducao` é utilizado internamente (não mapeado diretamente via rota resource).

#### Vendas (`routes/vendas.php`)

| Método | URI | Nome | Componente / Controller |
|---|---|---|---|
| `GET` | `/vendas/pedidos` | `vendas.pedidos` | `Livewire\Vendas\PedidosVenda` (via view) |
| `GET` | `/vendas/precificacao` | `vendas.precificacao` | `Livewire\Vendas\TabelasPrecificacao` |
| `GET` | `/salesReports/print` | `salesReports.print` | `SalesReportController@print` |
| `Route::resource` | `requests` | `requests.*` | `RequestsController` |
| `Route::resource` | `visits` | `visits.*` | `VisitsController` |
| `Route::resource` | `sales_report` | `sales_report.*` | `SalesReportController` |

#### Compras (`routes/compras.php`)

| Método | URI | Nome | Componente |
|---|---|---|---|
| `GET` | `/compras/solicitacoes` | `compras.solicitacoes` | `Livewire\Compras\SolicitacoesCompra` |
| `GET` | `/compras/pedidos` | `compras.pedidos` | `Livewire\Compras\PedidosCompra` |
| `GET` | `/compras/cotacoes` | `compras.cotacoes` | `Livewire\Compras\Cotacoes` |

#### Fiscal (`routes/fiscal.php`)

| Método | URI | Nome | Componente / Controller |
|---|---|---|---|
| `GET` | `/fiscal/notas-fiscais` | `fiscal.nfe.index` | `Livewire\Fiscal\NotaFiscal` |
| `GET` | `/fiscal/tipos-operacao` | `fiscal.tipo-operacao.index` | `Livewire\Fiscal\TipoOperacao\Index` |
| `GET` | `/fiscal/tipos-operacao/create` | `fiscal.tipo-operacao.create` | `Livewire\Fiscal\TipoOperacao\Form` |
| `GET` | `/fiscal/tipos-operacao/{operacao}/edit` | `fiscal.tipo-operacao.edit` | `Livewire\Fiscal\TipoOperacao\Form` |
| `GET` | `/fiscal/grupos-tributarios` | `fiscal.grupo-tributario.index` | `Livewire\Fiscal\GrupoTributario\Index` |
| `GET` | `/fiscal/grupos-tributarios/create` | `fiscal.grupo-tributario.create` | `Livewire\Fiscal\GrupoTributario\Form` |
| `GET` | `/fiscal/grupos-tributarios/{grupo}/edit` | `fiscal.grupo-tributario.edit` | `Livewire\Fiscal\GrupoTributario\Form` |
| `Route::resource` | `/fiscal/entrada` | `fiscal.entrance.*` | `EntranceController` |
| `Route::resource` | `/fiscal/saida` | `fiscal.exit.*` | `ExitController` |

#### Financeiro (`routes/financeiro.php`)

| Método | URI | Nome | Componente / Controller |
|---|---|---|---|
| `GET` | `/plans_of_accounts` | `plans_of_accounts.index` | `Livewire\Financeiro\PlanoContas` |
| `GET` | `/contas-bancarias` | `contas_bancarias.index` | `Livewire\Financeiro\ContaBancaria` |
| `GET` | `/accounts_payable` | `accounts_payable.index` | `Livewire\Financeiro\ContasPagar` |
| `GET` | `/accounts_receivable` | `accounts_receivable.index` | `Livewire\Financeiro\ContasReceber` |
| `GET` | `/cash_flow` | `cash_flow.index` | `Livewire\Financeiro\FluxoCaixa` |
| `GET` | `/financialReports/print` | `financialReports.print` | `FinancialReportsController` |
| `Route::resource` | `baccarat_accounts` | `baccarat_accounts.*` | `BaccaratAccountsController` |
| `Route::resource` | `financial_reports` | `financial_reports.*` | `FinancialReportsController` |

#### RH (`routes/rh.php`)

| Método | URI | Nome | Componente / Controller |
|---|---|---|---|
| `GET` | `/working_day` | `working_day.index` | `Livewire\Rh\JornadaTrabalho` |
| `GET` | `/payroll` | `payroll.index` | `Livewire\Rh\FolhaPagamento` |
| `GET` | `/holerite` | `holerite.index` | `Livewire\Rh\Holerite` |
| `GET` | `/holerite/{id}/imprimir` | `holerite.print` | `HoleriteController@print` |
| `GET` | `/stitch_beat` | `stitch_beat.index` | `Livewire\Rh\BatidaPonto` |
| `GET` | `/rhReports/print` | `rhReports.print` | `RhReportsController@print` |
| `Route::resource` | `employee_management` | `employee_management.*` | `EmployeeManagementController` |
| `Route::resource` | `rh_reports` | `rh_reports.*` | `RhReportsController` |

#### Logística (`routes/logistica.php`)

| Método | URI | Nome | Componente / Controller |
|---|---|---|---|
| `GET` | `/logistica/agendamento-entregas` | `scheduling_of_deliveries.index` | `Livewire\Logistica\AgendamentoEntregas` |
| `GET` | `/transportReport/print` | `transportReport.print` | `TransportReportController@print` |
| `GET` | `/romaneio/print` | `romaneio.print` | `RomaneioController@print` |
| `Route::resource` | `route_management` | `route_management.*` | `RouteManagementController` |
| `Route::resource` | `routing` | `routing.*` | `RoutingController` |
| `Route::resource` | `monitoring_of_deliveries` | `monitoring_of_deliveries.*` | `MonitoringOfDeliveriesController` |
| `Route::resource` | `driver_management` | `driver_management.*` | `DriverManagementController` |
| `Route::resource` | `romaneio` | `romaneio.*` | `RomaneioController` |
| `Route::resource` | `vehicle_tracking` | `vehicle_tracking.*` | `VehicleTrackingController` |
| `Route::resource` | `vehicle_maintenance` | `vehicle_maintenance.*` | `VehicleMaintenanceController` |
| `Route::resource` | `transport_report` | `transport_report.*` | `TransportReportController` |

#### Estoque (`routes/estoque.php`)

- `Route::resource` → `stock`

---

## API REST (`routes/api.php`)

Todos os endpoints ficam sob o prefixo `/api`.

### Proxy BrasilAPI (middleware `auth`)

| Método | URI | Descrição |
|---|---|---|
| `GET` | `/api/proxy/cnpj/{cnpj}` | Consulta dados de CNPJ via BrasilAPI |
| `GET` | `/api/proxy/cep/{cep}` | Consulta dados de CEP via BrasilAPI |

> Esses endpoints requerem sessão autenticada e fazem proxy para `https://brasilapi.com.br/api`.

### Produtos (middleware `api`)

| Método | URI | Descrição |
|---|---|---|
| `GET` | `/api/products` | Listar todos (inclui `suppliers`) |
| `POST` | `/api/products` | Criar produto |
| `GET` | `/api/products/{product}` | Detalhar (inclui `suppliers`) |
| `PUT / PATCH` | `/api/products/{product}` | Atualizar |
| `DELETE` | `/api/products/{product}` | Remover |

**Campos obrigatórios para criação:** `name`, `unit_of_measure`, `sale_price`, `stock`, `category`. Opcionais: `ean` (13 dígitos), `description`, `expiration_date`, `image` (jpg/jpeg/png/webp, máx. 2 MB).

### Fornecedores (middleware `api`)

| Método | URI | Descrição |
|---|---|---|
| `GET` | `/api/suppliers` | Listar todos |
| `POST` | `/api/suppliers` | Criar fornecedor |
| `GET` | `/api/suppliers/{supplier}` | Detalhar (inclui `products`) |
| `PUT / PATCH` | `/api/suppliers/{supplier}` | Atualizar |
| `DELETE` | `/api/suppliers/{supplier}` | Remover |

**Campos obrigatórios:** `name`, `social_name`, `taxNumber` (CNPJ, único), `email`, `phone_number`, `address_zip_code`, `address_street`, `address_number`, `address_district`, `address_city`, `address_state`.

### Clientes (middleware `api`)

| Método | URI | Descrição |
|---|---|---|
| `GET` | `/api/clients` | Listar todos |
| `POST` | `/api/clients` | Criar cliente |
| `GET` | `/api/clients/{client}` | Detalhar |
| `PUT / PATCH` | `/api/clients/{client}` | Atualizar |
| `DELETE` | `/api/clients/{client}` | Remover |

**Campos obrigatórios:** `name`, `taxNumber` (único), `email` (único), `phone_number`, `address`.

### Relacionamento Produto × Fornecedor (middleware `api`)

| Método | URI | Descrição |
|---|---|---|
| `GET` | `/api/products/{product}/suppliers` | Listar fornecedores do produto |
| `POST` | `/api/products/{product}/suppliers` | Vincular fornecedor ao produto |
| `DELETE` | `/api/products/{product}/suppliers/{supplier}` | Desvincular fornecedor |

### Movimentações de Estoque (middleware `api`)

| Método | URI | Descrição |
|---|---|---|
| `GET` | `/api/stock-movements` | Listar movimentações |
| `POST` | `/api/stock-movements` | Criar movimentação |
| `GET` | `/api/stock-movements/{stockMovement}` | Detalhar movimentação |
| `PUT / PATCH` | `/api/stock-movements/{stockMovement}` | Atualizar movimentação |
| `DELETE` | `/api/stock-movements/{stockMovement}` | Remover movimentação |

### Contas a Pagar (middleware `api`)

| Método | URI | Descrição |
|---|---|---|
| `GET` | `/api/accounts-payable` | Listar contas a pagar |
| `POST` | `/api/accounts-payable` | Criar conta a pagar |
| `GET` | `/api/accounts-payable/{accountPayable}` | Detalhar conta a pagar |
| `PUT / PATCH` | `/api/accounts-payable/{accountPayable}` | Atualizar conta a pagar |
| `DELETE` | `/api/accounts-payable/{accountPayable}` | Remover conta a pagar |

### Contas a Receber (middleware `api`)

| Método | URI | Descrição |
|---|---|---|
| `GET` | `/api/accounts-receivable` | Listar contas a receber |
| `POST` | `/api/accounts-receivable` | Criar conta a receber |
| `GET` | `/api/accounts-receivable/{accountReceivable}` | Detalhar conta a receber |
| `PUT / PATCH` | `/api/accounts-receivable/{accountReceivable}` | Atualizar conta a receber |
| `DELETE` | `/api/accounts-receivable/{accountReceivable}` | Remover conta a receber |

### Pedidos de Venda (middleware `api`)

Prefixo: `/api/sales-orders`

| Método | URI | Descrição |
|---|---|---|
| `GET` | `/api/sales-orders` | Listar pedidos |
| `POST` | `/api/sales-orders` | Criar pedido |
| `GET` | `/api/sales-orders/statistics` | Estatísticas de pedidos |
| `POST` | `/api/sales-orders/calculate` | Simulação/cálculo de pedido |
| `GET` | `/api/sales-orders/{order}` | Detalhar pedido |
| `PUT / PATCH` | `/api/sales-orders/{order}` | Atualizar pedido |
| `DELETE` | `/api/sales-orders/{order}` | Remover pedido |
| `POST` | `/api/sales-orders/{order}/approve` | Aprovar pedido |
| `POST` | `/api/sales-orders/{order}/cancel` | Cancelar pedido |
| `GET` | `/api/sales-orders/{order}/logs` | Histórico/logs do pedido |
| `GET` | `/api/sales-orders/{order}/attachments` | Listar anexos do pedido |

---

## Testes

Rodar toda a suíte:

```bash
composer test
```

Ou diretamente:

```bash
php artisan test
```

O projeto usa **Pest 3** com o plugin Laravel. Toda nova funcionalidade deve incluir testes Pest.

---

## Comandos Úteis

```bash
# Listar todas as rotas registradas
php artisan route:list

# Recriar banco e rodar seeders
php artisan migrate:fresh --seed

# Rodar todos os seeders
php artisan db:seed --no-interaction

# Rodar seeder de configurações
php artisan db:seed --class=SettingsSeeder --no-interaction

# Rodar seeder de logs de exemplo
php artisan db:seed --class=SystemLogSeeder --no-interaction

# Rodar seeders no Docker (container app)
docker compose exec app php artisan db:seed --no-interaction

# Limpar todos os caches (incluindo cache de configurações)
php artisan optimize:clear

# Build de assets para produção
npm run build

# Verificar status da aplicação
php artisan about
```

---

## Diretrizes de Desenvolvimento

- **Controllers**: slim — lógica de negócio em classes de serviço em `app/Services/`.
- **Livewire**: componentes full-page usam `#[Layout('layouts.app')]` e `#[Title('...')]`.
- **CSS**: edite apenas os arquivos parciais em `resources/css/`; rode `npm run build` após cada alteração em produção.
- **Enums**: sempre em `app/Enums/`, com cast no modelo e uso em toda a aplicação.
- **Testes**: obrigatórios para novas funcionalidades (Pest).
- **Migrations**: nunca encadear vários `make:migration` com `&&`; rodar um por vez para evitar timestamps idênticos.
- **Observers**: registrar via atributo PHP diretamente no modelo (`#[ObservedBy([...])]`), não no `AppServiceProvider`.
- **Flash messages** no Blade: usar diretiva `@session()`.
- **Seleções e checkboxes** no Blade: usar `@selected()` e `@checked()`.
- **Ambiente não-local**: `AppServiceProvider` força HTTPS automaticamente.
- **Comandos Laravel via Sail**: usar `./vendor/bin/sail artisan ...` se o projeto estiver rodando via Sail.
- **Novas rotas**: ao criar uma rota que deve renderizar normalmente (não "Em Breve"), adicionar o padrão `rotaNova.*` ao whitelist em `app/Http/Middleware/MaintenanceERP.php`.
- **Configurações do sistema**: usar `Setting::get()` / `Setting::set()` — nunca acessar a tabela `settings` diretamente fora do model.
- **Logs de auditoria**: usar `LogService::success()`, `::warning()` ou `::error()` — nunca escrever diretamente em `SystemLog`.
- **Integração BrasilAPI**: usar `BrasilAPIService` injetado via DI ou o endpoint proxy `/api/proxy/cnpj/{cnpj}` e `/api/proxy/cep/{cep}` no front-end.
- **Notificações**: usar o sistema nativo de notificações do Laravel (`Notifiable` trait no `User`); disparar via `$user->notify(new MinhaNotification())`. O `NotificationDropdown` atualiza automaticamente via `refreshNotifications`.
- **Avatar de usuário**: armazenado em `storage/app/public/avatars/`; acessar via `Storage::url($user->avatar)`. Usar `ProfileController::uploadAvatar()` e `removeAvatar()` para gerenciar.
- **Plano de Contas**: usar `PlanOfAccount` com hierarquia `parent_id`; contas sintéticas (`isSynthetic()`) não devem receber lançamentos diretos.
- **Contas Bancárias**: usar `BaccaratAccount`; vincular sempre a uma conta do Plano de Contas do tipo `ativo` e `is_selectable = true`. Transferências entre contas usam `increment`/`decrement` em transação.
- **Contas a Pagar/Receber**: usar `ContasPagarService` / `ContasReceberService` para todas as operações. O método `syncOverdueStatus()` é chamado automaticamente ao montar o componente Livewire.
- **Folha de Pagamento**: usar `PayrollService`; folhas em status `Draft` ainda aceitam itens; `Closed` e `Paid` são imutáveis. O método `recalculate()` no modelo `Payroll` recalcula totais a partir dos itens.
- **Jornada/Ponto**: usar `JornadaService`; registros de ponto são únicos por `employee_id + date` (upsert automático). Horas trabalhadas calculadas via accessor `worked_minutes` descontando a pausa.
- **Movimentações de Estoque**: ao criar/excluir um `StockMovement`, atualizar o campo `stock` do `Product` correspondente manualmente (não há observer automático ainda).
- **Categorias de Produto**: usar `ProductCategory` com `product_category_id` na tabela `products`. Slug gerado automaticamente a partir do nome.
- **Unidades de Medida**: usar `UnitOfMeasure` com `unit_of_measure_id` na tabela `products`. Abreviação sempre normalizada para maiúsculas.
- **Compras — Solicitações**: usar `SolicitacaoCompraStatus` e `SolicitacaoCompraPrioridade` via cast no modelo. A conversão para Pedido ou Cotação é feita via transação DB no componente Livewire.
- **Compras — Pedidos**: usar `PurchaseOrder::calculateTotals()` após inserir/alterar itens para recalcular `subtotal_amount`, `total_amount`, etc.
- **Cotações**: após selecionar o melhor preço por item, usar a conversão para `PurchaseOrder` passando os preços da `CotacaoResposta` vencedora.
- **Fiscal — NF-e**: usar `FiscalNote` com `FiscalNoteStatus`; ambiente `homologacao` por padrão (alterar em Configurações do Sistema → Regras Fiscais).
- **Fiscal — Grupos Tributários**: vincular `GrupoTributario` ao produto; os campos de ICMS/IPI/PIS/COFINS são usados na geração da NF-e.
- **Logística — Agendamento**: usar `SchedulingOfDeliveries` com `DeliveryTimeWindow`; reagendamento atualiza data/janela e muda status para `reagendado`.
- **Vendas — Pedidos**: usar `SalesOrderService` para todas as operações de criação/atualização; o log é gravado automaticamente pelo service. Nunca manipular `SalesOrderLog` diretamente.
- **Vendas — Precificação**: usar `PricingService::calculateFinalPrice()` para obter preço final. Vincular `PriceTable` ao pedido de venda via `price_table_id`.
- **RH — Batida de Ponto**: usar `PontoService`; o componente `BatidaPonto` identifica o funcionário pelo e-mail do usuário logado. Registros são únicos por `employee_id + date`.
- **RH — Holerite**: usar a rota `/holerite` para a página dedicada ou o modal dentro de `FolhaPagamento`. Impressão via `HoleriteController@print`.
- **Produção — OP**: usar `ProductionOrder` com `ProductionOrderProduct` (multi-produto) e `ProductionItem` (BOM); o método de progresso atualiza `produced_quantity` em cada `ProductionOrderProduct`.

---

## Licença

MIT
