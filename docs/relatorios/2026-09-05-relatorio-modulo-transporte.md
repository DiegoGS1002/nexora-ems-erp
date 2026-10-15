# Relatório do módulo de Transporte

Data: 05/09/2026  
Escopo: rotas, roteirização, agendamento, monitoramento, motoristas, romaneios, rastreamento, manutenção e relatórios.

## Visão geral

O módulo possui gestão básica de rotas, uma tela de roteirização com mapa e um componente funcional de agendamento de entregas. Grande parte das demais opções ainda aponta para telas de desenvolvimento. Antes de ampliar rastreamento, convém consolidar expedição, viagem, romaneio e prova de entrega como um fluxo único.

## Falhas e riscos

| ID | Prioridade | Achado | Evidência e impacto | Ajuste necessário |
| --- | --- | --- | --- | --- |
| TRA-01 | P0 | Marcar agendamento como entregue altera o pedido diretamente | `AgendamentoEntregas::changeStatus()` atualiza SalesOrder sem serviço de domínio | Criar serviço de entrega transacional e validar pré-condições |
| TRA-02 | P1 | Status do agendamento aceita string sem transição validada | Método grava o valor recebido diretamente | Validar enum e máquina de estados |
| TRA-03 | P1 | Entrega e pedido não são atualizados atomicamente | Atualizações são separadas | Transação e bloqueio do agendamento/pedido |
| TRA-04 | P1 | Reagendamento não é transacional | Cria novo registro e depois altera o original | Executar ambos na mesma transação e impedir correntes duplicadas |
| TRA-05 | P1 | Entrega pode ser excluída sem preservar ocorrência | `delete()` remove o agendamento diretamente | Cancelar com motivo e manter histórico |
| TRA-06 | P1 | Veículo, motorista e capacidade não são validados em conjunto | Campos podem ser nulos e não há conflito/capacidade visível | Validar disponibilidade, habilitação, peso, volume e janela |
| TRA-07 | P1 | Várias rotas CRUD apontam para métodos ausentes | `Route::resource()` registra ações; controllers examinados implementam somente `index()` | Restringir com `only('index')` ou implementar as operações |
| TRA-08 | P1 | Romaneio e relatório possuem impressão 501 | Ações expostas abortam como não implementadas | Ocultar até concluir ou implementar documento validado |
| TRA-09 | P2 | Monitoramento, motoristas, rastreamento e manutenção são placeholders | Controllers retornam `system.desenvolvimento` | Identificar disponibilidade real no catálogo de módulos |
| TRA-10 | P2 | Roteirização parece limitada ao navegador | Tela usa Google Maps e lógica cliente; persistência/otimização não foi evidenciada no controller | Salvar plano, pontos, versão, distância e responsável |
| TRA-11 | P2 | Ausência visível de escopo empresarial | Rotas e agendamentos usam consultas diretas | Aplicar empresa e policies em pedidos, veículos, rotas e motoristas |
| TRA-12 | P2 | Não há prova de entrega no fluxo examinado | Status entregue registra apenas `delivered_at` | Guardar recebedor, evidência, ocorrência e localização conforme política |

## Ajustes necessários

1. Criar entidades claras para expedição, viagem, parada e entrega.
2. Centralizar mudanças de status e integração com pedido.
3. Restringir as rotas resource ainda não implementadas.
4. Validar conflito de agenda e capacidade antes da confirmação.
5. Implementar cancelamento/reagendamento auditável.
6. Definir política de localização, retenção e acesso.
7. Tratar indisponibilidade do provedor de mapas e custos de integração.

## Melhorias

- Consolidação de pedidos em cargas e viagens.
- Otimização por capacidade, janela, distância e prioridade.
- Aplicativo ou página móvel para motorista.
- Ocorrências de entrega e logística reversa.
- Custos por rota, veículo, quilômetro e entrega.
- Plano de manutenção por data e quilometragem.
- Indicadores OTIF, atraso, ocupação e custo por entrega.

## Páginas sugeridas

| Página | Conteúdo principal | Prioridade |
| --- | --- | --- |
| Expedição e cargas | Pedidos, volumes, conferência, veículo e fechamento | P1 |
| Romaneio | Carga, sequência, itens, impressão e histórico | P1 |
| Viagens e paradas | Rota planejada, execução, horários e ocorrências | P1 |
| Prova de entrega | Recebedor, foto/assinatura, data e divergência | P1 |
| Painel do motorista | Próxima parada, navegação, ocorrência e confirmação | P2 |
| Manutenção de frota | Plano, OS, custos, odômetro e indisponibilidade | P2 |
| Rastreamento | Última posição, horário, histórico e sinal desatualizado | P3 |
| Indicadores logísticos | OTIF, ocupação, custo, atraso e produtividade | P2 |

## Ordem de execução

1. Corrigir estados, transações e rotas inválidas.
2. Entregar expedição, viagem, romaneio e prova de entrega.
3. Implementar capacidade, conflitos e custos.
4. Adicionar rastreamento após validar fornecedor e privacidade.

## Critérios de aceite

- [ ] Entrega e pedido mudam de estado na mesma transação.
- [ ] Transição inválida é recusada.
- [ ] Reagendamento preserva histórico e não cria duplicidade.
- [ ] Rota própria não aponta para método inexistente.
- [ ] Veículo indisponível ou sem capacidade não é alocado.
- [ ] Prova de entrega fica vinculada ao pedido e protegida por permissão.

## Arquivos principais analisados

- `app/Livewire/Logistica/AgendamentoEntregas.php`
- `app/Http/Controllers/RouteManagementController.php`
- `app/Http/Controllers/RoutingController.php`
- `app/Http/Controllers/RomaneioController.php`
- `app/Http/Controllers/VehicleTrackingController.php`
- `app/Http/Controllers/VehicleMaintenanceController.php`
- `routes/logistica.php`

