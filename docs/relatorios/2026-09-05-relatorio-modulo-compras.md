# Relatório do módulo de Compras

Data: 05/09/2026  
Escopo: solicitações, cotações, respostas de fornecedores, pedidos, aprovações, recebimento, estoque e financeiro.

## Visão geral

O módulo cobre um ciclo significativo: solicitação, cotação, pedido e integração com estoque e contas a pagar. As gravações de formulários utilizam transações em vários pontos. Os riscos mais relevantes são repetição de recebimento, transições livres, aprovação sem alçada explícita e conversões que podem produzir documentos incoerentes.

## Falhas e riscos

| ID | Prioridade | Achado | Evidência e impacto | Ajuste necessário |
| --- | --- | --- | --- | --- |
| COM-01 | P0 | Recebimento total pode ser aplicado repetidamente | Toda mudança para `RecebidoTotal` cria movimentos e conta a pagar novamente | Bloquear pedido, verificar evento já aplicado e usar constraints de origem |
| COM-02 | P0 | Status é alterado antes da transação de efeitos | `changeStatus()` atualiza o pedido e depois abre transação | Fazer status, estoque e financeiro na mesma transação |
| COM-03 | P0 | Há risco de dupla entrada entre Compras e Fiscal | Pedido recebido gera estoque/financeiro; escrituração da NF também gera estoque/financeiro | Definir processo canônico e conciliar pedido, recebimento e NF sem duplicar |
| COM-04 | P1 | Mudança de status aceita valor sem transição de domínio | `changeStatus()` grava a string recebida | Validar enum e origem/destino permitidos |
| COM-05 | P1 | Aprovação não demonstra alçada ou segregação | Métodos `approve()` gravam aprovador atual diretamente | Policy, faixa de valor, centro de custo e regra de autoaprovação |
| COM-06 | P1 | Conversão de cotação escolhe um fornecedor principal para todos os itens | O primeiro fornecedor selecionado vira fornecedor do pedido, mesmo se outros itens tiverem respostas de fornecedores diferentes | Gerar um pedido por fornecedor ou impedir seleção mista |
| COM-07 | P1 | Conversões podem ser repetidas | Métodos de conversão não demonstram bloqueio/constraint antes de criar novo destino | Tornar conversões idempotentes e validar status de origem |
| COM-08 | P1 | Cancelamento não trata efeitos posteriores | Status é alterado diretamente para cancelado | Impedir cancelamento após recebimento ou executar estorno controlado |
| COM-09 | P1 | Recebimento total ignora quantidade efetivamente recebida | Usa `item->quantity`; existe infraestrutura de recebimento parcial em outros serviços | Registrar recebimentos por item, lote e documento |
| COM-10 | P2 | Seleção da cotação não registra critério | Marca resposta como selecionada, sem justificativa visível | Guardar preço, prazo, frete, qualidade, motivo e aprovador |
| COM-11 | P2 | Busca e KPIs não demonstram empresa | Consultas dos componentes são diretas | Aplicar escopo empresarial em solicitações, fornecedores, produtos e pedidos |

## Ajustes necessários

1. Modelar recebimento como evento próprio, com itens e quantidades.
2. Vincular conta a pagar à NF/recebimento para impedir duplicidade.
3. Implementar máquina de estados para os três documentos.
4. Criar alçadas de aprovação e trilha de decisão.
5. Separar pedidos por fornecedor ao converter cotação mista.
6. Preservar versões ou impedir edição depois do envio/aprovação.
7. Cobrir concorrência e duplo clique em testes.

## Melhorias

- Mapa comparativo com preço, prazo, frete, condição e avaliação.
- Aprovação por valor, categoria e centro de custo.
- Lead time previsto versus realizado.
- Avaliação de fornecedor por qualidade, prazo e divergência.
- Contratos e compras recorrentes.
- Sugestão de compra baseada em estoque, consumo e produção.

## Páginas sugeridas

| Página | Conteúdo principal | Prioridade |
| --- | --- | --- |
| Recebimento de mercadorias | Quantidade, lote, divergência, aceite e NF | P1 |
| Mapa de cotações | Comparação por item e fornecedor, motivo da escolha | P1 |
| Central de aprovações | Alçada, valor, orçamento, decisão e histórico | P1 |
| Necessidades de compra | Estoque, reservas, produção e sugestão | P2 |
| Contratos de fornecimento | Vigência, limite, preço, reajuste e consumo | P2 |
| Avaliação de fornecedores | Prazo, qualidade, devolução e score | P2 |
| Acompanhamento de pedidos | Previsão, atraso, recebimento e pendências | P2 |

## Ordem de execução

1. Eliminar duplicidade de recebimento e integração fiscal.
2. Corrigir transações, estados e conversões.
3. Implementar alçadas e recebimento por item.
4. Evoluir comparativos e desempenho de fornecedores.

## Critérios de aceite

- [ ] Receber o mesmo pedido duas vezes não duplica saldo nem título.
- [ ] NF vinculada não repete efeitos já lançados pelo recebimento.
- [ ] Cotação com fornecedores diferentes gera documentos coerentes.
- [ ] Conversão repetida retorna o documento existente.
- [ ] Aprovação respeita alçada e segregação definidas.
- [ ] Cancelamento preserva ou estorna corretamente os efeitos.

## Arquivos principais analisados

- `app/Livewire/Compras/SolicitacoesCompra.php`
- `app/Livewire/Compras/Cotacoes.php`
- `app/Livewire/Compras/PedidosCompra.php`
- `app/Services/PurchaseInvoiceMatchingService.php`
- `routes/compras.php`

