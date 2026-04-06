# Documentação de Interface: Logs do Sistema (Nexora EMS ERP)

Este documento define os requisitos para a tela de monitoramento de eventos, fornecendo uma visão clara sobre as métricas de performance e o histórico detalhado de ações realizadas no sistema.

---

## 1. Indicadores de Performance (Kpis Superior)
O topo da página exibe cinco cards com métricas agregadas para uma análise rápida da saúde do sistema.

| Indicador | Descrição | Estilo Visual |
| :--- | :--- | :--- |
| **Total de Logs** | Volume total de eventos registrados. | Azul (com % de variação) |
| **Logs de Sucesso** | Operações concluídas sem erros. | Verde (% do total) |
| **Logs de Aviso** | Alertas que não impediram a execução. | Amarelo (% do total) |
| **Logs de Erro** | Falhas críticas ou exceções. | Vermelho (% do total) |
| **Usuários Ativos** | Quantidade de usuários nas últimas 24h. | Azul Claro (% variação) |

---

## 2. Filtros de Busca e Ações
Abaixo dos indicadores, há uma seção de filtros para refinamento de dados.

* **Filtros Disponíveis:** Período (Date Range), Nível do Log (Sucesso, Aviso, Erro), Módulo (Vendas, Financeiro, etc.), Usuário, Ação/Evento e Status.
* **Busca Rápida:** Campo de texto para pesquisa global nos registros.
* **Botão Atualizar:** Recarrega os dados em tempo real.
* **Botão Exportar:** Permite baixar os logs em formatos como CSV ou PDF.

---

## 3. Tabela de Registros (Auditoria)
A tabela principal deve conter as seguintes colunas, garantindo a rastreabilidade total:

1.  **Data/Hora:** Carimbo de tempo preciso da ocorrência.
2.  **Nível:** Badge colorido indicando o status (Sucesso, Aviso, Erro).
3.  **Usuário:** Foto, nome e e-mail do autor da ação (ou "Sistema" para automações).
4.  **Ação:** Tipo de evento (LOGIN, ALTERAÇÃO, EXCLUSÃO, BACKUP, etc.).
5.  **Módulo:** Badge identificando a área do ERP (Segurança, Cadastros, Vendas, etc.).
6.  **Descrição:** Texto explicativo detalhando o que ocorreu (ex: "Falha ao excluir pedido").
7.  **IP:** Endereço de rede de onde partiu a requisição.
8.  **Mais Informações:** Botão "Detalhes" para abrir um modal com o JSON completo da requisição e menu de contexto (três pontos).

---

## 4. Paginação e Navegação
* **Rodapé da Tabela:** Exibe o contador de registros (Ex: "Mostrando 1 a 10 de 24.752 registros").
* **Controle de Paginação:** Navegação entre páginas e seletor de quantidade de itens por página (Ex: 10, 50, 100).

---

## 5. Regras de Negócio (Backend)

### A. Escrita de Logs
Toda ação de escrita (Create, Update, Delete) no banco de dados do Nexora deve disparar um evento de Log automaticamente.
* **Dica Laravel:** Utilize *Observers* ou *Spatie Activity Log* para automatizar essa captura nos Models.

### B. Níveis de Severidade
* **Sucesso:** Operações normais (Ex: Login, Cadastro salvo).
* **Aviso:** Tentativas de acesso negadas ou validações de negócio falhas.
* **Erro:** Exceções do sistema, erros de integração (ex: erro 500 na BrasilAPI).

### C. Retenção de Dados
Devido ao alto volume de logs em um ERP, defina uma política de expurgo ou arquivamento para registros com mais de 90 dias para não sobrecarregar o banco de dados principal.

---

## 6. Exemplo de Registro de Integração
Conforme visualizado na imagem, o sistema rastreia integrações externas:
* **Evento:** Sincronização.
* **Descrição:** "Sincronização com API externa - API: BrasilAPI - CEP - 15 registros".

---
*Nexora ERP - Módulo de Auditoria e Segurança - v1.0*
