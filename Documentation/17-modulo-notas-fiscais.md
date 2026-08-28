# Módulo de Notas Fiscais — NF-e, NFC-e e entradas

## 1. Objetivo e estado atual

O módulo fiscal controla notas de saída, notas de entrada, integração com a SEFAZ, XML, DANFE, cancelamento, Carta de Correção e numeração fiscal.

Este documento representa a base do módulo após as correções dos itens 2.3 a 2.8. A ampliação fiscal dos problemas 3 a 6 está documentada em [`18-ajustes-fiscais-nfe-2026.md`](18-ajustes-fiscais-nfe-2026.md).

| Responsabilidade | Classe |
|---|---|
| Comunicação com a SEFAZ | `App\Services\NFeService` |
| Numeração fiscal | `App\Services\FiscalNumberService` |
| DANFE | `App\Services\DanfeService` |
| Nota, itens e eventos | `FiscalNote`, `FiscalNoteItem`, `FiscalNoteEvent` |
| Sequências | `FiscalNumberSequence` |
| Listagem de saídas | `App\Livewire\Fiscal\NotaFiscal` |
| Gestão de uma NF-e | `App\Livewire\Fiscal\NFeGestao` |
| Notas de entrada | `App\Livewire\Fiscal\NotaFiscalEntrada` |
| API | `App\Http\Controllers\Api\NFeController` |

## 2. Funcionalidades anteriores e atuais

### 2.1 Funções que já existiam

- Cadastro manual de NF-e e NFC-e.
- Pré-NF-e a partir de pedido de venda.
- Montagem e assinatura do XML.
- Transmissão síncrona e assíncrona.
- Consulta de recibo, chave e disponibilidade da SEFAZ.
- Cancelamento.
- Armazenamento do XML enviado.
- Geração, visualização e download do DANFE.
- Download do XML.
- Listagem, filtros, indicadores, edição e exclusão.
- Componente de notas fiscais de entrada e importação de XML.
- Escrituração de entrada, estoque e contas a pagar.

### 2.2 Funções acrescentadas ou corrigidas

- Resultado individual em `protNFe/infProt`, selecionado pela chave.
- Separação entre resultado do lote e resultado fiscal.
- Estados de autorização, rejeição, denegação, processamento, falha e indeterminação.
- Preservação do recibo assíncrono.
- Composição e armazenamento do `nfeProc`.
- Separação entre XML enviado e XML oficial.
- DANFE e download baseados no `nfeProc` após autorização.
- Rota funcional de NF-e de entrada.
- Carta de Correção eletrônica — CC-e.
- Cancelamento com contrato uniforme `cancelled` e resultado individual.
- Numeração transacional por empresa, tipo, série e ambiente.
- Restrição única de número fiscal no banco.
- Pedido preservado em seu estado comercial enquanto a NF-e é rascunho ou não autorizada.
- Pedido alterado para `NfTransmitida` somente após protocolo `100` ou `150`.

### 2.3 Antes e agora

| Área | Antes | Agora |
|---|---|---|
| Transmissão | Primeiro `cStat` | `infProt` correspondente à chave |
| XML | Apenas XML assinado | XML enviado preservado e `nfeProc` oficial |
| DANFE | Podia usar XML sem protocolo | Usa o XML protocolado quando autorizado |
| Entrada | Placeholder | `NotaFiscalEntrada` acessível |
| CC-e | Método inexistente | Envio, parser, sequência e evento |
| Cancelamento | Divergência `canceled/cancelled` | Contrato único `cancelled` |
| Falha de cancelamento | Podia cancelar localmente | Não altera estado sem confirmação |
| Numeração | `max()+1` global | Reserva transacional por escopo |
| Pedido de venda | Rascunho já marcava o pedido como faturado | Somente autorização atualiza para `NfTransmitida` |

## 3. Estados fiscais

`FiscalNoteStatus`:

| Valor | Significado |
|---|---|
| `draft` | Rascunho |
| `sent` | Enviada ou aguardando definição |
| `authorized` | Uso autorizado |
| `rejected` | Rejeitada |
| `denied` | Denegada |
| `cancelled` | Cancelada pela SEFAZ |

`NFeTransmissionState` separa o transporte do estado persistido: `Authorized`, `Rejected`, `Denied`, `Processing`, `Indeterminate` e `Failed`.

Protocolos `100` e `150` autorizam. Códigos `110`, `205`, `301`, `302` e `303` denegam. Outros códigos individuais são rejeições.

## 4. Rotas

### 4.1 Telas

| Método | URI | Nome | Destino |
|---|---|---|---|
| GET | `/fiscal/notas-fiscais` | `fiscal.nfe.index` | `NotaFiscal` |
| GET | `/fiscal/nfe/{noteId}/editar` | `fiscal.nfe.editar` | `NFeGestao` |
| GET | `/fiscal/saida` | `fiscal.exit.index` | `NotaFiscal` |
| GET | `/fiscal/entrada` | `fiscal.entrance.index` | `NotaFiscalEntrada` |

A rota de entrada não é mais um resource e não possui endpoints CRUD adicionais.

### 4.2 API

| Método | URI | Nome | Operação |
|---|---|---|---|
| POST | `/api/nfe/{note}/transmitir` | `api.nfe.transmitir` | Transmissão |
| POST | `/api/nfe/{note}/cancelar` | `api.nfe.cancelar` | Cancelamento |
| GET | `/api/nfe/status` | `api.nfe.status` | Status do serviço |
| POST | `/api/nfe/consultar` | `api.nfe.consultar` | Consulta por chave |
| GET | `/api/nfe/{note}/danfe` | `api.nfe.danfe` | Download DANFE |
| GET | `/api/nfe/{note}/danfe/visualizar` | `api.nfe.danfe.visualizar` | DANFE inline |
| GET | `/api/nfe/{note}/xml` | `api.nfe.xml` | XML oficial |

## 5. Inventário completo do `NFeService`

### 5.1 Funções públicas

| Função | Histórico | Comportamento atual |
|---|---|---|
| `__construct()` | Existente | Carrega configuração, certificado e `Tools` |
| `transmitir()` | Existente/corrigida | Monta, assina, envia, interpreta, armazena e atualiza |
| `consultarRecibo()` | Acrescentada ao fluxo | Finaliza transmissão assíncrona e gera `nfeProc` |
| `buildXml()` | Existente/corrigida | Monta XML 4.00, tributos, totais e pagamento |
| `cartaCorrecao()` | Nova | Valida, envia e registra CC-e |
| `cancelar()` | Existente/corrigida | Cancela, interpreta evento individual e persiste |
| `consultarStatus()` | Existente | Consulta disponibilidade da SEFAZ |
| `consultarChave()` | Existente | Consulta situação da chave |

### 5.2 Montagem do XML

- `loadTools()`, `buildConfigJson()`, `loadCertificate()`.
- `buildIde()`, `buildEmit()`, `buildEnderEmit()`.
- `buildDest()`, `buildEnderDest()`.
- `buildProd()`, `buildImposto()`.
- `buildIcms()`, `buildIcmsSn()`, `buildIpi()`, `buildPis()`, `buildCofins()`.
- `buildIcmsTotal()`, `buildTransp()`, `buildPag()`, `buildDetPag()`, `buildInfAdic()`.

### 5.3 Transmissão segura e XML autorizado

- `parseTransmissionResponse()`: separa protocolo e lote.
- `loadResponseDocument()`: XML seguro com `LIBXML_NONET`.
- `findProtocolNode()`: encontra `infProt` pela chave.
- `xpathValue()`: leitura XML controlada.
- `protocolState()` e `batchState()`: classificam o retorno.
- `extractAccessKey()`: obtém a chave de `infNFe/@Id`.
- `applyTransmissionResult()`: atualiza nota, protocolo e recibo.
- `saveXml()`: persiste artefatos.
- `saveAuthorizedXml()`: gera `nfeProc` com `Complements::toAuthorize()`.
- `validateAuthorizedXml()`: confere raiz, chave, código e protocolo.

### 5.4 Carta de Correção

- `sendCce()`: wrapper de `Tools::sefazCCe()`.
- `nextCceSequence()`: próxima sequência de 1 a 20.
- `parseCceResponse()`: valida evento `110110`, chave e sequência.
- `saveCceResponse()`: salva resposta sem sobrescrever cartas anteriores.

### 5.5 Cancelamento

- `sendCancellation()`: wrapper de envio.
- `parseCancelResponse()`: valida evento `110111` e chave e retorna `cancelled`.

## 6. Contratos de retorno

### 6.1 Transmissão

```php
[
    'state' => NFeTransmissionState::Authorized,
    'is_final' => true,
    'authorized' => true,
    'code' => '100',
    'message' => 'Autorizado o uso da NF-e',
    'batch_code' => '104',
    'batch_message' => 'Lote processado',
    'receipt' => null,
    'protocol_code' => '100',
    'protocol_message' => 'Autorizado o uso da NF-e',
    'access_key' => '44 dígitos',
    'protocol' => '...',
    'received_at' => '...',
    'raw_response' => '<xml>...</xml>',
]
```

### 6.2 CC-e

```php
[
    'registered' => true,
    'success' => true,
    'code' => '135',
    'message' => 'Evento registrado e vinculado à NF-e',
    'protocol' => '...',
    'registered_at' => '...',
    'sequence' => 1,
    'response_path' => '...',
]
```

### 6.3 Cancelamento

```php
[
    'cancelled' => true,
    'code' => '135',
    'message' => 'Evento registrado e vinculado à NF-e',
    'protocol' => '...',
    'registered_at' => '...',
    'raw_response' => '<xml>...</xml>',
]
```

`canceled` não faz parte do contrato atual.

## 7. XML e DANFE

| Artefato | Conteúdo |
|---|---|
| `xml_sent_path` | XML assinado enviado |
| `xml_path` antes da autorização | XML para pré-visualização |
| `xml_path` após autorização | `nfeProc` oficial |
| `*_resposta.xml` | Resposta da transmissão/consulta |
| `*_cce_N_resposta.xml` | Resposta da CC-e N |
| `*_cancelamento.xml` | Resposta do cancelamento |

O `nfeProc` somente é criado para protocolo `100` ou `150`, com chaves correspondentes.

`DanfeService` mantém as funções existentes:

- `generate()`: lê `xml_path`, renderiza e salva o PDF.
- `download()`: devolve o DANFE como download.
- `getContent()`: gera PDF em memória para exibição inline.

Como `xml_path` é promovido ao `nfeProc`, o DANFE autorizado usa o XML protocolado.

## 8. Numeração fiscal

O escopo é:

```text
company_id + document_type + series + environment
```

`FiscalNumberService` possui:

| Função | Finalidade |
|---|---|
| `reserveForCurrentCompany()` | Resolve empresa no tenant/usuário e reserva |
| `reserve()` | Reserva explicitamente para empresa e escopo |

O serviço valida o escopo, inicializa a sequência pelo maior valor numérico daquele escopo, usa transação com `lockForUpdate()`, incrementa `next_number` e retorna nove dígitos.

O banco protege `fiscal_notes` com unicidade em:

```text
company_id + type + series + environment + invoice_number
```

Não existe mais `FiscalNote::max('invoice_number') + 1` nos fluxos de criação. Uma reserva exige empresa definida e números reservados não são reutilizados automaticamente.

## 9. Carta de Correção

Pré-condições:

- nota autorizada;
- chave com 44 dígitos;
- protocolo de autorização;
- texto entre 15 e 1.000 caracteres;
- sequência entre 1 e 20.

```text
NFeGestao::enviarCce()
 → NFeService::cartaCorrecao()
 → Tools::sefazCCe()
 → valida 110110 + chave + sequência
 → salva resposta
 → cria FiscalNoteEvent(cce)
```

O modal fecha apenas no sucesso e preserva o texto em caso de erro.

## 10. Cancelamento

```text
Interface/API
 → NFeService::cancelar()
 → Tools::sefazCancela()
 → valida 110111 + chave
 → atualiza FiscalNote
 → salva XML
 → cria FiscalNoteEvent(cancelled)
```

A interface não repete a persistência. Falha de certificado, conexão ou SEFAZ não cancela a nota localmente.

## 11. NF-e de entrada

`NotaFiscalEntrada` está acessível em `fiscal.entrance.index`.

| Grupo | Funções públicas |
|---|---|
| Consultas | `invoices()`, `stats()`, `suppliers()`, `purchaseOrders()` |
| Filtros | `updatedSearch()`, `updatedFilterStatus()` |
| Modal | `openModal()`, `openEdit()`, `closeModal()` |
| Detalhes | `openDetail()`, `closeDetail()` |
| XML | `openXmlModal()`, `closeXmlModal()`, `importXml()` |
| Itens | `addItem()`, `removeItem()`, `updatedInvoiceItems()`, `recalcTotals()` |
| Produtos | `searchProducts()`, `selectProduct()` |
| Persistência | `save()`, `escriturar()`, `cancel()`, `delete()` |
| Exibição | `render()` |

O importador aceita `NFe` e `nfeProc`, preenche cabeçalho, fornecedor, totais e itens e tenta associar cadastros existentes. A escrituração integra estoque, contas a pagar e pedido de compra conforme os dados associados.

## 12. Interfaces de saída

### 12.1 `NotaFiscal`

| Grupo | Funções públicas |
|---|---|
| Dados | `notes()`, `kpis()`, `clients()`, `viewingNote()` |
| Cadastro | `openCreate()`, `openEdit()`, `save()`, `closeModal()` |
| Visualização | `openView()`, `closeView()` |
| Cancelamento | `openCancel()`, `confirmCancel()`, `closeCancelModal()` |
| Transmissão | `openTransmit()`, `confirmTransmit()`, `closeTransmitModal()` |
| SEFAZ | `checkSefazStatus()` |
| Exclusão | `confirmDelete()`, `delete()`, `cancelDelete()` |
| Filtros | `updatingSearch()`, `updatingFilterStatus()`, `updatingFilterType()`, `updatingFilterEnv()` |
| Exibição | `render()` |

Na criação, `save()` reserva o número com `FiscalNumberService`.

### 12.2 `NFeGestao`

| Grupo | Funções públicas |
|---|---|
| Ciclo | `mount()`, `loadNote()`, `render()` |
| Transmissão | `transmitir()` |
| Cancelamento | `openCancelModal()`, `closeCancelModal()`, `cancelar()` |
| CC-e | `openCceModal()`, `closeCceModal()`, `enviarCce()` |
| Eventos | `openEventsModal()`, `closeEventsModal()` |

Eventos são apresentados do mais recente ao mais antigo por `FiscalNote::events()`.

## 13. Persistência e migrations

### 13.1 Integração com pedido de venda

Gerar ou reutilizar uma pré-NF-e não altera o status do pedido. `NFeService::applyTransmissionResult()` centraliza a regra para todos os canais de transmissão, inclusive API e consulta assíncrona:

- autorização `100/150`: atualiza o pedido para `NfTransmitida` e grava número, série, chave, protocolo e data;
- processamento, rejeição, denegação ou indeterminação: preserva o status comercial e registra somente `nfe_status`;
- reaplicação da mesma autorização preserva `nfe_issued_at`.

O vínculo é mantido por `fiscal_notes.sales_order_id` e `sales_orders.fiscal_note_id`.

### 13.2 Campos e migrations

Campos principais de `fiscal_notes`:

- `company_id`, `client_id`, `sales_order_id`;
- `invoice_number`, `series`, `type`, `environment`;
- `status`, `access_key`, `protocol`, `receipt_number`;
- `sefaz_message`, `authorized_at`;
- `cancel_protocol`, `cancel_reason`, `cancelled_at`;
- `xml_path`, `xml_sent_path`, `xml_cancel_path`;
- `amount`, `notes`, `emitted_by`.

Eventos: `Authorized`, `Rejected`, `Denied`, `Cancelled` e `Cce`. Cada evento pode armazenar descrição, protocolo, JSON de resposta e usuário.

| Migration | Finalidade |
|---|---|
| `2026_08_26_000000_create_fiscal_note_events_table.php` | Histórico fiscal |
| `2026_08_26_010000_add_receipt_number_to_fiscal_notes_table.php` | Recibo assíncrono |
| `2026_08_27_000000_add_xml_sent_path_to_fiscal_notes_table.php` | XML enviado separado |
| `2026_08_27_010000_create_fiscal_number_sequences_table.php` | Empresa, sequência e unicidade |
| `2026_08_27_020000_add_sales_order_id_to_fiscal_notes_table.php` | Vínculo entre nota e pedido |

Aplicação:

```bash
php artisan migrate
```

## 14. Testes de regressão

| Teste | Cobertura |
|---|---|
| `FiscalNoteRelationshipsTest` | Itens e eventos |
| `NFeTransmissionResponseTest` | Protocolo, lote, SOAP e estados |
| `NFeXmlTest` | Tributos, totais e pagamento |
| `NFeAuthorizedXmlTest` | XML enviado e `nfeProc` |
| `NFeCorrectionLetterTest` | CC-e e sequência |
| `NFeCancellationTest` | Contrato e evento de cancelamento |
| `FiscalNumberServiceTest` | Sequência, escopos e unicidade |
| `FiscalInvoiceEntranceRouteTest` | Rota funcional de entrada |
| `SalesOrderFiscalAuthorizationTest` | Rascunho, autorização e resultados não autorizados |

```bash
VIEW_COMPILED_PATH=/tmp php artisan test \
  tests/Feature/FiscalNoteRelationshipsTest.php \
  tests/Feature/NFeTransmissionResponseTest.php \
  tests/Feature/NFeXmlTest.php \
  tests/Feature/NFeAuthorizedXmlTest.php \
  tests/Feature/NFeCorrectionLetterTest.php \
  tests/Feature/NFeCancellationTest.php \
  tests/Feature/FiscalNumberServiceTest.php \
  tests/Feature/FiscalInvoiceEntranceRouteTest.php
```

## 15. Cuidados operacionais

- Resultado do lote não equivale à autorização.
- Nota autorizada deve possuir `nfeProc`.
- Não reutilizar números reservados automaticamente.
- Não cancelar localmente sem confirmação da SEFAZ.
- Não duplicar eventos entre interface e serviço.
- CC-e não pode alterar valores, impostos, partes ou datas vedadas.
- Toda reserva fiscal exige empresa/tenant definido.
- Certificados, senhas e `.env` nunca devem ser versionados.

## 16. Referências

- [Integração SEFAZ](./41-integracao-sefaz.md)
- [Pedidos de venda](./12-modulo-pedidos-venda.md)
- [Estoque](./24-modulo-estoque.md)
- [Empresas](./28-modulo-empresas.md)
- [Configurações](./29-modulo-configuracoes.md)
