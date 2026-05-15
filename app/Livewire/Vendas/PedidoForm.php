<?php

namespace App\Livewire\Vendas;

use App\Enums\CanalVenda;
use App\Enums\FiscalNoteStatus;
use App\Enums\OrigemPedido;
use App\Enums\SalesOrderStatus;
use App\Enums\TipoFrete;
use App\Enums\TipoOperacaoVenda;
use App\Models\Carrier;
use App\Models\Client;
use App\Models\FiscalNote;
use App\Models\FiscalNoteItem;
use App\Models\Product;
use App\Models\PriceTable;
use App\Models\SalesOrder;
use App\Models\SalesOrderItem;
use App\Models\TipoOperacaoFiscal;
use App\Models\User;
use App\Services\NFeService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Pedido de Venda')]
class PedidoForm extends Component
{
    /* ─── Routing / Identity ─── */
    public ?int  $orderId    = null;   // null = novo pedido
    public string $activeTab = 'geral';

    /* ─── Fiscal Action Panel ─── */
    public bool   $showFiscalPanel  = false;
    public string $finalizationType = ''; // 'romaneio' | 'nota_fiscal' | 'pedido'

    /* ── FORM — 1. Geral ── */
    public string $client_id               = '';
    public string $seller_id               = '';
    public string $status                  = '';
    public string $operation_type          = '';
    public string $tipo_operacao_fiscal_id = '';
    public string $sales_channel           = '';
    public string $origin                  = '';
    public string $company_branch          = '';
    public string $order_date              = '';
    public string $expected_delivery_date  = '';
    public string $delivery_date           = '';
    public string $price_table_id          = '';
    public string $payment_condition       = '';
    public string $internal_notes          = '';
    public string $customer_notes          = '';
    public string $fiscal_notes_obs        = '';

    /* ── FORM — 2. Itens ── */
    public array  $orderItems    = [];
    public string $searchProduct = '';
    public array  $searchResults = [];

    /* ── FORM — 3. Totais / Descontos ── */
    public string $discount_amount  = '0';
    public string $additions_amount = '0';
    public string $shipping_amount  = '0';
    public string $insurance_amount = '0';
    public string $other_expenses   = '0';

    /* ── FORM — 4. Endereços ── */
    public array $billing  = ['zip_code'=>'','street'=>'','number'=>'','complement'=>'','district'=>'','city'=>'','state'=>''];
    public array $delivery = ['zip_code'=>'','street'=>'','number'=>'','complement'=>'','district'=>'','city'=>'','state'=>''];
    public bool  $same_billing_delivery = true;

    /* ── FORM — 5. Logística ── */
    public string $carrier_id     = '';
    public string $freight_type   = '';
    public string $gross_weight   = '';
    public string $net_weight     = '';
    public string $volumes        = '';
    public string $tracking_code  = '';
    public string $delivery_notes = '';

    /* ── FORM — 6. Pagamento ── */
    public string $payment_method   = '';
    public string $installments_qty = '1';

    /* ── Validation warnings (fiscal) ── */
    public array $fiscalWarnings = [];

    /* ─────────────────────────────────────────
       LIFECYCLE — mount
    ───────────────────────────────────────── */
    public function mount(?int $orderId = null): void
    {
        $this->orderId    = $orderId;
        $this->order_date = now()->format('Y-m-d\TH:i');
        $this->status     = SalesOrderStatus::Aberto->value;
        $this->origin     = OrigemPedido::Manual->value;
        $this->sales_channel = CanalVenda::Balcao->value;
        $this->operation_type = TipoOperacaoVenda::VendaNormal->value;

        if ($orderId) {
            $this->loadOrder($orderId);
        }
    }

    /* ─────────────────────────────────────────
       COMPUTED
    ───────────────────────────────────────── */
    #[Computed]
    public function order(): ?SalesOrder
    {
        return $this->orderId ? SalesOrder::with(['client','seller','items.product','addresses','fiscalNotes'])->find($this->orderId) : null;
    }

    #[Computed]
    public function clients()
    {
        return Client::orderBy('name')->get(['id','name','social_name','taxNumber','situation','credit_limit','payment_condition_default','price_table_id','address_zip_code','address_street','address_number','address_complement','address_district','address_city','address_state']);
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
       TOTALS
    ───────────────────────────────────────── */
    public function getSubtotal(): float
    {
        return collect($this->orderItems)->sum(function ($item) {
            $qty  = (float) ($item['quantity']         ?? 0);
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
        return $this->getSubtotal()
            - (float) ($this->discount_amount  ?? 0)
            + (float) ($this->additions_amount ?? 0)
            + (float) ($this->shipping_amount  ?? 0)
            + (float) ($this->insurance_amount ?? 0)
            + (float) ($this->other_expenses   ?? 0);
    }

    public function getTaxEstimate(): float
    {
        return collect($this->orderItems)->sum(function ($item) {
            $total   = (float) ($item['quantity'] ?? 1) * (float) ($item['unit_price'] ?? 0);
            $icms    = $total * ((float) ($item['icms_percent']   ?? 0) / 100);
            $ipi     = $total * ((float) ($item['ipi_percent']    ?? 0) / 100);
            $pis     = $total * ((float) ($item['pis_percent']    ?? 0) / 100);
            $cofins  = $total * ((float) ($item['cofins_percent'] ?? 0) / 100);
            return $icms + $ipi + $pis + $cofins;
        });
    }

    /* ─────────────────────────────────────────
       PRODUCT SEARCH
    ───────────────────────────────────────── */
    public function updatedSearchProduct(): void
    {
        // Se digitar apenas "." mostra todos os produtos
        if ($this->searchProduct === '.') {
            $this->searchResults = Product::orderBy('name')
                ->limit(15)
                ->get(['id','name','product_code','ean','sale_price'])->toArray();
            return;
        }

        if (strlen($this->searchProduct) >= 2) {
            $this->searchResults = Product::where(function ($q) {
                $q->where('name', 'like', '%'.$this->searchProduct.'%')
                  ->orWhere('product_code', 'like', '%'.$this->searchProduct.'%')
                  ->orWhere('ean', 'like', '%'.$this->searchProduct.'%');
            })->limit(8)->get(['id','name','product_code','ean','sale_price'])->toArray();
        } else {
            $this->searchResults = [];
        }
    }

    public function addProduct(string $productId): void
    {
        $product = Product::find($productId);
        if (!$product) return;
        if (collect($this->orderItems)->firstWhere('product_id', $productId)) {
            $this->addError('searchProduct', 'Produto já adicionado.');
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
            'ncm'              => $product->ncm ?? '',
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

    public function removeItem(int $index): void
    {
        array_splice($this->orderItems, $index, 1);
    }

    /* ─────────────────────────────────────────
       AUTO-FILL CLIENT
    ───────────────────────────────────────── */
    public function updatedClientId($value): void
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

    public function updatedSameBillingDelivery($value): void
    {
        if ($value) $this->delivery = $this->billing;
    }

    /* ─────────────────────────────────────────
       APPLY TIPO OPERAÇÃO FISCAL
    ───────────────────────────────────────── */
    public function applyTipoOperacao(): void
    {
        if (!$this->tipo_operacao_fiscal_id) return;
        $tipo = TipoOperacaoFiscal::find($this->tipo_operacao_fiscal_id);
        if (!$tipo) return;
        foreach ($this->orderItems as $idx => $item) {
            $this->orderItems[$idx]['cfop']          = $tipo->cfop             ?? $item['cfop']          ?? '';
            $this->orderItems[$idx]['cst']           = $tipo->icms_cst         ?? $item['cst']           ?? '';
            $this->orderItems[$idx]['icms_percent']  = $tipo->icms_aliquota !== null ? (string) $tipo->icms_aliquota  : ($item['icms_percent']  ?? '');
            $this->orderItems[$idx]['ipi_percent']   = $tipo->ipi_aliquota  !== null ? (string) $tipo->ipi_aliquota   : ($item['ipi_percent']   ?? '');
            $this->orderItems[$idx]['pis_percent']   = $tipo->pis_aliquota  !== null ? (string) $tipo->pis_aliquota   : ($item['pis_percent']   ?? '');
            $this->orderItems[$idx]['cofins_percent']= $tipo->cofins_aliquota !== null ? (string) $tipo->cofins_aliquota : ($item['cofins_percent'] ?? '');
        }
        session()->flash('fiscal_applied', 'Tipo de operação aplicado a todos os itens!');
    }

    /* ─────────────────────────────────────────
       FISCAL VALIDATION
    ───────────────────────────────────────── */
    public function validateFiscal(): array
    {
        $warnings = [];

        if (!$this->client_id) {
            $warnings[] = ['type' => 'error', 'msg' => 'Cliente não informado.'];
        } else {
            $client = Client::find($this->client_id);
            if ($client && !$client->taxNumber) {
                $warnings[] = ['type' => 'error', 'msg' => 'Cliente sem CPF/CNPJ cadastrado.'];
            }
        }

        foreach ($this->orderItems as $i => $item) {
            $num = $i + 1;
            if (empty($item['ncm'])) {
                $warnings[] = ['type' => 'warning', 'msg' => "Item #{$num} ({$item['product_name']}): NCM não informado."];
            }
            if (empty($item['cfop'])) {
                $warnings[] = ['type' => 'warning', 'msg' => "Item #{$num}: CFOP não informado."];
            }
            if (empty($item['cst']) && empty($item['csosn'])) {
                $warnings[] = ['type' => 'warning', 'msg' => "Item #{$num}: CST/CSOSN não informado."];
            }
        }

        if (empty($this->orderItems)) {
            $warnings[] = ['type' => 'error', 'msg' => 'Nenhum produto adicionado.'];
        }

        return $warnings;
    }

    /* ─────────────────────────────────────────
       SAVE ORDER
    ───────────────────────────────────────── */
    public function save(string $targetStatus = ''): void
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

        DB::transaction(function () use ($targetStatus) {
            $data = $this->buildOrderData();
            if ($targetStatus) {
                $data['status'] = $targetStatus;
            }

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

            if ($this->orderId) {
                $order = SalesOrder::findOrFail($this->orderId);
                $order->update($data);
                $order->items()->delete();
                $order->addresses()->delete();
            } else {
                $data['status'] = $targetStatus ?: ($this->status ?: SalesOrderStatus::Aberto->value);
                $order = SalesOrder::create($data);
                $this->orderId = $order->id;
            }

            foreach ($this->orderItems as $item) {
                SalesOrderItem::create([
                    'sales_order_id'   => $order->id,
                    'product_id'       => $item['product_id'],
                    'sku'              => $item['sku']           ?? '',
                    'ean'              => $item['ean']           ?? '',
                    'description'      => $item['product_name']  ?? '',
                    'unit'             => $item['unit']          ?? 'UN',
                    'quantity'         => $item['quantity'],
                    'unit_price'       => $item['unit_price'],
                    'discount'         => $item['discount']        ?? 0,
                    'discount_percent' => $item['discount_percent'] ?? 0,
                    'addition'         => $item['addition']         ?? 0,
                    'cfop'             => $item['cfop']           ?? null,
                    'ncm'              => $item['ncm']            ?? null,
                    'cst'              => $item['cst']            ?? null,
                    'csosn'            => $item['csosn']          ?? null,
                    'origin'           => $item['origin']         ?? '0',
                    'icms_percent'     => $item['icms_percent']   ?? 0,
                    'ipi_percent'      => $item['ipi_percent']    ?? 0,
                    'pis_percent'      => $item['pis_percent']    ?? 0,
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

        session()->flash('message', $this->orderId ? 'Pedido salvo com sucesso!' : 'Pedido criado com sucesso!');
        unset($this->order);
    }

    /* ─────────────────────────────────────────
       OPEN FISCAL PANEL
    ───────────────────────────────────────── */
    public function openFiscalPanel(): void
    {
        // Salva primeiro
        $this->save();

        // Executa validações fiscais
        $this->fiscalWarnings = $this->validateFiscal();

        $this->showFiscalPanel = true;
    }

    public function closeFiscalPanel(): void
    {
        $this->showFiscalPanel = false;
        $this->fiscalAction    = '';
    }

    /* ─────────────────────────────────────────
       FISCAL ACTIONS
    ───────────────────────────────────────── */

    /** Ação 1: Finalizar como Romaneio (sem NF-e) */
    public function finalizarRomaneio(): void
    {
        $this->ensureOrderSaved();
        $order = SalesOrder::findOrFail($this->orderId);

        $order->update([
            'status' => SalesOrderStatus::ProntoFaturar->value,
        ]);

        $this->closeFiscalPanel();
        session()->flash('message', 'Pedido finalizado como Romaneio (gerencial).');
        $this->redirectToList();
    }

    /** Ação 2: Gerar Nota Fiscal (abre nova aba) */
    public function gerarNotaFiscal(): void
    {
        $this->ensureOrderSaved();
        $order = SalesOrder::findOrFail($this->orderId);

        // Garante que existe uma NF-e
        $fiscalNote = $this->ensurePreNFe($order);

        if (!$fiscalNote) {
            session()->flash('error', 'Erro ao gerar NF-e.');
            return;
        }

        // Atualiza status do pedido
        $order->update(['status' => SalesOrderStatus::Invoiced->value]);

        // Redireciona para página da NF-e em nova aba
        $this->dispatch('open-nfe-window', noteId: $fiscalNote->id);

        $this->closeFiscalPanel();
        session()->flash('message', 'NF-e gerada! Abrindo em nova aba...');
    }

    /** Ação 3: Imprimir Pedido (PDF simples) */
    public function imprimirPedido(): void
    {
        $this->ensureOrderSaved();

        // Abre rota de impressão em nova aba
        $this->dispatch('open-print-window', orderId: $this->orderId);

        $this->closeFiscalPanel();
    }

    /** REMOVIDO: saveWithoutTransmit e transmitNow - agora feito na página da NF-e */

    /* ─────────────────────────────────────────
       HELPERS
    ───────────────────────────────────────── */
    private function ensureOrderSaved(): void
    {
        if (!$this->orderId) {
            $this->save();
        }
    }

    private function ensurePreNFe(SalesOrder $order): ?FiscalNote
    {
        // Reaproveitamos NF em rascunho existente ou criamos uma nova
        $existing = FiscalNote::where('sales_order_id', $order->id)
            ->whereIn('status', [FiscalNoteStatus::Draft->value])
            ->first();

        if ($existing) return $existing;

        $lastNumber = FiscalNote::max('invoice_number') ?? '0';
        $newNumber  = str_pad((int) $lastNumber + 1, 9, '0', STR_PAD_LEFT);

        $note = FiscalNote::create([
            'client_id'      => $order->client_id,
            'sales_order_id' => $order->id,
            'invoice_number' => $newNumber,
            'series'         => '1',
            'type'           => 'nfe',
            'environment'    => config('settings.fiscal_ambiente', 'homologation'),
            'status'         => FiscalNoteStatus::Draft->value,
            'amount'         => $order->total_amount ?? 0,
            'emitted_by'     => auth()->id(),
            'notes'          => 'Gerado do Pedido #'.$order->order_number,
        ]);

        // Cria os itens da NF-e a partir dos itens do pedido
        foreach ($order->items as $idx => $item) {
            FiscalNoteItem::create([
                'fiscal_note_id'  => $note->id,
                'item_number'     => $idx + 1,
                'product_code'    => $item->sku     ?? (string) $item->product_id,
                'ean'             => $item->ean      ?? 'SEM GTIN',
                'description'     => $item->description ?? $item->product?->name ?? 'Produto',
                'ncm'             => $item->ncm      ?? '',
                'cfop'            => $item->cfop     ?? '5102',
                'unit'            => $item->unit     ?? 'UN',
                'quantity'        => $item->quantity,
                'unit_price'      => $item->unit_price,
                'total'           => (float) $item->quantity * (float) $item->unit_price,
                'discount'        => $item->discount ?? 0,
                'freight'         => 0,
                'origin'          => $item->origin   ?? '0',
                'cst'             => $item->cst      ?? '00',
                'csosn'           => $item->csosn    ?? null,
                'icms_percent'    => $item->icms_percent  ?? 0,
                'icms_base'       => (float) $item->quantity * (float) $item->unit_price,
                'icms_amount'     => ((float) $item->quantity * (float) $item->unit_price) * ((float) ($item->icms_percent ?? 0) / 100),
                'pis_cst'         => $item->pis_cst  ?? '99',
                'pis_percent'     => $item->pis_percent  ?? 0,
                'pis_base'        => (float) $item->quantity * (float) $item->unit_price,
                'pis_amount'      => ((float) $item->quantity * (float) $item->unit_price) * ((float) ($item->pis_percent ?? 0) / 100),
                'cofins_cst'      => $item->cofins_cst ?? '99',
                'cofins_percent'  => $item->cofins_percent ?? 0,
                'cofins_base'     => (float) $item->quantity * (float) $item->unit_price,
                'cofins_amount'   => ((float) $item->quantity * (float) $item->unit_price) * ((float) ($item->cofins_percent ?? 0) / 100),
                'ipi_cst'         => $item->ipi_cst  ?? '99',
                'ipi_percent'     => $item->ipi_percent  ?? 0,
                'ipi_base'        => 0,
                'ipi_amount'      => 0,
            ]);
        }

        return $note;
    }

    private function buildOrderData(): array
    {
        return [
            'client_id'              => $this->client_id,
            'user_id'                => auth()->id(),
            'seller_id'              => $this->seller_id              ?: null,
            'is_fiscal'              => true, // Sempre true, decisão de gerar NF-e é na finalização
            'operation_type'         => $this->operation_type         ?: null,
            'sales_channel'          => $this->sales_channel          ?: null,
            'origin'                 => $this->origin                 ?: null,
            'company_branch'         => $this->company_branch         ?: null,
            'order_date'             => $this->order_date             ?: now(),
            'expected_delivery_date' => $this->expected_delivery_date ?: null,
            'delivery_date'          => $this->delivery_date          ?: null,
            'payment_condition'      => $this->payment_condition      ?: null,
            'price_table_id'         => $this->price_table_id         ?: null,
            'discount_amount'        => $this->discount_amount        ?: 0,
            'additions_amount'       => $this->additions_amount       ?: 0,
            'shipping_amount'        => $this->shipping_amount        ?: 0,
            'insurance_amount'       => $this->insurance_amount       ?: 0,
            'other_expenses'         => $this->other_expenses         ?: 0,
            'carrier_id'             => $this->carrier_id             ?: null,
            'freight_type'           => $this->freight_type           ?: null,
            'gross_weight'           => $this->gross_weight           ?: null,
            'net_weight'             => $this->net_weight             ?: null,
            'volumes'                => $this->volumes                ?: null,
            'tracking_code'          => $this->tracking_code          ?: null,
            'delivery_notes'         => $this->delivery_notes         ?: null,
            'internal_notes'         => $this->internal_notes         ?: null,
            'customer_notes'         => $this->customer_notes         ?: null,
            'fiscal_notes_obs'       => $this->fiscal_notes_obs       ?: null,
            'payment_method'         => $this->payment_method         ?: null,
            'status'                 => $this->status ?: SalesOrderStatus::Aberto->value,
        ];
    }

    private function loadOrder(int $id): void
    {
        $order = SalesOrder::with(['items', 'addresses'])->findOrFail($id);
        $this->client_id              = (string) $order->client_id;
        $this->seller_id              = (string) ($order->seller_id ?? '');
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
        $this->payment_method         = $order->payment_method ?? '';

        $this->orderItems = $order->items->map(fn ($item) => [
            'product_id'       => $item->product_id,
            'product_name'     => $item->product?->name ?? 'N/A',
            'sku'              => $item->sku           ?? '',
            'ean'              => $item->ean           ?? '',
            'unit'             => $item->unit          ?? 'UN',
            'quantity'         => (string) $item->quantity,
            'unit_price'       => (string) $item->unit_price,
            'discount'         => (string) $item->discount,
            'discount_percent' => (string) ($item->discount_percent ?? 0),
            'addition'         => (string) ($item->addition ?? 0),
            'cfop'             => $item->cfop          ?? '5102',
            'ncm'              => $item->ncm           ?? '',
            'cst'              => $item->cst           ?? '',
            'csosn'            => $item->csosn         ?? '',
            'origin'           => $item->origin        ?? '0',
            'icms_percent'     => (string) ($item->icms_percent  ?? 0),
            'ipi_percent'      => (string) ($item->ipi_percent   ?? 0),
            'pis_percent'      => (string) ($item->pis_percent   ?? 0),
            'cofins_percent'   => (string) ($item->cofins_percent ?? 0),
        ])->toArray();

        $billingAddr  = $order->addresses->firstWhere('type', 'billing');
        $deliveryAddr = $order->addresses->firstWhere('type', 'delivery');
        if ($billingAddr) {
            $this->billing = ['zip_code'=>$billingAddr->zip_code??'','street'=>$billingAddr->street??'','number'=>$billingAddr->number??'','complement'=>$billingAddr->complement??'','district'=>$billingAddr->district??'','city'=>$billingAddr->city??'','state'=>$billingAddr->state??''];
        }
        if ($deliveryAddr) {
            $this->same_billing_delivery = false;
            $this->delivery = ['zip_code'=>$deliveryAddr->zip_code??'','street'=>$deliveryAddr->street??'','number'=>$deliveryAddr->number??'','complement'=>$deliveryAddr->complement??'','district'=>$deliveryAddr->district??'','city'=>$deliveryAddr->city??'','state'=>$deliveryAddr->state??''];
        }
    }

    private function redirectToList(): void
    {
        $this->redirect(route('vendas.pedidos'), navigate: true);
    }

    /* ─────────────────────────────────────────
       RENDER
    ───────────────────────────────────────── */
    public function render(): View
    {
        return view('livewire.vendas.pedido-form', [
            'clients'             => $this->clients,
            'sellers'             => $this->sellers,
            'carriers'            => $this->carriers,
            'priceTables'         => $this->priceTables,
            'tiposOperacaoFiscal' => $this->tiposOperacaoFiscal,
            'statuses'            => SalesOrderStatus::cases(),
            'operacoes'           => TipoOperacaoVenda::cases(),
            'canais'              => CanalVenda::cases(),
            'origens'             => OrigemPedido::cases(),
            'tiposFrete'          => TipoFrete::cases(),
            'subtotal'            => $this->getSubtotal(),
            'totalGeral'          => $this->getTotal(),
            'taxEstimate'         => $this->getTaxEstimate(),
        ]);
    }
}


