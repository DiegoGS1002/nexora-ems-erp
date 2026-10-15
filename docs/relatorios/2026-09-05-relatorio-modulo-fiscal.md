# Relatório do módulo Fiscal

Data: 05/09/2026  
Escopo: NF-e de saída e entrada, numeração, transmissão, cancelamento, CC-e, distribuição, escrituração, arquivos e integrações.

## Visão geral

O módulo fiscal é uma das áreas mais desenvolvidas do projeto. Há serviços para transmissão, numeração, manifestação, distribuição, matching de compras, escrituração, reversão, DANFE e arquivamento, além de testes específicos. Os controles mais urgentes são autorização uniforme, isolamento empresarial e consistência entre escrituração e saldo materializado de estoque.

## Falhas e riscos

| ID | Prioridade | Achado | Evidência e impacto | Ajuste necessário |
| --- | --- | --- | --- | --- |
| FIS-01 | P0 | API fiscal não aplica explicitamente a policy existente | `NFeController` transmite, cancela e entrega arquivos sem chamada visível a `authorize()` | Aplicar policy a cada nota e permissão específica para consulta geral |
| FIS-02 | P0 | Download de XML/DANFE depende apenas do binding e autenticação | Um ID conhecido pode alcançar arquivo se não houver escopo fora do controller | Autorizar recurso, validar empresa e testar tentativa cruzada |
| FIS-03 | P0 | Escrituração registra movimento sem atualizar saldo do produto | `FiscalEntryPostingService` cria `StockMovement`, mas o sistema também usa `Product::stock` | Passar pelo serviço central de estoque ou abandonar o saldo duplicado |
| FIS-04 | P0 | Reversão cria ajuste negativo sem atualizar saldo materializado | `FiscalEntryReversalService` registra movimento, sem alterar `Product::stock` | Estornar pelo mesmo serviço de ledger e saldo |
| FIS-05 | P0 | Tela permite salvar status `authorized` manualmente | `NotaFiscal::save()` aceita status e define `authorized_at` | Status fiscal deve resultar de protocolo/evento validado, com modo de migração separado |
| FIS-06 | P1 | Valor da nota usa conversão frágil de moeda | Remove todos os pontos e troca vírgula por ponto depois de validar como numérico | Normalizar antes da validação e usar decimal/centavos |
| FIS-07 | P1 | Regras de autorização variam entre API e Livewire | Há policy, mas componentes usam buscas e ações diretas em vários pontos | Aplicar `Gate`/policy em transmitir, cancelar, corrigir, excluir e escriturar |
| FIS-08 | P1 | Consulta por chave pode permitir pesquisa fora do escopo pretendido | Endpoint recebe qualquer chave de 44 dígitos | Definir permissão e registrar consulta; ocultar dados não necessários |
| FIS-09 | P1 | Jobs precisam de contexto empresarial verificável | Jobs buscam nota por ID; serviços dependem de configuração/emissor | Definir emissor pela nota dentro do job e testar múltiplas empresas |
| FIS-10 | P1 | Entrega por e-mail é síncrona no serviço examinado | Geração de PDF e envio podem prolongar requisição | Enfileirar, registrar tentativas e impedir reenvio acidental |
| FIS-11 | P1 | Retorno de erro expõe mensagem interna | API responde diretamente com `$e->getMessage()` e status 500 | Gerar código de correlação, resposta segura e log técnico protegido |
| FIS-12 | P2 | Backup agendado não comprova restauração | Há cópia diária declarada, mas nenhum teste de restore observado | Criar verificação, retenção, alerta e exercício de restauração |

## Ajustes necessários

1. Aplicar autorização por empresa e ação em API, Livewire, downloads e jobs.
2. Bloquear alteração manual de estados controlados pela SEFAZ.
3. Integrar escrituração e reversão ao ledger único de estoque.
4. Definir idempotência por nota, evento, protocolo e ambiente.
5. Padronizar erros da SEFAZ sem expor detalhes internos ao usuário final.
6. Garantir segregação entre homologação e produção, inclusive em numeração e arquivos.
7. Homologar timeout, retorno tardio, consulta por recibo e reconciliação.

## Melhorias

- Painel operacional de rejeições e pendências.
- Alertas de validade do certificado e falha de distribuição.
- Linha do tempo imutável de eventos por documento.
- Busca por chave, protocolo, pedido e cliente com escopo autorizado.
- Monitoramento de filas, tentativas e tempo de autorização.
- Política de retenção, integridade e restauração de XML.

## Páginas sugeridas

| Página | Conteúdo principal | Prioridade |
| --- | --- | --- |
| Centro de operações fiscais | Fila, rejeições, recibos, tentativas e ações seguras | P1 |
| Monitor de certificados | Validade, empresa, ambiente e alertas | P1 |
| Eventos da NF-e | Autorização, cancelamento, CC-e, manifestação e protocolos | P1 |
| Pendências de escrituração | Divergências, vínculo com pedido e bloqueios | P1 |
| Distribuição DF-e | NSU, documentos, manifestação e download | P2 |
| Auditoria de arquivos | XML presente, hash, backup e restauração | P2 |
| Relatórios fiscais | Totais, operações, tributos e divergências | P2 |

## Ordem de execução

1. Corrigir autorização e alteração manual de estados.
2. Corrigir integração de estoque da entrada e reversão.
3. Homologar idempotência e retornos assíncronos.
4. Implantar centro operacional, certificados e auditoria de arquivos.

## Critérios de aceite

- [ ] Usuário de outra empresa não acessa nota, XML ou DANFE.
- [ ] Status autorizado só surge de retorno fiscal válido ou migração controlada.
- [ ] Escriturar e reverter alteram movimentos e saldo exatamente uma vez.
- [ ] Repetir transmissão/cancelamento não duplica evento.
- [ ] Job usa a configuração da empresa da própria nota.
- [ ] Backup possui teste documentado de restauração.

## Arquivos principais analisados

- `app/Http/Controllers/Api/NFeController.php`
- `app/Livewire/Fiscal/NotaFiscal.php`
- `app/Livewire/Fiscal/NFeGestao.php`
- `app/Livewire/Fiscal/NotaFiscalEntrada.php`
- `app/Services/NFeService.php`
- `app/Services/FiscalEntryPostingService.php`
- `app/Services/FiscalEntryReversalService.php`
- `app/Jobs/TransmitFiscalNote.php`
- `app/Jobs/ReconcileFiscalNoteStatus.php`

