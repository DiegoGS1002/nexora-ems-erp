# Assistente IA e RAG (Retrieval-Augmented Generation)

## 1. Visão Geral

O Assistente de IA é um componente inovador do Nexora ERP que fornece suporte inteligente aos usuários através de um chatbot contextual. Utiliza técnicas avançadas de IA incluindo RAG (Retrieval-Augmented Generation), function calling e múltiplos provedores de LLM.

### Funcionalidades Principais
- Chat contextual disponível em qualquer página do sistema
- Suporte a múltiplos provedores de IA (OpenAI GPT-4o, Google Gemini)
- RAG com busca vetorial híbrida (lexical + semântica)
- Function calling para consultar dados do ERP em tempo real
- Diagnóstico determinístico de erros SEFAZ
- Playbook engine para raciocínio estruturado por domínio
- Fallback chain para alta disponibilidade

---

## 2. Arquitetura

### 2.1 Fluxo de Processamento

```
┌──────────────┐
│   Usuário    │
│ (AiChatBubble)│
└──────┬───────┘
       │
       ▼
┌──────────────────────┐
│ AiAssistantService   │
│ - Detecta contexto   │
│ - Monta prompt       │
└──────┬───────────────┘
       │
       ▼
┌──────────────────────┐
│   AgenteService      │
│ - Loop function call │
│ - Multi-provedor     │
└──────┬───────────────┘
       │
       ├─────────────────┐
       │                 │
       ▼                 ▼
┌──────────┐      ┌──────────┐
│  OpenAI  │      │  Gemini  │
│  GPT-4o  │      │  Flash   │
└──────┬───┘      └────┬─────┘
       │               │
       │    Fallback   │
       ▼               ▼
┌──────────────────────────┐
│  FallbackResponder       │
│  (resposta genérica)     │
└──────────────────────────┘
       │
       ▼
┌──────────────────────────┐
│   ToolRegistry           │
│   - 10 ferramentas       │
│   - Consultas ao ERP     │
└──────┬───────────────────┘
       │
       ▼
┌──────────────────────────┐
│     RagService           │
│ - Busca vetorial MySQL   │
│ - 47 documentos base     │
└──────────────────────────┘
```

### 2.2 Componentes Principais

#### A. AiChatBubble (Livewire Component)
**Caminho**: `app/Livewire/AiChatBubble.php`

Interface do chat flutuante no canto inferior direito.

**Características**:
- Disponível em todas as páginas
- Minimizável/maximizável
- Histórico de conversação
- Indicador de digitação
- Detecção automática de módulo atual
- Polling a cada 2s para respostas longas

#### B. AiAssistantService
**Caminho**: `app/Services/AiAssistantService.php`

Orquestra a interação com o usuário.

**Métodos**:
```php
processMessage(string $message, string $module, array $context): string
    // Processa mensagem do usuário
    // - Detecta contexto (ticket, pedido, etc)
    // - Monta prompt com histórico
    // - Chama AgenteService
    // - Retorna resposta formatada

getContextualPrompt(string $module): string
    // Busca prompt específico do módulo em config/ai_contexts.php
```

#### C. AgenteService
**Caminho**: `app/Ai/AgenteService.php`

Implementa o loop de function calling com múltiplos provedores.

**Lógica**:
```php
1. Tenta OpenAI (GPT-4o-mini)
   - Suporta function calling nativo
   - Tokens: ~0.15 USD/1M input, ~0.60 USD/1M output
   
2. Se falha, tenta Gemini (1.5 Flash)
   - Suporta function calling via Google AI
   - Tokens: grátis até 15 RPM
   
3. Se ambos falham, FallbackResponder
   - Resposta genérica sem IA
   - Sempre disponível
```

**Loop de Function Calling**:
```php
while ($needsToolCall && $iterations < 5) {
    $response = $provider->call($messages, $tools);
    
    if ($response->hasToolCalls()) {
        foreach ($response->toolCalls as $call) {
            $result = ToolRegistry::execute($call->name, $call->arguments);
            $messages[] = ['role' => 'tool', 'content' => $result];
        }
        $iterations++;
    } else {
        $needsToolCall = false;
    }
}
```

#### D. ToolRegistry
**Caminho**: `app/Ai/ToolRegistry.php`

Registra e executa ferramentas disponíveis para o agente.

**Ferramentas Registradas**:
1. `buscar_pedido`: Consulta pedidos de venda
2. `consultar_cliente`: Dados de clientes
3. `consultar_estoque`: Saldo de produtos
4. `consultar_financeiro`: Contas a pagar/receber
5. `consultar_ticket`: Dados de tickets de suporte
6. `verificar_nfe`: Status de notas fiscais
7. `analisar_xml_nfe`: Validação de XMLs
8. `buscar_codigo_fonte`: Pesquisa no código-fonte
9. `consultar_diagnostico_sefaz`: Diagnóstico de rejeições
10. `inspecionar_logs`: Logs de auditoria

**Métodos**:
```php
getTools(): array
    // Retorna array de definições de ferramentas no formato OpenAI

execute(string $toolName, array $arguments): mixed
    // Executa ferramenta e retorna resultado
```

#### E. RagService
**Caminho**: `app/Ai/RagService.php`

Implementa RAG com busca vetorial híbrida.

**Busca Híbrida**:
```sql
-- MySQL 9.x (vetor nativo)
SELECT *, 
       COSINE_SIMILARITY(embedding, :query_vector) as similarity
FROM rag_documents
WHERE category = :category
ORDER BY similarity DESC
LIMIT 5

-- MySQL 8.x (JSON)
SELECT *,
       /* cálculo de cosseno em PHP */ as similarity
FROM rag_documents
WHERE MATCH(content) AGAINST(:query IN NATURAL LANGUAGE MODE)
ORDER BY similarity DESC
LIMIT 5
```

**Métodos**:
```php
search(string $query, ?string $category = null, int $limit = 5): array
    // Busca semântica + lexical
    // 1. Gera embedding da query via OpenAI
    // 2. Busca por cosseno vetorial
    // 3. Combina com busca fulltext
    // 4. Retorna top 5 documentos relevantes

addDocument(string $title, string $content, string $category): void
    // Adiciona novo documento ao RAG
    // 1. Gera embedding via OpenAI
    // 2. Salva em rag_documents

vectorize(): void
    // Re-gera embeddings de todos os documentos
```

#### F. PlaybookEngine
**Caminho**: `app/Ai/PlaybookEngine.php`

Raciocínio estruturado por domínio.

**Playbooks**:
```php
'fiscal' => [
    'passos' => [
        '1. Verificar certificado digital',
        '2. Validar CNPJ/IE do destinatário',
        '3. Conferir NCM e CFOP',
        '4. Verificar cálculo de impostos',
        '5. Analisar código de rejeição SEFAZ',
    ],
    'ferramentas' => ['verificar_nfe', 'consultar_diagnostico_sefaz', 'analisar_xml_nfe'],
    'documentacao' => 'docs/NF-e/IMPLEMENTACAO_API_NFE.md',
],

'financeiro' => [
    'passos' => [
        '1. Verificar saldo em contas bancárias',
        '2. Conferir contas a pagar vencidas',
        '3. Analisar fluxo de caixa projetado',
        '4. Verificar inadimplência de clientes',
    ],
    'ferramentas' => ['consultar_financeiro'],
],
```

**Método**:
```php
getPlaybook(string $domain): ?array
    // Detecta domínio do problema e retorna playbook
```

#### G. SefazDiagnostics
**Caminho**: `app/Models/SefazDiagnostic.php`

Diagnóstico determinístico de rejeições SEFAZ.

**Estrutura**:
```php
protected $fillable = [
    'codigo',           // Ex: 539
    'tipo',             // rejeicao, denegacao, erro_tecnico
    'mensagem',         // "CNPJ destinatário inválido"
    'causa',            // Explicação da causa
    'solucao',          // Passo a passo para resolver
    'documentacao_url', // Link para doc oficial
    'exemplo',          // Exemplo de correção
    'criticidade',      // alta, media, baixa
    'modulo_afetado',   // fiscal, cadastro, etc.
];
```

**Prioridade de Busca**:
```
1. SefazDiagnostics (determinístico) → solução exata
2. RagService (semântico) → documentos similares
3. LLM (geração) → resposta contextualizada
```

---

## 3. Ferramentas (Tools)

### 3.1 BaseTool (Abstração)
**Caminho**: `app/Ai/Tools/BaseTool.php`

Todas as ferramentas estendem esta classe.

```php
abstract class BaseTool
{
    abstract public function getName(): string;
    abstract public function getDescription(): string;
    abstract public function getParameters(): array;
    abstract public function execute(array $arguments): mixed;
    
    public function toArray(): array
    {
        return [
            'type' => 'function',
            'function' => [
                'name' => $this->getName(),
                'description' => $this->getDescription(),
                'parameters' => $this->getParameters(),
            ],
        ];
    }
}
```

### 3.2 BuscarPedidoTool
**Caminho**: `app/Ai/Tools/BuscarPedidoTool.php`

```php
public function execute(array $arguments): array
{
    $query = SalesOrder::query();
    
    if (isset($arguments['numero'])) {
        $query->where('order_number', $arguments['numero']);
    }
    
    if (isset($arguments['cliente_id'])) {
        $query->where('client_id', $arguments['cliente_id']);
    }
    
    $pedidos = $query->with(['client', 'items', 'payments'])
                     ->limit(10)
                     ->get();
    
    return [
        'total' => $pedidos->count(),
        'pedidos' => $pedidos->map(fn($p) => [
            'numero' => $p->order_number,
            'cliente' => $p->client->name,
            'status' => $p->status->value,
            'total' => $p->total_amount,
            'data' => $p->order_date->format('d/m/Y'),
        ]),
    ];
}
```

**Definição OpenAI**:
```json
{
  "type": "function",
  "function": {
    "name": "buscar_pedido",
    "description": "Busca pedidos de venda por número ou cliente",
    "parameters": {
      "type": "object",
      "properties": {
        "numero": {
          "type": "integer",
          "description": "Número do pedido"
        },
        "cliente_id": {
          "type": "string",
          "description": "UUID do cliente"
        }
      }
    }
  }
}
```

### 3.3 ConsultarDiagnosticoSefazTool
**Caminho**: `app/Ai/Tools/ConsultarDiagnosticoSefazTool.php`

```php
public function execute(array $arguments): array
{
    $codigo = $arguments['codigo'];
    
    $diagnostico = SefazDiagnostic::where('codigo', $codigo)->first();
    
    if (!$diagnostico) {
        return ['erro' => "Código {$codigo} não encontrado"];
    }
    
    return [
        'codigo' => $diagnostico->codigo,
        'mensagem' => $diagnostico->mensagem,
        'causa' => $diagnostico->causa,
        'solucao' => $diagnostico->solucao,
        'criticidade' => $diagnostico->criticidade,
        'exemplo' => $diagnostico->exemplo,
    ];
}
```

**Exemplo de Uso**:
```
Usuário: "Minha NF-e foi rejeitada com código 539"

Agente: [chama consultar_diagnostico_sefaz com codigo=539]

Resultado:
{
  "codigo": 539,
  "mensagem": "Rejeição: CNPJ destinatário inválido",
  "causa": "CNPJ informado não é válido (dígitos verificadores incorretos)",
  "solucao": "1. Verifique o CNPJ do cliente em /clients\n2. Use um validador de CNPJ\n3. Corrija e retransmita",
  "criticidade": "alta"
}

Agente: "Identificado: Rejeição 539 - CNPJ inválido. Para resolver: ..."
```

### 3.4 BuscarCodigoFonteTool
**Caminho**: `app/Ai/Tools/BuscarCodigoFonteTool.php`

Permite ao agente consultar o código-fonte do ERP.

```php
public function execute(array $arguments): array
{
    $termo = $arguments['termo'];
    $tipo = $arguments['tipo'] ?? 'all'; // class, function, route
    
    $resultados = [];
    
    // Busca em Models
    if (in_array($tipo, ['all', 'class'])) {
        $models = glob(app_path('Models/*.php'));
        foreach ($models as $file) {
            $content = file_get_contents($file);
            if (stripos($content, $termo) !== false) {
                $resultados[] = [
                    'arquivo' => basename($file),
                    'tipo' => 'Model',
                    'caminho' => str_replace(base_path(), '', $file),
                ];
            }
        }
    }
    
    // Busca em Controllers
    // Busca em Services
    // etc.
    
    return [
        'total' => count($resultados),
        'resultados' => array_slice($resultados, 0, 10),
    ];
}
```

---

## 4. Configuração

### 4.1 Arquivo `.env`
```env
# OpenAI
OPENAI_API_KEY=sk-proj-...
OPENAI_MODEL=gpt-4o-mini
OPENAI_EMBEDDING_MODEL=text-embedding-3-small

# Google Gemini
GEMINI_API_KEY=AIzaSy...
GEMINI_MODEL=gemini-1.5-flash

# RAG
RAG_ENABLED=true
RAG_TOP_K=5
RAG_MIN_SIMILARITY=0.7

# Assistente
AI_MAX_ITERATIONS=5
AI_TIMEOUT=30
```

### 4.2 Arquivo `config/ai_contexts.php`
```php
return [
    'vendas' => [
        'description' => 'Módulo de vendas e pedidos',
        'prompt' => 'Você é especialista em vendas...',
        'tools' => ['buscar_pedido', 'consultar_cliente'],
    ],
    
    'fiscal' => [
        'description' => 'Módulo fiscal e NF-e',
        'prompt' => 'Você é especialista em legislação fiscal...',
        'tools' => ['verificar_nfe', 'consultar_diagnostico_sefaz', 'analisar_xml_nfe'],
    ],
    
    'financeiro' => [
        'description' => 'Módulo financeiro',
        'prompt' => 'Você é especialista em finanças...',
        'tools' => ['consultar_financeiro'],
    ],
    
    // ... demais módulos
];
```

---

## 5. Base de Conhecimento RAG

### 5.1 Model: `App\Models\RagDocument`
**Caminho**: `app/Models/RagDocument.php`

```php
protected $fillable = [
    'title',            // Título do documento
    'content',          // Conteúdo completo
    'category',         // fiscal, financeiro, vendas, etc.
    'embedding',        // VECTOR(1536) no MySQL 9.x, LONGTEXT (JSON) no 8.x
    'metadata',         // JSON com informações extras
    'source',           // manual, import, api
    'version',          // Versão do documento
    'is_active',        // Ativo/inativo
];
```

### 5.2 Seeder: RagKnowledgeSeeder
**Caminho**: `database/seeders/RagKnowledgeSeeder.php`

Popula 47 documentos base:
- Fluxos de módulos (vendas, compras, fiscal)
- Regras de negócio
- Soluções de problemas comuns
- Documentação técnica
- Códigos de rejeição SEFAZ

```bash
php artisan db:seed --class=RagKnowledgeSeeder
```

### 5.3 Comando: `rag:seed`
**Caminho**: `app/Console/Commands/RagSeedCommand.php`

```bash
php artisan rag:seed
```

Executa:
1. Popula documentos base
2. Gera embeddings via OpenAI
3. Salva no banco (campo `embedding`)

### 5.4 Comando: `rag:search`
**Caminho**: `app/Console/Commands/RagSearchCommand.php`

Teste de busca RAG via CLI:
```bash
php artisan rag:search "como emitir nota fiscal"
```

Retorna:
```
Top 5 documentos relevantes:
1. [0.89] Emissão de NF-e: Passo a passo
2. [0.85] Configuração SEFAZ
3. [0.78] Solução de rejeições comuns
4. [0.72] Certificado digital A1
5. [0.68] DANFE e XML
```

---

## 6. Fluxos de Uso

### 6.1 Usuário Pergunta sobre Pedido

```
Usuário: "Qual o status do pedido 1234?"

1. AiChatBubble envia mensagem
2. AiAssistantService detecta contexto: vendas
3. Monta prompt com histórico
4. AgenteService chama OpenAI
5. OpenAI identifica: precisa de ferramenta buscar_pedido
6. ToolRegistry executa BuscarPedidoTool(numero=1234)
7. Retorna: { numero: 1234, status: "aprovado", total: 5000.00 }
8. OpenAI gera resposta natural:
   "O pedido 1234 está aprovado e seu valor total é R$ 5.000,00"
9. Resposta exibida no chat
```

### 6.2 Usuário Relata Erro SEFAZ

```
Usuário: "NF-e rejeitada com código 539, como resolver?"

1. AiAssistantService detecta: fiscal
2. PlaybookEngine identifica: erro SEFAZ
3. AgenteService chama OpenAI
4. OpenAI usa ferramenta: consultar_diagnostico_sefaz(codigo=539)
5. SefazDiagnostic retorna:
   - Causa: CNPJ inválido
   - Solução: Corrigir cadastro do cliente
6. RagService busca documentos sobre validação de CNPJ
7. OpenAI combina diagnóstico + RAG + conhecimento próprio
8. Resposta estruturada:
   "Identifiquei o problema: Rejeição 539 (CNPJ inválido).
    
    Para resolver:
    1. Acesse /clients/{id}/edit
    2. Valide o CNPJ usando o botão 'Consultar Receita'
    3. Corrija o número
    4. Salve e retransmita a NF-e
    
    Documentação: docs/NF-e/VALIDACAO_CNPJ.md"
```

### 6.3 Busca Geral no Sistema

```
Usuário: "Como faço para dar baixa em uma conta a pagar?"

1. Contexto: geral (não está em módulo específico)
2. RagService busca: "baixa conta pagar"
3. Retorna documentos:
   - Fluxo de contas a pagar
   - Tutorial de baixa de pagamento
4. OpenAI sintetiza resposta baseada nos documentos
5. Resposta com passo a passo visual
```

---

## 7. Integrações

### 7.1 Módulo de Suporte
- Assistente tem acesso aos dados do ticket atual
- Ferramenta `consultar_ticket` busca histórico
- Pode sugerir soluções baseadas em tickets similares

### 7.2 Todos os Módulos
Cada ferramenta acessa dados específicos:
- **buscar_pedido** → SalesOrder
- **consultar_cliente** → Client
- **consultar_estoque** → Stock, StockMovement
- **consultar_financeiro** → AccountPayable, AccountReceivable
- **verificar_nfe** → FiscalNote
- **inspecionar_logs** → SystemLog

---

## 8. Custos e Limites

### 8.1 OpenAI (GPT-4o-mini)
- **Input**: ~$0.15 / 1M tokens
- **Output**: ~$0.60 / 1M tokens
- **Embeddings**: ~$0.02 / 1M tokens
- **Rate Limit**: 500 RPM (requests per minute)

Estimativa: 1000 conversas/mês ≈ $5-10 USD

### 8.2 Google Gemini (1.5 Flash)
- **Grátis**: até 15 RPM, 1M tokens/day
- **Pago**: $0.35 / 1M input, $1.05 / 1M output

---

## 9. Exemplos de Código

### 9.1 Usar Assistente Programaticamente
```php
use App\Services\AiAssistantService;

$assistant = app(AiAssistantService::class);

$resposta = $assistant->processMessage(
    message: "Qual o saldo do produto ABC-123?",
    module: 'estoque',
    context: ['product_code' => 'ABC-123']
);

echo $resposta;
// "O produto ABC-123 possui 150 unidades em estoque."
```

### 9.2 Adicionar Novo Documento ao RAG
```php
use App\Ai\RagService;

$rag = app(RagService::class);

$rag->addDocument(
    title: 'Como configurar certificado digital',
    content: 'Passo 1: Baixe o certificado A1...',
    category: 'fiscal'
);
```

### 9.3 Criar Nova Ferramenta
```php
// app/Ai/Tools/ConsultarDepartamentoTool.php

namespace App\Ai\Tools;

class ConsultarDepartamentoTool extends BaseTool
{
    public function getName(): string
    {
        return 'consultar_departamento';
    }
    
    public function getDescription(): string
    {
        return 'Busca informações sobre departamentos da empresa';
    }
    
    public function getParameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'nome' => [
                    'type' => 'string',
                    'description' => 'Nome do departamento',
                ],
            ],
            'required' => ['nome'],
        ];
    }
    
    public function execute(array $arguments): mixed
    {
        $dept = Department::where('name', 'like', "%{$arguments['nome']}%")->first();
        
        return $dept ? [
            'nome' => $dept->name,
            'gerente' => $dept->manager->name,
            'funcionarios' => $dept->employees->count(),
        ] : ['erro' => 'Departamento não encontrado'];
    }
}

// Registrar em ToolRegistry:
$this->tools[] = new ConsultarDepartamentoTool();
```

---

## 10. Testes

### 10.1 Testes de Feature
- `AiAssistantTest`: Conversação básica
- `RagServiceTest`: Busca semântica
- `ToolRegistryTest`: Execução de ferramentas
- `FallbackTest`: Fallback chain

### 10.2 Exemplo de Teste
```php
test('assistente responde consulta de pedido', function () {
    $order = SalesOrder::factory()->create(['order_number' => 9999]);
    
    $assistant = app(AiAssistantService::class);
    $response = $assistant->processMessage(
        'Status do pedido 9999',
        'vendas',
        []
    );
    
    expect($response)->toContain('9999');
});
```

---

## 11. Performance e Otimizações

### 11.1 Cache de Embeddings
Embeddings são gerados uma vez e armazenados:
```php
// Não regenerar se já existe
if (!$document->embedding) {
    $document->embedding = $this->generateEmbedding($document->content);
    $document->save();
}
```

### 11.2 Busca Vetorial MySQL 9.x
MySQL 9.x suporta `VECTOR()` nativo com cálculo de cosseno otimizado:
```sql
SELECT *,
       COSINE_SIMILARITY(embedding, :query_vector) as similarity
FROM rag_documents
WHERE similarity > 0.7
ORDER BY similarity DESC
LIMIT 5
```

### 11.3 Fallback em Múltiplas Camadas
```
1. OpenAI (primário, mais preciso)
2. Gemini (secundário, grátis)
3. FallbackResponder (sempre disponível)
```

---

## 12. Troubleshooting

### 12.1 Problemas Comuns
- **Rate limit OpenAI**: Usar Gemini como fallback
- **Embedding muito lento**: Usar modelo menor (ada-002)
- **Respostas genéricas**: Melhorar documentos RAG
- **Tools não executadas**: Verificar definição de parâmetros

### 12.2 Logs e Debug
```bash
# Ativar logs detalhados
AI_DEBUG=true

# Ver logs do assistente
tail -f storage/logs/laravel.log | grep "AI:"
```

---

**Caminho do Código**:
- `app/Livewire/AiChatBubble.php`
- `app/Services/AiAssistantService.php`
- `app/Ai/`
- `config/ai_contexts.php`
- `database/seeders/RagKnowledgeSeeder.php`

