<?php

namespace App\Livewire\Compras;

use App\Enums\SolicitacaoCompraStatus;
use App\Enums\SolicitacaoCompraPrioridade;
use App\Enums\PurchaseOrderOrigin;
use App\Models\Cotacao;
use App\Models\CotacaoItem;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\PurchaseRequisition;
use App\Models\PurchaseRequisitionItem;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Solicitações de Compra')]
class SolicitacoesCompra extends Component
{
    use WithPagination;

    /* ─── Filters ─── */
    public string $search          = '';
    public string $filterStatus    = '';
    public string $filterPriority  = '';

    /* ─── Modal State ─── */
    public bool   $showModal       = false;
    public bool   $showDetail      = false;
    public bool   $showRejectModal = false;
    public string $activeTab       = 'geral';
    public ?int   $editingId       = null;
    public ?int   $viewingId       = null;

    /* ══════════════════════════════════════════
       FORM — 1. Geral
    ══════════════════════════════════════════ */
    public string $title        = '';
    public string $status       = '';
    public string $priority     = '';
    public string $requester_id = '';
    public string $department   = '';
    public string $needed_by    = '';
    public string $justification = '';
    public string $notes        = '';

    /* ══════════════════════════════════════════
       FORM — 2. Itens
    ══════════════════════════════════════════ */
    public array  $reqItems      = [];
    public string $searchProduct = '';
    public array  $searchResults = [];

    /* ─── Reject ─── */
    public string $rejection_reason = '';

    /* ─────────────────────────────────────────
       COMPUTED
    ───────────────────────────────────────── */
    #[Computed]
    public function requisitions()
    {
        return PurchaseRequisition::withCount('items')
            ->with(['requester'])
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('number', 'like', '%' . $this->search . '%')
                       ->orWhere('title', 'like', '%' . $this->search . '%')
                       ->orWhere('department', 'like', '%' . $this->search . '%')
                       ->orWhereHas('requester', fn($u) => $u->where('name', 'like', '%' . $this->search . '%'));
                });
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterPriority, fn($q) => $q->where('priority', $this->filterPriority))
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function stats(): array
    {
        $counts     = PurchaseRequisition::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status')->toArray();
        $totalValue = PurchaseRequisition::with('items')->get()->sum(fn($r) => $r->getTotalEstimado());

        return [
            'total'      => array_sum($counts),
            'aguardando' => $counts[SolicitacaoCompraStatus::AguardandoAprovacao->value] ?? 0,
            'aprovadas'  => $counts[SolicitacaoCompraStatus::Aprovada->value]            ?? 0,
            'rejeitadas' => $counts[SolicitacaoCompraStatus::Rejeitada->value]           ?? 0,
            'convertidas'=> $counts[SolicitacaoCompraStatus::Convertida->value]          ?? 0,
            'total_value'=> $totalValue,
        ];
    }

    #[Computed]
    public function users()
    {
        return User::where('is_active', true)->orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function viewingRequisition(): ?PurchaseRequisition
    {
        if (!$this->viewingId) return null;

        return PurchaseRequisition::with(['items.product', 'requester', 'approver', 'purchaseOrder', 'cotacao'])
            ->find($this->viewingId);
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

        if (collect($this->reqItems)->firstWhere('product_id', $productId)) {
            session()->flash('error', 'Produto já adicionado.');
            return;
        }

        $this->reqItems[] = [
            'product_id'      => $product->id,
            'description'     => $product->name,
            'sku'             => $product->product_code ?? '',
            'unit'            => $product->unit_of_measure ?? 'UN',
            'quantity'        => '1',
            'estimated_price' => (string) ($product->cost_price ?? 0),
            'cost_center'     => '',
            'notes'           => '',
        ];

        $this->searchProduct = '';
        $this->searchResults = [];
    }

    public function addManualItem(): void
    {
        $this->reqItems[] = [
            'product_id'      => null,
            'description'     => '',
            'sku'             => '',
            'unit'            => 'UN',
            'quantity'        => '1',
            'estimated_price' => '0',
            'cost_center'     => '',
            'notes'           => '',
        ];
    }

    public function removeItem(int $index): void
    {
        array_splice($this->reqItems, $index, 1);
    }

    /* ─────────────────────────────────────────
       MODAL OPEN / CLOSE
    ───────────────────────────────────────── */
    public function openModal(): void
    {
        $this->resetForm();
        $this->status      = SolicitacaoCompraStatus::Rascunho->value;
        $this->priority    = SolicitacaoCompraPrioridade::Normal->value;
        $this->requester_id = (string) auth()->id();
        $this->showModal   = true;
        $this->activeTab   = 'geral';
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
        unset($this->viewingRequisition);
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->viewingId  = null;
        unset($this->viewingRequisition);
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
        $req = PurchaseRequisition::with('items')->findOrFail($id);

        $this->editingId    = $req->id;
        $this->title        = $req->title;
        $this->status       = $req->status->value;
        $this->priority     = $req->priority->value;
        $this->requester_id = (string) ($req->requester_id ?? '');
        $this->department   = $req->department ?? '';
        $this->needed_by    = $req->needed_by?->format('Y-m-d') ?? '';
        $this->justification = $req->justification ?? '';
        $this->notes        = $req->notes ?? '';

        $this->reqItems = $req->items->map(fn($item) => [
            'product_id'      => $item->product_id,
            'description'     => $item->description,
            'sku'             => $item->sku ?? '',
            'unit'            => $item->unit ?? 'UN',
            'quantity'        => (string) $item->quantity,
            'estimated_price' => (string) $item->estimated_price,
            'cost_center'     => $item->cost_center ?? '',
            'notes'           => $item->notes ?? '',
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
            'title'                        => 'required|string|max:255',
            'reqItems'                     => 'required|array|min:1',
            'reqItems.*.description'       => 'required|string|max:255',
            'reqItems.*.quantity'          => 'required|numeric|min:0.001',
        ], [
            'title.required'        => 'O título da solicitação é obrigatório.',
            'reqItems.required'     => 'Adicione pelo menos um item.',
            'reqItems.min'          => 'Adicione pelo menos um item.',
        ]);

        DB::transaction(function () {
            $data = [
                'title'         => $this->title,
                'status'        => $this->status       ?: SolicitacaoCompraStatus::Rascunho->value,
                'priority'      => $this->priority     ?: SolicitacaoCompraPrioridade::Normal->value,
                'requester_id'  => $this->requester_id ?: auth()->id(),
                'department'    => $this->department   ?: null,
                'needed_by'     => $this->needed_by    ?: null,
                'justification' => $this->justification ?: null,
                'notes'         => $this->notes        ?: null,
            ];

            if ($this->editingId) {
                $req = PurchaseRequisition::findOrFail($this->editingId);
                $req->update($data);
                $req->items()->delete();
            } else {
                $req = PurchaseRequisition::create($data);
            }

            foreach ($this->reqItems as $item) {
                PurchaseRequisitionItem::create([
                    'purchase_requisition_id' => $req->id,
                    'product_id'              => $item['product_id'] ?: null,
                    'description'             => $item['description'],
                    'sku'                     => $item['sku']             ?? null,
                    'unit'                    => $item['unit']            ?? 'UN',
                    'quantity'                => (float) ($item['quantity']        ?? 1),
                    'estimated_price'         => (float) ($item['estimated_price'] ?? 0),
                    'cost_center'             => $item['cost_center']     ?: null,
                    'notes'                   => $item['notes']           ?: null,
                ]);
            }
        });

        session()->flash('message', $this->editingId ? 'Solicitação atualizada com sucesso!' : 'Solicitação criada com sucesso!');
        $this->closeModal();
        unset($this->requisitions, $this->stats);
    }

    /* ─────────────────────────────────────────
       SUBMIT FOR APPROVAL
    ───────────────────────────────────────── */
    public function submitForApproval(int $id): void
    {
        $req = PurchaseRequisition::findOrFail($id);
        $req->update(['status' => SolicitacaoCompraStatus::AguardandoAprovacao->value]);
        session()->flash('message', 'Solicitação enviada para aprovação!');
        unset($this->viewingRequisition, $this->requisitions, $this->stats);
    }

    /* ─────────────────────────────────────────
       APPROVE
    ───────────────────────────────────────── */
    public function approve(int $id): void
    {
        PurchaseRequisition::findOrFail($id)->update([
            'status'      => SolicitacaoCompraStatus::Aprovada->value,
            'approved_by' => auth()->id(),
            'approved_at' => now(),
        ]);
        session()->flash('message', 'Solicitação aprovada com sucesso!');
        $this->closeDetail();
        unset($this->requisitions, $this->stats);
    }

    /* ─────────────────────────────────────────
       REJECT
    ───────────────────────────────────────── */
    public function openRejectModal(): void
    {
        $this->rejection_reason = '';
        $this->showRejectModal  = true;
    }

    public function closeRejectModal(): void
    {
        $this->showRejectModal  = false;
        $this->rejection_reason = '';
    }

    public function reject(): void
    {
        $this->validate([
            'rejection_reason' => 'required|string|min:5',
        ], ['rejection_reason.required' => 'Informe o motivo da rejeição.']);

        PurchaseRequisition::findOrFail($this->viewingId)->update([
            'status'           => SolicitacaoCompraStatus::Rejeitada->value,
            'rejection_reason' => $this->rejection_reason,
        ]);

        session()->flash('message', 'Solicitação rejeitada.');
        $this->closeRejectModal();
        $this->closeDetail();
        unset($this->requisitions, $this->stats);
    }

    /* ─────────────────────────────────────────
       CANCEL
    ───────────────────────────────────────── */
    public function cancel(int $id): void
    {
        PurchaseRequisition::findOrFail($id)->update(['status' => SolicitacaoCompraStatus::Cancelada->value]);
        session()->flash('message', 'Solicitação cancelada.');
        $this->closeDetail();
        unset($this->requisitions, $this->stats);
    }

    /* ─────────────────────────────────────────
       CONVERT TO PURCHASE ORDER
    ───────────────────────────────────────── */
    public function convertToPurchaseOrder(int $id): void
    {
        $req = PurchaseRequisition::with('items')->findOrFail($id);

        DB::transaction(function () use ($req) {
            $order = PurchaseOrder::create([
                'supplier_id' => null,
                'origin'      => PurchaseOrderOrigin::Solicitacao->value,
                'order_date'  => now(),
                'notes'       => 'Gerado a partir da solicitação ' . $req->number . ' — ' . $req->title,
                'buyer_id'    => auth()->id(),
            ]);

            foreach ($req->items as $item) {
                $qty   = (float) $item->quantity;
                $price = (float) $item->estimated_price;

                PurchaseOrderItem::create([
                    'purchase_order_id' => $order->id,
                    'product_id'        => $item->product_id,
                    'description'       => $item->description,
                    'sku'               => $item->sku,
                    'unit'              => $item->unit,
                    'quantity'          => $qty,
                    'unit_price'        => $price,
                    'discount'          => 0,
                    'total'             => $qty * $price,
                    'cost_center'       => $item->cost_center,
                ]);
            }

            $order->refresh();
            $order->calculateTotals();
            $order->save();

            $req->update([
                'status'           => SolicitacaoCompraStatus::Convertida->value,
                'purchase_order_id'=> $order->id,
            ]);
        });

        session()->flash('message', 'Pedido de Compra gerado com sucesso!');
        $this->closeDetail();
        unset($this->requisitions, $this->stats);
    }

    /* ─────────────────────────────────────────
       CONVERT TO COTAÇÃO
    ───────────────────────────────────────── */
    public function convertToCotacao(int $id): void
    {
        $req = PurchaseRequisition::with('items')->findOrFail($id);

        DB::transaction(function () use ($req) {
            $cotacao = Cotacao::create([
                'title'  => $req->number . ' — ' . $req->title,
                'status' => \App\Enums\CotacaoStatus::Aberta->value,
                'notes'  => 'Criada a partir da solicitação ' . $req->number,
            ]);

            foreach ($req->items as $item) {
                CotacaoItem::create([
                    'cotacao_id'  => $cotacao->id,
                    'product_id'  => $item->product_id,
                    'description' => $item->description,
                    'sku'         => $item->sku,
                    'unit'        => $item->unit,
                    'quantity'    => $item->quantity,
                ]);
            }

            $req->update([
                'status'    => SolicitacaoCompraStatus::Convertida->value,
                'cotacao_id'=> $cotacao->id,
            ]);
        });

        session()->flash('message', 'Cotação criada com sucesso!');
        $this->closeDetail();
        unset($this->requisitions, $this->stats);
    }

    /* ─────────────────────────────────────────
       HELPERS
    ───────────────────────────────────────── */
    public function clearFilters(): void
    {
        $this->search         = '';
        $this->filterStatus   = '';
        $this->filterPriority = '';
        $this->resetPage();
        unset($this->requisitions);
    }

    public function updatingSearch(): void        { $this->resetPage(); unset($this->requisitions); }
    public function updatingFilterStatus(): void  { $this->resetPage(); unset($this->requisitions); }
    public function updatingFilterPriority(): void{ $this->resetPage(); unset($this->requisitions); }

    public function getSubtotal(): float
    {
        return collect($this->reqItems)->sum(function ($item) {
            return (float) ($item['quantity'] ?? 0) * (float) ($item['estimated_price'] ?? 0);
        });
    }

    private function resetForm(): void
    {
        $this->editingId     = null;
        $this->title         = '';
        $this->status        = '';
        $this->priority      = '';
        $this->requester_id  = '';
        $this->department    = '';
        $this->needed_by     = '';
        $this->justification = '';
        $this->notes         = '';
        $this->reqItems      = [];
        $this->searchProduct = '';
        $this->searchResults = [];
        $this->activeTab     = 'geral';
        $this->resetValidation();
    }

    /* ─────────────────────────────────────────
       RENDER
    ───────────────────────────────────────── */
    public function render(): View
    {
        return view('livewire.compras.solicitacoes-compra', [
            'requisitions' => $this->requisitions,
            'stats'        => $this->stats,
            'statuses'     => SolicitacaoCompraStatus::cases(),
            'priorities'   => SolicitacaoCompraPrioridade::cases(),
            'users'        => $this->users,
            'subtotal'     => $this->getSubtotal(),
        ]);
    }
}

