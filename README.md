# Nexora EMS ERP

ERP web modular e multiempresa para centralizar operações administrativas, comerciais e fiscais. O sistema reúne cadastros, vendas, compras, estoque, produção, logística, financeiro, RH, suporte e emissão de NF-e em uma aplicação Laravel com interfaces reativas em Livewire.

> Projeto em desenvolvimento ativo. Antes de usar em produção, revise credenciais, integrações externas, filas, armazenamento e políticas de acesso.

## Principais recursos

- Cadastros de clientes, fornecedores, produtos, funcionários, funções, veículos, categorias e unidades de medida.
- Pedidos de venda, tabelas de preços, solicitações, cotações e pedidos de compra.
- Estoque, ordens de produção, agendamento e acompanhamento logístico.
- Plano de contas, contas bancárias, contas a pagar e receber e fluxo de caixa.
- Jornada, ponto, folha de pagamento e holerites.
- NF-e, tipos de operação, grupos tributários, DANFE e diagnóstico de integração com a SEFAZ.
- Operação fiscal completa de saída e entrada: XML 4.00/NT 2025.002, Distribuição DF-e, manifestação, conferência de compras, escrituração e reversão idempotente. Consulte [`Documentation/18-ajustes-fiscais-nfe-2026.md`](Documentation/18-ajustes-fiscais-nfe-2026.md).
- Dashboard, empresas, usuários, permissões, notificações, auditoria e suporte, com isolamento de dados por empresa.
- Assistente de IA com OpenAI, Gemini, RAG e ferramentas especializadas por domínio.
- API autenticada para clientes, fornecedores, produtos, estoque, financeiro e pedidos de venda.
- Painel administrativo com Filament.

## Tecnologias

| Camada | Tecnologia |
| --- | --- |
| Backend | PHP 8.4 e Laravel 13 |
| Interface reativa | Livewire 4 e Blade |
| Painel administrativo | Filament 5 |
| Estilos | Tailwind CSS 4 e Bootstrap 5 |
| Assets | Vite 7 |
| Banco de dados | MySQL por padrão; testes com SQLite em memória |
| Testes | Pest 4 |
| PDFs e documentos fiscais | DomPDF, SPED-DA e SPED-NFe |
| IA | OpenAI PHP e Google Gemini PHP |

As restrições completas de versões estão em [`composer.json`](composer.json) e [`package.json`](package.json). As versões efetivamente instaladas ficam registradas nos respectivos arquivos de lock.

## Requisitos

- PHP 8.4 ou superior, com as extensões exigidas pelo Laravel e pelos pacotes fiscais.
- Composer 2.
- Node.js 20 ou superior e npm.
- MySQL para o ambiente padrão. SQLite também é necessário para executar os testes conforme a configuração atual.

## Instalação local

Clone o repositório e entre no diretório do projeto:

```bash
git clone https://github.com/DiegoGS1002/nexora-ems-erp.git
cd nexora-ems-erp
```

Instale as dependências PHP e prepare o ambiente:

```bash
composer install
cp .env.example .env
php artisan key:generate
```

Crie o banco de dados e ajuste, no mínimo, estas variáveis no `.env`:

```dotenv
APP_NAME="Nexora EMS ERP"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nexora
DB_USERNAME=root
DB_PASSWORD=

# E-mails com acesso administrativo global, separados por vírgula
PLATFORM_ADMIN_EMAILS=admin@nexora.local
```

Em seguida, prepare banco e assets:

```bash
php artisan migrate
npm install
npm run build
```

Para popular um ambiente local com dados de demonstração, use:

```bash
php artisan db:seed
```

Os seeders criam uma conta administrativa de desenvolvimento:

- E-mail: `admin@nexora.local`
- Senha: `admin12345`

Altere essa senha imediatamente em qualquer ambiente compartilhado e não execute seeders de demonstração em produção sem revisar seu conteúdo. Os seeders também criam empresas e usuários vinculados para testar o isolamento multiempresa.

### Setup automatizado

O Composer também oferece um atalho que instala dependências, cria o `.env` quando necessário, gera a chave, executa as migrations e compila os assets:

```bash
composer run setup
```

Esse comando pressupõe que o banco indicado no `.env` já existe e está acessível.

## Executando o projeto

Para iniciar servidor Laravel, listener da fila e Vite com HMR em um único terminal:

```bash
composer run dev
```

A aplicação fica disponível, por padrão, em `http://127.0.0.1:8000`. Entre com a conta criada pelos seeders. O painel Filament usa a rota `/admin`.

Se preferir processos separados:

```bash
php artisan serve
php artisan queue:listen --tries=1
npm run dev
```

Execute cada comando em seu próprio terminal.

O projeto usa filas com o driver `database`. Mantenha um worker ativo para processar tarefas assíncronas, especialmente transmissões e conciliações fiscais.

## Multiempresa e acesso

As páginas web e a API exigem uma sessão autenticada. O middleware de tenancy resolve a empresa a partir do usuário e bloqueia contas sem empresa, empresas inativas e acessos fora do contexto permitido. Os e-mails informados em `PLATFORM_ADMIN_EMAILS` atuam como administradores da plataforma e não ficam presos a um único tenant.

Ao criar usuários comuns, vincule-os a uma empresa. Em ambientes reais, substitua o e-mail administrativo padrão e mantenha a lista de administradores globais tão restrita quanto possível.

## Configurações opcionais

O arquivo [`.env.example`](.env.example) lista todas as opções disponíveis. As integrações abaixo só são necessárias para os recursos correspondentes:

```dotenv
# Mapas e rotas
GOOGLE_MAPS_API_KEY=

# Assistente com OpenAI
OPENAI_API_KEY=
OPENAI_ORGANIZATION=
OPENAI_REQUEST_TIMEOUT=30

# Assistente com Gemini
GEMINI_API_KEY=
GEMINI_MODEL=gemini-2.0-flash-exp
GEMINI_TEMPERATURE=0.7
GEMINI_MAX_TOKENS=2048
GEMINI_REQUEST_TIMEOUT=60
```

Sem chaves de IA válidas, o assistente utiliza o fluxo de fallback implementado pela aplicação. Não versione chaves, certificados, senhas ou arquivos `.env` preenchidos.

## Banco de dados e seeders

Comandos mais usados:

```bash
php artisan migrate
php artisan migrate:status
php artisan db:seed
php artisan migrate:fresh --seed
```

`migrate:fresh` remove todas as tabelas do banco configurado. Use-o somente em ambientes descartáveis.

O `DatabaseSeeder` organiza cargas de base, cadastros, RH, fiscal, compras, vendas, estoque, produção, financeiro e logs. Seeders específicos podem ser executados assim:

```bash
php artisan db:seed --class=SettingsSeeder
php artisan db:seed --class=RagKnowledgeSeeder
php artisan db:seed --class=SefazDiagnosticsSeeder
```

Para carregar a base de conhecimento do assistente, inclusive sem uma chave da OpenAI, use:

```bash
php artisan ai:index-knowledge
php artisan rag:search "Como consultar o estoque?"
```

Sem `OPENAI_API_KEY`, a indexação mantém a busca lexical. Depois de configurar a chave, gere os embeddings dos documentos existentes com:

```bash
php artisan ai:vectorize
```

O comando `php artisan rag:seed` é uma alternativa para popular FAQs, regras e diagnósticos SEFAZ diretamente pelo serviço RAG, mas exige `OPENAI_API_KEY` e pode fazer várias chamadas à API.

## API

Os endpoints ficam sob `/api` e reutilizam a autenticação por sessão, proteção CSRF e o contexto multiempresa da interface web. Entre os recursos disponíveis estão clientes, fornecedores, produtos, vínculos produto-fornecedor, movimentações de estoque, contas a pagar e receber e pedidos de venda.

Esta API não usa tokens bearer na configuração atual. Clientes externos precisam estabelecer uma sessão válida e enviar o token CSRF; consulte [a documentação da API](Documentation/38-api-rest.md) e confirme os contratos atuais em [`routes/api.php`](routes/api.php).

## Testes e qualidade

A suíte usa Pest e um banco SQLite em memória:

```bash
composer test
```

Também é possível executar um arquivo, grupo ou teste específico:

```bash
php artisan test tests/Feature
php artisan test --filter=SupplierFormTest
```

Formate alterações PHP antes de enviá-las:

```bash
vendor/bin/pint
```

Para gerar os assets de produção:

```bash
npm run build
```

## Estrutura do projeto

```text
app/
├── Ai/                 # Agentes, RAG, playbooks e ferramentas de IA
├── Http/               # Controllers, requests e middlewares
├── Livewire/           # Telas e componentes reativos por domínio
├── Models/             # Entidades Eloquent
├── Services/           # Regras e orquestração de casos de uso
└── Support/            # Utilitários compartilhados
database/
├── factories/
├── migrations/
└── seeders/
resources/
├── css/
├── js/
└── views/
routes/                 # Rotas separadas por domínio de negócio
tests/
├── Feature/
└── Unit/
Documentation/         # Guias técnicos e documentação dos módulos
```

As rotas autenticadas são agrupadas em [`routes/web.php`](routes/web.php) e carregam arquivos específicos como `cadastro.php`, `financeiro.php`, `fiscal.php`, `rh.php` e `vendas.php`. A API está definida em [`routes/api.php`](routes/api.php).

## Documentação

Use os documentos abaixo como ponto de partida:

- [Guia de início rápido](Documentation/00-guia-inicio-rapido.md)
- [Índice da documentação](Documentation/README.md)
- [Arquitetura do sistema](Documentation/37-arquitetura-sistema.md)
- [API REST](Documentation/38-api-rest.md)
- [Middlewares](Documentation/39-middlewares.md)
- [Services e helpers](Documentation/40-services-helpers.md)
- [Integração SEFAZ](Documentation/41-integracao-sefaz.md)
- [Assistente de IA e RAG](Documentation/36-modulo-assistente-ia.md)

Os demais arquivos em `Documentation/` detalham cada domínio funcional. Como o sistema está em evolução, confirme exemplos de rotas, classes e versões na implementação atual.

## Comandos úteis

```bash
# Desenvolvimento
composer run dev

# Filas
php artisan queue:work

# Limpar caches da aplicação
php artisan optimize:clear

# Verificar configuração fiscal e mapas
php artisan fiscal:verify
php artisan maps:verify

# Testar integrações e resolução de módulos
php artisan nfe:test-connection
php artisan ai:test financeiro
php artisan test:module-resolution
```

## Contribuição

1. Mantenha a lógica de domínio nos diretórios correspondentes em `app/`.
2. Agrupe componentes, views e rotas pelo mesmo domínio de negócio.
3. Adicione testes de regressão para correções e cubra validação, autorização e falhas relevantes.
4. Execute `vendor/bin/pint`, `composer test` e `npm run build` conforme o tipo de alteração.
5. Use commits curtos e imperativos, preferencialmente no padrão Conventional Commits, como `feat:`, `fix:` e `docs:`.

Ao abrir um pull request, descreva o problema e a solução, módulos afetados, comandos de verificação e eventuais migrations, seeders, filas ou novas variáveis de ambiente. Inclua capturas de tela quando houver mudanças visuais.
