# Relatório do módulo de Produção

Data: 05/09/2026  
Escopo: ordens de produção, produtos produzidos, componentes, progresso e integração com estoque.

## Visão geral

O módulo já permite cadastrar ordens, relacionar produtos e insumos, registrar progresso, pausar e concluir a produção. A gravação principal usa transações. O maior risco está na conclusão: mudanças de status e lançamentos de estoque podem ser repetidos ou ficar parcialmente aplicados.

## Falhas e riscos

| ID | Prioridade | Achado | Evidência e impacto | Ajuste necessário |
| --- | --- | --- | --- | --- |
| PRD-01 | P0 | Concluir a mesma OP pode movimentar estoque mais de uma vez | `OrdemProducao::changeStatus()` não bloqueia uma segunda transição para `Completed`; cada chamada cria novas saídas e entradas | Criar serviço idempotente, bloquear a OP e registrar `stock_posted_at` ou vínculo único dos movimentos |
| PRD-02 | P0 | A conclusão não é uma única operação atômica | Quantidade produzida é alterada antes da transação; status é salvo depois dela | Colocar validação, progresso, estoque e status na mesma transação com `lockForUpdate()` |
| PRD-03 | P1 | Exclusão de OP concluída não estorna estoque | `delete()` remove a ordem diretamente, sem analisar movimentos originados por ela | Impedir exclusão de OP efetivada; oferecer cancelamento/estorno auditável |
| PRD-04 | P1 | Transições de status não seguem uma máquina de estados | O método aceita qualquer caso válido do enum, sem verificar a origem | Definir transições permitidas: planejada → em produção → pausada/concluída; cancelamento com motivo |
| PRD-05 | P1 | Conclusão força produção igual à meta | Ao concluir, `produced_quantity` recebe `target_quantity`, descartando apontamento real de sobra ou perda | Exigir quantidade final real e registrar refugo, retrabalho e variação |
| PRD-06 | P1 | Consumo usa quantidade planejada | Insumos saem pelo `required_qty`, sem consumo real por apontamento | Guardar consumo previsto e realizado separadamente |
| PRD-07 | P1 | Estoque insuficiente não é validado na conclusão | Componentes são decrementados diretamente | Reservar ou validar saldo, respeitando a configuração de estoque negativo |
| PRD-08 | P1 | Atualização de estoque está duplicada no domínio | Produção cria `StockMovement` e também altera `Product::stock` localmente | Centralizar no mesmo serviço transacional usado por Compras, Fiscal e Estoque |
| PRD-09 | P2 | Progresso pode superar a meta | A validação exige apenas valor não negativo | Limitar ou exigir justificativa para excedente e preservar histórico dos apontamentos |
| PRD-10 | P2 | Falta escopo empresarial e autorização por ação | Consultas e `findOrFail()` não demonstram policy nem filtro empresarial | Aplicar policies e escopo a OPs, produtos, insumos e custos |

## Ajustes necessários

1. Criar `ProductionOrderService` para iniciar, pausar, apontar, concluir e estornar.
2. Adicionar vínculo do movimento de estoque com a OP e constraint contra duplicidade.
3. Proibir edição estrutural depois do início, salvo fluxo de revisão aprovado.
4. Registrar usuário, momento, quantidade anterior/nova e motivo em cada mudança.
5. Calcular custo real com consumo, perdas e horas, mantendo o estimado para comparação.
6. Incluir testes de concorrência, repetição da conclusão, falha intermediária e estoque insuficiente.

## Melhorias

- Planejamento por capacidade, prioridade e data prometida.
- Reserva de insumos na liberação da OP.
- Lotes e rastreabilidade de matéria-prima até o produto acabado.
- Indicadores de eficiência, perda, atraso e custo previsto versus realizado.
- Apontamento por etapa, operador, máquina e turno.
- Anexos de instrução, inspeção e controle de qualidade.

## Páginas sugeridas

| Página | Conteúdo principal | Prioridade |
| --- | --- | --- |
| Fichas técnicas/BOM | Versões, componentes, quantidades, perdas e vigência | P1 |
| Apontamentos de produção | Quantidade boa, refugo, retrabalho, tempo e operador | P1 |
| Planejamento de produção | Calendário, capacidade, materiais e prioridades | P2 |
| Necessidade de materiais | Demanda por período, saldo, reserva e falta prevista | P2 |
| Controle de qualidade | Inspeções, não conformidades, bloqueio e liberação | P2 |
| Rastreabilidade de lotes | Origem de insumos, OP, lote final e destinos | P2 |
| Custos industriais | Custo previsto/real, variações e detalhamento | P2 |

## Ordem de execução

1. Corrigir idempotência, transação e transições de status.
2. Centralizar movimentos de estoque e validar disponibilidade.
3. Implementar ficha técnica versionada e apontamento real.
4. Evoluir planejamento, qualidade e custos.

## Critérios de aceite

- [ ] Concluir a mesma OP duas vezes não repete movimentos.
- [ ] Uma falha durante a conclusão não deixa estoque ou status parcial.
- [ ] OP concluída não é excluída sem estorno formal.
- [ ] Consumo e produção reais permanecem auditáveis.
- [ ] Usuário sem permissão não altera status nem visualiza custos.
- [ ] Dados de empresas diferentes ficam isolados.

## Arquivos principais analisados

- `app/Livewire/Producao/OrdemProducao.php`
- `app/Models/ProductionOrder.php`
- `app/Models/ProductionOrderProduct.php`
- `app/Models/ProductionItem.php`
- `routes/producao.php`

