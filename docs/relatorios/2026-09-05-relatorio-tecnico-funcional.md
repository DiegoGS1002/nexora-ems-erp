# Relatório técnico e funcional — Nexora EMS ERP

Data: 05/09/2026  
Referência: commit `9958e2d`, incluindo o estado local dos arquivos na data da análise.  
Objetivo: identificar falhas, ajustes necessários, melhorias e oportunidades de novas páginas.

## 1. Escopo e método

Foi realizada uma análise do repositório Laravel, com leitura de rotas, middlewares, controllers, modelos, serviços, componentes Livewire, templates selecionados, configuração de testes e dependências. Também foram executados o inventário de rotas e a suíte de testes existente.

Este documento é uma revisão ampla por amostragem orientada a risco. Não representa certificação de segurança, homologação fiscal ou validação integral de todas as telas. Não houve navegação manual em navegador, teste de carga, emissão real de documentos fiscais, avaliação de produção ou auditoria externa de dependências. Sugestões fiscais e de RH descrevem necessidades de software; regras legais e tabelas devem ser validadas separadamente pelos responsáveis.

As alterações locais preexistentes em CSS, formulários, Docker, migration de sessões e service worker foram preservadas. Os achados referentes ao service worker refletem sua versão local, ainda modificada no Git.

Classificação das evidências:

- **Confirmado no código:** comportamento diretamente demonstrado pela implementação, sem afirmar reprodução integral em produção.
- **Confirmado em execução:** resultado observado nos comandos executados.
- **Risco a validar:** hipótese sustentada pela arquitetura que requer cenário específico de teste.
- **Melhoria proposta:** evolução de produto ou engenharia, sem caracterizar defeito atual.

Prioridades:

| Prioridade | Tratamento |
| --- | --- |
| P0 | Corrigir antes de ampliar uso com dados reais ou entre empresas. |
| P1 | Próximo ciclo de estabilização; afeta integridade ou operação central. |
| P2 | Evolução planejada de qualidade e funcionalidade. |
| P3 | Expansão posterior, conforme demanda. |

## 2. Avaliação geral

O projeto já possui uma base funcional ampla: cadastros em Livewire, vendas, compras, financeiro, estoque, produção, RH, suporte e serviços fiscais especializados. Existem testes específicos para autenticação, contexto empresarial, cadastros, indicadores e diferentes operações fiscais. A divisão de rotas por domínio e a presença de serviços são pontos favoráveis à manutenção.

A principal necessidade é consolidar os controles transversais e a integridade dos lançamentos. A existência de contexto empresarial não garante, por si só, isolamento das consultas. O catálogo de módulos também apresenta funcionalidades que apontam para telas genéricas ou incompletas. Há divergências concretas entre a API de estoque e o fluxo Livewire, além de problemas no tratamento de valores recebidos.

Ordem recomendada: permissões e isolamento → valores financeiros e estoque → ambiente de testes → rotas incompletas → ampliação funcional.

## 3. Falhas identificadas

### F01 — Configurações globais acessíveis sem exigência de administrador

**Prioridade:** P0. **Evidência:** confirmado no código.

Fontes: `routes/administracao.php`, `app/Http/Requests/StoreConfigurationRequest.php`, `app/Http/Controllers/ConfigurationController.php`, `app/Models/Setting.php`.

As rotas GET e POST de `/configuracoes` estão fora do grupo `admin`. O FormRequest retorna `true` em `authorize()`, e o controller não verifica permissão administrativa. O middleware `MaintenanceERP` libera `configuration.*`. A tabela lógica de configurações é consultada por chave, sem escopo empresarial.

**Impacto:** um usuário autenticado que satisfaça os middlewares gerais pode consultar e alterar parâmetros globais, incluindo configurações comerciais, de segurança e integração.

**Ajuste:** proteger leitura e gravação com autorização explícita; separar parâmetros da plataforma dos parâmetros da empresa; evitar devolver segredos nos formulários.

**Aceite:** usuário comum recebe 403 nas duas rotas; administrador empresarial só altera parâmetros da sua empresa; configurações de plataforma exigem perfil próprio.

### F02 — Isolamento empresarial incompleto nos cadastros examinados

**Prioridade:** P0. **Evidência:** confirmado no código para produtos; abrangência dos demais domínios a validar.

Fontes: `app/Http/Middleware/ResolveTenantContext.php`, `app/Tenancy/TenantContext.php`, `app/Providers/AppServiceProvider.php`, `app/Models/Product.php`, `app/Http/Controllers/Api/ProductApiController.php`.

O middleware define uma empresa no contexto, mas a consulta de produtos não utiliza esse contexto. O modelo Product não declara escopo empresarial, e seus campos de preenchimento não incluem `company_id`. A API consulta e resolve produtos globalmente. O provider examinado registra o contexto, sem instalar filtro global de produtos.

**Impacto:** se o ERP pretende separar catálogos por empresa, a implementação examinada permite compartilhamento indevido de consulta e manutenção. Caso o catálogo global seja deliberado, isso precisa estar definido e protegido por permissões específicas.

**Ajuste:** definir a propriedade de cada entidade; implementar filtros obrigatórios, vínculo empresarial, validações de relacionamentos e migração dos dados existentes. Abranger exports, jobs e ferramentas de IA, além das telas.

**Aceite:** duas empresas com registros distintos não conseguem consultar, alterar, excluir ou associar registros uma da outra, inclusive usando IDs conhecidos e API direta.

### F03 — Permissões do menu não protegem automaticamente os endpoints

**Prioridade:** P0. **Evidência:** confirmado no código no fluxo de produtos.

Fontes: `app/Http/Controllers/ModulePageController.php::accessibleModules`, `routes/api.php`, `app/Http/Controllers/Api/ProductApiController.php`, `app/Http/Requests/Api/StoreProductRequest.php`.

A navegação filtra módulos pelo campo `modules`. Entretanto, a API de produtos exige autenticação e contexto, mas não verifica acesso ao módulo ou à operação. O FormRequest de criação autoriza incondicionalmente.

**Impacto:** ocultar o módulo no menu não impede chamadas diretas para suas operações.

**Ajuste:** definir policies por recurso e ação, aplicadas em controllers e métodos Livewire. Separar visualizar, criar, alterar, excluir, aprovar e exportar.

**Aceite:** usuário sem acesso a cadastro recebe 403 também na API de produtos; permissões de leitura não permitem gravação.

### F04 — Conversão de valores pode multiplicar recebimentos por 100

**Prioridade:** P0. **Evidência:** confirmado no código.

Fontes: `app/Livewire/Financeiro/ContasReceber.php::openReceipt` e `registerReceipt`, `resources/views/livewire/financeiro/contas-receber/index.blade.php`.

O formulário inicia o valor com `(string) $account->amount`, valida como `numeric` e depois remove pontos antes de converter vírgula em ponto. Assim, `100.50` passa na validação e se transforma em `10050`. Por outro lado, o campo sugere `0,00`, formato incompatível com a validação numérica utilizada sem normalização prévia.

**Impacto:** baixa de título com valor incorreto e experiência inconsistente para moeda brasileira.

**Ajuste:** definir formato único de transporte, normalizar antes da validação e trabalhar com decimal controlado ou centavos inteiros.

**Aceite:** cobrir `100.50`, `100,50`, `1.234,56`, valor padrão do modal, valores inválidos e repetição da baixa, com resultado monetário exato.

### F05 — Edição de movimento não recompõe o saldo de estoque

**Prioridade:** P1. **Evidência:** confirmado no código.

Fonte: `app/Livewire/Estoque/Movimentacao.php::save`, `edit` e `delete`.

Ao editar um movimento, somente o registro é atualizado. O saldo do produto não recebe a diferença entre o movimento antigo e o novo. Se o produto do lançamento for alterado, os saldos dos dois produtos também não são recompostos.

**Exemplo:** uma entrada de 10 unidades alterada para 20 permanece com efeito de 10 no saldo. A exclusão posterior tenta reverter 20.

**Ajuste:** preferir estorno e novo lançamento, preservando histórico, ou reverter o efeito anterior e aplicar o novo na mesma transação.

**Aceite:** criação, edição, troca de produto e estorno mantêm saldo conciliado com o histórico.

### F06 — Movimento e saldo são gravados sem atomicidade

**Prioridade:** P1. **Evidência:** confirmado no código; efeito sob concorrência a validar.

Fonte: `app/Livewire/Estoque/Movimentacao.php::save` e `delete`.

A criação do movimento e a gravação do produto são operações separadas, sem transação ou bloqueio. A exclusão também altera o saldo antes de remover o movimento. O saldo segue o padrão ler, calcular e salvar.

**Impacto:** falhas intermediárias podem deixar saldo e histórico divergentes; operações simultâneas podem sobrescrever alterações. A saída não verifica saldo disponível nesse método.

**Ajuste:** centralizar a movimentação em serviço transacional com bloqueio ou atualização atômica e aplicar a política de estoque negativo configurada.

**Aceite:** falha forçada entre gravações não persiste metade da operação; duas saídas simultâneas respeitam o saldo e a política de negócio.

### F07 — API de movimentações utiliza entidade diferente da tela

**Prioridade:** P1. **Evidência:** confirmado no código.

Fontes: `app/Http/Controllers/Api/StockMovementApiController.php`, `app/Models/Entrance.php`, `app/Models/StockMovement.php`, `app/Livewire/Estoque/Movimentacao.php`.

A API `/api/stock-movements` manipula Entrance, cujo preenchimento contém apenas `name` e `description`. A tela utiliza StockMovement com produto, quantidade, tipo e custo, além de alterar saldo.

**Impacto:** integrações podem receber sucesso sem registrar a mesma operação apresentada na tela ou atualizar estoque.

**Ajuste:** unificar contrato e serviço de movimentação; definir migração ou descontinuação da estrutura legada.

**Aceite:** movimento criado pela API aparece na tela e afeta o saldo exatamente uma vez.

### F08 — Recebimentos parciais e indicadores não representam saldo residual

**Prioridade:** P1. **Evidência:** confirmado no código.

Fonte: `app/Services/ContasReceberService.php`.

`registerReceipt()` substitui `received_amount`; não registra parcelas de recebimento em histórico próprio. `getKpiData()` soma o valor integral dos títulos parciais em `totalPending`. `syncOverdueStatus()` considera apenas títulos Pending, excluindo Partial.

**Exemplo:** título de R$ 1.000 com R$ 400 recebidos ainda contribui com R$ 1.000 para o total pendente, em vez de R$ 600.

**Ajuste:** registrar cada recebimento, calcular saldo residual, controlar estornos e estabelecer o comportamento de títulos parciais vencidos. Esclarecer se o valor informado é incremental ou acumulado.

**Aceite:** recebimentos sucessivos de 400 e 600 liquidam um título de 1.000; o primeiro deixa saldo de 600, sem perder histórico.

### F09 — Pagamento de folha sem proteção contra duplicidade no serviço

**Prioridade:** P1. **Evidência:** confirmado no código; repetição pela interface a validar.

Fonte: `app/Services/PayrollService.php::markAsPaid`.

Cada chamada marca a folha como paga e cria uma nova conta a pagar quitada. Não há verificação de pagamento anterior nem transação abrangendo as duas operações nesse serviço.

**Impacto:** repetição ou concorrência pode duplicar o histórico financeiro; falha na criação do título pode deixar folha paga sem contrapartida.

**Ajuste:** vínculo único entre folha e lançamento financeiro, operação idempotente e transação. Validar transições de estado também no serviço.

**Aceite:** repetir o pagamento, inclusive simultaneamente, produz uma única contrapartida financeira.

### F10 — Rotas registradas para métodos inexistentes

**Prioridade:** P1. **Evidência:** confirmado em inventário e análise estática.

`php artisan route:list --json` retornou **296 rotas**. O cruzamento das ações `App\\Http\\Controllers\\...@metodo` com as declarações dos controllers encontrou **109 ações sem método correspondente**. A classe base Controller examinada está vazia.

Exemplos: `BankAccountController@store`, `DriverManagementController@create` e ações CRUD de StockController, que implementa apenas `index()`.

**Impacto:** superfície de rotas anuncia operações não implementadas; quando a requisição alcança essas ações, pode ocorrer erro de execução. Middlewares podem impedir ou interceptar parte dos acessos; não foram feitas 109 reproduções HTTP.

**Ajuste:** restringir `Route::resource()` com `only()`/`except()` e implementar somente operações aprovadas.

**Aceite:** nenhuma rota própria aponta para método inexistente; rotas removidas respondem 404/405 conforme o caso.

### F11 — Funcionalidades anunciadas ainda são placeholders ou atalhos genéricos

**Prioridade:** P2. **Evidência:** confirmado no código.

Fontes: `app/Http/Controllers/ModulePageController.php`, FinancialReportsController, SalesReportController, RomaneioController, TransportReportController, VehicleTrackingController e VehicleMaintenanceController.

Relatórios financeiros, relatórios de vendas, romaneio, relatórios de transporte, rastreamento e manutenção possuem telas de desenvolvimento nas implementações examinadas. Algumas ações de impressão abortam com 501. Inventários e Transferência apontam para `stock.index`; Ficha Técnica aponta para `products.index`.

**Impacto:** o usuário pode interpretar itens do menu como processos completos, embora a rota exista sem a funcionalidade correspondente. `Route::has()` não verifica maturidade funcional.

**Ajuste:** explicitar disponibilidade por funcionalidade e separar os fluxos realmente diferentes. Concluir relatórios prioritários antes de promover sua impressão.

**Aceite:** cada item disponível abre o processo prometido; itens futuros são identificados claramente e não apresentam ações de impressão inoperantes.

### F12 — Filtro de estoque exclui parte do último dia

**Prioridade:** P1. **Evidência:** confirmado no código.

Fonte: `app/Livewire/Estoque/Movimentacao.php::movements` e `stats`.

Os filtros usam `whereBetween('created_at', [$startDate, $endDate])`, com datas no formato `Y-m-d`. O limite superior não cobre horários posteriores ao início da data final.

**Impacto:** movimentos do dia corrente podem não aparecer nas listagens e estatísticas iniciais.

**Ajuste:** usar início do primeiro dia e limite exclusivo do dia seguinte ao último, considerando timezone e armazenamento dos timestamps.

**Aceite:** registros às 00:00, 12:00 e 23:59 do dia final são incluídos; o dia seguinte é excluído.

### F13 — Cache de API sem separação explícita por usuário

**Prioridade:** P1. **Evidência:** confirmado no código; exposição entre sessões a validar em navegador.

Fonte: `public/sw.js::networkFirst`.

Respostas GET da API são armazenadas em cache dinâmico e recuperadas pela requisição quando a rede falha. O nome do cache não inclui usuário ou empresa; o service worker examinado não implementa limpeza de logout.

**Impacto:** respostas privadas podem permanecer no navegador e reaparecer offline em equipamento compartilhado, dependendo de como as respostas variam e de eventuais limpezas feitas em outros fluxos.

**Ajuste:** não armazenar respostas autenticadas por padrão; se houver requisito offline, definir escopo de identidade, validade e limpeza verificável na troca de sessão.

**Aceite:** após logout e login com outro usuário, dados anteriores não reaparecem offline. Verificar também migração dos caches já existentes.

### F14 — Sincronização offline remove pendência mesmo com erro HTTP

**Prioridade:** P1. **Evidência:** confirmado no código do consumidor da fila.

Fonte: `public/sw.js::syncPendingRequests`.

A rotina chama `fetch()` e remove o item sem verificar `response.ok`. Respostas 419, 422 ou 500 não rejeitam automaticamente a promise de fetch.

**Impacto:** quando houver itens pendentes, uma falha de autenticação, validação ou servidor pode ser tratada como sincronização bem-sucedida.

**Ajuste:** remover somente após confirmação de sucesso, distinguir erros recuperáveis e definitivos, renovar autenticação quando necessário e usar identificador idempotente. A existência de produtores ativos dessa fila ainda deve ser validada.

**Aceite:** respostas 419/422/500 preservam a pendência ou a movem para uma fila de erro visível, sem perda silenciosa.

### F15 — Sequência de código de produto baseada em contagem

**Prioridade:** P2. **Evidência:** confirmado no código; colisão concorrente a validar.

Fonte: `app/Models/Product.php::booted`.

O código automático usa `withTrashed()->count() + 1`. Criações simultâneas podem calcular o mesmo número; remoções definitivas também tornam a contagem inadequada como sequência.

**Ajuste:** usar sequência transacional, garantir unicidade no banco e tratar conflito sem perder a operação.

**Aceite:** criações concorrentes geram códigos distintos e não dependem da quantidade atual de registros.

## 4. Ajustes necessários de engenharia e operação

| ID | Prioridade | Ajuste | Evidência e resultado esperado |
| --- | --- | --- | --- |
| A01 | P1 | Corrigir permissões do ambiente de desenvolvimento/testes | Logs e views pertencem a `nobody:nogroup`; usuário CLI não consegue escrever. Alinhar UID/GID e permissões específicas, sem recorrer a liberação global de escrita. |
| A02 | P1 | Limitar tentativas de login | `SessionController::store` e `/login` não apresentam limitador próprio. Introduzir controle por identidade/IP e testar resposta 429; eventual proteção de proxy não foi avaliada. |
| A03 | P1 | Revisar revogação de acesso | Login permite administrador inativo e usa lembrar sessão sempre. Definir regra explícita para desativação, sessões existentes e perfil administrativo; não pressupor que desativação atual revogue sessão imediatamente. |
| A04 | P2 | Invalidar apenas cache de configurações | `ConfigurationController::store` chama `Cache::flush()` apesar de `Setting::set` já esquecer sua chave. Evitar apagar entradas de outros recursos no mesmo store. |
| A05 | P1 | Gravar configurações de forma atômica | Várias chamadas `Setting::set()` podem persistir parte do formulário antes de uma falha. Validar tudo, transacionar gravações e invalidar cache após conclusão. |
| A06 | P2 | Paginar e limitar APIs | ProductApiController permite `paginate=false` e recebe `per_page` sem limite explícito; API de estoque usa `get()` integral. Aplicar limites e exportação assíncrona para volumes grandes. |
| A07 | P2 | Restringir faixas de dependências fiscais | `composer.json` usa `*` em sped-da e sped-nfe. Definir faixas compatíveis e validar atualizações pelo lockfile. Isso não constitui afirmação de vulnerabilidade conhecida. |
| A08 | P2 | Consolidar documentação | Existem `Documentation/` e `docs/`. Manter índice canônico e links cruzados; preservar este relatório em `docs/relatorios` conforme solicitado. |
| A09 | P2 | Revisar controllers e modelos legados | Há controllers de placeholder coexistindo com páginas Livewire funcionais. Classificar uso real antes de remover e evitar duas implementações para o mesmo processo. |
| A10 | P1 | Validar jobs e agendador operacionalmente | `bootstrap/app.php` agenda distribuição, reconciliação fiscal e backup. Verificar workers, tentativas, alertas, execução em múltiplas instâncias e restauração; agendamento declarado não comprova operação em produção. |
| A11 | P2 | Ampliar auditoria de alterações | `Loggable` grava ação e identificação, sem diferenças antes/depois no fluxo examinado. Incluir campos relevantes, empresa e correlação, com mascaramento de dados sensíveis. |
| A12 | P1 | Revisar consistência entre contas a pagar e receber | Baixa de contas a pagar marca Paid sem política de parcial no serviço; reagendamento redefine status sem limpar ou reconciliar valores anteriores. Definir transições e saldo residual comuns. |

## 5. Melhorias recomendadas

### 5.1 Arquitetura e integridade

- Centralizar estoque, liquidação financeira e pagamento de folha em serviços transacionais compartilhados por Livewire, APIs e jobs.
- Adotar chaves de idempotência e vínculos únicos para operações que podem ser repetidas por clique, fila ou integração.
- Representar valores monetários com precisão definida e regras explícitas de arredondamento; evitar conversões locais espalhadas.
- Preferir estornos rastreáveis a exclusões de lançamentos efetivados. Definir estados editáveis, aprovados, concluídos e cancelados por processo.
- Aplicar constraints, índices e unicidade conforme empresa, documento e estado; testar migração dos dados já existentes.

### 5.2 Interface e produtividade

Estas são propostas; responsividade e acessibilidade não foram medidas em navegador nesta revisão.

- Padronizar filtros, mensagens de validação, confirmação de estorno, estados vazios e indicadores de carregamento.
- Preservar filtros na URL quando útil e oferecer filtros salvos por usuário.
- Oferecer busca remota em seletores extensos: a movimentação de estoque atualmente carrega todos os produtos.
- Identificar empresa ativa de forma consistente e mostrar claramente o escopo de operações administrativas.
- Exibir status de processamento de tarefas longas, com atualização e mensagem recuperável em falha.
- Validar navegação por teclado, foco de modais, labels, contraste e layouts em celular usando um roteiro visual específico.

### 5.3 Fiscal e integrações

- Aproveitar os serviços e jobs fiscais existentes para construir visibilidade operacional de rejeições, reconciliações, certificados e pendências.
- Testar respostas atrasadas, timeout após autorização, reenvio e reconciliação sem duplicidade.
- Definir retenção e restauração dos arquivos arquivados, além do procedimento de backup já agendado.
- Unificar contratos de APIs, paginação, erros e permissões. Documentar que a API atual utiliza sessão e CSRF antes de destiná-la a integrações externas.

### 5.4 IA e suporte

Fontes de referência: `app/Ai/`, especialmente `Tools/ConsultarFinanceiroTool.php`, e `app/Livewire/Suporte/Chat.php`.

- Propagar identidade, permissões e empresa para cada ferramenta. A ferramenta financeira examinada recebe `userId`, mas monta consultas financeiras sem usá-lo como escopo nesse método; verificar também o controle no despachante.
- Criar avaliações de resposta com perguntas que tentem obter dados de outra empresa ou módulo não autorizado.
- Exibir origem e momento da consulta para números financeiros e operacionais.
- Medir latência, falhas e custo de uso; permitir encaminhamento humano com contexto suficiente.
- Definir quais dados podem sair do ERP para serviços externos, com minimização e mascaramento apropriados.

### 5.5 Qualidade contínua

- Criar pipeline com testes, Pint em modo de verificação e build de assets. Não foi encontrado diretório `.github` nesta cópia; automações externas não foram avaliadas.
- Complementar SQLite com testes de integração no banco usado em produção, sobretudo para concorrência, constraints e recursos específicos de banco.
- Adicionar cenários de API e Livewire que exercitem o mesmo serviço e comparem o resultado.
- Medir consultas e tempo de resposta com dados representativos antes de implementar otimizações amplas.

## 6. Dicas de adição e conclusão de páginas

Rotas abaixo são sugestões de desenho, não endpoints já criados. Páginas existentes como placeholders estão explicitamente marcadas como conclusão. Toda nova página deve respeitar empresa, permissões, filtros e auditoria.

| Página e situação | Público / benefício | Conteúdo mínimo e ações | Dependências | Prioridade |
| --- | --- | --- | --- | --- |
| Central de pendências — nova (`/operacao/pendencias`) | Gestores; reúne trabalho que exige ação | Pedidos aguardando aprovação, documentos fiscais com erro, títulos vencidos, estoque crítico; responsável e link para origem | Estados confiáveis e permissões por domínio | P2 |
| Inventário físico — fluxo dedicado; hoje atalho (`/estoque/inventarios`) | Estoquista; concilia físico e sistema | Abertura de contagem, contagem cega, recontagem, divergência, aprovação e lançamento de ajuste | Serviço de estoque corrigido e saldo por local | P1 |
| Transferências — fluxo dedicado; hoje atalho (`/estoque/transferencias`) | Estoque/logística; rastreia trânsito | Origem, destino, itens, expedição, recebimento parcial e cancelamento | Locais de estoque e movimentos vinculados | P2 |
| Kardex do produto — nova (`/estoque/produtos/{id}/kardex`) | Estoque e auditoria; explica o saldo | Saldo inicial, entradas, saídas, estornos, custo e documento de origem | Histórico consistente e permissões de custo | P1 |
| Conciliação bancária — nova (`/financeiro/conciliacao`) | Financeiro; reduz baixa manual | Importação de extrato, sugestões de vínculo, diferenças, confirmação e desfazer | Lançamentos de recebimento/pagamento e identificação única de importação | P2 |
| DRE gerencial — conclusão de relatórios financeiros | Gestão; acompanha resultado | Período, regime de apuração explícito, receitas, custos, despesas, comparativo e detalhamento | Plano de contas e classificação de lançamentos | P2 |
| Cobrança e inadimplência — nova (`/financeiro/cobranca`) | Financeiro/comercial; prioriza recuperação | Faixas de atraso, saldo residual, promessas de pagamento e histórico de contatos | Correção dos recebimentos parciais | P2 |
| Central de aprovações — nova (`/aprovacoes`) | Gestores; controla alçadas | Compras, descontos e ajustes; valor, justificativa, aprovação e rejeição | Policies, alçadas e prevenção de autoaprovação conforme regra definida | P2 |
| Ficha técnica/BOM — fluxo dedicado; hoje atalho (`/producao/fichas-tecnicas`) | Produção; planeja consumo | Componentes, quantidades, perdas, versões e vigência | Produtos, unidades, custos e vínculo com ordens | P2 |
| Apontamento de produção — expansão das ordens | Chão de fábrica; mede execução | Etapas, horas, quantidade boa, perdas, consumo real e conclusão | Serviço de estoque e estados da ordem | P2 |
| Romaneio e prova de entrega — conclusão/expansão | Expedição e motorista; fecha entrega | Itens, volumes, rota, impressão, ocorrência e comprovante | Agendamento existente e controle de acesso a anexos | P2 |
| Manutenção de frota — conclusão do placeholder | Gestor de frota; previne paradas | Plano por data/quilometragem, ordem de serviço, custo, indisponibilidade e alertas | Veículos e integração financeira | P2 |
| Rastreamento — conclusão do placeholder | Operação logística; acompanha rota | Última posição com horário, histórico e indicação de sinal desatualizado | Provedor de localização e política de acesso/retenção | P3 |
| Espelho de ponto e ajustes — expansão do RH | RH e colaborador; resolve divergências | Período, batidas, ocorrências, justificativa e aprovação de correção | Jornada, regras de cálculo e auditoria | P2 |
| Centro de operações fiscais — expansão da gestão NF-e | Fiscal; acompanha exceções | Fila, rejeições, tentativas, certificado, distribuição e downloads autorizados | Jobs e serviços fiscais existentes | P1 |
| Perfis e permissões por ação — consolidação administrativa | Administrador; aplica menor privilégio | Matriz por módulo/ação, prévia de acesso e histórico de alteração | Policies e separação plataforma/empresa | P1 |
| Saúde de integrações — nova (`/administracao/integracoes`) | Administração técnica; reduz diagnóstico manual | Último sucesso, falhas, filas, latência e teste controlado de conexão | Métricas e ocultação de credenciais | P2 |
| Importação de cadastros — nova (`/cadastros/importacoes`) | Implantação; reduz digitação | Modelo de arquivo, prévia, validação por linha, duplicidades e resultado | Validações reutilizáveis e jobs | P2 |

Para a primeira entrega de cada página, preferir um processo completo e verificável: autorização, listagem, ação principal, erro recuperável e trilha de auditoria. Gráficos e personalizações podem vir depois da consistência dos dados.

## 7. Panorama por módulo

| Módulo | Base observada | Principal próximo passo |
| --- | --- | --- |
| Administração | Empresas, usuários, configurações e logs | Restringir configurações e formalizar escopo de administrador. |
| Cadastros | Formulários Livewire e testes específicos | Isolamento empresarial, policies e consistência das APIs. |
| Vendas | Pedidos, precificação, serviços e testes fiscais | Validar alçadas, concorrência e integração ponta a ponta com faturamento. |
| Compras | Solicitações, cotações, pedidos e matching fiscal | Consolidar aprovações e recebimento parcial rastreável. |
| Estoque | Tela de movimentos e saldo no produto | Corrigir saldo/edição, API, atomicidade e filtros antes de inventário. |
| Financeiro | Contas a pagar/receber, fluxo e plano de contas | Corrigir moeda, parcial, saldo residual e transições. |
| Fiscal | Serviços, jobs e testes de várias operações | Homologar ambiente real e acompanhar exceções operacionais. |
| Produção | Ordens e itens | Ficha técnica dedicada, consumo real e apontamentos. |
| RH | Jornada, ponto, folha e holerite | Tornar pagamento idempotente e validar fechamento/reabertura. |
| Logística | Rotas e agendamento; vários placeholders | Completar expedição/romaneio antes de rastreamento avançado. |
| Dashboard | Overview, KPI e testes | Garantir que totais reflitam saldo residual e escopo empresarial. |
| IA/suporte | Ferramentas de consulta e chat | Verificar autorização por ferramenta e rastreabilidade das respostas. |

A presença de arquivos e testes nessa tabela indica implementação existente; não significa que o módulo tenha sido homologado integralmente.

## 8. Verificações executadas e limitações

| Verificação | Resultado |
| --- | --- |
| `php -v` | PHP CLI 8.4.24. |
| `php artisan route:list --json` | Concluído; 296 rotas registradas. |
| Cruzamento estático de rotas/controllers | 109 ações de controllers próprios sem método declarado. Contagem de ações, não de páginas nem de defeitos independentes. |
| `php artisan test --compact` | 174 falhas, 1 ignorado, 136 aprovações, 343 assertions; 20,57 s. |
| Repetição dos testes fora do sandbox | Mesmo resultado: 174 falhas, 1 ignorado, 136 aprovações; 21,53 s. |
| Inspeção de permissões | `storage/logs/laravel.log`: `nobody:nogroup`, modo 774; `storage/framework/views`: `nobody:nogroup`, modo 775. |

A saída de testes apresenta repetidamente `Permission denied` ao abrir o log e `tempnam(): file created in the system's temporary directory` durante gravação de views. A repetição fora do sandbox não resolveu esses erros. Portanto, **174 testes falhando não significam 174 defeitos de negócio**. Erros do ambiente podem mascarar a causa original; é necessário corrigir a escrita e repetir a suíte para obter diagnóstico funcional confiável.

Não foram executados build de frontend, auditoria de pacotes, testes de navegador, alteração de permissões, migrations em banco persistente ou operações fiscais reais. Não foi calculada cobertura de código. A existência de 136 testes aprovados não garante cobertura dos achados deste relatório.

## 9. Plano de ação sugerido

### Etapa 1 — Segurança e valores críticos

1. Corrigir F01–F04: configurações, escopo empresarial, autorização e conversão monetária.
2. Corrigir permissões de execução dos testes sem alterar a segurança global do host.
3. Adicionar cenários com duas empresas e usuários sem permissão de módulo.
4. Revisar cache privado do service worker e troca de sessão.

**Saída esperada:** acesso indevido negado e valores de baixa preservados exatamente.

### Etapa 2 — Integridade operacional

1. Unificar API e Livewire de estoque, corrigir edição, estorno, concorrência e período.
2. Corrigir recebimentos parciais e indicadores de saldo.
3. Tornar pagamento de folha transacional e idempotente.
4. Revisar sincronização offline e duplicidade nas operações repetíveis.

**Saída esperada:** saldo e histórico reconciliáveis, com testes de falha intermediária e repetição.

### Etapa 3 — Coerência de rotas e qualidade

1. Remover rotas de ações inexistentes ou implementar o escopo necessário.
2. Marcar funcionalidades futuras e concluir as impressões priorizadas.
3. Instalar verificações contínuas e consolidar documentação.
4. Revisar paginação, cache e consultas com dados representativos.

**Saída esperada:** nenhuma rota própria inválida e suíte executável com diagnóstico funcional limpo.

### Etapa 4 — Expansão do produto

1. Entregar Kardex e inventário sobre estoque estabilizado.
2. Consolidar perfis por ação e centro de operações fiscais.
3. Evoluir conciliação, DRE, romaneio e ficha técnica conforme demanda dos usuários.
4. Priorizar rastreamento e recursos adicionais após validar custos e integrações necessários.

**Saída esperada:** novos processos completos com responsáveis, permissões e critérios de aceite definidos.

## 10. Checklist de validação após correções

- [ ] Usuário comum não lê nem grava configurações administrativas globais.
- [ ] Empresa A não acessa registros de B por URL, API, exportação, job ou ferramenta de IA.
- [ ] Permissões são verificadas na ação, além do menu.
- [ ] Valores monetários brasileiros e canônicos têm interpretação consistente.
- [ ] Pagamentos e recebimentos parciais preservam histórico e saldo residual.
- [ ] Repetir pagamento de folha não duplica conta a pagar.
- [ ] API e tela de estoque usam o mesmo serviço e contrato de domínio.
- [ ] Edição/estorno de estoque preserva a conciliação entre saldo e movimentos.
- [ ] Concorrência e falhas intermediárias não geram gravações parciais.
- [ ] Filtros incluem integralmente o último dia selecionado.
- [ ] Nenhuma rota própria referencia método ausente.
- [ ] Funcionalidades incompletas são identificadas corretamente no menu.
- [ ] Logout e troca de usuário não expõem cache de dados privados.
- [ ] Erros HTTP não eliminam pendências offline silenciosamente.
- [ ] Logs e views são graváveis pelo usuário correto de execução.
- [ ] Testes, formatação PHP e build passam após as correções aplicáveis.
- [ ] Fluxos críticos têm validação manual em navegador e no banco adotado em produção.

## 11. Entregável e acompanhamento

Este relatório documenta os problemas e as propostas; não aplica as correções descritas. Para acompanhar a execução, transformar cada F/A prioritário em tarefa vinculada às fontes indicadas, atribuir responsável, registrar o cenário de reprodução e anexar evidência do critério de aceite. Reavaliar prioridades após a suíte funcionar sem os impedimentos de ambiente.
