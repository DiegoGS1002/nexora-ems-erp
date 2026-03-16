# Nexora EMS ERP

Sistema ERP em Laravel com múltiplos módulos de gestão empresarial (cadastros, produção, vendas, fiscal, financeiro, RH, logística e estoque), com versão web e empacotamento desktop via Electron.

## Visão Geral

O projeto evoluiu de um sistema acadêmico para uma base de ERP modular com recursos de:

- Cadastros centrais (clientes, fornecedores, produtos, funcionários e veículos)
- Operação comercial e produtiva
- Controles financeiros e fiscais
- RH e logística
- Painel administrativo e relatórios

Status atual: desenvolvimento ativo.

## Requisitos

### Backend/Web

- PHP 8.2+
- Composer 2+
- Node.js 20+ e npm 10+
- Banco de dados SQLite (padrão) ou MySQL/PostgreSQL (ajustando `.env`)

### Desktop (opcional)

- Node.js 20+
- Dependências do Electron Builder para empacotamento no Windows

## Stack e Dependências Principais

- Laravel 12
- Filament 4.5
- Blade
- Vite 7
- Tailwind CSS 4
- Bootstrap 5
- Pest (testes)
- Electron (aplicação desktop)

## Estrutura de Pastas

```text
app/                 # Models, controllers, middlewares, providers
config/              # Configurações Laravel
database/            # Migrations, seeders e factories
resources/           # Front-end (views, css, js)
routes/              # Rotas web e API separadas por domínio
tests/               # Testes Unit e Feature
nexora-desktop/      # Aplicativo desktop Electron
```

## Instalação e Execução (Web)

### 1) Clonar o projeto

```bash
git clone https://github.com/DiegoGS1002/nexora-ems-erp.git
cd nexora-ems-erp
```

### 2) Setup rápido (recomendado)

O projeto possui script de setup no Composer:

```bash
composer run setup
```

Esse comando executa:

- instalação de dependências PHP
- criação de `.env` (se não existir)
- geração de chave da aplicação
- migrations
- instalação de dependências front-end
- build de assets

### 3) Rodar ambiente de desenvolvimento

```bash
composer run dev
```

Esse comando sobe, em paralelo:

- servidor Laravel
- listener da fila
- Vite em modo desenvolvimento

### 4) Alternativa manual

```bash
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run dev
php artisan serve
```

## Configuração de Banco

Padrão do projeto no `.env.example`:

- `DB_CONNECTION=sqlite`

Para SQLite local, garanta que o arquivo exista:

```bash
type nul > database/database.sqlite
```

Depois rode:

```bash
php artisan migrate
```

Se quiser MySQL/PostgreSQL, ajuste as variáveis `DB_*` no `.env` antes de migrar.

## Execução Desktop (Electron)

O projeto possui subprojeto desktop em `nexora-desktop/`.

### Desenvolvimento desktop

```bash
cd nexora-desktop
npm install
npm start
```

### Build instalador desktop

```bash
cd nexora-desktop
npm run build
```

Observações:

- O desktop inicia um servidor Laravel interno com `artisan serve`.
- O app cria `.env` automaticamente (quando ausente) e gera `APP_KEY` se necessário.

## Testes

Rodar suíte de testes:

```bash
composer test
```

Ou diretamente:

```bash
php artisan test
```

## Detalhamento de Rotas

As rotas web estão organizadas por domínio em arquivos separados dentro de `routes/`, todos incluídos por `routes/web.php` sob middleware `MaintenanceERP`.

### Rota base

- `GET /` -> página inicial (`home`)

### Padrão de rotas `Route::resource`

Cada recurso abaixo expõe as rotas REST padrão Laravel:

- `GET /recurso` (index)
- `GET /recurso/create` (create)
- `POST /recurso` (store)
- `GET /recurso/{id}` (show)
- `GET /recurso/{id}/edit` (edit)
- `PUT/PATCH /recurso/{id}` (update)
- `DELETE /recurso/{id}` (destroy)

### Rotas Web por módulo

#### Administração

- Recursos: `profile`, `configuration`

#### Cadastro

- Recursos: `clients`, `products`, `suppliers`, `employees`, `role`, `vehicles`
- Extras:
	- `GET /clients/print`
	- `GET /products/print`
	- `GET /suppliers/print`
	- `GET /employees/print`
	- `GET /vehicles/print`
	- `GET /products/{product}/suppliers`
	- `POST /products/{product}/suppliers`
	- `DELETE /products/{product}/suppliers/{supplier}`

#### Produção

- Recursos: `dashboard`, `production_orders`

#### Vendas

- Recursos: `requests`, `visits`, `sales_report`
- Extra:
	- `GET /salesReports/print`

#### Fiscal

- Recursos: `entrance`, `exit`

#### Financeiro

- Recursos: `plans_of_accounts`, `baccarat_accounts`, `accounts_payable`, `accounts_receivable`, `cash_flow`, `financial_reports`
- Extra:
	- `GET /financialReports/print`

#### RH

- Recursos: `working_day`, `stitch_beat`, `payroll`, `employee_management`, `rh_reports`
- Extra:
	- `GET /rhReports/print`

#### Logística

- Recursos: `route_management`, `routing`, `scheduling_of_deliveries`, `monitoring_of_deliveries`, `driver_management`, `romaneio`, `vehicle_tracking`, `vehicle_maintenance`, `transport_report`
- Extras:
	- `GET /transportReport/print`
	- `GET /romaneio/print`

#### Estoque

- Recurso: `stock`

#### Perfil/Segurança

- Recursos: `users`, `permissions`, `logs`

### API (`routes/api.php`)

Endpoints REST para:

- Produtos: `/api/products`
- Fornecedores: `/api/suppliers`
- Clientes: `/api/clients`
- Relacionamento produto x fornecedor:
	- `GET /api/products/{product}/suppliers`
	- `POST /api/products/{product}/suppliers`
	- `DELETE /api/products/{product}/suppliers/{supplier}`

Cada entidade principal da API oferece operações: listar, criar, detalhar, atualizar (PUT/PATCH) e remover.

## Comandos Úteis

```bash
php artisan route:list
php artisan migrate:fresh --seed
php artisan optimize:clear
npm run build
```

## Outras Informações Relevantes

- Middleware global de manutenção ERP aplicado às rotas web.
- Projeto com separação por domínio via múltiplos arquivos de rota.
- Existe histórico de execução em Replit, mas o foco atual está no ambiente local e no empacotamento desktop.
- O projeto usa convenções Laravel e pode ser expandido com autenticação/autorização adicional conforme necessidade do negócio.

## Licença

Este projeto está licenciado sob MIT (ver dependências e cabeçalhos dos pacotes quando aplicável).
