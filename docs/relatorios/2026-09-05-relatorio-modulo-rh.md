# Relatório do módulo de RH

Data: 05/09/2026  
Escopo: jornada, batida de ponto, folha, holerite e integração financeira.

## Visão geral

O módulo oferece jornada, ponto com localização, folha, itens de provento/desconto e holerite. O fluxo de ponto é simples e adequado a um primeiro estágio. Os riscos mais relevantes estão em privacidade, autorização de holerites, consistência das marcações e duplicidade financeira no pagamento da folha.

## Falhas e riscos

| ID | Prioridade | Achado | Evidência e impacto | Ajuste necessário |
| --- | --- | --- | --- | --- |
| RH-01 | P0 | Impressão de holerite não demonstra autorização do funcionário | `HoleriteController::print()` busca qualquer folha por ID | Autorizar RH/gestor ou o próprio colaborador, sempre dentro da empresa |
| RH-02 | P0 | Marcar folha como paga pode duplicar conta a pagar | Toda chamada de `PayrollService::markAsPaid()` cria novo título | Transação, vínculo único e operação idempotente |
| RH-03 | P1 | Serviço permite pagar folha em qualquer estado | Não verifica se está fechada ou já paga | Máquina de estados e validação no serviço |
| RH-04 | P1 | Exclusão de folha paga não trata contrapartida financeira | `deletePayroll()` remove itens e folha diretamente | Impedir exclusão; usar estorno/reabertura autorizada |
| RH-05 | P1 | E-mail é usado para vincular usuário a funcionário | `getEmployeeByEmail()` pode ser ambíguo ou mudar | Criar `user_id` único no funcionário e migrar vínculos |
| RH-06 | P1 | Registro de ponto não é protegido contra concorrência | Duas requisições podem criar/alterar a mesma marcação do dia | Constraint funcionário/data e transação com bloqueio |
| RH-07 | P1 | Fluxo suporta apenas um intervalo | Campos fixos `break_start` e `break_end` não representam múltiplas pausas | Modelar marcações/eventos e calcular jornada a partir deles |
| RH-08 | P1 | Ajuste de ponto pode apagar rastreabilidade | Jornada permite editar e excluir registros | Manter original, solicitação, motivo, aprovador e versão corrigida |
| RH-09 | P1 | Localização e IP exigem governança | TimeRecord guarda IP, latitude e longitude | Definir finalidade, acesso, retenção e transparência ao colaborador |
| RH-10 | P1 | Bases do holerite são aproximações | Controller calcula bases com totais genéricos; não há memória de cálculo legal completa visível | Separar cálculo oficial, incidências por verba e tabelas versionadas |
| RH-11 | P2 | Data de referência é mutada no serviço | `startOfMonth()` altera o objeto Carbon recebido | Usar cópia/objeto imutável para evitar efeitos laterais |
| RH-12 | P2 | Consultas não demonstram escopo empresarial | Funcionários, folhas e marcações são consultados diretamente | Escopo por empresa e policies para dados sensíveis |

## Ajustes necessários

1. Implementar autorização específica para folha, holerite, ponto e localização.
2. Vincular usuário e funcionário por chave, não por e-mail.
3. Modelar marcações imutáveis e ajustes aprovados.
4. Tornar pagamento da folha transacional, idempotente e reversível.
5. Impedir alteração de itens após fechamento sem reabertura formal.
6. Versionar regras, incidências e tabelas usadas no cálculo.
7. Validar requisitos trabalhistas e de privacidade com os responsáveis da empresa.

## Melhorias

- Banco de horas, escalas e múltiplos intervalos.
- Solicitação e aprovação de ajuste pelo colaborador.
- Férias, afastamentos, benefícios e documentos.
- Fechamento de ponto antes da folha.
- Memória de cálculo por verba.
- Portal do colaborador com acesso restrito aos próprios dados.
- Alertas de jornada incompleta e inconsistências.

## Páginas sugeridas

| Página | Conteúdo principal | Prioridade |
| --- | --- | --- |
| Espelho de ponto | Marcações, totais, ocorrências e assinatura | P1 |
| Solicitações de ajuste | Motivo, evidência, aprovação e histórico | P1 |
| Fechamento de ponto | Pendências, horas, aprovação e bloqueio | P1 |
| Banco de horas | Créditos, débitos, saldo e compensações | P2 |
| Portal do colaborador | Holerites, ponto, documentos e solicitações próprias | P1 |
| Férias e afastamentos | Período, aprovação, saldo e documentos | P2 |
| Auditoria da folha | Alterações, incidências, cálculo e aprovação | P2 |

## Ordem de execução

1. Proteger holerites e dados por empresa/usuário.
2. Corrigir pagamento e exclusão de folha.
3. Tornar marcações e ajustes auditáveis.
4. Evoluir cálculo, fechamento e portal do colaborador.

## Critérios de aceite

- [ ] Colaborador acessa apenas os próprios holerites.
- [ ] Repetir pagamento não duplica lançamento financeiro.
- [ ] Folha paga não pode ser alterada ou excluída diretamente.
- [ ] Duas batidas simultâneas não criam registros duplicados.
- [ ] Ajuste preserva marcação original, motivo e aprovador.
- [ ] Localização e IP possuem controle de acesso e retenção definidos.

## Arquivos principais analisados

- `app/Livewire/Rh/BatidaPonto.php`
- `app/Livewire/Rh/JornadaTrabalho.php`
- `app/Livewire/Rh/FolhaPagamento.php`
- `app/Livewire/Rh/Holerite.php`
- `app/Services/PontoService.php`
- `app/Services/PayrollService.php`
- `app/Http/Controllers/HoleriteController.php`

