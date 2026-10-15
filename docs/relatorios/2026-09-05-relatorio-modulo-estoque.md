# Relatório do módulo de Estoque

Data: 05/09/2026  
Escopo: movimentações, saldo de produtos, API, filtros e integrações com Compras, Fiscal e Produção.

## Visão geral

O módulo possui tela Livewire para entradas, saídas, ajustes e transferências, além de histórico por produto e usuário. A arquitetura atual mantém simultaneamente um saldo no produto e uma tabela de movimentos. Essa estratégia exige atomicidade e um serviço único, que ainda não estão presentes em todos os fluxos.

## Falhas e riscos

| ID | Prioridade | Achado | Evidência e impacto | Ajuste necessário |
| --- | --- | --- | --- | --- |
| EST-01 | P0 | API usa a entidade errada | `StockMovementApiController` manipula `Entrance`, com apenas nome e descrição; a tela usa `StockMovement` | Unificar API e tela em um serviço de movimentação |
| EST-02 | P0 | Edição não recompõe saldo | A tela atualiza o movimento, mas não desfaz o efeito anterior nem aplica a diferença | Estornar o movimento anterior e lançar o novo na mesma transação |
| EST-03 | P0 | Movimento e saldo não são atômicos | Criação/exclusão grava movimento e produto separadamente | Usar transação e bloqueio do produto |
| EST-04 | P0 | Escrituração fiscal cria movimento sem aumentar `Product::stock` | `FiscalEntryPostingService` registra `StockMovement`, mas não altera o saldo mantido no produto | Fazer todas as origens chamarem o mesmo serviço de saldo |
| EST-05 | P0 | Estorno fiscal também não altera o saldo do produto | `FiscalEntryReversalService` cria ajuste negativo sem atualizar `Product::stock` | Corrigir no serviço central e testar lançamento/estorno completo |
| EST-06 | P1 | Saída pode gerar saldo negativo | `Movimentacao::save()` não valida saldo nem usa a configuração de permissão | Aplicar política de estoque negativo com autorização quando prevista |
| EST-07 | P1 | Conclusão/recebimento repetido duplica saldo | Produção e Compras executam incrementos/decrementos ao mudar status sem proteção suficiente | Usar chave idempotente por origem, documento, item e evento |
| EST-08 | P1 | Transferência não tem origem e destino de estoque | O tipo `transfer` não altera saldo e o modelo não representa depósitos | Criar locais, saldo por local e movimentos pareados |
| EST-09 | P1 | Filtro exclui horários do último dia | `whereBetween(created_at, [Y-m-d, Y-m-d])` usa meia-noite como limite superior | Usar início do dia e início exclusivo do dia seguinte |
| EST-10 | P2 | Ajuste sempre soma quantidade | Ajuste é tratado como entrada; não há diferença positiva/negativa explicitada | Definir quantidade assinada ou direção do ajuste e exigir motivo |
| EST-11 | P2 | Exclusão apaga história operacional | O movimento pode ser removido e o saldo revertido | Preferir estorno vinculado e manter lançamento original imutável |
| EST-12 | P2 | Ausência visível de escopo empresarial | Produtos e movimentos examinados não demonstram filtro empresarial | Definir se catálogo/saldo é global ou por empresa e aplicar constraints |

## Ajustes necessários

1. Criar um ledger de estoque imutável com serviço transacional único.
2. Decidir se `Product::stock` será saldo materializado ou se será calculado; se materializado, reconciliá-lo automaticamente.
3. Vincular movimentos a documento de origem, item, empresa, depósito e movimento de estorno.
4. Criar constraints contra duplicidade de eventos externos.
5. Bloquear o saldo durante saídas concorrentes.
6. Migrar ou descontinuar `Entrance` após verificar uso legado.
7. Executar rotina inicial de conciliação entre saldo e movimentos.

## Melhorias

- Estoque reservado, disponível, em trânsito e bloqueado.
- Custo médio e histórico de valorização.
- Alertas de mínimo, ruptura, validade e lote.
- Leitura de código de barras e importação por coletor.
- Endereçamento físico por depósito, rua, posição e lote.
- Inventário rotativo com recontagem e aprovação de divergências.

## Páginas sugeridas

| Página | Conteúdo principal | Prioridade |
| --- | --- | --- |
| Kardex do produto | Saldo inicial, movimentos, estornos, custo e origem | P1 |
| Inventário físico | Contagem cega, recontagem, divergências e aprovação | P1 |
| Transferências | Expedição, trânsito, recebimento parcial e cancelamento | P1 |
| Saldos por depósito | Físico, reservado, disponível e bloqueado | P2 |
| Lotes e validade | Lote, fabricação, validade, rastreabilidade e alertas | P2 |
| Divergências | Saldo materializado versus ledger, causa e correção | P1 |
| Reposição | Estoque mínimo, consumo, lead time e sugestão de compra | P2 |

## Ordem de execução

1. Unificar o serviço e corrigir Fiscal, Compras, Produção e API.
2. Impedir duplicidade, saldo negativo indevido e exclusão destrutiva.
3. Conciliar dados existentes.
4. Adicionar depósitos, inventário e reservas.

## Critérios de aceite

- [ ] Todo movimento da API aparece na tela e afeta saldo uma vez.
- [ ] Editar ou estornar mantém ledger e saldo conciliados.
- [ ] Escrituração e estorno fiscal alteram saldo corretamente.
- [ ] Operações concorrentes respeitam disponibilidade.
- [ ] O último dia do filtro é incluído por completo.
- [ ] Transferência mantém a soma global e altera os locais corretos.

## Arquivos principais analisados

- `app/Livewire/Estoque/Movimentacao.php`
- `app/Http/Controllers/Api/StockMovementApiController.php`
- `app/Models/StockMovement.php`
- `app/Models/Entrance.php`
- `app/Services/FiscalEntryPostingService.php`
- `app/Services/FiscalEntryReversalService.php`

