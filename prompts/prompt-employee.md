# Documentação de Interface: Cadastro de Funcionários (Nexora EMS ERP)

Este documento define os requisitos e a organização dos dados para o formulário de inclusão de novos colaboradores, garantindo a padronização das informações de RH e controle de acesso.

---

## 1. Estrutura de Navegação (Abas)
O formulário é organizado em abas para separar informações pessoais, profissionais e sensíveis:
1. **Dados Pessoais** (Aba Ativa)
2. **Dados Profissionais**
3. **Acesso ao Sistema**
4. **Informações Bancárias**
5. **Documentos**
6. **Observações**

---

## 2. Painel Central: Dados Pessoais

### 2.1 Informações Pessoais
Dados básicos de identificação e contato do colaborador.

| Campo | Tipo | Descrição/Regra |
| :--- | :--- | :--- |
| **Nome Completo** * | Texto | Nome conforme documento oficial. |
| **Nome Social** | Texto | Nome de preferência do colaborador. |
| **CPF** * | Texto (Mask) | Validação obrigatória de dígito verificador. |
| **Data de Nascimento** * | Date | Calendário para seleção. |
| **Gênero** | Select | Opções: Feminino, Masculino, Outros. |
| **Estado Civil** | Select | Solteiro(a), Casado(a), Divorciado(a), etc. |
| **Nacionalidade** | Select | País de origem. |
| **E-mail** * | Email | Validação de formato (exibe ícone de check se válido). |
| **Telefone Principal** * | Tel (Mask) | Com flag de país (Brasil padrão). |
| **Telefone Secundário** | Tel (Mask) | Com flag de país. |
| **RG / Órgão Emissor** | Texto | Número do RG e sigla do órgão (ex: SSP/MG). |
| **Data de Expedição** | Date | Data de emissão do RG. |
| **Naturalidade** | Texto | Cidade e Estado de nascimento. |

### 2.2 Endereço
Integrado com a funcionalidade de busca automática.
* **CEP:** Possui botão **[🔍 Buscar CEP]** para preenchimento via BrasilAPI.
* **Logradouro, Número, Complemento, Bairro, Cidade, Estado, País.**
* **Endereço Completo:** Uma string concatenada exibida para conferência rápida.

### 2.3 Contato de Emergência
Informações para casos de necessidade médica ou administrativa.
* **Nome, Parentesco e Telefone.**

---

## 3. Painel Lateral (Direito)

### 3.1 Foto do Funcionário
* **Upload:** Área para clicar ou arrastar imagens (PNG, JPG, WEBP até 5MB).
* **Gestão:** Opção de "Remover foto" abaixo da miniatura.

### 3.2 Acesso ao Sistema
Define as permissões do colaborador no Nexora ERP.
* **Perfil de Acesso *:** (Vendedor, Administrador, RH, etc.) - Link para "Ver permissões".
* **Status:** Toggle/Badge entre **Ativo** e **Inativo**.
* **Data de Admissão *:** Data de início do contrato.
* **Jornada de Trabalho *:** Select (ex: 44h semanais).
* **Permitir Acesso ao Sistema:** Interruptor (Switch) para habilitar login.

### 3.3 Informações Adicionais (RH)
* **Departamento:** Select com botão `(+)` para adição rápida.
* **Cargo *:** Select com botão `(+)` para adição rápida.
* **Salário Base (R$):** Valor monetário da remuneração.
* **Registro Interno:** Código único do funcionário no ERP (ex: `FUNC-000123`).

---

## 4. Ações Globais
Localizadas no cabeçalho superior direito:
* **Cancelar:** Retorna à listagem sem salvar.
* **Salvar Funcionário:** Botão de ação principal com ícone de disquete.

---

## 5. Regras de Negócio e UX
1. **Validação Visual:** Campos como CPF, E-mail e Telefone devem exibir um ícone de "check" verde ao serem validados com sucesso.
2. **Campos Obrigatórios:** Marcados com asterisco vermelho, impedindo o envio do formulário se vazios.
3. **Máscaras de Input:** Aplicar máscaras automáticas para CPF, CEP, Telefone e Moeda.

---
*Nexora ERP - Módulo de Gestão de Pessoas - v1.0*
