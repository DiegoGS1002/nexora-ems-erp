# Validação Fiscal - Pedidos de Venda

## Visão Geral

Este documento descreve as validações fiscais implementadas no sistema de pedidos de venda (Sales Orders) para garantir que todos os produtos atendam aos requisitos fiscais brasileiros.

## Validações Implementadas

### 1. NCM (Nomenclatura Comum do Mercosul)

Para pedidos com **`is_fiscal = true`**, o sistema verifica se todos os produtos possuem NCM definido:

- **Verificação no Item**: Primeiro verifica se o campo `ncm` está preenchido no item do pedido (`sales_order_items.ncm`)
- **Verificação no Produto**: Se não houver NCM no item, verifica se o produto tem NCM definido (`products.ncm`)
- **Ação**: Se algum produto não tiver NCM, o pedido é marcado para aprovação com a razão: `"Produtos sem NCM: [lista de produtos]"`

### 2. Grupo Tributário

Para pedidos com **`is_fiscal = true`**, o sistema verifica se todos os produtos possuem um Grupo Tributário associado:

- **Verificação**: Valida se o campo `grupo_tributario_id` está preenchido no produto
- **Ação**: Se algum produto não tiver Grupo Tributário, o pedido é marcado para aprovação com a razão: `"Produtos sem Grupo Tributário: [lista de produtos]"`

## Como Funciona

### Fluxo de Validação

1. **Criação do Pedido**: Quando um pedido é criado, o `SalesOrderObserver` é acionado
2. **Carregamento dos Dados**: O sistema carrega todos os itens e seus produtos relacionados
3. **Verificação Fiscal**: Se o pedido for fiscal (`is_fiscal = true`), executa as validações
4. **Marcação para Aprovação**: Se houver produtos sem NCM ou Grupo Tributário, o pedido é marcado para aprovação
5. **Registro de Log**: As razões da aprovação são registradas no log do sistema

### Exemplo de Código

```php
// SalesOrderObserver.php - checkIfNeedsApproval()

if ($salesOrder->is_fiscal) {
    foreach ($salesOrder->items as $item) {
        // Verifica NCM
        $hasNCM = !empty($item->ncm) || (!empty($item->product) && !empty($item->product->ncm));
        if (!$hasNCM) {
            $itemsWithoutNCM[] = $item->description ?? $item->product?->name;
        }

        // Verifica Grupo Tributário
        if ($item->product && empty($item->product->grupo_tributario_id)) {
            $itemsWithoutGrupoTributario[] = $item->product->name;
        }
    }
}
```

## Campos Relacionados

### Tabela: `sales_orders`
- `is_fiscal` (boolean): Indica se o pedido é fiscal
- `needs_approval` (boolean): Marca o pedido para aprovação
- `approval_reason` (text): Motivo da necessidade de aprovação

### Tabela: `sales_order_items`
- `ncm` (string): NCM do item (pode ser preenchido diretamente)
- `product_id` (uuid): Relacionamento com o produto

### Tabela: `products`
- `ncm` (string): NCM padrão do produto
- `grupo_tributario_id` (bigint): ID do grupo tributário

### Tabela: `grupo_tributarios`
- `id` (bigint): ID do grupo tributário
- `codigo` (string): Código do grupo
- `nome` (string): Nome do grupo
- `ncm` (string): NCM padrão do grupo

## Outras Validações de Aprovação

Além das validações fiscais, o sistema também marca pedidos para aprovação nos seguintes casos:

1. **Desconto Alto**: Desconto acima de 10% do valor bruto
2. **Cliente Inadimplente**: Cliente com situação "defaulter"
3. **Limite de Crédito Excedido**: Valor do pedido excede o crédito disponível do cliente
4. **Valor Alto**: Pedidos acima de R$ 10.000,00

## Mensagens de Aprovação

As mensagens são concatenadas quando múltiplas validações falham:

```
Produtos sem NCM: Produto A, Produto B, Produto C; Produtos sem Grupo Tributário: Produto D
```

Se houver mais de 3 produtos sem NCM ou Grupo Tributário, a mensagem será:

```
Produtos sem NCM: Produto A, Produto B, Produto C e mais 5
```

## Logs

Todos os pedidos marcados para aprovação são registrados no log:

```php
Log::info("Pedido {$order_number} marcado para aprovação", [
    'reasons' => [...],
]);
```

## Como Configurar Produtos

### Passo 1: Cadastrar NCM
1. Acesse o cadastro de produtos
2. Edite o produto
3. Preencha o campo **NCM** com 8 dígitos (exemplo: 12345678)

### Passo 2: Associar Grupo Tributário
1. Primeiro, cadastre um Grupo Tributário (se ainda não existir)
2. No cadastro de produtos, selecione o **Grupo Tributário** adequado
3. O Grupo Tributário define:
   - CST/CSOSN de ICMS
   - Alíquotas de ICMS, IPI, PIS, COFINS
   - CFOP padrão

## Benefícios

✅ **Conformidade Fiscal**: Garante que todos os produtos tenham informações fiscais obrigatórias  
✅ **Prevenção de Erros**: Impede emissão de notas fiscais com dados incompletos  
✅ **Rastreabilidade**: Registra motivos claros para aprovações necessárias  
✅ **Flexibilidade**: Permite aprovação manual para casos especiais

## Próximos Passos

- [ ] Implementar validação de CFOP
- [ ] Validar CST/CSOSN antes da emissão de NF-e
- [ ] Criar relatório de produtos sem NCM/Grupo Tributário
- [ ] Adicionar notificações automáticas para aprovadores

---

**Última atualização**: 10/04/2026  
**Versão**: 1.0

