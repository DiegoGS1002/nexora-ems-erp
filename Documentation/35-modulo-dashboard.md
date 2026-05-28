# Módulo de Dashboard e KPIs

## 1. Visão Geral

O Dashboard é a página inicial do sistema, apresentando visão consolidada de KPIs (Key Performance Indicators) e métricas em tempo real de todos os módulos, com gráficos interativos e auto-refresh.

### Funcionalidades Principais
- Visão geral executiva
- KPIs em cards
- Gráficos interativos (ApexCharts)
- Dados em tempo real (auto-refresh 10s)
- Filtros por período
- Drill-down para detalhes
- Exportação de relatórios

---

## 2. KPIs Disponíveis

### 2.1 Financeiro
- **Receita do Mês**: Total recebido
- **Despesa do Mês**: Total pago
- **Saldo**: Receita - Despesa
- **Contas a Receber**: Total pendente
- **Contas a Pagar**: Total pendente
- **Inadimplência**: % de contas vencidas

### 2.2 Vendas
- **Pedidos do Mês**: Quantidade
- **Valor Total**: Soma dos pedidos
- **Ticket Médio**: Valor médio por pedido
- **Taxa de Conversão**: Pedidos / Orçamentos
- **Top 10 Clientes**: Maiores compradores
- **Top 10 Produtos**: Mais vendidos

### 2.3 Compras
- **Pedidos Ativos**: Em andamento
- **Valor Total**: Soma das compras
- **Aguardando Recebimento**: Qtd pendente

### 2.4 Estoque
- **Valor Total**: Estoque valorizado
- **Produtos em Falta**: stock < stock_min
- **Movimentações do Dia**: Quantidade

### 2.5 RH
- **Funcionários Ativos**: Total
- **Folha de Pagamento**: Valor total
- **Ausências**: Do mês

---

## 3. Estrutura Técnica

### 3.1 Livewire: `App\Livewire\Dashboard\Overview`

```php
class Overview extends Component
{
    public $period = 'month'; // today, week, month, year
    public $refreshInterval = 10000; // 10 segundos
    
    public function mount()
    {
        $this->loadMetrics();
    }
    
    public function loadMetrics()
    {
        $this->metrics = app(DashboardMetricsService::class)
            ->getPeriodMetrics($this->period);
    }
    
    #[On('refresh-dashboard')]
    public function refresh()
    {
        $this->loadMetrics();
    }
}
```

### 3.2 Service: `DashboardMetricsService`

```php
class DashboardMetricsService
{
    public function getPeriodMetrics(string $period): array
    {
        $dateRange = $this->getDateRange($period);
        
        return [
            'financial' => $this->getFinancialMetrics($dateRange),
            'sales' => $this->getSalesMetrics($dateRange),
            'purchases' => $this->getPurchasesMetrics($dateRange),
            'stock' => $this->getStockMetrics(),
            'hr' => $this->getHRMetrics($dateRange),
        ];
    }
    
    protected function getFinancialMetrics(array $dateRange): array
    {
        return [
            'revenue' => AccountReceivable::whereBetween('received_at', $dateRange)
                ->sum('received_amount'),
            
            'expense' => AccountPayable::whereBetween('payment_date', $dateRange)
                ->sum('paid_amount'),
            
            'balance' => $revenue - $expense,
            
            'receivable' => AccountReceivable::where('status', 'pending')
                ->sum('amount'),
            
            'payable' => AccountPayable::where('status', 'pending')
                ->sum('amount'),
        ];
    }
    
    protected function getSalesMetrics(array $dateRange): array
    {
        $orders = SalesOrder::whereBetween('order_date', $dateRange)->get();
        
        return [
            'total_orders' => $orders->count(),
            'total_value' => $orders->sum('total_amount'),
            'avg_ticket' => $orders->avg('total_amount'),
            'top_clients' => $this->getTopClients($dateRange),
            'top_products' => $this->getTopProducts($dateRange),
        ];
    }
}
```

### 3.3 Componente Livewire: `KpiReport`

Relatório detalhado com filtros avançados:
```php
class KpiReport extends Component
{
    public $startDate;
    public $endDate;
    public $selectedMetric = 'sales';
    
    public function generateReport()
    {
        $this->reportData = app(DashboardMetricsService::class)
            ->getDetailedReport(
                $this->selectedMetric,
                $this->startDate,
                $this->endDate
            );
    }
}
```

---

## 4. Gráficos

### 4.1 Gráfico de Linha: Vendas Mensais
```javascript
// ApexCharts
var options = {
    series: [{
        name: 'Vendas',
        data: @json($salesData)
    }],
    chart: {
        type: 'line',
        height: 350
    },
    xaxis: {
        categories: @json($months)
    }
};

var chart = new ApexCharts(document.querySelector("#sales-chart"), options);
chart.render();
```

### 4.2 Gráfico Donut: Vendas por Categoria
```javascript
var options = {
    series: @json($categoryValues),
    labels: @json($categoryNames),
    chart: {
        type: 'donut',
    },
    responsive: [{
        breakpoint: 480,
        options: {
            chart: {
                width: 200
            },
            legend: {
                position: 'bottom'
            }
        }
    }]
};
```

### 4.3 Gráfico de Barras: Comparativo Meta x Realizado
```javascript
var options = {
    series: [{
        name: 'Meta',
        data: @json($targets)
    }, {
        name: 'Realizado',
        data: @json($achieved)
    }],
    chart: {
        type: 'bar',
        height: 350
    },
    plotOptions: {
        bar: {
            horizontal: false,
            columnWidth: '55%',
        },
    },
};
```

---

## 5. Auto-Refresh

### 5.1 Livewire Polling
```blade
<div wire:poll.10s="refresh">
    <!-- Conteúdo do dashboard -->
    @foreach($metrics as $metric)
        <x-kpi-card :metric="$metric" />
    @endforeach
</div>
```

### 5.2 Manual Refresh
```blade
<button wire:click="refresh" class="btn btn-sm">
    <i class="fas fa-sync"></i> Atualizar
</button>
```

---

## 6. Filtros

### 6.1 Período
```blade
<select wire:model.live="period">
    <option value="today">Hoje</option>
    <option value="week">Esta Semana</option>
    <option value="month">Este Mês</option>
    <option value="year">Este Ano</option>
    <option value="custom">Personalizado</option>
</select>
```

### 6.2 Comparativo
```blade
<select wire:model.live="comparison">
    <option value="none">Sem comparação</option>
    <option value="previous_period">Período anterior</option>
    <option value="last_year">Ano passado</option>
</select>
```

---

## 7. Widgets Disponíveis

### 7.1 Card KPI
```blade
<div class="kpi-card">
    <div class="kpi-icon">
        <i class="{{ $icon }}"></i>
    </div>
    <div class="kpi-content">
        <h3>{{ $title }}</h3>
        <p class="kpi-value">{{ $value }}</p>
        <span class="kpi-change {{ $change >= 0 ? 'positive' : 'negative' }}">
            {{ $change }}% vs período anterior
        </span>
    </div>
</div>
```

### 7.2 Lista de Pedidos Recentes
```blade
<div class="recent-orders">
    <h4>Pedidos Recentes</h4>
    @foreach($recentOrders as $order)
        <div class="order-item">
            <span>#{{ $order->order_number }}</span>
            <span>{{ $order->client->name }}</span>
            <span>R$ {{ number_format($order->total_amount, 2, ',', '.') }}</span>
            <span class="badge badge-{{ $order->status->color() }}">
                {{ $order->status->label() }}
            </span>
        </div>
    @endforeach
</div>
```

### 7.3 Alertas
```blade
<div class="alerts-widget">
    @if($lowStockCount > 0)
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            {{ $lowStockCount }} produtos com estoque baixo
            <a href="{{ route('stock.index') }}">Ver detalhes</a>
        </div>
    @endif
    
    @if($overdueCount > 0)
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle"></i>
            {{ $overdueCount }} contas vencidas
            <a href="{{ route('accounts_payable.index') }}">Ver detalhes</a>
        </div>
    @endif
</div>
```

---

## 8. Permissões

### 8.1 Visibilidade por Perfil
```php
// Administrador: vê tudo
// Gerente: vê dados do departamento
// Vendedor: vê apenas suas vendas

if (auth()->user()->is_admin) {
    $orders = SalesOrder::all();
} elseif (auth()->user()->role === 'manager') {
    $orders = SalesOrder::where('department_id', auth()->user()->department_id)->get();
} else {
    $orders = SalesOrder::where('seller_id', auth()->id())->get();
}
```

---

## 9. Performance

### 9.1 Cache
```php
$metrics = Cache::remember("dashboard_metrics_{$userId}_{$period}", 300, function () {
    return app(DashboardMetricsService::class)->getPeriodMetrics($this->period);
});
```

### 9.2 Queries Otimizadas
```php
// Evitar N+1
$orders = SalesOrder::with(['client', 'items'])
    ->whereBetween('order_date', $dateRange)
    ->get();

// Usar selectRaw para agregações
$topProducts = SalesOrderItem::selectRaw('product_id, SUM(quantity) as total_quantity')
    ->groupBy('product_id')
    ->orderByDesc('total_quantity')
    ->take(10)
    ->with('product')
    ->get();
```

---

## 10. Exportação

### 10.1 PDF
```php
public function exportPdf()
{
    $pdf = PDF::loadView('reports.dashboard', [
        'metrics' => $this->metrics,
        'period' => $this->period,
    ]);
    
    return $pdf->download('dashboard-report.pdf');
}
```

### 10.2 Excel
```php
public function exportExcel()
{
    return Excel::download(
        new DashboardExport($this->metrics),
        'dashboard-report.xlsx'
    );
}
```

---

**Caminho**: `app/Livewire/Dashboard/Overview.php`, `app/Services/Dashboard/DashboardMetricsService.php`, `routes/producao.php`

