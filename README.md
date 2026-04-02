# Nexora EMS ERP

Sistema ERP modular desenvolvido em Laravel 12, cobrindo os principais domínios de uma empresa: cadastros, produção, vendas, compras, fiscal, financeiro, RH, logística e estoque. O front-end utiliza Blade + Tailwind CSS 4 + Bootstrap 5 com um design system próprio, e existe um painel administrativo via Filament 4.5.

---

## Visão Geral

A aplicação expõe uma página inicial (`/`) com todos os módulos disponíveis. Cada módulo possui uma página de detalhes com suas funcionalidades. As rotas ativas redirecionam para seus respectivos CRUDs; as funcionalidades ainda em construção exibem a tela **Em Breve** via middleware `MaintenanceERP`.

**Status:** desenvolvimento ativo.

**Última atualização da documentação:** 2026-04-02.

### Módulos

| Módulo | Status |
|---|---|
| Cadastro (Clientes, Fornecedores, Produtos, Funcionários, Funções, Veículos) | ✅ Ativo |
| Dashboard — Visão Geral (`/dashboard`) | 🔶 Implementado (dados de exemplo) |
| Dashboard — Indicadores KPI (`/dashboard/kpi`) | 🔶 Implementado (dados de exemplo) |
| Produção (Ordens de Produção) | 🚧 Em desenvolvimento |
| Estoque | 🚧 Em desenvolvimento |
| Vendas (Pedidos, CRM, Relatórios) | 🚧 Em desenvolvimento |
| Compras (Solicitações, Pedidos, Cotações) | 🚧 Em desenvolvimento |
| Fiscal (Entradas, Saídas) | 🚧 Em desenvolvimento |
| Financeiro (Plano de Contas, Contas, Caixa, DRE) | 🚧 Em desenvolvimento |
| RH (Jornada, Ponto, Folha, Relatórios) | 🚧 Em desenvolvimento |
| Transporte / Logística | 🚧 Em desenvolvimento |
| Perfil e Segurança (Usuários, Permissões, Logs) | 🚧 Em desenvolvimento |
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
  Http/
    Controllers/        # Slim controllers por domínio
      Api/              # Controllers da API REST
        ClientApiController.php
        ProductApiController.php
        ProductSupplierApiController.php
        SupplierApiController.php
    Middleware/
      MaintenanceERP.php
  Livewire/             # Componentes Livewire
    Cadastro/
      Clientes/         # Index + Form (full-page)
      Fornecedores/     # Index + Form
      Funcionarios/     # Index + Form
      Produtos/         # Index + Form
    Dashboard/
      Overview.php      # Visão Geral do Dashboard
      KpiReport.php     # Indicadores KPI com drill-down
  Models/               # Modelos Eloquent
  Providers/
    Filament/           # AdminPanelProvider (Filament)
  Services/             # Service classes com lógica de negócio
config/
database/
  migrations/
  seeders/
  factories/
resources/
  css/
    app.css             # Ponto de entrada (importa os partials abaixo)
    _base.css           # Reset, tipografia, utilitários globais
    _layout.css         # Sidebar, topbar, wrapper, variáveis CSS (:root)
    _components.css     # Cards, KPIs, badges, botões, gráficos, atividades
    _tables.css         # Estilos de tabelas dos módulos
  js/
  views/
    cadastro/           # Views CRUD: clientes, fornecedores, produtos, funcionários, funções, veículos
    administrativo/     # Views de usuários, permissões e logs
    components/
      dashboard/        # Blade components reutilizáveis do dashboard
        kpi-card.blade.php
        chart-line.blade.php
        chart-donut.blade.php
        table.blade.php
    layouts/            # Layout principal da aplicação
    livewire/
      cadastro/         # Views Livewire de cadastro
      dashboard/
        overview.blade.php    # View da Visão Geral
        kpi-report.blade.php  # View dos Indicadores KPI
    modules/            # Página de detalhes do módulo (show.blade.php)
    partials/           # Partials reutilizáveis
    system/
      desenvolvimento.blade.php  # Tela "Em Breve"
routes/
  web.php               # Ponto de entrada — inclui todos os arquivos abaixo
  administracao.php
  cadastro.php
  compras.php
  estoque.php
  financeiro.php
  fiscal.php
  logistica.php
  perfil.php
  producao.php          # Inclui também as rotas do Dashboard
  rh.php
  vendas.php
  api.php               # API REST
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

### Componente `<x-dashboard.kpi-card>`

| Prop | Tipo | Padrão | Descrição |
|---|---|---|---|
| `title` | `string` | — | Rótulo do card (ex.: `"Faturamento"`) |
| `value` | `int\|float` | `0` | Valor numérico |
| `currency` | `bool` | `false` | Se `true`, formata como `R$ x.xxx,xx` |
| `trend` | `string\|null` | `null` | Variação percentual (ex.: `"+15,7%"`); negativo se iniciar com `-` |
| `icon` | `string\|null` | `null` | SVG inline |
| `iconBg` | `string` | `#EFF6FF` | Cor de fundo do ícone |
| `iconColor` | `string` | `#3B82F6` | Cor do stroke do ícone |

Cores padrão por indicador:

| Indicador | `iconBg` | `iconColor` |
|---|---|---|
| Faturamento | `#EFF6FF` | `#3B82F6` (azul) |
| Produtos | `#F5F3FF` | `#7C3AED` (roxo) |
| Pedidos | `#FFFBEB` | `#D97706` (âmbar) |
| Despesas | `#FFF1F2` | `#E11D48` (vermelho) |

---

## Design System

O CSS é gerido por arquivos parciais importados em `resources/css/app.css`:

| Arquivo | Conteúdo |
|---|---|
| `_base.css` | Reset, tipografia global, utilitários |
| `_layout.css` | Sidebar, topbar, variáveis CSS (`:root`), app wrapper |
| `_components.css` | Cards, KPIs, badges, botões, gráficos, atividades, comparativos |
| `_tables.css` | Estilos de tabelas dos módulos |

Classes principais do dashboard:

| Classe CSS | Descrição |
|---|---|
| `.nx-dashboard-kpis` | Grid `repeat(4, 1fr)` para os 4 cards KPI lado a lado |
| `.nx-kpi-card` | Card branco com borda, sombra suave e hover elevado |
| `.nx-kpi-card-trend` | Linha de variação com seta ▲/▼ e cor verde/vermelho |
| `.nx-dashboard-grid-charts` | Grid `2fr 1fr` para gráfico de linha + donut |
| `.nx-activity-row` | Grid `1fr 1fr` para os dois painéis de atividade |
| `.nx-desempenho-stats` | Grid de 3 colunas para o bloco de desempenho |

> Após editar qualquer arquivo em `resources/css/`, rode `npm run build` para gerar o bundle de produção.

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

## Middleware `MaintenanceERP`

Aplicado globalmente a todas as rotas web. Rotas **liberadas** (renderizam normalmente):

- `home` (página inicial `/`)
- `module.show`
- `module.item.development`
- `products.*`
- `clients.*`
- `vehicles.*`
- `employees.*`
- `roles.*`
- `suppliers.*`

Todas as demais rotas retornam a view `system.desenvolvimento` ("Em Breve") até que o módulo esteja pronto.

> **Atenção:** o recurso de funções está registrado como `role.*` em `routes/cadastro.php`, mas o middleware verifica `roles.*`. Mantenha consistência ao adicionar novas permissões ao grupo liberado.

---

## Modelos Principais

### `Product`

| Campo | Tipo | Observações |
|---|---|---|
| `id` | UUID | Gerado automaticamente via `booted()` |
| `name` | string | Único |
| `ean` | string (13) | Código de barras, único |
| `description` | string | |
| `unit_of_measure` | string | |
| `sale_price` | decimal:2 | |
| `stock` | integer | |
| `expiration_date` | date | Nullable |
| `category` | string | |
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

| Campo | Tipo |
|---|---|
| `id` | UUID |
| `name` | string |
| `social_name` | string (nullable) |
| `taxNumber` | string (CPF/CNPJ) |
| `email` | string |
| `phone_number` | string |
| `address` | string |

### `Employees`

| Campo | Tipo |
|---|---|
| `id` | UUID |
| `name` | string |
| `identification_number` | string |
| `role` | string |
| `email` | string |
| `phone_number` | string |
| `address` | string |

### `Vehicle`

| Campo | Tipo |
|---|---|
| `name` | string |
| `model` | string |
| `brand` | string |
| `plate` | string |
| `renavam` | string |
| `chassis` | string |
| `fuel_type` | string |
| `year` | integer |
| `color` | string |

### `Role`

| Campo | Tipo |
|---|---|
| `name` | string |
| `description` | string |

---

## Detalhamento de Rotas

Todas as rotas web estão sob o middleware `MaintenanceERP`. O arquivo `routes/web.php` inclui os arquivos de domínio.

### Rota base

| Método | URI | Nome | Descrição |
|---|---|---|---|
| `GET` | `/` | `home` | Página inicial com todos os módulos |
| `GET` | `/modulo/{module}` | `module.show` | Página de detalhes do módulo |
| `GET` | `/modulo/{module}/item/{item}` | `module.item.development` | Tela de funcionalidade em desenvolvimento |

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

- `Route::resource` → `profile`, `configuration`

#### Cadastro (`routes/cadastro.php`)

- Rotas Livewire (GET):
  - `clients.index`, `clients.create`, `clients.edit`
  - `products.index`, `products.create`, `products.edit`
  - `suppliers.index`, `suppliers.create`, `suppliers.edit`
  - `employees.index`, `employees.create`, `employees.edit`
- `Route::resource` → `role`, `vehicles`
- Rotas extras de impressão:
  - `GET /clients/print` → `clients.print`
  - `GET /products/print` → `products.print`
  - `GET /suppliers/print` → `suppliers.print`
  - `GET /employees/print` → `employees.print`
  - `GET /vehicles/print` → `vehicles.print`
- Relacionamento produto × fornecedor:
  - `GET /products/{product}/suppliers` → `products.suppliers.index`
  - `POST /products/{product}/suppliers` → `products.suppliers.store`
  - `DELETE /products/{product}/suppliers/{supplier}` → `products.suppliers.destroy`

#### Produção + Dashboard (`routes/producao.php`)

> As rotas do Dashboard estão agrupadas neste arquivo até a criação de `routes/dashboard.php`.

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

#### Perfil / Segurança (`routes/perfil.php`)

- `Route::resource` → `users`, `permissions`, `logs`

---

## API REST (`routes/api.php`)

Todos os endpoints ficam sob o prefixo `/api` com middleware `api`.

### Produtos

| Método | URI | Descrição |
|---|---|---|
| `GET` | `/api/products` | Listar todos (inclui `suppliers`) |
| `POST` | `/api/products` | Criar produto |
| `GET` | `/api/products/{product}` | Detalhar (inclui `suppliers`) |
| `PUT / PATCH` | `/api/products/{product}` | Atualizar |
| `DELETE` | `/api/products/{product}` | Remover |

**Campos obrigatórios para criação:** `name`, `ean` (13 dígitos, único), `description`, `unit_of_measure`, `sale_price`, `stock`, `category`. Opcionais: `expiration_date`, `image` (jpg/jpeg/png/webp, máx. 2 MB).

### Fornecedores

| Método | URI | Descrição |
|---|---|---|
| `GET` | `/api/suppliers` | Listar todos |
| `POST` | `/api/suppliers` | Criar fornecedor |
| `GET` | `/api/suppliers/{supplier}` | Detalhar (inclui `products`) |
| `PUT / PATCH` | `/api/suppliers/{supplier}` | Atualizar |
| `DELETE` | `/api/suppliers/{supplier}` | Remover |

**Campos obrigatórios:** `name`, `social_name`, `taxNumber` (CNPJ, único), `email`, `phone_number`, `address_zip_code`, `address_street`, `address_number`, `address_district`, `address_city`, `address_state`.

### Clientes

| Método | URI | Descrição |
|---|---|---|
| `GET` | `/api/clients` | Listar todos |
| `POST` | `/api/clients` | Criar cliente |
| `GET` | `/api/clients/{client}` | Detalhar |
| `PUT / PATCH` | `/api/clients/{client}` | Atualizar |
| `DELETE` | `/api/clients/{client}` | Remover |

**Campos obrigatórios:** `name`, `taxNumber` (único), `email` (único), `phone_number`, `address`.

### Relacionamento Produto × Fornecedor

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

# Limpar todos os caches
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

---

## Licença

MIT
