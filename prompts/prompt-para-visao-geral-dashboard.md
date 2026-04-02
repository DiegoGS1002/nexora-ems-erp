# 🚀 Guia Completo — Dashboard Overview Profissional

## 🎯 Objetivo

Transformar uma tela simples em um **dashboard SaaS moderno**, com:

* Cards KPI
* Gráficos interativos
* Layout profissional
* Estrutura escalável

---

# 🧱 ETAPA 1 — Criar componente

```bash
php artisan make:livewire Overview
```

---

# 🧠 ETAPA 2 — Dados (Overview.php)

```php
class Overview extends Component
{
    public $kpis = [];

    public function mount()
    {
        $this->kpis = [
            [
                'title' => 'Faturamento',
                'value' => 'R$ 128.590,00',
                'icon' => '💰',
                'trend' => '+15,7%'
            ],
            [
                'title' => 'Produtos',
                'value' => '5.284',
                'icon' => '📦',
                'trend' => null
            ],
            [
                'title' => 'Pedidos',
                'value' => '72',
                'icon' => '🛒',
                'trend' => '+3,5%'
            ],
            [
                'title' => 'Despesas',
                'value' => 'R$ 78.445,00',
                'icon' => '💸',
                'trend' => '-8,3%'
            ],
        ];
    }

    public function render()
    {
        return view('livewire.overview');
    }
}
```

---

# 🎨 ETAPA 3 — Header

```blade
<div class="flex justify-between items-center mb-6">

    <div>
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
        <p class="text-gray-500">Visão geral do sistema</p>
    </div>

    <a href="/kpi"
       class="bg-blue-600 text-white px-5 py-2 rounded-xl shadow hover:bg-blue-700 transition">
        Ver Indicadores KPI
    </a>

</div>
```

---

# 🧩 ETAPA 4 — Componente KPI

```blade
<div class="bg-white rounded-2xl shadow-sm p-5 flex items-center justify-between hover:shadow-md transition">

    <div>
        <p class="text-gray-500 text-sm">{{ $title }}</p>
        <h2 class="text-xl font-bold text-gray-800">{{ $value }}</h2>

        @if($trend)
            <span class="text-sm {{ str_contains($trend, '+') ? 'text-green-500' : 'text-red-500' }}">
                {{ $trend }}
            </span>
        @endif
    </div>

    <div class="p-3 rounded-xl bg-gray-100 text-xl">
        {{ $icon }}
    </div>

</div>
```

---

# 🧱 ETAPA 5 — Grid KPI

```blade
<div class="grid grid-cols-4 gap-6 mb-6">
    @foreach($kpis as $kpi)
        <x-kpi-card
            :title="$kpi['title']"
            :value="$kpi['value']"
            :trend="$kpi['trend']"
            :icon="$kpi['icon']"
        />
    @endforeach
</div>
```

---

# 📊 ETAPA 6 — Gráficos

## CDN

```html
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
```

---

## Linha

```blade
<div class="bg-white p-5 rounded-2xl shadow-sm">
    <h2 class="font-semibold mb-4">Faturamento</h2>
    <div id="chart-line"></div>
</div>
```

---

## Donut

```blade
<div class="bg-white p-5 rounded-2xl shadow-sm">
    <h2 class="font-semibold mb-4">Distribuição</h2>
    <div id="chart-donut"></div>
</div>
```

---

# ⚡ ETAPA 7 — Scripts

```javascript
document.addEventListener('livewire:load', function () {

    new ApexCharts(document.querySelector("#chart-line"), {
        chart: { type: 'line', height: 300 },
        series: [{ data: [12000, 19000, 30000, 50000] }],
        xaxis: { categories: ['Jan', 'Fev', 'Mar', 'Abr'] }
    }).render();

    new ApexCharts(document.querySelector("#chart-donut"), {
        chart: { type: 'donut' },
        series: [34, 28, 20, 18],
        labels: ['Comércio', 'Construção', 'Serviços', 'Tecnologia']
    }).render();

});
```

---

# 🧱 ETAPA 8 — Layout Final

```blade
<div class="p-6 bg-gray-50 min-h-screen">

    <!-- Header -->
    @include('partials.dashboard-header')

    <!-- KPIs -->
    @include('partials.kpis')

    <!-- Charts -->
    <div class="grid grid-cols-3 gap-6">

        <div class="col-span-2 bg-white p-5 rounded-2xl shadow-sm">
            <div id="chart-line"></div>
        </div>

        <div class="bg-white p-5 rounded-2xl shadow-sm">
            <div id="chart-donut"></div>
        </div>

    </div>

</div>
```

---

# 🎨 ETAPA 9 — Design

Use sempre:

```
rounded-2xl
shadow-sm
hover:shadow-md
bg-white
bg-gray-50
```

---

# 🚀 ETAPA 10 — Evolução

* Filtros globais
* Drill-down
* Tempo real

```blade
wire:poll.10s
```

---
