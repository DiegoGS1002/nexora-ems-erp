<?php

namespace App\Livewire\Compras;

use App\Enums\PurchaseOrderStatus;
use App\Enums\PurchaseOrderOrigin;
use App\Enums\TipoFrete;
use App\Models\Carrier;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Pedidos de Compra')]
class PedidosCompra extends Component
{
    use WithPagination;

    /* ─── Filters ─── */
    public string $search       = '';
    public string $filterStatus = '';

    /* ─── Modal State ─── */
    public bool    $showModal  = false;
    public bool    $showDetail = false;
    public string  $activeTab  = 'geral';
    public ?int    $editingId  = null;
    public ?int    $viewingId  = null;

    /* ══════════════════════════════════════════
       FORM — 1. Cabeçalho / Geral
    ══════════════════════════════════════════ */
    public string $supplier_id            = '';
    public string $buyer_id               = '';
    public string $status                 = '';
    public string $origin                 = '';
    public string $order_date             = '';
    public string $expected_delivery_date = '';

    /* ══════════════════════════════════════════
       FORM — 2. Itens
    ══════════════════════════════════════════ */
    public array  $orderItems    = [];
    public string $searchProduct = '';
    public array  $searchResults = [];

    /* ══════════════════════════════════════════
       FORM — 3. Pagamento
    ══════════════════════════════════════════ */
    public string $payment_condition = '';
    public string $payment_method    = '';

    /* ══════════════════════════════════════════
       FORM — 4. Totais
    ══════════════════════════════════════════ */
    public string $discount_amount = '0';
    public string $shipping_amount = '0';
    public string $other_expenses  = '0';

    /* ══════════════════════════════════════════
       FORM — 5. Logística
    ══════════════════════════════════════════ */
    public string $freight_type     = '';
    public string $carrier_id       = '';
    public string $delivery_address = '';

    /* ══════════════════════════════════════════
       FORM — 6. Observações
    ══════════════════════════════════════════ */
    public string $notes          = '';
    public string $notes_supplier = '';

    /* ─────────────────────────────────────────
       COMPUTED
    ───────────────────────────────────────── */
    #[Computed]
    public function orders()
    {
        return PurchaseOrder::with(['supplier', 'buyer', 'items'])
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('order_number', 'like', '%' . $this->search . '%')
                       ->orWhereHas('supplier', fn($s) => $s->where('social_name', 'like', '%' . $this->search . '%')
                           ->orWhere('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function stats(): array
    {
        $counts     = PurchaseOrder::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status')->toArray();
        $totalValue = PurchaseOrder::whereNotIn('status', [PurchaseOrderStatus::Cancelado->value])->sum('total_amount');

        return [
            'total'        => array_sum($counts),
            'total_value'  => $totalValue,
            'rascunho'     => $counts[PurchaseOrderStatus::Rascunho->value]            ?? 0,
            'aguardando'   => $counts[PurchaseOrderStatus::AguardandoAprovacao->value] ?? 0,
            'aprovado'     => $counts[PurchaseOrderStatus::Aprovado->value]            ?? 0,
            'recebido'     => $counts[PurchaseOrderStatus::RecebidoTotal->value]       ?? 0,
        ];
    }

    #[Computed]
    public function suppliers()
    {
        return Supplier::orderBy('social_name')->get(['id', 'social_name', 'name', 'taxNumber']);
    }

    #[Computed]
    public function buyers()
    {
        return User::where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function carriers()
    {
        return Carrier::where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function viewingOrder(): ?PurchaseOrder
    {
        if (!$this->viewingId) return null;

        return PurchaseOrder::with(['supplier', 'buyer', 'carrier', 'items.product', 'approver'])->find($this->viewingId);
    }

    /* ─────────────────────────────────────────
       PRODUCT SEARCH
    ───────────────────────────────────────── */
    public function updatedSearchProduct(): void
    {
        if (strlen($this->searchProduct) >= 2) {
            $this->searchResults = Product::where(function ($q) {
                    $q->where('name', 'like', '%' . $this->searchProduct . '%')
                      ->orWhere('product_code', 'like', '%' . $this->searchProduct . '%')
                      ->orWhere('ean', 'like', '%' . $this->searchProduct . '%');
                })
                ->limit(8)
                ->get(['id', 'name', 'product_code', 'ean', 'cost_price', 'unit_of_measure'])
                ->toArray();
        } else {
            $this->searchResults = [];
        }
    }

    public function addProduct(string $productId): void
    {
        $product = Product::find($productId);
        if (!$product) return;

        if (collect($this->orderItems)->firstWhere('product_id', $productId)) {
            session()->flash('error', 'Produto já adicionado.');
            return;
        }

        $this->orderItems[] = [
            'product_id'   => $product->id,
            'description'  => $product->name,
            'sku'          => $product->product_code ?? '',
            'unit'         => $product->unit_of_measure ?? 'UN',
            'quantity'     => '1',
            'unit_price'   => (string) ($product->cost_price ?? 0),
            'discount'     => '0',
            'total'        => (string) ($product->cost_price ?? 0),
            'cost_center'  => '',
        ];

        $this->searchProduct = '';
        $this->searchResults = [];
    }

    public function addManualItem(): void
    {
        $this->orderItems[] = [
            'product_id'  => null,
            'description' => '',
            'sku'         => '',
            'unit'        => 'UN',
            'quantity'    => '1',
            'unit_price'  => '0',
            'discount'    => '0',
            'total'       => '0',
            'cost_center' => '',
        ];
    }

    public function removeItem(int $index): void
    {
        array_splice($this->orderItems, $index, 1);
    }

    public function updatedOrderItems(): void
    {
        foreach ($this->orderItems as $i => $item) {
            $qty   = (float) ($item['quantity']   ?? 0);
            $price = (float) ($item['unit_price']  ?? 0);
            $disc  = (float) ($item['discount']    ?? 0);
            $this->orderItems[$i]['total'] = number_format(max(0, ($qty * $price) - $disc), 2, '.', '');
        }
    }

    /* ─────────────────────────────────────────
       MODAL OPEN / CLOSE
    ───────────────────────────────────────── */
    public function openModal(): void
    {
        $this->resetForm();
        $this->status     = PurchaseOrderStatus::Rascunho->value;
        $this->origin     = PurchaseOrderOrigin::Manual->value;
        $this->order_date = now()->format('Y-m-d\TH:i');
        $this->showModal  = true;
        $this->activeTab  = 'geral';
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function openDetail(int $id): void
    {
        $this->viewingId  = $id;
        $this->showDetail = true;
        unset($this->viewingOrder);
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->viewingId  = null;
        unset($this->viewingOrder);
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    /* ─────────────────────────────────────────
       EDIT
    ───────────────────────────────────────── */
    public function edit(int $id): void
    {
        $order = PurchaseOrder::with('items')->findOrFail($id);

        $this->editingId             = $order->id;
        $this->supplier_id           = (string) $order->supplier_id;
        $this->buyer_id              = (string) ($order->buyer_id ?? '');
        $this->status                = $order->status->value;
        $this->origin                = $order->origin->value;
        $this->order_date            = $order->order_date?->format('Y-m-d\TH:i') ?? '';
        $this->expected_delivery_date = $order->expected_delivery_date?->format('Y-m-d') ?? '';
        $this->payment_condition     = $order->payment_condition ?? '';
        $this->payment_method        = $order->payment_method ?? '';
        $this->freight_type          = $order->freight_type ?? '';
        $this->carrier_id            = (string) ($order->carrier_id ?? '');
        $this->delivery_address      = $order->delivery_address ?? '';
        $this->discount_amount       = (string) ($order->discount_amount ?? 0);
        $this->shipping_amount       = (string) ($order->shipping_amount ?? 0);
        $this->other_expenses        = (string) ($order->other_expenses ?? 0);
        $this->notes                 = $order->notes ?? '';
        $this->notes_supplier        = $order->notes_supplier ?? '';

        $this->orderItems = $order->items->map(fn($item) => [
            'product_id'  => $item->product_id,
            'description' => $item->description,
            'sku'         => $item->sku ?? '',
            'unit'        => $item->unit ?? 'UN',
            'quantity'    => (string) $item->quantity,
            'unit_price'  => (string) $item->unit_price,
            'discount'    => (string) $item->discount,
            'total'       => (string) $item->total,
            'cost_center' => $item->cost_center ?? '',
        ])->toArray();

        $this->showDetail = false;
        $this->viewingId  = null;
        $this->showModal  = true;
        $this->activeTab  = 'geral';
    }

    /* ─────────────────────────────────────────
       SAVE
    ───────────────────────────────────────── */
    public function save(): void
    {
        $this->validate([
            'supplier_id'                 => 'required',
            'order_date'                  => 'required',
            'orderItems'                  => 'required|array|min:1',
            'orderItems.*.description'    => 'required|string|max:255',
            'orderItems.*.quantity'       => 'required|numeric|min:0.001',
            'orderItems.*.unit_price'     => 'required|numeric|min:0',
        ], [
            'supplier_id.required'     => 'Selecione um fornecedor.',
            'order_date.required'      => 'A data do pedido é obrigatória.',
            'orderItems.required'      => 'Adicione pelo menos um item.',
            'orderItems.min'           => 'Adicione pelo menos um item.',
        ]);

        DB::transaction(function () {
            $data = [
                'supplier_id'            => $this->supplier_id,
                'buyer_id'               => $this->buyer_id             ?: null,
                'status'                 => $this->status               ?: PurchaseOrderStatus::Rascunho->value,
                'origin'                 => $this->origin               ?: PurchaseOrderOrigin::Manual->value,
                'order_date'             => $this->order_date           ?: now(),
                'expected_delivery_date' => $this->expected_delivery_date ?: null,
                'payment_condition'      => $this->payment_condition    ?: null,
                'payment_method'         => $this->payment_method       ?: null,
                'freight_type'           => $this->freight_type         ?: null,
                'carrier_id'             => $this->carrier_id           ?: null,
                'delivery_address'       => $this->delivery_address     ?: null,
                'discount_amount'        => $this->discount_amount      ?: 0,
                'shipping_amount'        => $this->shipping_amount      ?: 0,
                'other_expenses'         => $this->other_expenses       ?: 0,
                'notes'                  => $this->notes                ?: null,
                'notes_supplier'         => $this->notes_supplier       ?: null,
            ];

            if ($this->editingId) {
                $order = PurchaseOrder::findOrFail($this->editingId);
                $order->update($data);
                $order->items()->delete();
            } else {
                $order = PurchaseOrder::create($data);
            }

            foreach ($this->orderItems as $item) {
                $qty   = (float) ($item['quantity']  ?? 0);
                $price = (float) ($item['unit_price'] ?? 0);
                $disc  = (float) ($item['discount']   ?? 0);

                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'product_id'        => $item['product_id'] ?: null,
                    'description'       => $item['description'],
                    'sku'               => $item['sku']          ?? null,
                    'unit'              => $item['unit']         ?? 'UN',
                    'quantity'          => $qty,
                    'unit_price'        => $price,
                    'discount'          => $disc,
                    'total'             => max(0, ($qty * $price) - $disc),
                    'cost_center'       => $item['cost_center']  ?: null,
                ]);
            }

            $order->refresh();
            $order->calculateTotals();
            $order->save();
        });

        session()->flash('message', $this->editingId ? 'Pedido atualizado com sucesso!' : 'Pedido criado com sucesso!');
        $this->closeModal();
        unset($this->orders, $this->stats);
    }

    /* ─────────────────────────────────────────
       ACTIONS
    ───────────────────────────────────────── */
    public function changeStatus(int $id, string $status): void
    {
        PurchaseOrder::findOrFail($id)->update(['status' => $status]);
        session()->flash('message', 'Status atualizado!');
        unset($this->orders, $this->stats);
    }

    public function approve(int $id): void
    {
        $order = PurchaseOrder::findOrFail($id);
        $order->update([
            'status'      => PurchaseOrderStatus::Aprovado->value,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        session()->flash('message', 'Pedido aprovado!');
        $this->closeDetail();
        unset($this->orders, $this->stats);
    }

    public function cancelOrder(int $id): void
    {
        PurchaseOrder::findOrFail($id)->update(['status' => PurchaseOrderStatus::Cancelado->value]);
        session()->flash('message', 'Pedido cancelado.');
        $this->closeDetail();
        unset($this->orders, $this->stats);
    }

    /* ─────────────────────────────────────────
       HELPERS
    ───────────────────────────────────────── */
    public function clearFilters(): void
    {
        $this->search       = '';
        $this->filterStatus = '';
        $this->resetPage();
        unset($this->orders);
    }

    public function updatingSearch(): void       { $this->resetPage(); unset($this->orders); }
    public function updatingFilterStatus(): void { $this->resetPage(); unset($this->orders); }

    public function getSubtotal(): float
    {
        return collect($this->orderItems)->sum(function ($item) {
            $qty   = (float) ($item['quantity']  ?? 0);
            $price = (float) ($item['unit_price'] ?? 0);
            $disc  = (float) ($item['discount']   ?? 0);
            return max(0, ($qty * $price) - $disc);
        });
    }

    public function getTotal(): float
    {
        return $this->getSubtotal()
            - (float) ($this->discount_amount ?? 0)
            + (float) ($this->shipping_amount ?? 0)
            + (float) ($this->other_expenses  ?? 0);
    }

    private function resetForm(): void
    {
        $this->editingId             = null;
        $this->supplier_id           = '';
        $this->buyer_id              = '';
        $this->status                = '';
        $this->origin                = '';
        $this->order_date            = '';
        $this->expected_delivery_date = '';
        $this->payment_condition     = '';
        $this->payment_method        = '';
        $this->freight_type          = '';
        $this->carrier_id            = '';
        $this->delivery_address      = '';
        $this->discount_amount       = '0';
        $this->shipping_amount       = '0';
        $this->other_expenses        = '0';
        $this->notes                 = '';
        $this->notes_supplier        = '';
        $this->orderItems            = [];
        $this->searchProduct         = '';
        $this->searchResults         = [];
        $this->activeTab             = 'geral';
        $this->resetValidation();
    }

    /* ─────────────────────────────────────────
       RENDER
    ───────────────────────────────────────── */
    public function render(): View
    {
        return view('livewire.compras.pedidos-compra', [
            'orders'    => $this->orders,
            'stats'     => $this->stats,
            'suppliers' => $this->suppliers,
            'buyers'    => $this->buyers,
            'carriers'  => $this->carriers,
            'statuses'  => PurchaseOrderStatus::cases(),
            'origens'   => PurchaseOrderOrigin::cases(),
            'fretes'    => TipoFrete::cases(),
            'subtotal'  => $this->getSubtotal(),
            'total'     => $this->getTotal(),
        ]);
    }
}

