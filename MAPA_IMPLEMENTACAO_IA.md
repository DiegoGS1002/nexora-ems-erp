# 🗺️ Mapa da Implementação - Agentes de IA

Este documento mostra exatamente onde cada componente está localizado no projeto.

## 📁 Estrutura de Arquivos

```
nexora-ems-erp/
│
├── 📱 FRONTEND
│   └── resources/views/
│       ├── layouts/
│       │   └── app.blade.php                    ← Chat integrado aqui
│       └── livewire/
│           └── ai-chat-bubble.blade.php         ← Interface do chat (180 linhas)
│
├── ⚙️ BACKEND
│   └── app/
│       ├── Services/
│       │   └── AiAssistantService.php           ← Lógica principal (250 linhas)
│       │
│       ├── Livewire/
│       │   └── AiChatBubble.php                 ← Componente reativo (130 linhas)
│       │
│       ├── Http/Middleware/
│       │   └── DetectAiModule.php               ← Detecção de módulo (80 linhas)
│       │
│       └── Console/Commands/
│           └── TestAiAssistant.php              ← Comando de teste (90 linhas)
│
├── 🔧 CONFIGURAÇÃO
│   └── config/
│       ├── gemini.php                           ← Config API Gemini (65 linhas)
│       └── ai_contexts.php                      ← 11 contextos especializados (350 linhas)
│
├── 📚 DOCUMENTAÇÃO
│   ├── docs/
│   │   ├── AGENTES_IA_MODULOS.md               ← Doc completa (~50 páginas)
│   │   ├── INICIO_RAPIDO_IA.md                 ← Guia 5 min (~15 páginas)
│   │   └── EXEMPLOS_INTEGRACAO_IA.md           ← Exemplos práticos (~20 páginas)
│   │
│   ├── README_IMPLEMENTACAO_IA.md              ← Visão técnica (~25 páginas)
│   ├── RESUMO_EXECUTIVO_IA.md                  ← Visão executiva (~10 páginas)
│   └── INDICE_DOCUMENTACAO_IA.md               ← Índice completo (~5 páginas)
│
├── 🛠️ SCRIPTS
│   └── install-ai.sh                            ← Instalação automatizada
│
└── 📝 CONFIGURAÇÃO
    └── .env.example                             ← Variáveis de ambiente (+ 10 linhas)
```

---

## 🎯 Localização por Função

### 1. Interface do Usuário

**Onde**: `resources/views/livewire/ai-chat-bubble.blade.php`

```blade
📍 Linha 1-40:   Botão flutuante e estrutura principal
📍 Linha 41-90:  Header do chat e controles
📍 Linha 91-140: Container de mensagens
📍 Linha 141-180: Input area e form
```

**O que faz**:
- Renderiza o chat flutuante
- Mostra mensagens e perguntas sugeridas
- Gerencia interações do usuário

---

### 2. Lógica do Chat (Livewire Component)

**Onde**: `app/Livewire/AiChatBubble.php`

```php
📍 Linha 1-20:   Propriedades públicas
📍 Linha 21-30:  Mount (inicialização)
📍 Linha 31-60:  sendMessage() - Enviar mensagem
📍 Linha 61-70:  clearChat() - Limpar conversa
📍 Linha 71-90:  Métodos auxiliares
📍 Linha 91-130: Getters de propriedades computadas
```

**O que faz**:
- Gerencia estado do chat
- Rate limiting
- Validação de entrada
- Interação com o Service

---

### 3. Lógica de Negócio (Service)

**Onde**: `app/Services/AiAssistantService.php`

```php
📍 Linha 1-40:   getResponse() - Resposta da IA
📍 Linha 41-70:  getStreamingResponse() - Streaming
📍 Linha 71-100: getSystemInstruction() - Contexto
📍 Linha 101-130: buildConversationHistory()
📍 Linha 131-160: formatContextData()
📍 Linha 161-190: getSuggestedQuestions()
📍 Linha 191-250: Gerenciamento de cache/histórico
```

**O que faz**:
- Integração com Gemini API
- Gerenciamento de contexto
- Cache de histórico
- Perguntas sugeridas

---

### 4. Detecção de Módulo (Middleware)

**Onde**: `app/Http/Middleware/DetectAiModule.php`

```php
📍 Linha 1-20:  Declaração da classe
📍 Linha 21-50: Mapeamento de rotas → módulos
📍 Linha 51-80: Lógica de detecção e compartilhamento
```

**O que faz**:
- Detecta módulo pela URL
- Compartilha com views
- Adiciona ao request

---

### 5. Comando de Teste (CLI)

**Onde**: `app/Console/Commands/TestAiAssistant.php`

```php
📍 Linha 1-20:  Assinatura do comando
📍 Linha 21-40: Validações iniciais
📍 Linha 41-70: Chamada à API
📍 Linha 71-90: Exibição de resultados
```

**O que faz**:
- Testa integração via CLI
- Valida configuração
- Mostra perguntas sugeridas

---

### 6. Configuração da API

**Onde**: `config/gemini.php`

```php
📍 Linha 1-20:  API Key
📍 Linha 21-40: Modelo padrão
📍 Linha 41-65: Timeouts e parâmetros
```

**O que faz**:
- Centraliza configurações da API
- Define valores padrão

---

### 7. Contextos dos Módulos

**Onde**: `config/ai_contexts.php`

```php
📍 Linha 1-30:   Contexto Financeiro
📍 Linha 31-60:  Contexto RH
📍 Linha 61-90:  Contexto Produção
📍 Linha 91-120: Contexto Estoque
📍 Linha 121-350: Demais módulos (7)
```

**O que faz**:
- Define instruções especializadas
- Conhecimento específico por módulo

---

## 🔗 Fluxo de Dados

```
1. Usuário clica no botão flutuante
   📍 resources/views/livewire/ai-chat-bubble.blade.php (linha ~35)
   
2. Chat abre e carrega histórico
   📍 app/Livewire/AiChatBubble.php → mount() (linha ~21)
   
3. Usuário digita mensagem
   📍 resources/views/livewire/ai-chat-bubble.blade.php (linha ~165)
   
4. Form submitido
   📍 app/Livewire/AiChatBubble.php → sendMessage() (linha ~35)
   
5. Validação + Rate limiting
   📍 app/Livewire/AiChatBubble.php (linha ~37-50)
   
6. Chama o Service
   📍 app/Services/AiAssistantService.php → getResponse() (linha ~25)
   
7. Busca contexto do módulo
   📍 config/ai_contexts.php (linha correspondente ao módulo)
   
8. Chama Gemini API
   📍 app/Services/AiAssistantService.php (linha ~40)
   
9. Retorna resposta
   📍 app/Livewire/AiChatBubble.php (linha ~60)
   
10. Atualiza interface
    📍 resources/views/livewire/ai-chat-bubble.blade.php (linha ~130)
```

---

## 🎨 Customização por Arquivo

### Mudar Visual do Chat
**Arquivo**: `resources/views/livewire/ai-chat-bubble.blade.php`
```blade
Linha 33-36: Cores do botão flutuante
Linha 56: Tamanho da janela (w-96 h-[600px])
Linha 61-65: Cores do header
Linha 150-155: Cores das mensagens
```

### Adicionar Novo Módulo
**Arquivo 1**: `config/ai_contexts.php`
```php
Adicionar no final: 'novo_modulo' => "Instruções..."
```

**Arquivo 2**: `app/Services/AiAssistantService.php`
```php
Linha ~167: Adicionar em getSuggestedQuestions()
```

**Arquivo 3**: `app/Http/Middleware/DetectAiModule.php`
```php
Linha ~25: Adicionar mapeamento de rota
```

### Ajustar Rate Limiting
**Arquivo**: `app/Livewire/AiChatBubble.php`
```php
Linha ~44: Mudar de 10 para outro valor
```

### Mudar Modelo da IA
**Arquivo**: `.env`
```env
Linha ~72: GEMINI_MODEL=gemini-pro (ou outro)
```

### Ajustar Timeout
**Arquivo**: `.env`
```env
Linha ~75: GEMINI_REQUEST_TIMEOUT=120 (segundos)
```

---

## 📝 Onde Editar Para...

### "Quero mudar as perguntas sugeridas"
→ `app/Services/AiAssistantService.php` (linha 161-190)

### "Quero adicionar um novo módulo"
→ 3 arquivos:
1. `config/ai_contexts.php` (nova chave)
2. `app/Services/AiAssistantService.php` (perguntas)
3. `app/Http/Middleware/DetectAiModule.php` (rota)

### "Quero mudar a cor do chat"
→ `resources/views/livewire/ai-chat-bubble.blade.php` (classes Tailwind)

### "Quero aumentar o limite de mensagens"
→ `app/Livewire/AiChatBubble.php` (linha 44)

### "Quero mudar o tempo de cache"
→ `app/Services/AiAssistantService.php` (linha 220)

### "Quero adicionar logs customizados"
→ `app/Services/AiAssistantService.php` (linha 35-40)

### "Quero integrar em uma página específica"
→ Ver exemplos em `docs/EXEMPLOS_INTEGRACAO_IA.md`

---

## 🔍 Busca Rápida de Código

### Buscar "Gemini API call"
```bash
grep -n "Gemini::" app/Services/AiAssistantService.php
# Resultado: linha ~40
```

### Buscar "Rate limiting"
```bash
grep -n "RateLimiter" app/Livewire/AiChatBubble.php
# Resultado: linha ~44
```

### Buscar "system instruction"
```bash
grep -n "systemInstruction" app/Services/AiAssistantService.php
# Resultado: linhas ~42, ~75
```

### Buscar contextos
```bash
ls -la config/ai_contexts.php
cat config/ai_contexts.php | grep "=>"
```

---

## 🗂️ Arquivos por Tamanho

| Arquivo | Linhas | Tamanho |
|---------|--------|---------|
| `config/ai_contexts.php` | 350 | ~15 KB |
| `app/Services/AiAssistantService.php` | 250 | ~10 KB |
| `resources/views/livewire/ai-chat-bubble.blade.php` | 180 | ~8 KB |
| `app/Livewire/AiChatBubble.php` | 130 | ~5 KB |
| `app/Console/Commands/TestAiAssistant.php` | 90 | ~4 KB |
| `app/Http/Middleware/DetectAiModule.php` | 80 | ~3 KB |
| `config/gemini.php` | 65 | ~2 KB |

**Total de código**: ~1.145 linhas | ~47 KB

---

## 📦 Dependências

### Composer (package instalado)
```bash
vendor/google-gemini-php/client/
vendor/google-gemini-php/laravel/
```

### Uso no código
```php
// Em AiAssistantService.php (linha 4)
use Gemini\Laravel\Facades\Gemini;
```

---

## 🎯 Checklist de Localização

Marque quando souber onde está cada componente:

- [ ] Interface do chat (Blade)
- [ ] Componente Livewire (PHP)
- [ ] Service principal (PHP)
- [ ] Middleware de detecção (PHP)
- [ ] Comando de teste (PHP)
- [ ] Config da API (PHP)
- [ ] Contextos dos módulos (PHP)
- [ ] Documentação completa (MD)
- [ ] Script de instalação (SH)

---

## 💡 Dica Pro

Use seu editor/IDE para:

1. **Buscar símbolos**: `Ctrl+Shift+F` (VS Code)
2. **Ir para definição**: `F12` (VS Code)
3. **Buscar referências**: `Shift+F12` (VS Code)
4. **Estrutura do arquivo**: `Ctrl+Shift+O` (VS Code)

---

## 🎓 Conclusão

Agora você sabe **exatamente onde está cada coisa**!

- ✅ 7 arquivos de código-fonte
- ✅ 2 arquivos de configuração
- ✅ 6 arquivos de documentação
- ✅ 1 script de instalação

**Total**: 16 arquivos mapeados

---

**Use este mapa sempre que precisar editar algo!** 🗺️

