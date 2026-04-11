# Exemplos de Uso - Módulo de Pedidos de Venda

Este documento fornece exemplos práticos de como usar o módulo de pedidos de venda.

---

## 📝 Exemplo 1: Criar um Pedido Simples

```php
use App\Services\SalesOrderService;
use App\Enums\CanalVenda;
use App\Enums\OrigemPedido;

$service = new SalesOrderService();

$order = $service->createOrder([
    'client_id' => 'uuid-do-cliente',
    'seller_id' => 1,
    'is_fiscal' => false,
    'sales_channel' => CanalVenda::Balcao,
    'origin' => OrigemPedido::Manual,
    'payment_condition' => 'À vista',
    
    'items' => [
        [
            'product_id' => 'product-uuid-1',
            'quantity' => 10,
            'unit_price' => 100.00,
            'discount' => 50.00, // R$ 50 de desconto
        ],
        [
            'product_id' => 'product-uuid-2',
            'quantity' => 5,
            'unit_price' => 200.00,
            'discount_percent' => 10, // 10% de desconto
        ],
    ],
    
    'payment' => [
        'payment_condition' => 'À vista',
        'payment_method' => 'Dinheiro',
        'installments' => 1,
    ],
]);

echo "Pedido criado: " . $order->order_number;
echo "Total: R$ " . number_format($order->total_amount, 2, ',', '.');
```

---

## 📝 Exemplo 2: Criar um Pedido Fiscal Completo

```php
use App\Services\SalesOrderService;
use App\Enums\CanalVenda;
use App\Enums\TipoOperacaoVenda;
use App\Enums\TipoFrete;

$service = new SalesOrderService();

$order = $service->createOrder([
    'client_id' => 'uuid-do-cliente',
    'seller_id' => 1,
    'is_fiscal' => true, // Pedido fiscal
    'operation_type' => TipoOperacaoVenda::VendaNormal,
    'sales_channel' => CanalVenda::Online,
    'carrier_id' => 1,
    'freight_type' => TipoFrete::CIF,
    
    'items' => [
        [
            'product_id' => 'product-uuid-1',
            'quantity' => 10,
            'unit_price' => 100.00,
            'discount_percent' => 5,
            
            // Dados fiscais
            'cfop' => '5102',
            'ncm' => '84715010',
            'cst' => '00',
            'origin' => '0',
            'icms_percent' => 18,
            'ipi_percent' => 5,
            'pis_percent' => 1.65,
            'cofins_percent' => 7.6,
        ],
    ],
    
    'addresses' => [
        'billing' => [
            'zip_code' => '01310-100',
            'street' => 'Av. Paulista',
            'number' => '1000',
            'district' => 'Bela Vista',
            'city' => 'São Paulo',
            'state' => 'SP',
        ],
        'delivery' => [
            'zip_code' => '04538-133',
            'street' => 'Av. Brigadeiro Faria Lima',
            'number' => '2000',
            'district' => 'Itaim Bibi',
            'city' => 'São Paulo',
            'state' => 'SP',
        ],
    ],
    
    'payment' => [
        'payment_condition' => '30/60 dias',
        'payment_method' => 'Boleto',
        'installments' => 2,
        'total_amount' => 950.00,
    ],
    
    'internal_notes' => 'Cliente preferencial',
    'customer_notes' => 'Entregar no período da tarde',
    'fiscal_notes_obs' => 'Mercadoria destinada à revenda',
]);
```

---

## 📝 Exemplo 3: Trabalhar Diretamente com o Model

```php
use App\Models\SalesOrder;
use App\Models\Client;
use App\Enums\SalesOrderStatus;

// Criar pedido
$order = SalesOrder::create([
    'client_id' => 'uuid-do-cliente',
    'user_id' => auth()->id(),
    'is_fiscal' => false,
]);

// Adicionar itens
$order->items()->create([
    'product_id' => 'product-uuid',
    'quantity' => 5,
    'unit_price' => 100.00,
]);

$order->items()->create([
    'product_id' => 'product-uuid-2',
    'quantity' => 3,
    'unit_price' => 150.00,
]);

// Calcular totais
$order->calculateTotals();
$order->save();

// Adicionar endereço de entrega
$order->addresses()->create([
    'type' => 'delivery',
    'zip_code' => '01310-100',
    'street' => 'Av. Paulista',
    'number' => '1000',
    'city' => 'São Paulo',
    'state' => 'SP',
]);
```

---

## 📝 Exemplo 4: Aprovar um Pedido

```php
use App\Services\SalesOrderService;
use App\Models\SalesOrder;

$service = new SalesOrderService();
$order = SalesOrder::find(1);

// Aprovar
$service->approveOrder($order, 'Aprovado pelo gerente comercial');

// Ou diretamente no model
$order->approve(auth()->user(), 'Crédito aprovado');
```

---

## 📝 Exemplo 5: Cancelar um Pedido

```php
use App\Services\SalesOrderService;
use App\Models\SalesOrder;

$service = new SalesOrderService();
$order = SalesOrder::find(1);

$service->cancelOrder($order, 'Cliente solicitou cancelamento');
```

---

## 📝 Exemplo 6: Buscar Pedidos com Filtros

```php
use App\Models\SalesOrder;
use App\Enums\SalesOrderStatus;

// Pedidos em aberto
$openOrders = SalesOrder::where('status', SalesOrderStatus::Aberto)->get();

// Pedidos de um cliente
$clientOrders = SalesOrder::where('client_id', 'uuid')->get();

// Pedidos de um vendedor
$sellerOrders = SalesOrder::where('seller_id', 1)->get();

// Pedidos do dia
$todayOrders = SalesOrder::whereDate('order_date', today())->get();

// Pedidos com valor acima de R$ 1000
$highValueOrders = SalesOrder::where('total_amount', '>', 1000)->get();

// Pedidos que precisam de aprovação
$needsApproval = SalesOrder::where('needs_approval', true)->get();

// Pedidos com todos os relacionamentos
$order = SalesOrder::with([
    'client',
    'seller',
    'items.product',
    'addresses',
    'payments.installments',
    'logs',
    'attachments',
])->find(1);
```

---

## 📝 Exemplo 7: Trabalhar com Tabelas de Preço

```php
use App\Models\PriceTable;
use App\Models\PriceTableItem;

// Criar tabela de preço
$priceTable = PriceTable::create([
    'name' => 'Tabela Atacado',
    'code' => 'ATK-001',
    'is_active' => true,
    'is_default' => false,
    'valid_from' => now(),
    'valid_until' => now()->addMonths(6),
]);

// Adicionar produtos à tabela
$priceTable->items()->create([
    'product_id' => 'product-uuid',
    'price' => 100.00,
    'minimum_price' => 80.00,
    'promotional_price' => 90.00,
    'promotional_valid_from' => now(),
    'promotional_valid_until' => now()->addDays(30),
]);

// Preço por quantidade
$priceTable->items()->create([
    'product_id' => 'product-uuid-2',
    'price' => 50.00,
    'quantity_from' => 10,
    'quantity_to' => 50,
    'quantity_price' => 45.00, // R$ 45 para compras de 10 a 50 unidades
]);

// Buscar preço efetivo
$priceTableItem = PriceTableItem::where('product_id', 'product-uuid')->first();
$effectivePrice = $priceTableItem->getEffectivePrice(15); // quantidade = 15
```

---

## 📝 Exemplo 8: Controle de Crédito

```php
use App\Models\Client;

$client = Client::find('uuid');

// Verificar se está ativo
if (!$client->isActive()) {
    // Cliente inativo
}

// Verificar se é inadimplente
if ($client->isDefaulter()) {
    // Cliente inadimplente
}

// Verificar crédito disponível
if ($client->hasAvailableCredit(1000.00)) {
    // Cliente tem crédito disponível para compra de R$ 1000
} else {
    // Sem crédito disponível
}

// Ver crédito utilizado
$usedCredit = $client->salesOrders()
    ->whereIn('status', ['approved', 'invoiced'])
    ->sum('total_amount');

echo "Limite: R$ " . $client->credit_limit;
echo "Utilizado: R$ " . $usedCredit;
echo "Disponível: R$ " . ($client->credit_limit - $usedCredit);
```

---

## 📝 Exemplo 9: Histórico e Auditoria

```php
use App\Models\SalesOrder;

$order = SalesOrder::find(1);

// Ver histórico completo
foreach ($order->logs as $log) {
    echo "{$log->created_at}: {$log->action} - {$log->description}";
    echo "Usuário: {$log->user->name}";
    if ($log->changes) {
        print_r($log->changes);
    }
}

// Registrar ação manualmente
$order->logAction('custom_action', 'Ação customizada realizada');
```

---

## 📝 Exemplo 10: Anexos

```php
use App\Models\SalesOrder;
use Illuminate\Support\Facades\Storage;

$order = SalesOrder::find(1);

// Upload de arquivo
$file = request()->file('attachment');
$path = $file->store('sales_orders/' . $order->id, 'public');

$attachment = $order->attachments()->create([
    'uploaded_by' => auth()->id(),
    'type' => 'pedido_assinado',
    'file_name' => $file->getClientOriginalName(),
    'file_path' => $path,
    'mime_type' => $file->getMimeType(),
    'file_size' => $file->getSize(),
    'description' => 'Pedido assinado pelo cliente',
]);

// Listar anexos
foreach ($order->attachments as $attachment) {
    echo $attachment->file_name;
    echo $attachment->file_size_formatted; // Ex: "2.5 MB"
    echo $attachment->url; // URL para download
}
```

---

## 📝 Exemplo 11: Relatórios

```php
use App\Models\SalesOrder;
use App\Enums\SalesOrderStatus;
use Illuminate\Support\Facades\DB;

// Vendas do mês
$monthlyTotal = SalesOrder::whereMonth('order_date', now()->month)
    ->whereYear('order_date', now()->year)
    ->sum('total_amount');

// Vendas por vendedor
$salesByVendor = SalesOrder::select('seller_id', DB::raw('SUM(total_amount) as total'))
    ->where('status', SalesOrderStatus::Invoiced)
    ->groupBy('seller_id')
    ->with('seller')
    ->get();

// Vendas por canal
$salesByChannel = SalesOrder::select('sales_channel', DB::raw('COUNT(*) as count, SUM(total_amount) as total'))
    ->groupBy('sales_channel')
    ->get();

// Ticket médio
$averageTicket = SalesOrder::where('status', SalesOrderStatus::Invoiced)
    ->avg('total_amount');

// Produtos mais vendidos
$topProducts = DB::table('sales_order_items')
    ->join('sales_orders', 'sales_orders.id', '=', 'sales_order_items.sales_order_id')
    ->where('sales_orders.status', SalesOrderStatus::Invoiced->value)
    ->select('product_id', DB::raw('SUM(quantity) as total_quantity'))
    ->groupBy('product_id')
    ->orderByDesc('total_quantity')
    ->limit(10)
    ->get();
```

---

## 📝 Exemplo 12: Integração com Estoque

```php
use App\Models\SalesOrder;
use App\Models\StockMovement;
use App\Enums\SalesOrderStatus;

$order = SalesOrder::find(1);

// Ao aprovar pedido, reservar estoque
if ($order->status == SalesOrderStatus::Approved) {
    foreach ($order->items as $item) {
        // Criar reserva de estoque
        StockMovement::create([
            'product_id' => $item->product_id,
            'quantity' => -$item->quantity,
            'type' => 'reservation',
            'reference_type' => 'sales_order',
            'reference_id' => $order->id,
        ]);
    }
}

// Ao faturar, dar baixa definitiva
if ($order->status == SalesOrderStatus::Invoiced) {
    foreach ($order->items as $item) {
        // Remover reserva
        // Criar movimentação de saída
        StockMovement::create([
            'product_id' => $item->product_id,
            'quantity' => -$item->quantity,
            'type' => 'sale',
            'reference_type' => 'sales_order',
            'reference_id' => $order->id,
        ]);
    }
}
```

---

## 📝 Exemplo 13: Integração Financeira

```php
use App\Models\SalesOrder;
use App\Models\AccountReceivable;
use App\Enums\SalesOrderStatus;

$order = SalesOrder::find(1);

// Ao faturar, gerar contas a receber
if ($order->status == SalesOrderStatus::Invoiced) {
    foreach ($order->installments as $installment) {
        AccountReceivable::create([
            'client_id' => $order->client_id,
            'description' => "Pedido {$order->order_number} - Parcela {$installment->installment_number}",
            'due_date' => $installment->due_date,
            'amount' => $installment->amount,
            'status' => 'pending',
            'reference_type' => 'sales_order',
            'reference_id' => $order->id,
        ]);
    }
}
```

---

## 🔍 Queries Úteis

```php
use App\Models\SalesOrder;
use App\Enums\SalesOrderStatus;

// Pedidos pendentes de aprovação
$pendingApproval = SalesOrder::where('needs_approval', true)
    ->where('status', SalesOrderStatus::Draft)
    ->with('client', 'seller')
    ->get();

// Pedidos em separação
$inSeparation = SalesOrder::where('status', SalesOrderStatus::EmSeparacao)
    ->whereNull('separation_date')
    ->get();

// Pedidos atrasados
$lateOrders = SalesOrder::where('status', SalesOrderStatus::Approved)
    ->where('expected_delivery_date', '<', now())
    ->get();

// Pedidos faturados hoje
$todayInvoiced = SalesOrder::where('status', SalesOrderStatus::Invoiced)
    ->whereDate('nfe_issued_at', today())
    ->get();
```

---

## 💡 Dicas

1. **Sempre use transações** para operações que envolvem múltiplas tabelas
2. **Valide os dados** antes de criar pedidos
3. **Use os métodos do Service** para operações complexas
4. **Aproveite os relacionamentos** do Eloquent para carregar dados relacionados
5. **Use scopes** para queries frequentes
6. **Registre logs** de ações importantes
7. **Calcule totais** sempre após adicionar/modificar itens

---

**Versão:** 1.0.0  
**Data:** 10 de Abril de 2026

