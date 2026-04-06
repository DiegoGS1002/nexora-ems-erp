# Documentação de Interface: Cadastro de Veículos (Nexora EMS ERP)

Este documento define os campos, a hierarquia e as regras de negócio para o formulário de novos veículos, garantindo a rastreabilidade e a gestão eficiente da frota.

---

## 1. Navegação por Abas
O módulo de veículos utiliza um sistema de abas para organizar o ciclo de vida do ativo:
1. **Dados Gerais** (Aba Ativa)
2. **Documentos**
3. **Manutenção**
4. **Seguro**
5. **Custos**
6. **Observações**
7. **Histórico**

---

## 2. Painel Central: Dados Gerais

### 2.1 Identificação do Veículo
Campos fundamentais para a legalização e registro do veículo.

| Campo | Tipo | Regra de Negócio / Máscara |
| :--- | :--- | :--- |
| **Placa** * | Texto | Formato Mercosul ou Antigo (ex: ABC1D23). |
| **RENAVAM** * | Texto | 11 dígitos numéricos com validação. |
| **Chassi** * | Texto | 17 caracteres alfanuméricos. |
| **Tipo de Veículo** * | Select | Utilitário, Caminhão, Passeio, etc. |
| **Categoria** * | Select | Particular, Aluguel, Oficial. |
| **Espécie** * | Select | Passageiro, Carga, Misto. |
| **Ano Fabricação** * | Select | Ano de produção. |
| **Ano Modelo** * | Select | Ano do modelo comercial. |
| **Marca / Modelo** * | Select | Vinculação hierárquica (ex: Toyota -> Corolla). |
| **Cor / Combustível** * | Select | Prata, Branco / Flex, Diesel, Elétrico. |

### 2.2 Informações Adicionais (Técnicas)
Dados para controle de capacidade e especificações de motorização.
* **Potência (cv) / Cilindradas (cm³):** Dados de performance.
* **Número de Portas / Cap. de Passageiros:** Configuração interna.
* **Tipo de Câmbio / Tração:** Automático/Manual e Dianteira/4x4.
* **Pesos (Bruto/Líquido) e Cap. de Carga (kg):** Crucial para logística e fretes.

### 2.3 Vinculação e Localização
Define a responsabilidade e o paradeiro atual do veículo.
* **Departamento / Motorista Responsável:** Vinculação ao RH.
* **Centro de Custo / Unidade:** Para rateio de despesas financeiras.
* **Localização Atual:** Indica a Unidade, Local/Pátio e observação específica (ex: Próximo ao portão).

---

## 3. Painel Lateral (Direito)

### 3.1 Status e Aquisição
* **Status *:** Toggle visual entre **Ativo** (Operacional) e **Inativo** (Baixado/Vendido).
* **Data de Aquisição:** Data da compra do veículo.
* **Valor de Aquisição (R$):** Valor de nota fiscal para cálculo de depreciação.

### 3.2 Foto do Veículo (Galeria)
Diferente de outros módulos, o veículo permite **múltiplas fotos** (carrossel/miniatura).
* **Upload:** Drag & drop para arquivos PNG, JPG ou WEBP até 5MB.
* **Gestão:** Opção de "Remover todas" ou gerenciar miniaturas individuais.

### 3.3 Resumo das Informações
Um card de visualização rápida (Read-only) com os dados principais:
* Placa, Marca/Modelo, Ano, Combustível e Status.

---

## 4. Cabeçalho de Ações
* **Localização:** Canto superior direito.
* **Botão Cancelar:** Aborta o preenchimento.
* **Botão Salvar Veículo:** Grava os dados no banco de dados.

---

## 5. Regras de Negócio e Validações (Backend)
1. **Unicidade:** O sistema não deve permitir dois veículos ativos com a mesma Placa, RENAVAM ou Chassi.
2. **Validação Visual:** Ícones de "check" verde ao lado dos campos Placa e RENAVAM indicam que o formato está correto e o dado foi validado.
3. **Integração:** Ao salvar, o veículo deve estar disponível para seleção no módulo de "Vendas/Entregas" e "Financeiro (Abastecimento)".

---
*Nexora ERP - Módulo de Gestão de Frota - v1.0*
