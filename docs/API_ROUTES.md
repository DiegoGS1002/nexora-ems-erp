# Rotas da API - Nexora EMS ERP

Este documento detalha as rotas da API disponíveis no sistema.

## Movimentação de Estoque

### Listar todas as movimentações
```
GET /api/stock-movements
```
Retorna todas as movimentações de estoque ordenadas por data de criação (mais recentes primeiro).

### Exibir uma movimentação específica
```
GET /api/stock-movements/{id}
```

### Criar nova movimentação
```
POST /api/stock-movements
```
**Parâmetros:**
- `name` (obrigatório): Nome da movimentação
- `description` (opcional): Descrição da movimentação

### Atualizar movimentação
```
PUT/PATCH /api/stock-movements/{id}
```
**Parâmetros:**
- `name` (obrigatório): Nome da movimentação
- `description` (opcional): Descrição da movimentação

### Excluir movimentação
```
DELETE /api/stock-movements/{id}
```

---

## Contas a Pagar

### Listar todas as contas a pagar
```
GET /api/accounts-payable
```
Retorna todas as contas a pagar ordenadas por data de vencimento (ascendente).

### Exibir uma conta a pagar específica
```
GET /api/accounts-payable/{id}
```

### Criar nova conta a pagar
```
POST /api/accounts-payable
```
**Parâmetros:**
- `name` (obrigatório): Nome da conta
- `description` (opcional): Descrição da conta
- `value` (obrigatório): Valor da conta (numérico, mínimo 0)
- `due_date` (obrigatório): Data de vencimento (formato: YYYY-MM-DD)

### Atualizar conta a pagar
```
PUT/PATCH /api/accounts-payable/{id}
```
**Parâmetros:**
- `name` (obrigatório): Nome da conta
- `description` (opcional): Descrição da conta
- `value` (obrigatório): Valor da conta (numérico, mínimo 0)
- `due_date` (obrigatório): Data de vencimento (formato: YYYY-MM-DD)

### Excluir conta a pagar
```
DELETE /api/accounts-payable/{id}
```

---

## Contas a Receber

### Listar todas as contas a receber
```
GET /api/accounts-receivable
```
Retorna todas as contas a receber ordenadas por data de criação (mais recentes primeiro).

### Exibir uma conta a receber específica
```
GET /api/accounts-receivable/{id}
```

### Criar nova conta a receber
```
POST /api/accounts-receivable
```
**Parâmetros:**
- `name` (obrigatório): Nome da conta
- `description` (opcional): Descrição da conta

### Atualizar conta a receber
```
PUT/PATCH /api/accounts-receivable/{id}
```
**Parâmetros:**
- `name` (obrigatório): Nome da conta
- `description` (opcional): Descrição da conta

### Excluir conta a receber
```
DELETE /api/accounts-receivable/{id}
```

---

## Outras Rotas Disponíveis

### Produtos
- `GET /api/products` - Listar produtos
- `POST /api/products` - Criar produto
- `GET /api/products/{id}` - Exibir produto
- `PUT/PATCH /api/products/{id}` - Atualizar produto
- `DELETE /api/products/{id}` - Excluir produto

### Fornecedores
- `GET /api/suppliers` - Listar fornecedores
- `POST /api/suppliers` - Criar fornecedor
- `GET /api/suppliers/{id}` - Exibir fornecedor
- `PUT/PATCH /api/suppliers/{id}` - Atualizar fornecedor
- `DELETE /api/suppliers/{id}` - Excluir fornecedor

### Clientes
- `GET /api/clients` - Listar clientes
- `POST /api/clients` - Criar cliente
- `GET /api/clients/{id}` - Exibir cliente
- `PUT/PATCH /api/clients/{id}` - Atualizar cliente
- `DELETE /api/clients/{id}` - Excluir cliente

### Produto x Fornecedor
- `GET /api/products/{product}/suppliers` - Listar fornecedores de um produto
- `POST /api/products/{product}/suppliers` - Vincular fornecedor a produto
- `DELETE /api/products/{product}/suppliers/{supplier}` - Desvincular fornecedor

---

## Autenticação

Algumas rotas requerem autenticação:
- `GET /api/proxy/cnpj/{cnpj}` - Consultar CNPJ (requer autenticação)
- `GET /api/proxy/cep/{cep}` - Consultar CEP (requer autenticação)

---

## Formato de Resposta

### Sucesso (GET)
```json
[
  {
    "id": 1,
    "name": "Exemplo",
    "description": "Descrição",
    "created_at": "2026-04-09T00:00:00.000000Z",
    "updated_at": "2026-04-09T00:00:00.000000Z"
  }
]
```

### Sucesso (POST/PUT)
```json
{
  "message": "Registro criado/atualizado com sucesso",
  "data": {
    "id": 1,
    "name": "Exemplo",
    "description": "Descrição",
    "created_at": "2026-04-09T00:00:00.000000Z",
    "updated_at": "2026-04-09T00:00:00.000000Z"
  }
}
```

### Sucesso (DELETE)
```json
{
  "message": "Registro removido com sucesso"
}
```

### Erro de Validação
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": [
      "O campo name é obrigatório."
    ]
  }
}
```

