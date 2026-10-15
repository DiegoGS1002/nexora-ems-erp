# Relatório do módulo de Vendas

Data: 05/09/2026  
Escopo: pedidos, itens, clientes, pagamentos, preços, aprovação, faturamento e API.

## Visão geral

O módulo possui fluxo amplo de pedidos, formulário dedicado, precificação, endereços, parcelas, logs e integração fiscal. O serviço de pedidos usa transações em criação e edição. Os principais riscos estão na duplicação de regras entre telas e API, autorização, transições de status e consistência do contrato financeiro.

## Falhas e riscos

| ID | Prioridade | Achado | Evidência e impacto | Ajuste necessário |
| --- | --- | --- | --- | --- |
| VEN-01 | P0 | Endpoints não demonstram autorização por operação | FormRequests autorizam com `true`; controller da API não chama policy | Policies para listar, ver, editar, aprovar, cancelar, faturar e excluir |
| VEN-02 | P0 | Consultas e route binding não demonstram isolamento empresarial | `SalesOrder::with(...)` e bindings recebem pedidos diretamente | Escopo obrigatório e validação das relações cliente/produto/vendedor |
| VEN-03 | P1 | Tela altera status diretamente | `PedidosVenda::changeStatus()` grava o valor recebido sem usar o serviço de domínio | Centralizar transições e validá-las no backend |
| VEN-04 | P1 | Cancelamento da tela ignora regra e motivo | Atualização direta para Cancelled contorna `SalesOrderService::cancelOrder()` | Usar serviço, exigir motivo e tratar efeitos fiscais/estoque/financeiro |
| VEN-05 | P1 | Há dois formulários Livewire para o pedido | `PedidosVenda` e `PedidoForm` repetem criação, edição, itens e endereços | Escolher fluxo canônico e extrair regras compartilhadas |
| VEN-06 | P1 | API aceita ordenação sem lista permitida | `sort_by` e `sort_order` entram em `orderBy()` diretamente | Validar colunas e direção contra allowlist |
| VEN-07 | P1 | Contrato de endereços é divergente | Request valida `addresses.*.type`, mas serviço percorre `type => addressData` | Padronizar como lista de objetos ou mapa e testar ambos os fluxos |
| VEN-08 | P1 | Total do pagamento pode divergir do total do pedido | Serviço aceita `payment.total_amount` e detalhes informados sem conciliação explícita | Validar soma das parcelas, total do pedido e arredondamento |
| VEN-09 | P1 | Pedido não reserva nem baixa estoque no serviço | Criação/aprovação não demonstram reserva; efeitos ficam dispersos | Definir momento da reserva/baixa e integrar ao ledger de estoque |
| VEN-10 | P1 | Estado fiscal pode ser alterado manualmente em fluxo associado | A tela fiscal geral permite salvar status autorizado | Impedir autorização manual e derivar status de evento/SEFAZ |
| VEN-11 | P2 | Valores monetários usam `float` em cálculos | Totais, preços e parcelas passam por float | Usar decimal controlado ou centavos, com política de arredondamento |
| VEN-12 | P2 | Relatório de vendas permanece incompleto | Controller retorna tela de desenvolvimento e impressão 501 | Concluir relatório ou retirar disponibilidade operacional |

## Ajustes necessários

1. Tornar `SalesOrderService` a única porta para mudanças do pedido.
2. Implementar máquina de estados e efeitos por transição.
3. Criar policies e testes com vendedor, gestor e usuário sem acesso.
4. Validar crédito no momento correto e registrar aprovação excepcional, em vez de somente lançar exceção.
5. Conciliar total de itens, acréscimos, descontos, pagamentos e parcelas.
6. Impedir faturamento ou romaneio duplicado com vínculos únicos.
7. Fixar filtros, ordenação e paginação da API.

## Melhorias

- Funil comercial com orçamento, pedido, separação, faturamento e entrega.
- Alçadas para desconto, margem negativa, inadimplência e limite de crédito.
- Histórico de preço e margem por item.
- Reserva de estoque e previsão de atendimento.
- Comunicação ao cliente e rastreio de documentos enviados.
- Indicadores por vendedor, canal, produto, região e margem.

## Páginas sugeridas

| Página | Conteúdo principal | Prioridade |
| --- | --- | --- |
| Orçamentos/propostas | Versões, validade, aprovação do cliente e conversão | P2 |
| Central de aprovações comerciais | Crédito, desconto, margem e justificativas | P1 |
| Separação de pedidos | Reserva, picking, falta, substituição e conferência | P1 |
| Carteira de pedidos | Data prometida, bloqueios, faturamento e atraso | P2 |
| Comissões | Regra, base, estorno, fechamento e pagamento | P2 |
| Relatório de vendas | Receita, margem, ticket, vendedor, canal e produto | P1 |
| Pós-venda/devoluções | Ocorrência, autorização, logística reversa e crédito | P2 |

## Ordem de execução

1. Corrigir autorização, empresa e transições.
2. Consolidar formulários e contratos de API.
3. Conciliar pagamento, estoque e fiscal.
4. Entregar aprovações, separação e relatórios.

## Critérios de aceite

- [ ] Status inválido ou transição proibida retorna erro de negócio.
- [ ] Cancelamento registra motivo e reverte efeitos aplicáveis.
- [ ] Pagamentos e parcelas fecham exatamente com o total.
- [ ] Vendedor sem alçada não aprova sua própria exceção.
- [ ] Empresa A não acessa pedidos ou relações da empresa B.
- [ ] Repetir faturamento ou romaneio não cria duplicidade.

## Arquivos principais analisados

- `app/Livewire/Vendas/PedidoForm.php`
- `app/Livewire/Vendas/PedidosVenda.php`
- `app/Livewire/Vendas/TabelasPrecificacao.php`
- `app/Services/SalesOrderService.php`
- `app/Http/Controllers/Api/SalesOrderController.php`
- `app/Http/Requests/StoreSalesOrderRequest.php`

