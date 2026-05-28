# Módulo de Clientes

## 1. Visão Geral

O módulo de Clientes é responsável pelo cadastro e gestão de pessoas físicas e jurídicas que adquirem produtos ou serviços da empresa. Este módulo é fundamental para o sistema, pois é utilizado por diversos outros módulos como Vendas, Financeiro e Fiscal.

### Funcionalidades Principais
- Cadastro completo de clientes (PF e PJ)
- Gestão de limite de crédito
- Controle de situação (ativo, inativo, inadimplente)
- Vínculo com tabela de preços
- Definição de desconto limite
- Histórico de pedidos de venda

---

## 2. Regras de Negócio

### 2.1 Tipos de Pessoa
- **Pessoa Física**: Requer CPF válido
- **Pessoa Jurídica**: Requer CNPJ válido e pode incluir Inscrição Estadual

### 2.2 Situação do Cliente
- **Ativo**: Cliente regular, pode realizar compras normalmente
- **Inativo**: Cliente bloqueado, não pode realizar novas compras
- **Inadimplente**: Cliente com pendências, requer aprovação gerencial para novos pedidos

### 2.3 Limite de Crédito
- Se não definido, o cliente possui crédito ilimitado
- Se definido, o sistema verifica o crédito utilizado (soma de pedidos aprovados e faturados)
- Bloqueia automaticamente pedidos que ultrapassem o limite disponível
- Necessária aprovação gerencial para exceder o limite

### 2.4 Tabela de Preços
- Cliente pode ter uma tabela de preços padrão vinculada
- Se não vinculado, utiliza o preço base do produto
- Tabela de preços pode ser sobrescrita no pedido de venda

### 2.5 Desconto Limite
- Define o percentual máximo de desconto permitido para o cliente
- Aplicável no momento da venda
- Pode ser sobrescrito com aprovação gerencial

---

## 3. Estrutura Técnica

### 3.1 Model: `App\Models\Client`

**Caminho**: `app/Models/Client.php`

#### Propriedades
```php
protected $fillable = [
    'tipo_pessoa',           // Enum: TipoPessoa (fisica, juridica)
    'name',                  // Razão social ou nome completo
    'social_name',           // Nome fantasia (PJ) ou nome de tratamento (PF)
    'taxNumber',             // CPF ou CNPJ
    'inscricao_estadual',    // IE (apenas PJ)
    'email',
    'phone_number',
    'address',               // Endereço completo (legado)
    'address_zip_code',
    'address_street',
    'address_number',
    'address_complement',
    'address_district',
    'address_city',
    'address_state',
    'credit_limit',          // Limite de crédito em decimal(10,2)
    'payment_condition_default', // Condição de pagamento padrão
    'situation',             // active, inactive, defaulter
    'price_table_id',        // FK para PriceTable
    'discount_limit',        // Percentual máximo de desconto
];
```

#### Casts
```php
protected $casts = [
    'tipo_pessoa'    => TipoPessoa::class,
    'credit_limit'   => 'decimal:2',
    'discount_limit' => 'decimal:2',
];
```

#### Métodos Principais
- `getFullAddress()`: Retorna endereço completo formatado
- `isActive()`: Verifica se cliente está ativo
- `isDefaulter()`: Verifica se cliente está inadimplente
- `hasAvailableCredit(?float $amount = 0)`: Verifica limite de crédito disponível

#### Relacionamentos
- `priceTable()`: BelongsTo PriceTable
- `salesOrders()`: HasMany SalesOrder

### 3.2 Enum: `App\Enums\TipoPessoa`

**Caminho**: `app/Enums/TipoPessoa.php`

```php
enum TipoPessoa: string
{
    case Fisica = 'fisica';
    case Juridica = 'juridica';
}
```

### 3.3 Livewire Components

#### Index: `App\Livewire\Cadastro\Clientes\Index`
**Caminho**: `app/Livewire/Cadastro/Clientes/Index.php`

**Funcionalidades**:
- Listagem paginada de clientes
- Busca por nome, CPF/CNPJ, telefone ou e-mail
- Filtro por situação (ativo, inativo, inadimplente)
- Ordenação por colunas
- Ações: visualizar, editar, excluir
- Suporte a paginação configurável (10, 20, 50, 100, 200 itens)

#### Form: `App\Livewire\Cadastro\Clientes\Form`
**Caminho**: `app/Livewire/Cadastro/Clientes/Form.php`

**Funcionalidades**:
- Formulário de criação/edição
- Abas: Dados Gerais, Endereço, Financeiro
- Validação de CPF/CNPJ
- Busca de endereço por CEP (via BrasilAPI)
- Integração com consulta de CNPJ (via BrasilAPI)
- Upload de documentos (opcional)

### 3.4 Controller: `App\Http\Controllers\ClientController`

**Caminho**: `app/Http/Controllers/ClientController.php`

#### Métodos
- `print()`: Gera relatório em PDF de clientes

---

## 4. Rotas

### 4.1 Rotas Web

**Arquivo**: `routes/cadastro.php`

| Método | URI | Nome | Descrição |
|--------|-----|------|-----------|
| GET | `/clients` | `clients.index` | Listagem de clientes |
| GET | `/clients/create` | `clients.create` | Formulário de criação |
| GET | `/clients/{client}/edit` | `clients.edit` | Formulário de edição |
| GET | `/clients/print` | `clients.print` | Relatório PDF |

### 4.2 Rotas API

**Arquivo**: `routes/api.php`

| Método | URI | Descrição |
|--------|-----|-----------|
| GET | `/api/clients` | Lista clientes |
| POST | `/api/clients` | Cria cliente |
| GET | `/api/clients/{id}` | Detalha cliente |
| PUT/PATCH | `/api/clients/{id}` | Atualiza cliente |
| DELETE | `/api/clients/{id}` | Remove cliente |

---

## 5. Fluxos de Dados

### 5.1 Criação de Cliente

```
1. Usuário acessa /clients/create
2. Preenche formulário (tipo pessoa, dados, endereço)
3. Sistema valida CPF/CNPJ
4. Sistema pode buscar dados por CNPJ (Receita Federal via BrasilAPI)
5. Sistema salva cliente com UUID gerado
6. Registra log de auditoria
7. Redireciona para listagem
```

### 5.2 Validação de Crédito em Pedido de Venda

```
1. Pedido de venda é criado
2. Sistema busca cliente
3. Verifica situação do cliente
   - Se inativo: bloqueia
   - Se inadimplente: requer aprovação gerencial
4. Calcula crédito utilizado (pedidos aprovados + faturados)
5. Compara com limite de crédito
6. Se exceder: bloqueia e exige aprovação
7. Se dentro do limite: permite criação
```

### 5.3 Busca de Endereço por CEP

```
1. Usuário digita CEP no formulário
2. Sistema remove formatação (hífen)
3. Chama BrasilAPIService::consultarCep($cep)
4. Se sucesso: preenche automaticamente rua, bairro, cidade, estado
5. Se erro: exibe mensagem e mantém campos em branco
```

---

## 6. Integrações com Outros Módulos

### 6.1 Módulo de Vendas
- **Pedidos de Venda**: Todo pedido requer um cliente
- **Validação de Crédito**: Verifica limite antes de criar pedido
- **Tabela de Preços**: Define preço dos produtos no pedido
- **Condição de Pagamento**: Define forma de pagamento padrão

### 6.2 Módulo Financeiro
- **Contas a Receber**: Vincula parcelas ao cliente
- **Limite de Crédito**: Controla exposição financeira
- **Inadimplência**: Marca cliente como inadimplente automaticamente

### 6.3 Módulo Fiscal
- **NF-e**: Dados do cliente (CPF/CNPJ, IE, endereço) são obrigatórios
- **Validação SEFAZ**: Campos devem estar corretos

---

## 7. Traits e Recursos Auxiliares

### 7.1 Trait Loggable
O model Client utiliza a trait `Loggable` para registrar automaticamente:
- Criação de cliente
- Atualização de dados
- Exclusão

Logs registrados em `system_logs` com módulo "Cadastros" e nome "Cliente".

### 7.2 UUIDs
- Clientes utilizam UUID como chave primária
- Gerado automaticamente no evento `creating`
- Melhor para distribuição e segurança

---

## 8. Validações

### 8.1 Validações de Formulário
```php
'tipo_pessoa' => 'required|in:fisica,juridica',
'name' => 'required|string|max:255',
'taxNumber' => 'required|string|max:18|unique:clients,taxNumber',
'email' => 'nullable|email|max:255',
'phone_number' => 'nullable|string|max:20',
'credit_limit' => 'nullable|numeric|min:0',
'discount_limit' => 'nullable|numeric|min:0|max:100',
```

### 8.2 Validações de CPF/CNPJ
- Validação de dígitos verificadores
- Formato: CPF (000.000.000-00), CNPJ (00.000.000/0000-00)
- Verificação de números repetidos

---

## 9. Exemplos de Código

### 9.1 Criar Cliente via Service
```php
use App\Models\Client;
use App\Enums\TipoPessoa;

$client = Client::create([
    'tipo_pessoa' => TipoPessoa::Juridica,
    'name' => 'Empresa XYZ Ltda',
    'social_name' => 'XYZ',
    'taxNumber' => '12.345.678/0001-99',
    'inscricao_estadual' => '123456789',
    'email' => 'contato@xyz.com.br',
    'phone_number' => '(11) 98765-4321',
    'credit_limit' => 50000.00,
    'situation' => 'active',
]);
```

### 9.2 Verificar Crédito Disponível
```php
$client = Client::find($clientId);

if (!$client->hasAvailableCredit($valorPedido)) {
    throw new Exception('Cliente sem crédito disponível');
}
```

### 9.3 Buscar Cliente Ativo
```php
$clientes = Client::where('situation', 'active')
    ->orderBy('name')
    ->get();
```

---

## 10. Performance e Otimizações

### 10.1 Índices de Banco de Dados
- `taxNumber`: Índice único para busca rápida por CPF/CNPJ
- `email`: Índice para busca por e-mail
- `situation`: Índice para filtros de situação
- `created_at`: Índice para ordenação cronológica

### 10.2 Eager Loading
Ao carregar clientes com relacionamentos:
```php
Client::with(['priceTable', 'salesOrders'])->get();
```

---

## 11. Testes

### 11.1 Testes de Feature
- `ClientCreationTest`: Criação de cliente
- `ClientValidationTest`: Validações de CPF/CNPJ
- `ClientCreditTest`: Verificação de limite de crédito

### 11.2 Exemplo de Teste
```php
test('cliente inadimplente requer aprovação gerencial', function () {
    $client = Client::factory()->create(['situation' => 'defaulter']);
    
    expect($client->isDefaulter())->toBeTrue();
});
```

---

## 12. Manutenção e Troubleshooting

### 12.1 Problemas Comuns
- **Erro de CPF/CNPJ duplicado**: Verificar se cliente já existe
- **Limite de crédito ultrapassado**: Revisar pedidos ativos do cliente
- **Consulta BrasilAPI falha**: Verificar conexão de internet e status da API

### 12.2 Comandos Úteis
```bash
# Listar todos os clientes
php artisan tinker
>>> Client::all();

# Verificar crédito de um cliente
>>> Client::find('uuid')->hasAvailableCredit();

# Clientes inadimplentes
>>> Client::where('situation', 'defaulter')->count();
```

---

**Caminho do Código**: `app/Models/Client.php`, `app/Livewire/Cadastro/Clientes/`, `routes/cadastro.php`

