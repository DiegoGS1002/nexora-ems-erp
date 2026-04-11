# 🧪 Guia de Teste - Dashboard Ajustado

## ✅ Verificações Realizadas

- ✅ Sintaxe PHP validada
- ✅ Rotas configuradas corretamente
- ✅ Sem erros de compilação
- ✅ Integração com DashboardMetricsService

---

## 🚀 Como Testar os Dashboards

### 1. Acesse a Visão Geral
```
URL: http://localhost:8000/dashboard
```

**O que você deve ver:**
- ✅ 4 Cards KPI com dados reais
- ✅ Gráfico de evolução de faturamento (6 meses)
- ✅ Gráfico donut com categorias de produtos
- ✅ Lista de pedidos recentes
- ✅ Movimentações recentes (se houver dados em stock_movements)

### 2. Acesse Indicadores KPI
```
URL: http://localhost:8000/dashboard/kpi
```

**O que você deve ver:**
- ✅ Mesmos 4 Cards KPI
- ✅ Barra de busca funcional
- ✅ Gráfico de linha (clicável)
- ✅ Gráfico donut com categorias
- ✅ Seção "Desempenho Geral" com barras de progresso
- ✅ Gráfico de barras (Meta × Realizado)
- ✅ Tabela "Comparativos Mensais"
- ✅ Tabela detalhada com busca

---

## 📊 Valores Esperados

### Com Dados Reais:
```
Faturamento: Soma de (preço_venda × quantidade) das movimentações de saída
Produtos: Total de registros em products
Pedidos: Total de registros em requests
Despesas: Soma de accounts_payable + custos de entrada
```

### Sem Dados (Banco Vazio):
```
Faturamento: R$ 0,00
Produtos: 0
Pedidos: 0
Despesas: R$ 0,00
```

---

## 🔄 Auto-Refresh

Os dashboards atualizam automaticamente a cada **10 segundos**.

Você verá no console do navegador (F12):
```
Livewire: polling component...
```

---

## 🐛 Solução de Problemas

### Dashboard não carrega?
1. Verifique se o Docker está rodando:
   ```bash
   docker-compose ps
   ```

2. Verifique logs do Laravel:
   ```bash
   docker-compose exec app tail -f storage/logs/laravel.log
   ```

### Dados não aparecem?
1. Verifique se há produtos cadastrados:
   ```bash
   docker-compose exec db mysql -u root -proot nexora -e "SELECT COUNT(*) FROM products;"
   ```

2. Adicione movimentações de estoque:
   - Acesse: http://localhost:8000/stock
   - Crie algumas movimentações de entrada e saída

### Erro de conexão com banco?
1. Reinicie os containers:
   ```bash
   docker-compose restart
   ```

2. Verifique conexão:
   ```bash
   docker-compose exec app php artisan tinker
   >>> \DB::connection()->getPdo();
   ```

---

## 📝 Checklist de Teste

### Visão Geral:
- [ ] KPIs carregam com valores
- [ ] Gráfico de linha renderiza
- [ ] Gráfico donut aparece
- [ ] Pedidos recentes listados
- [ ] Movimentações aparecem

### Indicadores KPI:
- [ ] Busca funciona
- [ ] Clicar no gráfico filtra tabela
- [ ] Desempenho geral exibe percentuais
- [ ] Gráfico de barras renderiza
- [ ] Comparativos calculam variação
- [ ] Filtro de mês funciona

### Performance:
- [ ] Página carrega em < 2 segundos
- [ ] Auto-refresh funciona
- [ ] Sem erros no console
- [ ] Transições suaves

---

## 🎯 Teste Completo

### 1. Prepare Dados
```bash
# Acesse a movimentação de estoque
http://localhost:8000/stock

# Crie algumas movimentações:
- 3 entradas (input)
- 3 saídas (output)
- 1 ajuste (adjustment)
```

### 2. Acesse Dashboard
```bash
http://localhost:8000/dashboard
```

### 3. Verifique KPIs
- Faturamento deve ter valor > 0
- Produtos deve mostrar quantidade real
- Pedidos conforme banco
- Despesas calculadas

### 4. Teste Interatividade
```bash
http://localhost:8000/dashboard/kpi
```
- Clique em um ponto do gráfico
- Observe a tabela filtrar
- Use a busca
- Limpe o filtro

### 5. Verifique Auto-Refresh
- Abra outro navegador
- Adicione uma movimentação
- Aguarde 10 segundos
- Veja o dashboard atualizar

---

## ✅ Resultado Esperado

### Visão Geral:
```
✅ KPIs com valores dinâmicos
✅ Gráficos renderizados
✅ Dados atualizados a cada 10s
✅ Sem erros no console
```

### Indicadores KPI:
```
✅ Filtros funcionando
✅ Busca operacional
✅ Gráficos interativos
✅ Tabelas com dados reais
✅ Cálculos corretos
```

---

## 🎉 Status Final

Se todos os itens acima funcionarem:
**✅ DASHBOARD 100% OPERACIONAL COM DADOS REAIS!**

---

**Última Verificação**: 09/04/2026  
**Ambiente**: Docker (localhost:8000)  
**Status**: ✅ PRONTO PARA USO

