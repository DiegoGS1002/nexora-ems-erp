# Nexora EMS ERP

Sistema ERP modular desenvolvido em Laravel 12, cobrindo os principais domínios de uma empresa: cadastros, produção, vendas, compras, fiscal, financeiro, RH, logística e estoque. O front-end utiliza Blade + Tailwind CSS 4 + Bootstrap 5, e existe um painel administrativo via Filament 4.5.

---

## Visão Geral

A aplicação expõe uma página inicial (`/`) com todos os módulos disponíveis. Cada módulo possui uma página de detalhes com suas funcionalidades. As rotas ativas redirecionam para seus respectivos CRUDs; as funcionalidades ainda em construção exibem a tela **Em Breve** via middleware `MaintenanceERP`.

**Status:** desenvolvimento ativo.

### Módulos

| Módulo | Status |
|---|---|
| Cadastro (Clientes, Fornecedores, Produtos, Funcionários, Funções, Veículos) | ✅ Ativo |
| Dashboard / KPIs | 🚧 Em desenvolvimento |
| Produção (Ordens, Ficha Técnica) | 🚧 Em desenvolvimento |
| Estoque | 🚧 Em desenvolvimento |
| Vendas (Pedidos, CRM, Relatórios) | 🚧 Em desenvolvimento |
| Compras (Solicitações, Pedidos, Cotações) | 🚧 Em desenvolvimento |
| Fiscal (Entradas, Saídas, Apuração) | 🚧 Em desenvolvimento |
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

| Camada | Tecnologia |
|---|---|
| Backend | Laravel 12 |
| Admin Panel | Filament 4.5 |
| Templating | Blade |
| Build | Vite 7 |
| CSS | Tailwind CSS 4 + Bootstrap 5 |
| Testes | Pest 3 + Pest Plugin Laravel |
| PHP (mínimo) | 8.2 |

---

## Estrutura de Pastas

```text
app/
  Enums/               # Enums de domínio (Pascal case, criados aqui)
  Http/
    Controllers/       # Slim controllers por domínio
      Api/             # Controllers da API REST
    Middleware/        # MaintenanceERP e demais middlewares
  Models/              # Modelos Eloquent
  Providers/
    Filament/          # AdminPanelProvider (Filament)
  Services/            # Service classes com lógica de negócio
config/                # Configurações Laravel
database/
  migrations/          # Migrações (nomeadas por módulo)
  seeders/
  factories/
resources/
  css/
  js/
  views/
    cadastro/          # Views CRUD: clientes, fornecedores, produtos, funcionários, funções, veículos
    administrativo/    # Views de usuários, permissões e logs
    layouts/           # Layout principal da aplicação
    modules/           # Página de detalhes do módulo (show.blade.php)
    partials/          # Partials reutilizáveis
    perfil/
    system/
      desenvolvimento.blade.php  # Tela "Em Breve"
routes/
  web.php              # Ponto de entrada — inclui todos os arquivos abaixo
  administracao.php
  cadastro.php
  compras.php
  estoque.php
  financeiro.php
  fiscal.php
  logistica.php
  perfil.php
  producao.php
  rh.php
  vendas.php
  api.php              # API REST
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
4. `php artisan migrate`
5. `npm install`
6. `npm run build`

### 3. Ambiente de desenvolvimento

```bash
composer run dev
```

Sobe em paralelo:
- Servidor Laravel (`php artisan serve`)
- Listener de filas (`php artisan queue:listen --tries=1`)
- Vite em modo `dev`

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

---

## Painel Administrativo (Filament)

Acesse em: `/admin`

- Login habilitado por padrão.
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
- `products.*`
- `clients.*`
- `vehicles.*`
- `employees.*`
- `roles.*`
- `suppliers.*`

Todas as demais rotas retornam a view `system.desenvolvimento` ("Em Breve") até que o módulo esteja pronto.

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
| `image` | string | Caminho no disco `public` |

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

### Padrão `Route::resource`

Cada recurso expõe:

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

- `Route::resource` → `clients`, `products`, `suppliers`, `employees`, `role`, `vehicles`
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

#### Produção (`routes/producao.php`)

- `Route::resource` → `dashboard`, `production_orders`

#### Vendas (`routes/vendas.php`)

- `Route::resource` → `requests`, `visits`, `sales_report`
- `GET /salesReports/print` → `salesReports.print`

#### Compras (`routes/compras.php`)

- `GET /compras/solicitacoes` → `compras.solicitacoes` *(Em Desenvolvimento)*
- `GET /compras/pedidos` → `compras.pedidos` *(Em Desenvolvimento)*
- `GET /compras/cotacoes` → `compras.cotacoes` *(Em Desenvolvimento)*

#### Fiscal (`routes/fiscal.php`)

- `Route::resource` → `entrance`, `exit`

#### Financeiro (`routes/financeiro.php`)

- `Route::resource` → `plans_of_accounts`, `baccarat_accounts`, `accounts_payable`, `accounts_receivable`, `cash_flow`, `financial_reports`
- `GET /financialReports/print` → `financialReports.print`

#### RH (`routes/rh.php`)

- `Route::resource` → `working_day`, `stitch_beat`, `payroll`, `employee_management`, `rh_reports`
- `GET /rhReports/print` → `rhReports.print`

#### Logística (`routes/logistica.php`)

- `Route::resource` → `route_management`, `routing`, `scheduling_of_deliveries`, `monitoring_of_deliveries`, `driver_management`, `romaneio`, `vehicle_tracking`, `vehicle_maintenance`, `transport_report`
- `GET /transportReport/print` → `transportReport.print`
- `GET /romaneio/print` → `romaneio.print`

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

- **Comandos Laravel**: usar `./vendor/bin/sail` se o projeto estiver rodando via Sail.
- **Controllers**: slim — lógica de negócio em classes de serviço em `app/Services/`.
- **Enums**: sempre em `app/Enums/`, com cast no modelo e uso em toda a aplicação.
- **Testes**: obrigatórios para novas funcionalidades (Pest).
- **Migrations**: nunca encadear vários `make:migration` com `&&`; rodar um por vez para evitar timestamps idênticos.
- **Observers**: registrar via atributo PHP diretamente no modelo (`#[ObservedBy([...])]`), não no `AppServiceProvider`.
- **Flash messages** no Blade: usar diretiva `@session()`.
- **Seleções e checkboxes** no Blade: usar `@selected()` e `@checked()`.
- **Ambiente não-local**: `AppServiceProvider` força HTTPS automaticamente.

---

## Licença

MIT
