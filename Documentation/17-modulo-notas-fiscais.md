# Módulo de Notas Fiscais (NF-e)

## 1. Visão Geral

O módulo de Notas Fiscais gerencia todo o ciclo de vida de documentos fiscais eletrônicos (NF-e e NFC-e), desde a criação até a transmissão para a SEFAZ, cancelamento e armazenamento de XMLs e DANFEs. Este é um dos módulos mais críticos do sistema por envolver obrigações fiscais.

### Funcionalidades Principais
- Criação de NF-e (modelo 55) e NFC-e (modelo 65)
- Transmissão para SEFAZ (homologação e produção)
- Cancelamento de notas fiscais
- Geração de DANFE em PDF
- Armazenamento de XMLs assinados
- Consulta de status na SEFAZ
- Validação automática de campos obrigatórios
- Integração com pedidos de venda
- Controle de numeração sequencial
- Diagnóstico de rejeições SEFAZ

---

## 2. Regras de Negócio

### 2.1 Status da Nota Fiscal
O ciclo de vida de uma NF-e segue estes status:

1. **Draft** (Rascunho): Nota em elaboração, pode ser editada
2. **Validada**: Passou por validação interna, pronta para transmissão
3. **Transmitida**: Enviada para SEFAZ, aguardando retorno
4. **Autorizada**: Aprovada pela SEFAZ, chave de acesso recebida
5. **Cancelada**: Cancelada dentro do prazo legal (24h)
6. **Denegada**: Rejeitada pela SEFAZ por irregularidade fiscal
7. **Rejeitada**: Não passou na validação da SEFAZ

### 2.2 Ambientes SEFAZ
- **Homologação**: Para testes, não tem validade fiscal
- **Produção**: Ambiente real, gera obrigações fiscais

### 2.3 Numeração
- Numeração sequencial por série
- Não pode haver gaps (buracos na sequência)
- Série 1 = NF-e padrão
- Outras séries conforme necessidade da empresa

### 2.4 Prazo de Cancelamento
- NF-e pode ser cancelada em até **24 horas** após autorização
- Ou até antes da ocorrência do fato gerador (circulação da mercadoria)
- Após este prazo, necessário emitir Nota de Devolução

### 2.5 Chave de Acesso
Formato: **44 dígitos**
```
UF (2) + AAMM (4) + CNPJ (14) + Modelo (2) + Série (3) + Número (9) + Tipo Emissão (1) + Código Numérico (8) + DV (1)
```

### 2.6 Validações Obrigatórias
Antes de transmitir, o sistema valida:
- CNPJ/CPF do emitente e destinatário
- Inscrição Estadual válida
- Endereço completo (CEP, rua, número, cidade, UF, código IBGE)
- Pelo menos 1 item na nota
- NCM de 8 dígitos para cada item
- CFOP válido
- Valores: total dos itens = subtotal, impostos calculados corretamente
- Certificado A1 válido e não expirado

---

## 3. Estrutura Técnica

### 3.1 Model: `App\Models\FiscalNote`

**Caminho**: `app/Models/FiscalNote.php`

#### Propriedades Principais
```php
protected $fillable = [
    // Relacionamentos
    'client_id',              // UUID do cliente
    'sales_order_id',         // UUID do pedido de venda (opcional)
    'company_id',             // UUID da empresa emissora
    
    // Identificação
    'chave_acesso',           // 44 dígitos (após autorização)
    'numero',                 // Número sequencial
    'serie',                  // Série da nota (padrão: 1)
    'tipo',                   // nfe, nfce, nfs
    'modelo',                 // 55 (NF-e), 65 (NFC-e)
    
    // Status e ambiente
    'status',                 // Enum: FiscalNoteStatus
    'ambiente',               // homologacao, producao
    
    // Datas
    'data_emissao',           // Data/hora de emissão
    'data_autorizacao',       // Data/hora autorização SEFAZ
    'data_cancelamento',      // Data/hora cancelamento
    
    // Valores
    'total_products',         // Total dos produtos
    'total_icms',             // Total ICMS
    'total_ipi',              // Total IPI
    'total_pis',              // Total PIS
    'total_cofins',           // Total COFINS
    'freight_value',          // Valor do frete
    'insurance_value',        // Valor do seguro
    'other_expenses',         // Outras despesas
    'discount_value',         // Desconto total
    'total_value',            // Valor total da nota
    
    // Transporte
    'freight_type',           // 0=emitente, 1=destinatário, 2=terceiros
    'carrier_name',           // Nome da transportadora
    'carrier_cnpj',           // CNPJ da transportadora
    'vehicle_plate',          // Placa do veículo
    'vehicle_state',          // UF da placa
    
    // SEFAZ
    'protocol_number',        // Número do protocolo SEFAZ
    'rejection_reason',       // Motivo de rejeição/denegação
    'cancellation_reason',    // Motivo do cancelamento
    'xml_path',               // Caminho do XML autorizado
    'xml_cancelamento_path',  // Caminho do XML de cancelamento
    
    // Observações
    'additional_info',        // Informações adicionais
    'fiscal_notes_obs',       // Observações fiscais
];
```

#### Casts
```php
protected $casts = [
    'data_emissao'      => 'datetime',
    'data_autorizacao'  => 'datetime',
    'data_cancelamento' => 'datetime',
    'total_products'    => 'decimal:2',
    'total_value'       => 'decimal:2',
    'status'            => FiscalNoteStatus::class,
];
```

#### Relacionamentos
- `client()`: BelongsTo Client
- `salesOrder()`: BelongsTo SalesOrder
- `company()`: BelongsTo Company
- `items()`: HasMany FiscalNoteItem

#### Métodos Principais
```php
generateChaveAcesso(): string
    // Gera chave de acesso de 44 dígitos

canTransmit(): bool
    // Verifica se pode transmitir

canCancel(): bool
    // Verifica se pode cancelar (24h)

isAuthorized(): bool
    // Verifica se está autorizada
```

### 3.2 Model: `App\Models\FiscalNoteItem`

```php
protected $fillable = [
    'fiscal_note_id',
    'product_id',
    'item_number',            // Ordem do item (1, 2, 3...)
    'product_code',           // Código do produto
    'ean',                    // EAN
    'description',            // Descrição
    'ncm',                    // NCM (8 dígitos)
    'cfop',                   // CFOP
    'unit',                   // Unidade
    'quantity',               // Quantidade
    'unit_value',             // Valor unitário
    'total_value',            // Valor total
    'discount_value',         // Desconto
    
    // Origem e CST
    'origin',                 // 0=Nacional, 1=Estrangeira, etc.
    'cst',                    // CST do ICMS
    'csosn',                  // CSOSN (Simples Nacional)
    
    // ICMS
    'icms_base',              // Base de cálculo ICMS
    'icms_percent',           // Alíquota ICMS
    'icms_value',             // Valor ICMS
    
    // IPI
    'ipi_base',
    'ipi_percent',
    'ipi_value',
    
    // PIS
    'pis_base',
    'pis_percent',
    'pis_value',
    
    // COFINS
    'cofins_base',
    'cofins_percent',
    'cofins_value',
    
    'additional_info',        // Informações adicionais do item
];
```

### 3.3 Controller API: `App\Http\Controllers\Api\NFeController`

**Caminho**: `app/Http/Controllers/Api/NFeController.php`

Este controller gerencia as operações com a SEFAZ.

#### Métodos Principais
```php
transmitir(FiscalNote $note): JsonResponse
    // 1. Valida nota fiscal (campos obrigatórios)
    // 2. Carrega certificado A1
    // 3. Gera XML da NF-e
    // 4. Assina XML com certificado
    // 5. Transmite para SEFAZ
    // 6. Processa retorno (autorizada/rejeitada)
    // 7. Salva XML e atualiza nota

cancelar(FiscalNote $note, Request $request): JsonResponse
    // 1. Valida prazo de cancelamento (24h)
    // 2. Valida motivo (mínimo 15 caracteres)
    // 3. Gera XML de cancelamento
    // 4. Assina e transmite para SEFAZ
    // 5. Processa retorno
    // 6. Atualiza status da nota

status(): JsonResponse
    // Consulta status do serviço SEFAZ

consultar(Request $request): JsonResponse
    // Consulta situação de uma NF-e na SEFAZ pela chave

danfe(FiscalNote $note): Response
    // Gera DANFE em PDF e retorna para download

visualizarDanfe(FiscalNote $note): Response
    // Exibe DANFE no navegador (inline)

xml(FiscalNote $note): Response
    // Download do XML autorizado
```

### 3.4 Livewire Components

#### NotaFiscal: `App\Livewire\Fiscal\NotaFiscal`
**Caminho**: `app/Livewire/Fiscal/NotaFiscal.php`

**Funcionalidades**:
- Listagem de notas fiscais
- Filtros: status, tipo, ambiente, período
- Cards com KPIs (total, emitidas, canceladas, rascunhos)
- Ações rápidas: transmitir, cancelar, DANFE, XML
- Busca por número, chave ou cliente
- Paginação configurável

#### NFeGestao: `App\Livewire\Fiscal\NFeGestao`
**Caminho**: `app/Livewire/Fiscal/NFeGestao.php`

**Funcionalidades**:
- Formulário de edição de NF-e
- Abas: Identificação, Itens, Totais, Transporte
- Adição/remoção de itens
- Cálculo automático de impostos
- Validação em tempo real
- Transmissão e cancelamento

---

## 4. Rotas

### 4.1 Rotas Web

**Arquivo**: `routes/fiscal.php`

| Método | URI | Nome | Descrição |
|--------|-----|------|-----------|
| GET | `/fiscal/notas-fiscais` | `fiscal.nfe.index` | Listagem de NF-e |
| GET | `/fiscal/nfe/{noteId}/editar` | `fiscal.nfe.editar` | Editar NF-e |

### 4.2 Rotas API

**Arquivo**: `routes/fiscal.php`, prefixo `/api/nfe`, middleware `auth`

| Método | URI | Nome | Descrição |
|--------|-----|------|-----------|
| POST | `/api/nfe/{note}/transmitir` | `api.nfe.transmitir` | Transmite NF-e para SEFAZ |
| POST | `/api/nfe/{note}/cancelar` | `api.nfe.cancelar` | Cancela NF-e |
| GET | `/api/nfe/status` | `api.nfe.status` | Status do serviço SEFAZ |
| POST | `/api/nfe/consultar` | `api.nfe.consultar` | Consulta NF-e pela chave |
| GET | `/api/nfe/{note}/danfe` | `api.nfe.danfe` | Download DANFE |
| GET | `/api/nfe/{note}/danfe/visualizar` | `api.nfe.danfe.visualizar` | Visualizar DANFE |
| GET | `/api/nfe/{note}/xml` | `api.nfe.xml` | Download XML |

---

## 5. Fluxos de Dados

### 5.1 Fluxo Completo de Emissão de NF-e

```
┌────────────────────┐
│ 1. Criar Rascunho  │
│    - Via pedido    │
│    - Manual        │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│ 2. Preencher Dados │
│    - Cliente       │
│    - Itens         │
│    - Impostos      │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│ 3. Validar         │
│    - Campos obrig. │
│    - Cálculos      │
│    - NCM, CFOP     │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│ 4. Transmitir      │
│    - Gera XML      │
│    - Assina cert.  │
│    - Envia SEFAZ   │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│ 5. Processar       │
│    Retorno SEFAZ   │
│    - Autorizada    │
│    - Rejeitada     │
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│ 6. Se Autorizada   │
│    - Salva XML     │
│    - Gera chave    │
│    - Gera DANFE    │
│    - Status: autor.│
└─────────┬──────────┘
          │
          ▼
┌────────────────────┐
│ 7. Disponibilizar  │
│    - DANFE p/ imp. │
│    - XML p/ client.│
│    - Envio por e-  │
│      mail (opcion.)│
└────────────────────┘
```

### 5.2 Geração de NF-e a partir de Pedido

```php
// 1. Pedido muda status para "Invoiced"
$order->changeStatus(SalesOrderStatus::Invoiced);

// 2. Observer SalesOrderObserver::onInvoiced() é disparado

// 3. Sistema cria FiscalNote
$note = FiscalNote::create([
    'client_id' => $order->client_id,
    'sales_order_id' => $order->id,
    'tipo' => 'nfe',
    'modelo' => '55',
    'serie' => 1,
    'status' => FiscalNoteStatus::Draft,
    'ambiente' => config('nfe.ambiente'),
    'data_emissao' => now(),
]);

// 4. Copia itens do pedido
foreach ($order->items as $index => $item) {
    $note->items()->create([
        'item_number' => $index + 1,
        'product_id' => $item->product_id,
        'description' => $item->description,
        'ncm' => $item->ncm,
        'cfop' => $item->cfop,
        'quantity' => $item->quantity,
        'unit_value' => $item->unit_price,
        'total_value' => $item->total,
        // ... impostos
    ]);
}

// 5. Calcula totais da nota
$note->calculateTotals();

// 6. Nota fica em Draft, aguardando usuário transmitir
```

### 5.3 Transmissão para SEFAZ

```php
// 1. Usuário clica "Transmitir"
POST /api/nfe/{note}/transmitir

// 2. Controller valida nota
$this->validateNote($note);

// 3. Carrega certificado A1
$certificado = storage_path('app/nfe/certificado.pfx');
$senha = config('nfe.certificado_senha');

// 4. Monta XML da NF-e usando nfephp-org/sped-nfe
$nfe = new \NFePHP\NFe\Make();
$nfe->taginfNFe([
    'versao' => '4.00',
    'chave' => $note->chave_acesso,
    // ... demais tags
]);

// 5. Assina XML
$signedXml = $nfe->getXML();
$tools = new \NFePHP\NFe\Tools($config);
$signedXml = $tools->signNFe($signedXml);

// 6. Transmite
$response = $tools->sefazEnviaLote([$signedXml]);

// 7. Processa retorno
$ret = $tools->sefazConsultaRecibo($response);

if ($ret->cStat == 100) { // Autorizada
    $note->update([
        'status' => FiscalNoteStatus::Autorizada,
        'chave_acesso' => $ret->chNFe,
        'protocol_number' => $ret->nProt,
        'data_autorizacao' => now(),
        'xml_path' => storage_path("app/nfe/autorizadas/{$ret->chNFe}.xml"),
    ]);
    
    // Salva XML
    file_put_contents($note->xml_path, $ret->xml);
    
    // Gera DANFE
    $danfe = new \NFePHP\DA\NFe\Danfe($ret->xml);
    $pdf = $danfe->render();
    file_put_contents(
        storage_path("app/nfe/danfe/{$ret->chNFe}.pdf"),
        $pdf
    );
} else { // Rejeitada
    $note->update([
        'status' => FiscalNoteStatus::Rejeitada,
        'rejection_reason' => $ret->xMotivo,
    ]);
}
```

### 5.4 Cancelamento de NF-e

```php
// 1. Usuário clica "Cancelar" e informa motivo
POST /api/nfe/{note}/cancelar
{
    "motivo": "Cliente solicitou cancelamento do pedido"
}

// 2. Valida prazo (24h)
if (now()->diffInHours($note->data_autorizacao) > 24) {
    throw new Exception('Prazo de cancelamento expirado');
}

// 3. Valida motivo (mínimo 15 caracteres)
if (strlen($motivo) < 15) {
    throw new Exception('Motivo deve ter no mínimo 15 caracteres');
}

// 4. Monta XML de cancelamento
$tools = new \NFePHP\NFe\Tools($config);
$eventoCancelamento = $tools->sefazCancela(
    $note->chave_acesso,
    $note->protocol_number,
    $motivo
);

// 5. Processa retorno
if ($eventoCancelamento->cStat == 135) { // Cancelada
    $note->update([
        'status' => FiscalNoteStatus::Cancelada,
        'data_cancelamento' => now(),
        'cancellation_reason' => $motivo,
        'xml_cancelamento_path' => storage_path("app/nfe/canceladas/{$note->chave_acesso}-canc.xml"),
    ]);
    
    // Salva XML de cancelamento
    file_put_contents($note->xml_cancelamento_path, $eventoCancelamento->xml);
    
    // Reverte movimentações (se necessário)
    // - Estoque: cria StockMovement inverso
    // - Financeiro: cancela AccountReceivable
}
```

---

## 6. Integrações com Outros Módulos

### 6.1 Módulo de Vendas
- **Pedido Faturado**: cria FiscalNote automaticamente
- Copia dados do pedido (cliente, itens, valores)
- Vínculo bidirecional (pedido ↔ nota)

### 6.2 Módulo Financeiro
- **NF-e Autorizada**: confirma criação de AccountReceivable
- NF-e Cancelada: cancela contas a receber associadas

### 6.3 Módulo de Clientes
- Busca dados do destinatário (CPF/CNPJ, IE, endereço)
- Validação de campos obrigatórios

### 6.4 Módulo de Produtos
- Busca informações fiscais (NCM, CFOP, CST)
- Obtém descrição e unidade de medida

### 6.5 Módulo de Empresas
- Busca dados do emitente (CNPJ, IE, endereço)
- Configurações fiscais (regime tributário, CRT)

---

## 7. Configurações

### 7.1 Arquivo `.env`
```env
# Ambiente SEFAZ
NFE_AMBIENTE=homologacao    # ou producao

# Certificado A1
NFE_CERTIFICADO_PATH=/path/to/certificado.pfx
NFE_CERTIFICADO_SENHA=senha_do_certificado

# Empresa
NFE_RAZAO_SOCIAL="Minha Empresa Ltda"
NFE_CNPJ="12.345.678/0001-99"
NFE_IE="123456789"
NFE_UF=SP
NFE_REGIME_TRIBUTARIO=1    # 1=Simples Nacional, 3=Normal

# Série padrão
NFE_SERIE_PADRAO=1

# Timeout (segundos)
NFE_TIMEOUT=30
```

### 7.2 Arquivo `config/nfe.php`
```php
return [
    'ambiente' => env('NFE_AMBIENTE', 'homologacao'),
    'certificado' => [
        'path' => env('NFE_CERTIFICADO_PATH'),
        'senha' => env('NFE_CERTIFICADO_SENHA'),
    ],
    'emitente' => [
        'razao_social' => env('NFE_RAZAO_SOCIAL'),
        'cnpj' => env('NFE_CNPJ'),
        'ie' => env('NFE_IE'),
        'uf' => env('NFE_UF'),
        'regime_tributario' => env('NFE_REGIME_TRIBUTARIO', 1),
    ],
    'serie_padrao' => env('NFE_SERIE_PADRAO', 1),
    'timeout' => env('NFE_TIMEOUT', 30),
];
```

---

## 8. Diagnóstico de Rejeições

### 8.1 Códigos Comuns de Rejeição SEFAZ

| Código | Descrição | Solução |
|--------|-----------|---------|
| 204 | Rejeição: Duplicidade de NF-e | Número já foi usado, incrementar |
| 215 | Rejeição: Falha no schema XML | Verificar estrutura do XML |
| 539 | Rejeição: CNPJ destinatário inválido | Validar CNPJ do cliente |
| 540 | Rejeição: CPF destinatário inválido | Validar CPF do cliente |
| 542 | Rejeição: IE destinatário inválida | Validar IE do cliente |
| 680 | Rejeição: NCM inexistente | Verificar tabela NCM atualizada |
| 777 | Rejeição: Diferença valor total | Recalcular totais |

### 8.2 Model: `App\Models\SefazDiagnostic`
Armazena diagnósticos determinísticos de rejeições.

```php
SefazDiagnostic::where('codigo', $codigoRejeicao)->first();
// Retorna: causa, solução, documentação
```

---

## 9. Bibliotecas Utilizadas

### 9.1 nfephp-org/sped-nfe
Biblioteca oficial para NF-e no Brasil.

```bash
composer require nfephp-org/sped-nfe:^5.2
```

**Funcionalidades**:
- Montagem de XML conforme layout da SEFAZ
- Assinatura digital com certificado A1/A3
- Transmissão e consulta
- Eventos (cancelamento, carta de correção)

### 9.2 nfephp-org/sped-da
Geração de DANFE e DACTE.

```bash
composer require nfephp-org/sped-da:^1.1
```

---

## 10. Exemplos de Código

### 10.1 Criar NF-e Manual
```php
$note = FiscalNote::create([
    'client_id' => $client->id,
    'tipo' => 'nfe',
    'modelo' => '55',
    'serie' => 1,
    'status' => FiscalNoteStatus::Draft,
    'ambiente' => 'homologacao',
    'data_emissao' => now(),
]);

$note->items()->create([
    'product_id' => $product->id,
    'description' => $product->name,
    'ncm' => '12345678',
    'cfop' => '5102',
    'quantity' => 10,
    'unit_value' => 100.00,
    'total_value' => 1000.00,
]);
```

### 10.2 Transmitir NF-e
```php
$controller = app(NFeController::class);
$response = $controller->transmitir($note);

if ($response->status() === 200) {
    $data = $response->getData();
    echo "Chave: {$data->chave_acesso}";
    echo "Protocolo: {$data->protocol_number}";
}
```

### 10.3 Gerar DANFE
```php
$pdfContent = $controller->danfe($note)->getContent();
file_put_contents('/tmp/danfe.pdf', $pdfContent);
```

---

## 11. Performance e Otimizações

### 11.1 Índices
- `chave_acesso`: Índice único
- `numero, serie`: Índice composto único
- `status`: Índice para filtros
- `data_emissao`: Índice para relatórios

### 11.2 Armazenamento de XMLs
XMLs autorizados são armazenados em:
```
storage/app/nfe/
├── autorizadas/
│   ├── 35210212345678000199550010000000011234567890.xml
│   └── ...
├── canceladas/
│   └── ...
└── danfe/
    ├── 35210212345678000199550010000000011234567890.pdf
    └── ...
```

---

## 12. Testes

### 12.1 Testes de Feature
- `NFeCreationTest`
- `NFeTransmissionTest`
- `NFeCancellationTest`
- `NFeValidationTest`

### 12.2 Exemplo de Teste
```php
test('nfe é transmitida com sucesso em homologação', function () {
    $note = FiscalNote::factory()->create([
        'status' => FiscalNoteStatus::Draft,
        'ambiente' => 'homologacao',
    ]);
    
    $response = $this->postJson("/api/nfe/{$note->id}/transmitir");
    
    $response->assertOk();
    expect($note->fresh()->status)->toBe(FiscalNoteStatus::Autorizada);
});
```

---

## 13. Troubleshooting

### 13.1 Problemas Comuns
- **Certificado expirado**: Renovar certificado A1
- **Timeout na SEFAZ**: Aumentar `NFE_TIMEOUT`
- **Rejeição 204 (duplicidade)**: Verificar numeração sequencial
- **XML mal formado**: Validar dados antes de transmitir

### 13.2 Comandos Úteis
```bash
# Verificar status da SEFAZ
curl -X GET http://localhost:8000/api/nfe/status

# Listar notas rejeitadas
php artisan tinker
>>> FiscalNote::where('status', 'rejeitada')->get();

# Reprocessar nota
>>> $note = FiscalNote::find($id);
>>> $note->update(['status' => 'draft']);
```

---

**Caminho do Código**:
- `app/Models/FiscalNote.php`
- `app/Http/Controllers/Api/NFeController.php`
- `app/Livewire/Fiscal/`
- `routes/fiscal.php`
- `config/nfe.php`

