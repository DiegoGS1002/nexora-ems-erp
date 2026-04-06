# Documentação de Interface: Cadastro de Funções (Nexora EMS ERP)

Este guia define os requisitos para a página de criação e gerenciamento de funções, focando na hierarquia de permissões e controles de acesso granular.

---

## 1. Cabeçalho e Ações Principais
O topo da página contém o título informativo e os botões de controle de fluxo:
* **Título:** Cadastro de Funções (Subtítulo: "Crie e gerencie funções para controlar permissões de acesso ao sistema").
* **Botão Cancelar:** Descarta alterações.
* **Botão Salvar Função:** Ação primária com ícone de disquete.

---

## 2. Dados da Função (Painel Superior Esquerdo)
Contém as definições básicas para a identificação da função dentro do ERP.

| Campo | Tipo | Regra de Negócio |
| :--- | :--- | :--- |
| **Nome da Função** * | Texto | Nome amigável (ex: Analista Financeiro). |
| **Departamento** * | Select | Vinculação ao departamento (ex: Financeiro). |
| **Código** * | Texto | Identificador único (slug) para backend (ex: FIN-ANL). |
| **Função Superior** | Select | Opcional. Permite a **herança de permissões** de uma função pai. |
| **Descrição** | Textarea | Detalha as responsabilidades da função (limite 120/300 caracteres). |

---

## 3. Status e Configurações (Painel Superior Direito)
Configurações de estado e visibilidade da função.

* **Status *:** Toggle visual entre **Ativo** (Verde) e **Inativo** (Vermelho).
* **Permitir Atribuição:** Switch (Toggle). Define se esta função pode ser atribuída a funcionários agora.
* **Usuários com esta função:** Card informativo exibindo o contador de funcionários ativos e link para "detalhes".

---

## 4. Gerenciamento de Permissões (Área de Abas)
A parte inferior da página utiliza um sistema de abas para organizar o controle granular:
1. **Permissões** (Aba Ativa)
2. **Módulos do Sistema**
3. **Restrições de Dados**
4. **Aprovações**
5. **Histórico**

### 4.1 Interface de Permissões por Módulo
As permissões são organizadas em **Acordeões (Collapsibles)** por módulo (Dashboard, Cadastros, Vendas, etc.).

Cada módulo expandido apresenta um grid de checkboxes:
* **Visualizar / Criar / Editar / Excluir / Exportar.**
* *Comportamento:* O checkbox deve mudar para verde/marcado quando ativo.

---

## 5. Resumo e Ações Rápidas (Lateral Inferior)

### 5.1 Resumo de Permissões (Gráfico)
Um gráfico de rosca (Donut Chart) centralizado que exibe o progresso:
* **Permitidas:** (Verde)
* **Negadas:** (Vermelho)
* **Não configuradas:** (Cinza)

### 5.2 Ações Rápidas
Atalhos para produtividade do administrador:
* **Selecionar Todos:** Ativa todos os checkboxes visíveis.
* **Limpar Seleção:** Remove todas as permissões marcadas.
* **Copiar de Outra Função:** Abre modal para clonar permissões de um perfil já existente.

### 5.3 Dica de Segurança
Box informativo com ícone de lâmpada reforçando o **Princípio do Menor Privilégio**: "Garanta que usuários tenham apenas os acessos necessários."

---

## 6. Notas de Implementação (Backend & Frontend)

* **Real-time Feedback:** As permissões devem ser aplicadas em tempo real ao salvar (conforme nota informativa na interface).
* **Herança:** Se uma "Função Superior" for selecionada, os checkboxes herdados devem aparecer marcados (e possivelmente desabilitados, a menos que haja sobrescrita).
* **Acessibilidade:** Botão "Expandir Todos" para facilitar a visualização de todos os módulos de uma vez.

---
*Nexora ERP - Módulo de Segurança e Controle de Acesso - v1.0*
