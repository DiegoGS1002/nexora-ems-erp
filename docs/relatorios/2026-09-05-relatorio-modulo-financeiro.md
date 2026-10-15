# Relatório do módulo Financeiro

Data: 05/09/2026  
Escopo: contas a pagar, contas a receber, bancos, transferências, fluxo de caixa, plano de contas e relatórios.

## Visão geral

O módulo possui telas para títulos, baixas, reagendamentos, contas bancárias, transferências, fluxo de caixa e plano de contas. Há serviços para pagar e receber, porém os lançamentos ainda não formam um razão financeiro único. Os riscos mais graves são conversão de moeda, ausência de histórico de liquidações e transferências sem atomicidade.

## Falhas e riscos

| ID | Prioridade | Achado | Evidência e impacto | Ajuste necessário |
| --- | --- | --- | --- | --- |
| FIN-01 | P0 | Valor de pagamento pode ser multiplicado por 100 | O modal inicia `100.50`; a conversão remove o ponto e resulta em `10050` | Normalizar moeda antes de validar e transportar valor canônico |
| FIN-02 | P0 | O mesmo ocorre em recebimentos | `ContasReceber::registerReceipt()` usa a mesma conversão | Aplicar componente e value object monetário comum |
| FIN-03 | P0 | Transferência bancária não é transacional | Débito e crédito ocorrem em comandos separados | Transação, bloqueio das duas contas e registro único da transferência |
| FIN-04 | P1 | Transferência não deixa histórico financeiro próprio | Apenas os saldos das contas são alterados | Criar entidade de transferência com origem, destino, valor, usuário e estorno |
| FIN-05 | P1 | Recebimento parcial substitui o valor anterior | Serviço grava `received_amount` sem eventos de recebimento | Criar liquidações incrementais e saldo residual |
| FIN-06 | P1 | KPI pendente usa valor integral de título parcial | Títulos Partial entram pelo total original | Somar `amount - received_amount` ou saldo calculado do ledger |
| FIN-07 | P1 | Parcial vencido não passa para atraso | Sincronização trata apenas Pending | Definir estado/saldo de parcial vencido ou calcular atraso independentemente do status |
| FIN-08 | P1 | Conta a pagar é marcada paga independentemente do valor | `registerPayment()` sempre usa Paid | Validar valor, suportar parcial ou recusar divergência |
| FIN-09 | P1 | Regime de caixa usa vencimento e valor original | `FluxoCaixa` filtra `due_date_at` e soma `amount`, mesmo para títulos pagos/recebidos | Em caixa, usar data e valor das liquidações; em competência, data definida pela regra contábil |
| FIN-10 | P1 | Saldo inicial representa saldo bancário atual | O fluxo soma os saldos atuais e depois reaplica movimentos do período | Calcular saldo na data inicial a partir do razão ou snapshots conciliados |
| FIN-11 | P1 | Integrações podem duplicar títulos | Compras, Fiscal e Folha criam contas a pagar sem vínculo único uniforme | Criar chave de origem e constraint por documento/parcela |
| FIN-12 | P2 | Relatórios financeiros e impressão estão incompletos | Controller usa tela de desenvolvimento e `abort(501)` | Concluir DRE/relatórios ou retirar ações indisponíveis |

## Ajustes necessários

1. Criar entidades de pagamento e recebimento com estorno e conciliação.
2. Padronizar dinheiro em todo o módulo.
3. Construir razão bancário; saldo da conta deve decorrer de lançamentos conciliáveis.
4. Tornar transferências atômicas e idempotentes.
5. Definir estados e transições para parcial, vencido, pago, cancelado e estornado.
6. Vincular títulos à origem: pedido, NF, folha, venda ou lançamento manual.
7. Aplicar autorização, empresa e permissões para visualizar custos e executar baixas.

## Melhorias

- Conciliação bancária por arquivo e API.
- Projeção de caixa por cenários e probabilidade.
- Aprovação de pagamentos por alçada.
- Rateio por centro de custo e projeto.
- Recorrência, juros, desconto, multa e retenções.
- Fechamento de período e bloqueio retroativo.
- Aging de clientes e fornecedores.

## Páginas sugeridas

| Página | Conteúdo principal | Prioridade |
| --- | --- | --- |
| Conciliação bancária | Extrato, sugestões, diferenças, confirmação e desfazer | P1 |
| Razão bancário | Saldo inicial, lançamentos, conciliação e saldo final | P1 |
| Liquidações | Pagamentos/recebimentos parciais, estornos e documentos | P1 |
| Aprovação de pagamentos | Lotes, alçadas, vencimento e decisão | P2 |
| Cobrança e inadimplência | Saldo, atraso, contatos e promessa de pagamento | P2 |
| DRE gerencial | Receitas, custos, despesas e comparativos | P2 |
| Centros de custo | Orçamento, realizado, rateios e variações | P2 |
| Fechamento financeiro | Pendências, conciliação, bloqueio e reabertura | P2 |

## Ordem de execução

1. Corrigir moeda e transferências.
2. Implantar liquidações e saldo residual.
3. Corrigir fluxo de caixa e integrações duplicadas.
4. Adicionar conciliação, aprovação e relatórios.

## Critérios de aceite

- [ ] `100.50`, `100,50` e `1.234,56` resultam no valor esperado.
- [ ] Transferência falha integralmente se débito ou crédito falhar.
- [ ] Duas transferências simultâneas não ultrapassam o saldo.
- [ ] Recebimentos sucessivos preservam histórico e liquidam o saldo exato.
- [ ] Fluxo por caixa usa datas e valores efetivamente liquidados.
- [ ] Cada documento de origem gera cada parcela uma única vez.

## Arquivos principais analisados

- `app/Livewire/Financeiro/ContasPagar.php`
- `app/Livewire/Financeiro/ContasReceber.php`
- `app/Livewire/Financeiro/ContaBancaria.php`
- `app/Livewire/Financeiro/FluxoCaixa.php`
- `app/Livewire/Financeiro/PlanoContas.php`
- `app/Services/ContasPagarService.php`
- `app/Services/ContasReceberService.php`

