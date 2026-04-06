# ⚙️ Especificação Técnica: Módulo de Configurações e Suporte IA (Nexora ERP)

Este documento detalha as regras de negócio, a arquitetura de dados e a integração de Inteligência Artificial para o ecossistema **Nexora ERP**.

---

## 🛠️ 1. Arquitetura do Sistema
* **Backend:** Laravel 11.x (PHP 8.2+)
* **Frontend:** React + Tailwind CSS (via Inertia.js)
* **IA:** Integração via API (OpenAI/Gemini) com técnica de **RAG** (Retrieval-Augmented Generation).

---

## 📂 2. Estrutura de Configurações

### 2.1 Geral e Identidade
* **Identidade Visual:** Gerenciamento de logotipos (Dark/Light mode) e Favicon.
* **Localização:** Configuração de Timezone (UTC-3), Idioma e Formatos de Moeda.

### 2.2 Regras de Venda (Core Business)
* **Estoque Negativo:** Permite ou bloqueia a finalização de venda se `saldo < pedido`.
* **Regime de Venda:** * **Fiscal:** Emissão obrigatória de NF-e/NFC-e.
    * **Gerencial:** Apenas baixa de estoque e lançamento financeiro interno.
    * **Híbrido:** Seleção manual no PDV.
* **Desconto Máximo:** Trava percentual para usuários sem nível de gerência.

### 2.3 Módulo Fiscal (NF-e)
* **Certificado Digital:** Upload de arquivo `.pfx` com armazenamento criptografado.
* **Ambiente:** Alternância entre `Homologação` e `Produção`.
* **Sequenciamento:** Controle de Série e Número da última nota emitida.
* **Tributação:** Configuração de Alíquota padrão de ICMS, PIS e COFINS conforme o Regime Tributário da empresa.

---

## 🤖 3. Suporte Inteligente (Nexora AI)

O suporte é provido por um agente de IA integrado diretamente ao banco de dados e manuais do sistema.

### 3.1 Fluxo de Funcionamento (RAG)
1.  **Ingestão:** Manuais em PDF são convertidos em vetores no banco de dados.
2.  **Contextualização:** A IA identifica em qual rota (`url`) o usuário está para oferecer ajuda específica.
3.  **Resolução:** O agente responde dúvidas sobre "Como emitir nota" ou "Como cadastrar produto".
4.  **Escalonamento:** Caso a IA não resolva, um ticket de suporte humano é gerado automaticamente no banco `support_tickets`.

### 3.2 Comportamento Proativo
* **Alerta de Licença:** Se o status da licença for `EXPIRADO`, a IA inicia o chat automaticamente com orientações para regularização.
* **Erro de Validação:** Caso a SEFAZ retorne erro, a IA traduz o código técnico (ex: Rejeição 203) para uma linguagem simples para o usuário.

---

## 📊 4. Modelo de Dados (Tabelas Principais)

### Tabela `settings`
| Coluna | Tipo | Descrição |
| :--- | :--- | :--- |
| `key` | String (Unique) | Chave de identificação (ex: `sale_negative_stock`) |
| `value` | Text | Valor da configuração |
| `group` | String | Agrupador (General, Fiscal, Sales) |

### Tabela `support_knowledge`
| Coluna | Tipo | Descrição |
| :--- | :--- | :--- |
| `content` | LongText | Conteúdo do manual/documentação |
| `embedding` | Vector | Representação vetorial para busca semântica |

---

## 🚀 5. Roadmap de Implementação
- [ ] Implementar CRUD de Configurações via Key-Value Pair.
- [ ] Integrar biblioteca de manipulação de Certificado Digital (ex: `nfephp-org/nfephp`).
- [ ] Desenvolver Componente React de Chat com WebSocket (Laravel Reverb) para respostas em tempo real.
