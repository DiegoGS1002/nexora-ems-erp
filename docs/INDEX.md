# Documentação da API - Nexora EMS ERP

## 📋 Resumo

Este diretório contém toda a documentação das rotas da API do sistema Nexora EMS ERP, incluindo as novas rotas para:

- **Movimentação de Estoque** (`/api/stock-movements`)
- **Contas a Pagar** (`/api/accounts-payable`)
- **Contas a Receber** (`/api/accounts-receivable`)

## 📁 Arquivos Disponíveis

| Arquivo | Descrição | Tamanho |
|---------|-----------|---------|
| `API_ROUTES.md` | Documentação completa em Markdown | 4.4 KB |
| `API_ROUTES.html` | Versão HTML básica | 8.7 KB |
| `API_ROUTES_PDF.html` | Versão HTML otimizada para PDF | 17 KB |
| `README_PDF.md` | Guia para gerar PDF | 2.7 KB |

## 🚀 Como Usar

### Visualizar no Navegador

Abra o arquivo `API_ROUTES_PDF.html` em qualquer navegador para visualizar a documentação com formatação profissional.

### Gerar PDF

**Método Mais Simples:**

1. Abra `API_ROUTES_PDF.html` no navegador (Chrome, Firefox, Edge)
2. Pressione `Ctrl + P` (Windows/Linux) ou `Cmd + P` (Mac)
3. Selecione "Salvar como PDF"
4. Salve com o nome `Documentacao_API_Nexora.pdf`

**Método Automatizado:**

```bash
cd /home/dg/projects/nexora-ems-erp/docs

# Usando wkhtmltopdf (após instalação)
wkhtmltopdf API_ROUTES_PDF.html Documentacao_API_Nexora.pdf

# Ou usando weasyprint (após instalação)
weasyprint API_ROUTES_PDF.html Documentacao_API_Nexora.pdf
```

Para mais detalhes, consulte `README_PDF.md`.

## 📝 Conteúdo da Documentação

A documentação inclui:

### 1. Movimentação de Estoque
- `GET /api/stock-movements` - Listar movimentações
- `POST /api/stock-movements` - Criar movimentação
- `GET /api/stock-movements/{id}` - Exibir movimentação
- `PUT/PATCH /api/stock-movements/{id}` - Atualizar movimentação
- `DELETE /api/stock-movements/{id}` - Excluir movimentação

### 2. Contas a Pagar
- `GET /api/accounts-payable` - Listar contas a pagar
- `POST /api/accounts-payable` - Criar conta a pagar
- `GET /api/accounts-payable/{id}` - Exibir conta
- `PUT/PATCH /api/accounts-payable/{id}` - Atualizar conta
- `DELETE /api/accounts-payable/{id}` - Excluir conta

**Parâmetros:**
- `name` (obrigatório): Nome da conta
- `description` (opcional): Descrição
- `value` (obrigatório): Valor numérico
- `due_date` (obrigatório): Data de vencimento (YYYY-MM-DD)

### 3. Contas a Receber
- `GET /api/accounts-receivable` - Listar contas a receber
- `POST /api/accounts-receivable` - Criar conta a receber
- `GET /api/accounts-receivable/{id}` - Exibir conta
- `PUT/PATCH /api/accounts-receivable/{id}` - Atualizar conta
- `DELETE /api/accounts-receivable/{id}` - Excluir conta

**Parâmetros:**
- `name` (obrigatório): Nome da conta
- `description` (opcional): Descrição

### 4. Outras Rotas
- Produtos (`/api/products`)
- Fornecedores (`/api/suppliers`)
- Clientes (`/api/clients`)
- Produto x Fornecedor

## 🎨 Características do PDF

O arquivo HTML otimizado (`API_ROUTES_PDF.html`) inclui:

✅ Design profissional com cores corporativas  
✅ Formatação otimizada para impressão A4  
✅ Cabeçalho com título e data de geração  
✅ Endpoints destacados com cores por método HTTP  
✅ Parâmetros organizados e bem formatados  
✅ Exemplos de requisição e resposta JSON  
✅ Rodapé com informações do sistema  
✅ Quebras de página inteligentes  

## 🔧 Scripts Disponíveis

### `generate_api_pdf.py`
Script Python que detecta automaticamente a melhor ferramenta disponível e converte para PDF.

```bash
python3 generate_api_pdf.py
```

### `create_pdf_documentation.py`
Script que gera HTML otimizado para conversão em PDF com formatação profissional.

```bash
python3 create_pdf_documentation.py
```

## 📊 Formato de Resposta da API

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
    ...
  }
}
```

### Erro de Validação
```json
{
  "message": "The given data was invalid.",
  "errors": {
    "name": ["O campo name é obrigatório."]
  }
}
```

## 🔐 Autenticação

Algumas rotas requerem autenticação. Consulte a documentação completa para detalhes sobre autenticação e tokens.

## 📞 Suporte

Para dúvidas ou problemas:
- Consulte a documentação técnica completa
- Entre em contato com a equipe de desenvolvimento
- Verifique os logs do sistema

---

**Nexora EMS ERP** - Sistema de Gestão Empresarial  
Documentação gerada em 09 de Abril de 2026

