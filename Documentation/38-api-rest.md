# API REST do Nexora ERP

## 1. Visão Geral

A API REST do Nexora ERP fornece acesso programático a todos os recursos do sistema. Segue os princípios RESTful, utiliza JSON para troca de dados e autenticação via Laravel Sanctum.

### Características
- **RESTful**: Verbos HTTP semânticos (GET, POST, PUT, PATCH, DELETE)
- **JSON**: Formato padrão de requisição e resposta
- **Autenticação**: Bearer Token (Sanctum)
- **Versionamento**: Preparado para versionamento (v1, v2, etc.)
- **Rate Limiting**: 60 requisições por minuto por padrão
- **CORS**: Configurável via `config/cors.php`
- **Documentação**: OpenAPI 3.0 (planejado)

---

## 2. Autenticação

### 2.1 Obter Token de Acesso

**Endpoint**: `POST /api/auth/login`

**Request**:
```json
{
  "email": "usuario@exemplo.com",
  "password": "senha123"
}
```

**Response (200)**:
```json
{
  "token": "1|abc123...",
  "user": {
    "id": 1,
    "name": "João Silva",
    "email": "usuario@exemplo.com",
    "is_admin": false
  }
}
```

**Response (401)**:
```json
{
  "message": "Credenciais inválidas"
}
```

### 2.2 Usar Token nas Requisições

Incluir header `Authorization` em todas as requisições:

```http
GET /api/clients HTTP/1.1
Host: api.nexora.com
Authorization: Bearer 1|abc123...
Accept: application/json
```

### 2.3 Revogar Token (Logout)

**Endpoint**: `POST /api/auth/logout`

**Headers**:
```
Authorization: Bearer {token}
```

**Response (200)**:
```json
{
  "message": "Token revogado com sucesso"
}
```

---

## 3. Estrutura de Resposta Padrão

### 3.1 Sucesso (2xx)

#### Listagem (200)
```json
{
  "data": [
    { "id": "uuid-1", "name": "Cliente 1" },
    { "id": "uuid-2", "name": "Cliente 2" }
  ],
  "meta": {
    "current_page": 1,
    "last_page": 5,
    "per_page": 20,
    "total": 98
  },
  "links": {
    "first": "https://api.nexora.com/api/clients?page=1",
    "last": "https://api.nexora.com/api/clients?page=5",
    "prev": null,
    "next": "https://api.nexora.com/api/clients?page=2"
  }
}
```

#### Criação (201)
```json
{
  "data": {
    "id": "uuid-123",
    "name": "Cliente Novo",
    "email": "cliente@exemplo.com",
    "created_at": "2026-05-28T10:30:00Z"
  },
  "message": "Cliente criado com sucesso"
}
```

#### Atualização (200)
```json
{
  "data": {
    "id": "uuid-123",
    "name": "Cliente Atualizado",
    "updated_at": "2026-05-28T11:00:00Z"
  },
  "message": "Cliente atualizado com sucesso"
}
```

#### Exclusão (200 ou 204)
```json
{
  "message": "Cliente excluído com sucesso"
}
```

### 3.2 Erro (4xx, 5xx)

#### Validação (422)
```json
{
  "message": "Os dados fornecidos são inválidos",
  "errors": {
    "email": [
      "O campo email é obrigatório.",
      "O campo email deve ser um e-mail válido."
    ],
    "taxNumber": [
      "O CNPJ informado já está em uso."
    ]
  }
}
```

#### Não Encontrado (404)
```json
{
  "message": "Cliente não encontrado"
}
```

#### Não Autorizado (401)
```json
{
  "message": "Não autenticado"
}
```

#### Sem Permissão (403)
```json
{
  "message": "Você não tem permissão para acessar este recurso"
}
```

#### Erro do Servidor (500)
```json
{
  "message": "Erro interno do servidor",
  "error": "Detalhes do erro (apenas em desenvolvimento)"
}
```

---

## 4. Recursos da API

### 4.1 Clientes

**Base URL**: `/api/clients`

#### Listar Clientes
```http
GET /api/clients
```

**Query Parameters**:
- `search`: Busca por nome, CPF/CNPJ ou e-mail
- `situation`: Filtrar por situação (active, inactive, defaulter)
- `page`: Número da página (padrão: 1)
- `per_page`: Itens por página (padrão: 15, máx: 100)
- `sort_by`: Campo para ordenação (name, created_at)
- `sort_order`: Ordem (asc, desc)

**Exemplo**:
```http
GET /api/clients?search=João&situation=active&per_page=20&sort_by=name
```

**Response (200)**:
```json
{
  "data": [
    {
      "id": "uuid-1",
      "tipo_pessoa": "fisica",
      "name": "João Silva",
      "taxNumber": "123.456.789-00",
      "email": "joao@exemplo.com",
      "phone_number": "(11) 98765-4321",
      "credit_limit": "10000.00",
      "situation": "active",
      "created_at": "2026-01-15T10:00:00Z"
    }
  ],
  "meta": { "current_page": 1, "total": 45 }
}
```

#### Criar Cliente
```http
POST /api/clients
```

**Request Body**:
```json
{
  "tipo_pessoa": "juridica",
  "name": "Empresa XYZ Ltda",
  "social_name": "XYZ",
  "taxNumber": "12.345.678/0001-99",
  "inscricao_estadual": "123456789",
  "email": "contato@xyz.com.br",
  "phone_number": "(11) 98765-4321",
  "address_zip_code": "01310-100",
  "address_street": "Av. Paulista",
  "address_number": "1000",
  "address_city": "São Paulo",
  "address_state": "SP",
  "credit_limit": 50000.00,
  "situation": "active"
}
```

**Response (201)**:
```json
{
  "data": {
    "id": "uuid-new",
    "tipo_pessoa": "juridica",
    "name": "Empresa XYZ Ltda",
    // ... demais campos
  },
  "message": "Cliente criado com sucesso"
}
```

#### Obter Cliente por ID
```http
GET /api/clients/{id}
```

**Response (200)**:
```json
{
  "data": {
    "id": "uuid-1",
    "name": "Cliente ABC",
    "price_table": {
      "id": "uuid-table",
      "name": "Tabela Premium"
    },
    "sales_orders": [
      {
        "id": "uuid-order",
        "order_number": 1234,
        "total_amount": "5000.00",
        "status": "aprovado"
      }
    ]
  }
}
```

#### Atualizar Cliente
```http
PUT /api/clients/{id}
PATCH /api/clients/{id}
```

**Request Body**:
```json
{
  "email": "novoemail@exemplo.com",
  "credit_limit": 60000.00
}
```

**Response (200)**:
```json
{
  "data": { "id": "uuid-1", "email": "novoemail@exemplo.com" },
  "message": "Cliente atualizado com sucesso"
}
```

#### Excluir Cliente
```http
DELETE /api/clients/{id}
```

**Response (200)**:
```json
{
  "message": "Cliente excluído com sucesso"
}
```

---

### 4.2 Produtos

**Base URL**: `/api/products`

#### Listar Produtos
```http
GET /api/products
```

**Query Parameters**:
- `search`: Busca por código, nome, EAN ou NCM
- `category_id`: Filtrar por categoria
- `is_active`: Filtrar por status (true, false)
- `low_stock`: Apenas produtos com estoque baixo (true)

**Response (200)**:
```json
{
  "data": [
    {
      "id": "uuid-1",
      "product_code": "PROD-000001",
      "name": "Notebook Dell",
      "ean": "7891234567890",
      "ncm": "84713012",
      "cost_price": "2500.00",
      "sale_price": "3500.00",
      "stock": 10,
      "stock_min": 2,
      "is_active": true
    }
  ]
}
```

#### Criar Produto
```http
POST /api/products
```

**Request Body**:
```json
{
  "name": "Mouse Logitech MX Master 3",
  "product_type": "product",
  "nature": "revenda",
  "cost_price": 300.00,
  "sale_price": 450.00,
  "stock": 50,
  "stock_min": 5,
  "ncm": "84716060",
  "cfop_saida": "5102",
  "unit_of_measure_id": "uuid-un",
  "product_category_id": "uuid-informatica",
  "is_active": true
}
```

#### Fornecedores do Produto
```http
GET /api/products/{product}/suppliers
```

**Response (200)**:
```json
{
  "data": [
    {
      "supplier_id": "uuid-supplier",
      "supplier_name": "Fornecedor ABC",
      "supplier_code": "PROD-ABC-001",
      "cost_price": "280.00",
      "delivery_time_days": 7,
      "is_preferred": true
    }
  ]
}
```

#### Vincular Fornecedor
```http
POST /api/products/{product}/suppliers
```

**Request Body**:
```json
{
  "supplier_id": "uuid-supplier",
  "supplier_code": "PROD-ABC-002",
  "cost_price": 280.00,
  "delivery_time_days": 7,
  "is_preferred": false
}
```

---

### 4.3 Pedidos de Venda

**Base URL**: `/api/sales-orders`

#### Listar Pedidos
```http
GET /api/sales-orders
```

**Query Parameters**:
- `client_id`: Filtrar por cliente
- `status`: Filtrar por status (draft, aprovado, em_separacao, etc.)
- `start_date`: Data inicial (Y-m-d)
- `end_date`: Data final (Y-m-d)
- `seller_id`: Filtrar por vendedor

#### Criar Pedido
```http
POST /api/sales-orders
```

**Request Body**:
```json
{
  "client_id": "uuid-client",
  "seller_id": 1,
  "is_fiscal": true,
  "operation_type": "venda_normal",
  "sales_channel": "online",
  "items": [
    {
      "product_id": "uuid-product-1",
      "quantity": 2,
      "unit_price": 100.00,
      "discount_percent": 5
    },
    {
      "product_id": "uuid-product-2",
      "quantity": 1,
      "unit_price": 200.00
    }
  ],
  "addresses": {
    "delivery": {
      "zip_code": "01310-100",
      "street": "Av. Paulista",
      "number": "1000",
      "city": "São Paulo",
      "state": "SP"
    }
  },
  "payment": {
    "payment_condition": "parcelado",
    "payment_method": "boleto",
    "installments": 3
  }
}
```

**Response (201)**:
```json
{
  "data": {
    "id": "uuid-order",
    "order_number": 1234,
    "client": { "id": "uuid-client", "name": "Cliente ABC" },
    "status": "aberto",
    "subtotal": "400.00",
    "discount_total": "10.00",
    "total_amount": "390.00",
    "items": [
      {
        "product_id": "uuid-product-1",
        "description": "Produto 1",
        "quantity": 2,
        "unit_price": "100.00",
        "discount": "10.00",
        "total": "190.00"
      }
    ]
  },
  "message": "Pedido criado com sucesso"
}
```

#### Estatísticas de Vendas
```http
GET /api/sales-orders/statistics
```

**Query Parameters**:
- `period`: today, week, month, year
- `start_date`: Data inicial personalizada
- `end_date`: Data final personalizada

**Response (200)**:
```json
{
  "data": {
    "total_orders": 150,
    "total_value": "450000.00",
    "avg_ticket": "3000.00",
    "by_status": {
      "aprovado": { "count": 50, "value": "150000.00" },
      "em_separacao": { "count": 30, "value": "90000.00" }
    },
    "top_clients": [
      { "client_id": "uuid-1", "name": "Cliente A", "total": "50000.00" }
    ],
    "top_products": [
      { "product_id": "uuid-1", "name": "Produto X", "quantity": 200 }
    ]
  }
}
```

#### Calcular Pedido (Simulação)
```http
POST /api/sales-orders/calculate
```

**Request Body**:
```json
{
  "items": [
    { "product_id": "uuid-1", "quantity": 10, "discount_percent": 5 },
    { "product_id": "uuid-2", "quantity": 5 }
  ],
  "freight_value": 50.00
}
```

**Response (200)**:
```json
{
  "data": {
    "subtotal": "1500.00",
    "discount_total": "75.00",
    "freight_value": "50.00",
    "total_amount": "1475.00",
    "items": [
      {
        "product_id": "uuid-1",
        "quantity": 10,
        "unit_price": "100.00",
        "discount": "50.00",
        "total": "950.00"
      }
    ]
  }
}
```

#### Aprovar Pedido
```http
POST /api/sales-orders/{order}/approve
```

**Request Body**:
```json
{
  "reason": "Crédito aprovado pela gerência"
}
```

**Response (200)**:
```json
{
  "data": { "id": "uuid-order", "status": "aprovado" },
  "message": "Pedido aprovado com sucesso"
}
```

#### Cancelar Pedido
```http
POST /api/sales-orders/{order}/cancel
```

**Request Body**:
```json
{
  "reason": "Cliente solicitou cancelamento"
}
```

#### Logs do Pedido
```http
GET /api/sales-orders/{order}/logs
```

**Response (200)**:
```json
{
  "data": [
    {
      "id": 1,
      "action": "created",
      "description": "Pedido criado por João Silva",
      "user": { "id": 1, "name": "João Silva" },
      "created_at": "2026-05-28T10:00:00Z"
    },
    {
      "id": 2,
      "action": "status_changed",
      "description": "Status alterado de 'aberto' para 'aprovado'",
      "user": { "id": 2, "name": "Maria Santos" },
      "created_at": "2026-05-28T11:00:00Z"
    }
  ]
}
```

---

### 4.4 Movimentações de Estoque

**Base URL**: `/api/stock-movements`

#### Listar Movimentações
```http
GET /api/stock-movements
```

**Query Parameters**:
- `product_id`: Filtrar por produto
- `type`: Filtrar por tipo (input, output, adjustment, transfer)
- `start_date`: Data inicial
- `end_date`: Data final

#### Criar Movimentação
```http
POST /api/stock-movements
```

**Request Body**:
```json
{
  "product_id": "uuid-product",
  "quantity": 50,
  "type": "input",
  "origin": "Pedido de Compra #123",
  "unit_cost": 100.00
}
```

**Response (201)**:
```json
{
  "data": {
    "id": 123,
    "product": { "id": "uuid-product", "name": "Produto ABC" },
    "quantity": "50.000",
    "type": "input",
    "origin": "Pedido de Compra #123",
    "user": { "id": 1, "name": "João Silva" },
    "created_at": "2026-05-28T10:30:00Z"
  },
  "message": "Movimentação registrada com sucesso"
}
```

---

### 4.5 Contas a Pagar

**Base URL**: `/api/accounts-payable`

#### Listar Contas
```http
GET /api/accounts-payable
```

**Query Parameters**:
- `status`: pending, overdue, paid, canceled
- `supplier_id`: Filtrar por fornecedor
- `due_month`: Mês de vencimento (Y-m)

#### Criar Conta
```http
POST /api/accounts-payable
```

**Request Body**:
```json
{
  "description_title": "Pagamento Fornecedor ABC",
  "supplier_id": "uuid-supplier",
  "chart_of_account_id": "uuid-account",
  "amount": 5000.00,
  "due_date_at": "2026-06-15",
  "payment_method": "boleto",
  "is_recurring": false
}
```

#### Dar Baixa
```http
POST /api/accounts-payable/{id}/pay
```

**Request Body**:
```json
{
  "payment_date": "2026-05-28",
  "paid_amount": 5000.00,
  "payment_method": "boleto",
  "notes": "Pago via boleto bancário"
}
```

---

### 4.6 Contas a Receber

**Base URL**: `/api/accounts-receivable`

Similar a contas a pagar, mas com `client_id` em vez de `supplier_id`.

---

### 4.7 BrasilAPI Proxy

**Base URL**: `/api/proxy` (middleware `auth`)

#### Consultar CEP
```http
GET /api/proxy/cep/{cep}
```

**Exemplo**:
```http
GET /api/proxy/cep/01310100
```

**Response (200)**:
```json
{
  "cep": "01310-100",
  "street": "Avenida Paulista",
  "neighborhood": "Bela Vista",
  "city": "São Paulo",
  "state": "SP"
}
```

#### Consultar CNPJ
```http
GET /api/proxy/cnpj/{cnpj}
```

**Exemplo**:
```http
GET /api/proxy/cnpj/00000000000191
```

**Response (200)**:
```json
{
  "cnpj": "00.000.000/0001-91",
  "razao_social": "BANCO DO BRASIL S.A.",
  "nome_fantasia": "BANCO DO BRASIL",
  "uf": "DF",
  "municipio": "BRASÍLIA",
  "logradouro": "SBS Quadra 1 Bloco G Lote 32"
}
```

---

## 5. Rate Limiting

Por padrão, a API limita requisições a **60 por minuto** por IP/usuário.

**Response quando limite excedido (429)**:
```json
{
  "message": "Too Many Requests"
}
```

**Headers de Rate Limit**:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 45
X-RateLimit-Reset: 1653825600
```

Para aumentar o limite:
```php
// app/Http/Kernel.php
'api' => [
    'throttle:120,1', // 120 requisições por minuto
],
```

---

## 6. Paginação

Todas as listagens são paginadas automaticamente.

**Padrões**:
- `per_page` padrão: 15
- `per_page` máximo: 100

**Exemplo de uso**:
```http
GET /api/clients?page=2&per_page=50
```

**Meta de paginação na resposta**:
```json
{
  "meta": {
    "current_page": 2,
    "from": 16,
    "last_page": 5,
    "per_page": 15,
    "to": 30,
    "total": 73
  },
  "links": {
    "first": "https://api.nexora.com/api/clients?page=1",
    "last": "https://api.nexora.com/api/clients?page=5",
    "prev": "https://api.nexora.com/api/clients?page=1",
    "next": "https://api.nexora.com/api/clients?page=3"
  }
}
```

---

## 7. Filtros e Ordenação

### 7.1 Busca Global
```http
GET /api/clients?search=João
```

Busca em múltiplos campos (nome, e-mail, CPF/CNPJ).

### 7.2 Filtros Específicos
```http
GET /api/clients?situation=active&tipo_pessoa=juridica
```

### 7.3 Ordenação
```http
GET /api/clients?sort_by=created_at&sort_order=desc
```

### 7.4 Combinações
```http
GET /api/clients?search=Silva&situation=active&sort_by=name&sort_order=asc&per_page=20&page=1
```

---

## 8. Códigos de Status HTTP

| Código | Descrição | Uso |
|--------|-----------|-----|
| 200 | OK | Sucesso geral (GET, PUT, PATCH, DELETE) |
| 201 | Created | Recurso criado (POST) |
| 204 | No Content | Sucesso sem conteúdo (DELETE) |
| 400 | Bad Request | Requisição malformada |
| 401 | Unauthorized | Não autenticado |
| 403 | Forbidden | Sem permissão |
| 404 | Not Found | Recurso não encontrado |
| 422 | Unprocessable Entity | Erro de validação |
| 429 | Too Many Requests | Rate limit excedido |
| 500 | Internal Server Error | Erro do servidor |
| 503 | Service Unavailable | Serviço temporariamente indisponível |

---

## 9. Exemplos de Integração

### 9.1 JavaScript (Fetch API)
```javascript
const API_URL = 'https://api.nexora.com';
const token = 'seu-token-aqui';

// Listar clientes
async function getClients() {
  const response = await fetch(`${API_URL}/api/clients`, {
    headers: {
      'Authorization': `Bearer ${token}`,
      'Accept': 'application/json',
    }
  });
  
  const data = await response.json();
  console.log(data.data); // Array de clientes
}

// Criar pedido
async function createOrder(orderData) {
  const response = await fetch(`${API_URL}/api/sales-orders`, {
    method: 'POST',
    headers: {
      'Authorization': `Bearer ${token}`,
      'Content-Type': 'application/json',
      'Accept': 'application/json',
    },
    body: JSON.stringify(orderData),
  });
  
  return await response.json();
}
```

### 9.2 PHP (Guzzle)
```php
use GuzzleHttp\Client;

$client = new Client([
    'base_uri' => 'https://api.nexora.com',
    'headers' => [
        'Authorization' => 'Bearer ' . $token,
        'Accept' => 'application/json',
    ],
]);

// Listar produtos
$response = $client->get('/api/products', [
    'query' => [
        'search' => 'Notebook',
        'per_page' => 20,
    ],
]);

$products = json_decode($response->getBody(), true);
```

### 9.3 Python (Requests)
```python
import requests

API_URL = 'https://api.nexora.com'
TOKEN = 'seu-token-aqui'

headers = {
    'Authorization': f'Bearer {TOKEN}',
    'Accept': 'application/json',
}

# Listar clientes
response = requests.get(f'{API_URL}/api/clients', headers=headers)
clients = response.json()['data']

# Criar produto
product_data = {
    'name': 'Produto Novo',
    'cost_price': 100.00,
    'sale_price': 150.00,
    'stock': 50,
}

response = requests.post(
    f'{API_URL}/api/products',
    headers=headers,
    json=product_data
)
```

---

## 10. Erros Comuns e Soluções

### 10.1 401 Unauthorized
**Causa**: Token inválido ou expirado  
**Solução**: Obter novo token via `/api/auth/login`

### 10.2 422 Validation Error
**Causa**: Dados inválidos no body  
**Solução**: Verificar campo `errors` na resposta e corrigir

### 10.3 429 Too Many Requests
**Causa**: Rate limit excedido  
**Solução**: Aguardar reset ou otimizar requisições

### 10.4 500 Internal Server Error
**Causa**: Erro no servidor (bug)  
**Solução**: Reportar ao suporte com detalhes da requisição

---

## 11. Webhook (Planejado)

Em desenvolvimento: Sistema de webhooks para notificar integrações externas sobre eventos.

**Eventos planejados**:
- `order.created`
- `order.approved`
- `order.shipped`
- `payment.received`
- `stock.low`

---

**Documentação completa da API**: Em breve disponível em formato OpenAPI 3.0

