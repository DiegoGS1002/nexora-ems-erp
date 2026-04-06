# Nexora EMS ERP

Sistema ERP modular desenvolvido em Laravel 12, cobrindo os principais domínios de uma empresa: cadastros, produção, vendas, compras, fiscal, financeiro, RH, logística e estoque. O front-end utiliza Blade + Tailwind CSS 4 + Bootstrap 5 com um design system próprio, e existe um painel administrativo via Filament 4.5.

---

## Visão Geral

A aplicação expõe uma página inicial (`/`) com todos os módulos disponíveis. Cada módulo possui uma página de detalhes com suas funcionalidades. As rotas ativas redirecionam para seus respectivos CRUDs; as funcionalidades ainda em construção exibem a tela **Em Breve** via middleware `MaintenanceERP`.

**Status:** desenvolvimento ativo.

**Última atualização da documentação:** 2026-04-06 (README revisado).

> Guia rápido para ambiente Replit: veja `replit.md`.

### Módulos

| Módulo | Status |
|---|---|
| Cadastro (Clientes, Fornecedores, Produtos, Funcionários, Funções, Veículos) | ✅ Ativo |
| Dashboard — Visão Geral (`/dashboard`) | 🔶 Implementado (dados de exemplo) |
| Dashboard — Indicadores KPI (`/dashboard/kpi`) | 🔶 Implementado (dados de exemplo) |
| Controle de Usuários (`/users`) | ✅ Ativo (CRUD + status + licença + módulos) |
| Configurações do Sistema (`/configuracoes`) | ✅ Ativo (9 seções) |
| Suporte (`/suporte/chat`) | ✅ Ativo (tickets + chat em tempo real) |
| Logs de Auditoria (`/logs`) | ✅ Ativo (somente admin) |
| Produção (Ordens de Produção) | 🚧 Em desenvolvimento |
| Estoque | 🚧 Em desenvolvimento |
| Vendas (Pedidos, CRM, Relatórios) | 🚧 Em desenvolvimento |
| Compras (Solicitações, Pedidos, Cotações) | 🚧 Em desenvolvimento |
| Fiscal (Entradas, Saídas) | 🚧 Em desenvolvimento |
| Financeiro (Plano de Contas, Contas, Caixa, DRE) | 🚧 Em desenvolvimento |
| RH (Jornada, Ponto, Folha, Relatórios) | 🚧 Em desenvolvimento |
| Transporte / Logística | 🚧 Em desenvolvimento |
| Perfil e Segurança (Permissões) | 🚧 Em desenvolvimento |
| Painel Administrativo Filament (`/admin`) | ✅ Ativo |

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
    CategoriaVeiculo.php
    CombustivelVeiculo.php
    EspecieVeiculo.php
    NaturezaProduto.php
    PrioridadeTicketSuporte.php
    StatusTicketSuporte.php
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
    Cadastro/
      Clientes/         # Index + Form (full-page)
      Fornecedores/     # Index + Form
      Funcionarios/     # Index + Form
      Funcoes/          # Index + Form (Funções/Cargos)
      Produtos/         # Index + Form
      Veiculos/         # Index + Form
    Dashboard/
      Overview.php      # Visão Geral do Dashboard
      KpiReport.php     # Indicadores KPI com drill-down
    Forms/
      NovoTicketForm.php  # Livewire Form Object para criação de tickets
    Suporte/
      Chat.php          # Chat de suporte com tickets em tempo real
  Models/               # Modelos Eloquent
    MensagemSuporte.php   # Mensagens de tickets de suporte
    Setting.php           # Configurações do sistema (key-value com cache)
    SystemLog.php         # Registros de auditoria do sistema
    TicketSuporte.php     # Tickets de suporte
  Providers/
    Filament/           # AdminPanelProvider (Filament)
  Services/             # Service classes com lógica de negócio
    BrasilAPIService.php        # Integração com BrasilAPI (CNPJ e CEP)
    Dashboard/
      DashboardMetricsService.php
    LogService.php              # Registro centralizado de logs de auditoria
    RoleService.php             # Lógica de negócio de funções/cargos
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
      cadastro/         # Views Livewire de cadastro
      dashboard/
        overview.blade.php    # View da Visão Geral
        kpi-report.blade.php  # View dos Indicadores KPI
      suporte/
        chat.blade.php  # Interface de chat de suporte (tickets)
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

### 5. Execução no Replit

O ambiente Replit deste projeto está configurado em `.replit` com:

- workflow `Project` iniciando `php artisan serve --host=0.0.0.0 --port=5000`
- mapeamento de porta `5000 -> 80` para acesso web da aplicação
- porta `3000` disponível para Vite/HMR quando necessário
- `deployment` com `build = npm run build`

Fluxo mínimo para abrir no Replit:

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan serve --host=0.0.0.0 --port=5000
```

> Para detalhes operacionais e troubleshooting no Replit, consulte `replit.md`.

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

## Configurações do Sistema

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

Todas as demais rotas retornam a view `system.desenvolvimento` ("Em Breve") até que o módulo esteja pronto.

> **Atenção:** ao implementar uma nova rota que deve estar acessível, adicione o padrão `rotaNova.*` no bloco `if` do método `handle()` em `MaintenanceERP.php`.

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

---

## Services

| Service | Descrição |
|---|---|
| `BrasilAPIService` | Consulta CNPJ (`consultarCnpj`) e CEP (`consultarCep`) via BrasilAPI |
| `LogService` | Registro centralizado de logs: `::success()`, `::warning()`, `::error()` |
| `SuporteService` | Criação de tickets, envio de mensagens, atualização de status e marcação de leitura |
| `RoleService` | Lógica de negócio de funções/cargos |
| `DashboardMetricsService` | Métricas e dados agregados para o dashboard |

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
| `Route::resource` | `/profile` | `profile.*` | Perfil do usuário |

#### Perfil / Segurança (`routes/perfil.php`) — middleware `admin`

| Recurso / Rota | Nome(s) | Descrição |
|---|---|---|
| `Route::resource` `users` | `users.*` | Controle de usuários (sem `show`) |
| `Route::resource` `permissions` | `permissions.*` | Gerenciamento de permissões |
| `GET /logs` | `logs.index` | Listagem de logs de auditoria (Livewire) |

#### Cadastro (`routes/cadastro.php`)

Todas as rotas abaixo usam componentes **Livewire** (GET):

- `clients.index`, `clients.create`, `clients.edit`
- `products.index`, `products.create`, `products.edit`
- `suppliers.index`, `suppliers.create`, `suppliers.edit`
- `employees.index`, `employees.create`, `employees.edit`
- `roles.index`, `roles.create`, `roles.edit`
- `vehicles.index`, `vehicles.create`, `vehicles.edit`

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

#### Vendas (`routes/vendas.php`)

- `GET /salesReports/print` → `salesReports.print`
- `Route::resource` → `requests`, `visits`, `sales_report`

#### Compras (`routes/compras.php`)

- `GET /compras/solicitacoes` → `compras.solicitacoes` *(Em Desenvolvimento)*
- `GET /compras/pedidos` → `compras.pedidos` *(Em Desenvolvimento)*
- `GET /compras/cotacoes` → `compras.cotacoes` *(Em Desenvolvimento)*

#### Fiscal (`routes/fiscal.php`)

- `Route::resource` → `entrance`, `exit`

#### Financeiro (`routes/financeiro.php`)

- `GET /financialReports/print` → `financialReports.print`
- `Route::resource` → `plans_of_accounts`, `baccarat_accounts`, `accounts_payable`, `accounts_receivable`, `cash_flow`, `financial_reports`

#### RH (`routes/rh.php`)

- `GET /rhReports/print` → `rhReports.print`
- `Route::resource` → `working_day`, `stitch_beat`, `payroll`, `employee_management`, `rh_reports`

#### Logística (`routes/logistica.php`)

- `GET /transportReport/print` → `transportReport.print`
- `GET /romaneio/print` → `romaneio.print`
- `Route::resource` → `route_management`, `routing`, `scheduling_of_deliveries`, `monitoring_of_deliveries`, `driver_management`, `romaneio`, `vehicle_tracking`, `vehicle_maintenance`, `transport_report`

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

---

## Licença

MIT
