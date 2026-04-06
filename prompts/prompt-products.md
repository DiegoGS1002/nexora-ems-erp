# Documentação de Interface: Cadastro de Produtos (Nexora EMS ERP)

Este guia descreve os requisitos funcionais e a estrutura de dados para o módulo de cadastro de novos produtos, visando manter a consistência visual e a integridade das informações no banco de dados.

---

## 1. Visão Geral da Interface
A tela de **Novo Produto** é dividida em abas e seções modulares para facilitar o preenchimento e a leitura. A estrutura base deve seguir a ordem lógica do formulário principal.

### Abas Superiores
As abas permitem a navegação entre diferentes categorias de dados do produto:
1. **Dados Gerais** (Aba Ativa)
2. **Preços e Custos**
3. **Estoque**
4. **Imagens e Mídia**
5. **Fornecedores**
6. **Tributação**
7. **Detalhes Adicionais**

---

## 2. Seção: Informações Básicas
Esta seção é o núcleo do cadastro do produto. Os campos marcados com um asterisco vermelho (`*`) são obrigatórios.

| Campo | Tipo | Descrição/Regra | Exemplo |
| :--- | :--- | :--- | :--- |
| **Código do Produto** * | Text (Lock) | Identificador interno único, gerado automaticamente pelo sistema. | `PROD-000123` |
| **Código de Barras (EAN)** | Text | Código universal do produto para leitura via scanner. | `7891234567890` |
| **Nome do Produto** * | Text | Título principal do produto (Limite: 120 caracteres). | `Monitor LED 24" Full HD` |
| **Descrição Curta** | Text | Breve resumo comercial (Limite: 200 caracteres). | `Monitor LED 24 polegadas, resolução Full HD, 75Hz...` |
| **Categoria** * | Select | Grupo principal (Informatica, Móveis, etc.). | `Informática` |
| **Marca** | Text | Fabricante do produto. | `Dell` |
| **Unidade de Medida** * | Select | Unidade de venda (UN, CX, KG, LT). | `UN - Unidade` |
| **Tipo de Produto** * | Radio | Escolha entre: `Produto Físico` ou `Serviço`. | `Produto Físico` |
| **Natureza** * | Select | Finalidade (Mercadoria para Revenda, Uso e Consumo). | `Mercadoria para Revenda` |
| **Linha de Produto** | Select | Subgrupo ou coleção específica. | `Monitores` |

---

## 3. Seção: Dimensões e Peso
Essenciais para o cálculo automático de fretes e otimização de logística no estoque.

* **Peso Líquido (kg):** Peso real do item.
* **Peso Bruto (kg):** Peso do item + embalagem de envio.
* **Altura / Largura / Profundidade (cm):** Dimensões para cubagem.

---

## 4. Seção: Descrição Completa
Editor de texto rico (Rich Text) para detalhamento técnico, especificações e diferenciais.
* **Funcionalidades:** Negrito, Itálico, Listas (bullets/numeração), Inserção de links e imagens.
* **Sugestão de Uso:** Utilizar para tabelas de especificações técnicas detalhadas.

---

## 5. Seções Laterais (Colunas à Direita)

### A. Status do Produto
* **Ativo / Inativo:** Define se o produto aparecerá em vendas e pesquisas no sistema.
* **Data de Cadastro:** Campo preenchido automaticamente com data e hora.

### B. Imagem do Produto
* **Área de Dropzone:** Suporte para arraste de arquivos (PNG, JPG, WEBP até 5MB).
* **Miniatura:** Exibição da imagem principal com opção de remoção.

### C. Destaques do Produto
Lista de diferenciais em formato de tags azuis para facilitar a visualização comercial.
* *Exemplo:* `Tela Full HD`, `75Hz`, `Baixo Consumo`.

### D. Tags
Palavras-chave internas para otimização de buscas globais no sistema.
* *Exemplo:* `monitor`, `led`, `dell`, `full hd`.

---

## 6. Ações de Rodapé e Cabeçalho
* **Cancelar:** Descarta as alterações e retorna à lista de produtos.
* **Salvar e Novo:** Salva o registro atual e limpa os campos para um novo cadastro.
* **Salvar Produto:** Salva o registro e mantém o usuário na tela de edição.

---

### Notas de Desenvolvimento
* **Validação em Tempo Real:** O sistema deve informar erros de campos obrigatórios imediatamente ao sair do campo (`onBlur`).
* **Responsividade:** Manter a estrutura de colunas (2/3 para dados centrais e 1/3 para status/mídia) em telas desktop.
* **Acessibilidade:** Garantir que todos os campos sejam acessíveis via navegação por teclado (Tab).

---
*Nexora ERP - Módulo de Gestão de Catálogo - v1.0*
