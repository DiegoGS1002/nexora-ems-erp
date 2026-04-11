<?php
namespace App\Livewire\Vendas;
use App\Enums\SalesOrderStatus;
use App\Enums\TipoOperacaoVenda;
use App\Enums\CanalVenda;
use App\Enums\OrigemPedido;
use App\Enums\TipoFrete;
use App\Models\Carrier;
use App\Models\Client;
use App\Models\Product;
use App\Models\PriceTable;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\SalesOrderLog;
use App\Models\TipoOperacaoFiscal;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;
class PedidosVenda extends Component
{
    use WithPagination;
    /* ─── Filters ─── */
    public string $search = '';
    public string $filterStatus = '';
    /* ─── Modal State ─── */
    public bool $showModal   = false;
    public bool $showDetail  = false;
    public string $activeTab = 'geral';
    public ?int $editingId   = null;
    public ?int $viewingId   = null;
    /* ══════════════════════════════════════════
       FORM FIELDS — 1. Cabeçalho / Geral
    ══════════════════════════════════════════ */
    public string $client_id              = '';
    public string $seller_id              = '';
    public string $status                 = '';
    public bool   $is_fiscal              = false;
    public string $operation_type         = '';
    public string $tipo_operacao_fiscal_id = '';   // FK → TipoOperacaoFiscal
    public string $sales_channel          = '';
    public string $origin                 = '';
    public string $company_branch         = '';
    public string $order_date             = '';
    public string $expected_delivery_date = '';
    public string $delivery_date          = '';
    public string $price_table_id         = '';
    public string $payment_condition      = '';
    public string $internal_notes         = '';
    public string $customer_notes         = '';
    public string $fiscal_notes_obs       = '';
    /* ══════════════════════════════════════════
       FORM FIELDS — 2. Itens
    ══════════════════════════════════════════ */
    public array  $orderItems    = [];
    public string $searchProduct = '';
    public array  $searchResults = [];
    /* ══════════════════════════════════════════
       FORM FIELDS — 3. Totais
    ══════════════════════════════════════════ */
    public string $discount_amount  = '0';
    public string $additions_amount = '0';
    public string $shipping_amount  = '0';
    public string $insurance_amount = '0';
    public string $other_expenses   = '0';
    /* ══════════════════════════════════════════
       FORM FIELDS — 4. Endereços
    ══════════════════════════════════════════ */
    public array $billing  = ['zip_code'=>'','street'=>'','number'=>'','complement'=>'','district'=>'','city'=>'','state'=>''];
    public array $delivery = ['zip_code'=>'','street'=>'','number'=>'','complement'=>'','district'=>'','city'=>'','state'=>''];
    public bool  $same_billing_delivery = true;
    /* ══════════════════════════════════════════
       FORM FIELDS — 5. Logística
    ══════════════════════════════════════════ */
    public string $carrier_id     = '';
    public string $freight_type   = '';
    public string $gross_weight   = '';
    public string $net_weight     = '';
    public string $volumes        = '';
    public string $tracking_code  = '';
    public string $delivery_notes = '';
    /* ══════════════════════════════════════════
       FORM FIELDS — 6. Pagamento
    ══════════════════════════════════════════ */
    public string $payment_method    = '';
    public string $installments_qty  = '1';
    /* ─────────────────────────────────────────
       COMPUTED
    ───────────────────────────────────────── */
    #[Computed]
    public function orders()
    {
        $query = SalesOrder::with(['client', 'seller', 'items'])->latest();
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('order_number', 'like', '%'.$this->search.'%')
                  ->orWhereHas('client', fn($c) => $c->where('name', 'like', '%'.$this->search.'%'));
            });
        }
        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }
        return $query->paginate(15);
    }
    #[Computed]
    public function stats(): array
    {
        $counts = SalesOrder::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();
        $totalValue = SalesOrder::whereNotIn('status', ['cancelled'])->sum('total_amount');
        return [
            'total'        => array_sum($counts),
            'total_value'  => $totalValue,
            'aberto'       => $counts[SalesOrderStatus::Aberto->value]      ?? 0,
            'approved'     => $counts[SalesOrderStatus::Approved->value]    ?? 0,
            'em_separacao' => $counts[SalesOrderStatus::EmSeparacao->value] ?? 0,
            'invoiced'     => $counts[SalesOrderStatus::Invoiced->value]    ?? 0,
        ];
    }
    #[Computed]
    public function clients()
    {
        return Client::orderBy('name')->get(['id','name','taxNumber','situation','credit_limit','payment_condition_default','address_zip_code','address_street','address_number','address_complement','address_district','address_city','address_state']);
    }
    #[Computed]
    public function sellers()
    {
        return User::where('is_active', true)->orderBy('name')->get(['id','name']);
    }
    #[Computed]
    public function carriers()
    {
        return Carrier::where('is_active', true)->orderBy('name')->get(['id','name']);
    }
    #[Computed]
    public function priceTables()
    {
        return PriceTable::where('is_active', true)->orderBy('name')->get(['id','name','code']);
    }
    #[Computed]
    public function tiposOperacaoFiscal()
    {
        return TipoOperacaoFiscal::where('is_active', true)
            ->orderBy('codigo')
            ->get(['id','codigo','descricao','cfop','natureza_operacao','icms_cst','icms_aliquota','ipi_cst','ipi_aliquota','pis_cst','pis_aliquota','cofins_cst','cofins_aliquota']);
    }
    /* ─────────────────────────────────────────
       AUTO-FILL FISCAL FROM TipoOperacaoFiscal
    ───────────────────────────────────────── */
    public function applyTipoOperacao(): void
    {
        if (!$this->tipo_operacao_fiscal_id) return;

        $tipo = TipoOperacaoFiscal::find($this->tipo_operacao_fiscal_id);
        if (!$tipo) return;

        foreach ($this->orderItems as $idx => $item) {
            $this->orderItems[$idx]['cfop']         = $tipo->cfop ?? $item['cfop'] ?? '';
            $this->orderItems[$idx]['cst']          = $tipo->icms_cst ?? $item['cst'] ?? '';
            $this->orderItems[$idx]['icms_percent'] = $tipo->icms_aliquota !== null ? (string) $tipo->icms_aliquota : ($item['icms_percent'] ?? '');
            $this->orderItems[$idx]['ipi_percent']  = $tipo->ipi_aliquota  !== null ? (string) $tipo->ipi_aliquota  : ($item['ipi_percent']  ?? '');
            $this->orderItems[$idx]['pis_percent']  = $tipo->pis_aliquota  !== null ? (string) $tipo->pis_aliquota  : ($item['pis_percent']  ?? '');
            $this->orderItems[$idx]['cofins_percent']= $tipo->cofins_aliquota !== null ? (string) $tipo->cofins_aliquota : ($item['cofins_percent'] ?? '');
        }

        session()->flash('fiscal_applied', 'Tipo de operação aplicado a todos os itens!');
    }
    #[Computed]
    public function viewingOrder(): ?SalesOrder
    {
        if (!$this->viewingId) return null;
        return SalesOrder::with([
            'client', 'seller', 'items.product',
            'addresses', 'payments.installments', 'logs.user',
        ])->find($this->viewingId);
    }
    /* ─────────────────────────────────────────
       PRODUCT SEARCH
    ───────────────────────────────────────── */
    public function updatedSearchProduct()
    {
        if (strlen($this->searchProduct) >= 2) {
            $this->searchResults = Product::where(function($q) {
                    $q->where('name', 'like', '%'.$this->searchProduct.'%')
                      ->orWhere('product_code', 'like', '%'.$this->searchProduct.'%')
                      ->orWhere('ean', 'like', '%'.$this->searchProduct.'%');
                })
                ->limit(8)
                ->get(['id','name','product_code','ean','sale_price','unit_of_measure_id'])
                ->toArray();
        } else {
            $this->searchResults = [];
        }
    }
    public function addProduct(string $productId)
    {
        $product = Product::find($productId);
        if (!$product) return;
        if (collect($this->orderItems)->firstWhere('product_id', $productId)) {
            session()->flash('error', 'Produto já adicionado.');
            return;
        }
        $this->orderItems[] = [
            'product_id'       => $product->id,
            'product_name'     => $product->name,
            'sku'              => $product->product_code ?? '',
            'ean'              => $product->ean ?? '',
            'unit'             => 'UN',
            'quantity'         => '1',
            'unit_price'       => (string) ($product->sale_price ?? 0),
            'discount'         => '0',
            'discount_percent' => '0',
            'addition'         => '0',
            'cfop'             => '5102',
            'ncm'              => '',
            'cst'              => '00',
            'csosn'            => '',
            'origin'           => '0',
            'icms_percent'     => '0',
            'ipi_percent'      => '0',
            'pis_percent'      => '0',
            'cofins_percent'   => '0',
        ];
        $this->searchProduct = '';
        $this->searchResults = [];
    }
    public function removeItem(int $index)
    {
        array_splice($this->orderItems, $index, 1);
    }
    /* ─────────────────────────────────────────
       AUTO-FILL CLIENT DATA
    ───────────────────────────────────────── */
    public function updatedClientId($value)
    {
        if (!$value) return;
        $client = Client::find($value);
        if (!$client) return;
        if (!$this->payment_condition && $client->payment_condition_default) {
            $this->payment_condition = $client->payment_condition_default;
        }
        if (!$this->price_table_id && $client->price_table_id) {
            $this->price_table_id = (string) $client->price_table_id;
        }
        $this->billing = [
            'zip_code'   => $client->address_zip_code   ?? '',
            'street'     => $client->address_street     ?? '',
            'number'     => $client->address_number     ?? '',
            'complement' => $client->address_complement ?? '',
            'district'   => $client->address_district   ?? '',
            'city'       => $client->address_city       ?? '',
            'state'      => $client->address_state      ?? '',
        ];
        if ($this->same_billing_delivery) {
            $this->delivery = $this->billing;
        }
    }
    public function updatedSameBillingDelivery($value)
    {
        if ($value) {
            $this->delivery = $this->billing;
        }
    }
    /* ─────────────────────────────────────────
       MODAL OPEN / CLOSE
    ───────────────────────────────────────── */
    public function openModal()
    {
        $this->resetForm();
        $this->status         = SalesOrderStatus::Aberto->value;
        $this->origin         = OrigemPedido::Manual->value;
        $this->sales_channel  = CanalVenda::Balcao->value;
        $this->operation_type = TipoOperacaoVenda::VendaNormal->value;
        $this->order_date     = now()->format('Y-m-d\TH:i');
        $this->showModal      = true;
        $this->activeTab      = 'geral';
    }
    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }
    public function openDetail(int $id)
    {
        $this->viewingId = $id;
        $this->showDetail = true;
        unset($this->viewingOrder);
    }
    public function closeDetail()
    {
        $this->showDetail = false;
        $this->viewingId  = null;
        unset($this->viewingOrder);
    }
    public function setTab(string $tab)
    {
        $this->activeTab = $tab;
    }
    /* ─────────────────────────────────────────
       EDIT
    ───────────────────────────────────────── */
    public function edit(int $id)
    {
        $order = SalesOrder::with(['items', 'addresses'])->findOrFail($id);
        $this->editingId              = $order->id;
        $this->client_id              = (string) $order->client_id;
        $this->seller_id              = (string) ($order->seller_id ?? '');
        $this->is_fiscal              = $order->is_fiscal;
        $this->status                 = $order->status->value;
        $this->operation_type         = $order->operation_type?->value ?? '';
        $this->sales_channel          = $order->sales_channel?->value ?? '';
        $this->origin                 = $order->origin?->value ?? '';
        $this->company_branch         = $order->company_branch ?? '';
        $this->order_date             = $order->order_date?->format('Y-m-d\TH:i') ?? '';
        $this->expected_delivery_date = $order->expected_delivery_date?->format('Y-m-d') ?? '';
        $this->delivery_date          = $order->delivery_date?->format('Y-m-d') ?? '';
        $this->payment_condition      = $order->payment_condition ?? '';
        $this->price_table_id         = (string) ($order->price_table_id ?? '');
        $this->discount_amount        = (string) ($order->discount_amount ?? 0);
        $this->additions_amount       = (string) ($order->additions_amount ?? 0);
        $this->shipping_amount        = (string) ($order->shipping_amount ?? 0);
        $this->insurance_amount       = (string) ($order->insurance_amount ?? 0);
        $this->other_expenses         = (string) ($order->other_expenses ?? 0);
        $this->carrier_id             = (string) ($order->carrier_id ?? '');
        $this->freight_type           = $order->freight_type?->value ?? '';
        $this->gross_weight           = (string) ($order->gross_weight ?? '');
        $this->net_weight             = (string) ($order->net_weight ?? '');
        $this->volumes                = (string) ($order->volumes ?? '');
        $this->tracking_code          = $order->tracking_code ?? '';
        $this->delivery_notes         = $order->delivery_notes ?? '';
        $this->internal_notes         = $order->internal_notes ?? '';
        $this->customer_notes         = $order->customer_notes ?? '';
        $this->fiscal_notes_obs       = $order->fiscal_notes_obs ?? '';
        $this->orderItems = $order->items->map(fn($item) => [
            'product_id'       => $item->product_id,
            'product_name'     => $item->product?->name ?? 'N/A',
            'sku'              => $item->sku ?? '',
            'ean'              => $item->ean ?? '',
            'unit'             => $item->unit ?? 'UN',
            'quantity'         => (string) $item->quantity,
            'unit_price'       => (string) $item->unit_price,
            'discount'         => (string) $item->discount,
            'discount_percent' => (string) ($item->discount_percent ?? 0),
            'addition'         => (string) ($item->addition ?? 0),
            'cfop'             => $item->cfop ?? '5102',
            'ncm'              => $item->ncm ?? '',
            'cst'              => $item->cst ?? '',
            'csosn'            => $item->csosn ?? '',
            'origin'           => $item->origin ?? '0',
            'icms_percent'     => (string) ($item->icms_percent ?? 0),
            'ipi_percent'      => (string) ($item->ipi_percent ?? 0),
            'pis_percent'      => (string) ($item->pis_percent ?? 0),
            'cofins_percent'   => (string) ($item->cofins_percent ?? 0),
        ])->toArray();
        $billingAddr  = $order->addresses->firstWhere('type','billing');
        $deliveryAddr = $order->addresses->firstWhere('type','delivery');
        if ($billingAddr) {
            $this->billing = ['zip_code'=>$billingAddr->zip_code??'','street'=>$billingAddr->street??'','number'=>$billingAddr->number??'','complement'=>$billingAddr->complement??'','district'=>$billingAddr->district??'','city'=>$billingAddr->city??'','state'=>$billingAddr->state??''];
        }
        if ($deliveryAddr) {
            $this->same_billing_delivery = false;
            $this->delivery = ['zip_code'=>$deliveryAddr->zip_code??'','street'=>$deliveryAddr->street??'','number'=>$deliveryAddr->number??'','complement'=>$deliveryAddr->complement??'','district'=>$deliveryAddr->district??'','city'=>$deliveryAddr->city??'','state'=>$deliveryAddr->state??''];
        }
        $this->showDetail = false;
        $this->viewingId  = null;
        $this->showModal  = true;
        $this->activeTab  = 'geral';
    }
    /* ─────────────────────────────────────────
       SAVE
    ───────────────────────────────────────── */
    public function save()
    {
        $this->validate([
            'client_id'               => 'required|exists:clients,id',
            'orderItems'              => 'required|array|min:1',
            'orderItems.*.product_id' => 'required|exists:products,id',
            'orderItems.*.quantity'   => 'required|numeric|min:0.001',
            'orderItems.*.unit_price' => 'required|numeric|min:0',
        ], [
            'client_id.required'  => 'Selecione um cliente.',
            'orderItems.required' => 'Adicione pelo menos um produto.',
            'orderItems.min'      => 'Adicione pelo menos um produto.',
        ]);
        DB::transaction(function () {
            $data = [
                'client_id'             => $this->client_id,
                'user_id'               => auth()->id(),
                'seller_id'             => $this->seller_id              ?: null,
                'is_fiscal'             => $this->is_fiscal,
                'operation_type'        => $this->operation_type         ?: null,
                'sales_channel'         => $this->sales_channel          ?: null,
                'origin'                => $this->origin                 ?: null,
                'company_branch'        => $this->company_branch         ?: null,
                'order_date'            => $this->order_date             ?: now(),
                'expected_delivery_date'=> $this->expected_delivery_date ?: null,
                'delivery_date'         => $this->delivery_date          ?: null,
                'payment_condition'     => $this->payment_condition      ?: null,
                'price_table_id'        => $this->price_table_id         ?: null,
                'discount_amount'       => $this->discount_amount        ?: 0,
                'additions_amount'      => $this->additions_amount       ?: 0,
                'shipping_amount'       => $this->shipping_amount        ?: 0,
                'insurance_amount'      => $this->insurance_amount       ?: 0,
                'other_expenses'        => $this->other_expenses         ?: 0,
                'carrier_id'            => $this->carrier_id             ?: null,
                'freight_type'          => $this->freight_type           ?: null,
                'gross_weight'          => $this->gross_weight           ?: null,
                'net_weight'            => $this->net_weight             ?: null,
                'volumes'               => $this->volumes                ?: null,
                'tracking_code'         => $this->tracking_code          ?: null,
                'delivery_notes'        => $this->delivery_notes         ?: null,
                'internal_notes'        => $this->internal_notes         ?: null,
                'customer_notes'        => $this->customer_notes         ?: null,
                'fiscal_notes_obs'      => $this->fiscal_notes_obs       ?: null,
            ];
            $client = Client::find($this->client_id);
            if ($client) {
                $data['client_cpf_cnpj']      = $client->taxNumber;
                $data['client_ie']            = $client->inscricao_estadual ?? null;
                $data['client_type']          = $client->tipo_pessoa?->value;
                $data['client_credit_limit']  = $client->credit_limit;
                $data['client_situation']     = $client->situation ?? null;
                $data['client_contact_phone'] = $client->phone_number;
                $data['client_contact_email'] = $client->email;
            }
            if ($this->editingId) {
                $order = SalesOrder::findOrFail($this->editingId);
                $order->update($data);
                $order->items()->delete();
                $order->addresses()->delete();
            } else {
                $data['status'] = $this->status ?: SalesOrderStatus::Aberto->value;
                $order = SalesOrder::create($data);
            }
            foreach ($this->orderItems as $item) {
                SalesOrderItem::create([
                    'sales_order_id'   => $order->id,
                    'product_id'       => $item['product_id'],
                    'sku'              => $item['sku']          ?? '',
                    'ean'              => $item['ean']          ?? '',
                    'description'      => $item['product_name'] ?? '',
                    'unit'             => $item['unit']         ?? 'UN',
                    'quantity'         => $item['quantity'],
                    'unit_price'       => $item['unit_price'],
                    'discount'         => $item['discount']        ?? 0,
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'addition'         => $item['addition']        ?? 0,
                    'cfop'             => $item['cfop']          ?? null,
                    'ncm'              => $item['ncm']           ?? null,
                    'cst'              => $item['cst']           ?? null,
                    'csosn'            => $item['csosn']         ?? null,
                    'origin'           => $item['origin']        ?? '0',
                    'icms_percent'     => $item['icms_percent']  ?? 0,
                    'ipi_percent'      => $item['ipi_percent']   ?? 0,
                    'pis_percent'      => $item['pis_percent']   ?? 0,
                    'cofins_percent'   => $item['cofins_percent'] ?? 0,
                ]);
            }
            if (array_filter($this->billing)) {
                $order->addresses()->create(array_merge(['type' => 'billing'], $this->billing));
            }
            $deliveryData = $this->same_billing_delivery ? $this->billing : $this->delivery;
            if (array_filter($deliveryData)) {
                $order->addresses()->create(array_merge(['type' => 'delivery'], $deliveryData));
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
    public function changeStatus(int $id, string $status)
    {
        $order = SalesOrder::findOrFail($id);
        $order->update(['status' => $status]);
        SalesOrderLog::create([
            'sales_order_id' => $order->id,
            'user_id'        => auth()->id(),
            'action'         => 'status_changed',
            'new_status'     => $status,
            'description'    => 'Status alterado via painel',
        ]);
        session()->flash('message', 'Status alterado!');
        unset($this->orders, $this->stats);
    }
    public function approve(int $id)
    {
        $order = SalesOrder::findOrFail($id);
        $order->update([
            'status'      => SalesOrderStatus::Approved->value,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        session()->flash('message', 'Pedido aprovado!');
        $this->closeDetail();
        unset($this->orders, $this->stats);
    }
    public function cancelOrder(int $id)
    {
        $order = SalesOrder::findOrFail($id);
        $order->update(['status' => SalesOrderStatus::Cancelled->value]);
        SalesOrderLog::create([
            'sales_order_id' => $order->id,
            'user_id'        => auth()->id(),
            'action'         => 'cancelled',
            'description'    => 'Pedido cancelado via painel',
        ]);
        session()->flash('message', 'Pedido cancelado.');
        $this->closeDetail();
        unset($this->orders, $this->stats);
    }
    /* ─────────────────────────────────────────
       HELPERS
    ───────────────────────────────────────── */
    public function clearFilters()
    {
        $this->search = '';
        $this->filterStatus = '';
        $this->resetPage();
        unset($this->orders);
    }
    public function updatingSearch()       { $this->resetPage(); unset($this->orders); }
    public function updatingFilterStatus() { $this->resetPage(); unset($this->orders); }
    private function resetForm()
    {
        $this->editingId              = null;
        $this->client_id              = '';
        $this->seller_id              = '';
        $this->status                 = '';
        $this->is_fiscal              = false;
        $this->operation_type         = '';
        $this->tipo_operacao_fiscal_id = '';
        $this->sales_channel          = '';
        $this->origin                 = '';
        $this->company_branch         = '';
        $this->order_date             = '';
        $this->expected_delivery_date = '';
        $this->delivery_date          = '';
        $this->payment_condition      = '';
        $this->price_table_id         = '';
        $this->discount_amount        = '0';
        $this->additions_amount       = '0';
        $this->shipping_amount        = '0';
        $this->insurance_amount       = '0';
        $this->other_expenses         = '0';
        $this->carrier_id             = '';
        $this->freight_type           = '';
        $this->gross_weight           = '';
        $this->net_weight             = '';
        $this->volumes                = '';
        $this->tracking_code          = '';
        $this->delivery_notes         = '';
        $this->internal_notes         = '';
        $this->customer_notes         = '';
        $this->fiscal_notes_obs       = '';
        $this->payment_method         = '';
        $this->installments_qty       = '1';
        $this->orderItems             = [];
        $this->searchProduct          = '';
        $this->searchResults          = [];
        $this->billing                = ['zip_code'=>'','street'=>'','number'=>'','complement'=>'','district'=>'','city'=>'','state'=>''];
        $this->delivery               = ['zip_code'=>'','street'=>'','number'=>'','complement'=>'','district'=>'','city'=>'','state'=>''];
        $this->same_billing_delivery  = true;
        $this->activeTab              = 'geral';
        $this->resetValidation();
    }
    public function getSubtotal(): float
    {
        return collect($this->orderItems)->sum(function($item) {
            $qty   = (float) ($item['quantity']        ?? 0);
            $price = (float) ($item['unit_price']      ?? 0);
            $disc  = (float) ($item['discount']        ?? 0);
            $discP = (float) ($item['discount_percent'] ?? 0);
            $add   = (float) ($item['addition']        ?? 0);
            if ($discP > 0) $disc = ($qty * $price) * ($discP / 100);
            return ($qty * $price) - $disc + $add;
        });
    }
    public function getTotal(): float
    {
        $sub  = $this->getSubtotal();
        $disc = (float) ($this->discount_amount  ?? 0);
        $add  = (float) ($this->additions_amount ?? 0);
        $ship = (float) ($this->shipping_amount  ?? 0);
        $ins  = (float) ($this->insurance_amount ?? 0);
        $oth  = (float) ($this->other_expenses   ?? 0);
        return $sub - $disc + $add + $ship + $ins + $oth;
    }
    /* ─────────────────────────────────────────
       RENDER
    ───────────────────────────────────────── */
    public function render(): View
    {
        return view('livewire.vendas.pedidos-venda', [
            'orders'                => $this->orders,
            'stats'                 => $this->stats,
            'clients'               => $this->clients,
            'sellers'               => $this->sellers,
            'carriers'              => $this->carriers,
            'priceTables'           => $this->priceTables,
            'tiposOperacaoFiscal'   => $this->tiposOperacaoFiscal,
            'statuses'              => SalesOrderStatus::cases(),
            'operacoes'             => TipoOperacaoVenda::cases(),
            'canais'                => CanalVenda::cases(),
            'origens'               => OrigemPedido::cases(),
            'tiposFrete'            => TipoFrete::cases(),
            'subtotal'              => $this->getSubtotal(),
            'totalGeral'            => $this->getTotal(),
        ]);
    }
}
