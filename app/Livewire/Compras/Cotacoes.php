<?php

namespace App\Livewire\Compras;

use App\Enums\CotacaoStatus;
use App\Enums\PurchaseOrderOrigin;
use App\Enums\PurchaseOrderStatus;
use App\Models\Cotacao;
use App\Models\CotacaoItem;
use App\Models\CotacaoResposta;
use App\Models\Product;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Supplier;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Cotações de Compra')]
class Cotacoes extends Component
{
    use WithPagination;

    /* ─── Filters ─── */
    public string $search       = '';
    public string $filterStatus = '';

    /* ─── Modal State ─── */
    public bool   $showModal  = false;
    public bool   $showDetail = false;
    public string $activeTab  = 'geral';
    public ?int   $editingId  = null;
    public ?int   $viewingId  = null;

    /* ══════════════════════════════════════════
       FORM — 1. Geral
    ══════════════════════════════════════════ */
    public string $title         = '';
    public string $status        = '';
    public string $deadline_date = '';
    public string $notes         = '';

    /* ══════════════════════════════════════════
       FORM — 2. Itens
    ══════════════════════════════════════════ */
    public array  $cotacaoItems   = [];
    public string $searchProduct  = '';
    public array  $searchResults  = [];

    /* ══════════════════════════════════════════
       FORM — 3. Fornecedores
    ══════════════════════════════════════════ */
    public array  $cotacaoSuppliers    = [];
    public string $searchSupplierTerm  = '';
    public array  $supplierSearchResults = [];

    /* ─────────────────────────────────────────
       COMPUTED
    ───────────────────────────────────────── */
    #[Computed]
    public function cotacoes()
    {
        return Cotacao::withCount(['items', 'respostas'])
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('number', 'like', '%' . $this->search . '%')
                       ->orWhere('title', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function stats(): array
    {
        $counts = Cotacao::selectRaw('status, count(*) as total')->groupBy('status')->pluck('total', 'status')->toArray();

        return [
            'total'      => array_sum($counts),
            'abertas'    => $counts[CotacaoStatus::Aberta->value]     ?? 0,
            'aguardando' => $counts[CotacaoStatus::Aguardando->value] ?? 0,
            'respondidas'=> $counts[CotacaoStatus::Respondida->value] ?? 0,
            'aprovadas'  => $counts[CotacaoStatus::Aprovada->value]   ?? 0,
            'convertidas'=> $counts[CotacaoStatus::Convertida->value] ?? 0,
        ];
    }

    #[Computed]
    public function allSuppliers()
    {
        return Supplier::orderBy('social_name')->get(['id', 'social_name', 'name', 'taxNumber']);
    }

    #[Computed]
    public function viewingCotacao(): ?Cotacao
    {
        if (!$this->viewingId) return null;

        return Cotacao::with(['items.respostas.supplier', 'creator', 'purchaseOrder'])->find($this->viewingId);
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
                ->get(['id', 'name', 'product_code', 'ean', 'unit_of_measure'])
                ->toArray();
        } else {
            $this->searchResults = [];
        }
    }

    public function addProduct(string $productId): void
    {
        $product = Product::find($productId);
        if (!$product) return;

        if (collect($this->cotacaoItems)->firstWhere('product_id', $productId)) {
            session()->flash('error', 'Produto já adicionado.');
            return;
        }

        $this->cotacaoItems[] = [
            'product_id'  => $product->id,
            'description' => $product->name,
            'sku'         => $product->product_code ?? '',
            'unit'        => $product->unit_of_measure ?? 'UN',
            'quantity'    => '1',
        ];

        // Add price slot for this item to every existing supplier
        $newIndex = count($this->cotacaoItems) - 1;
        foreach ($this->cotacaoSuppliers as $si => $s) {
            $this->cotacaoSuppliers[$si]['prices'][$newIndex] = '0';
        }

        $this->searchProduct = '';
        $this->searchResults = [];
    }

    public function addManualItem(): void
    {
        $newIndex = count($this->cotacaoItems);
        $this->cotacaoItems[] = [
            'product_id'  => null,
            'description' => '',
            'sku'         => '',
            'unit'        => 'UN',
            'quantity'    => '1',
        ];

        foreach ($this->cotacaoSuppliers as $si => $s) {
            $this->cotacaoSuppliers[$si]['prices'][$newIndex] = '0';
        }
    }

    public function removeItem(int $index): void
    {
        array_splice($this->cotacaoItems, $index, 1);

        // Reindex prices in all suppliers
        foreach ($this->cotacaoSuppliers as $si => $s) {
            $prices = array_values($s['prices'] ?? []);
            array_splice($prices, $index, 1);
            $this->cotacaoSuppliers[$si]['prices'] = $prices;
        }
    }

    /* ─────────────────────────────────────────
       SUPPLIER SEARCH & MANAGEMENT
    ───────────────────────────────────────── */
    public function updatedSearchSupplierTerm(): void
    {
        if (strlen($this->searchSupplierTerm) >= 2) {
            $alreadyAdded = collect($this->cotacaoSuppliers)->pluck('supplier_id')->toArray();
            $this->supplierSearchResults = Supplier::where(function ($q) {
                    $q->where('social_name', 'like', '%' . $this->searchSupplierTerm . '%')
                      ->orWhere('name', 'like', '%' . $this->searchSupplierTerm . '%')
                      ->orWhere('taxNumber', 'like', '%' . $this->searchSupplierTerm . '%');
                })
                ->whereNotIn('id', $alreadyAdded)
                ->limit(8)
                ->get(['id', 'social_name', 'name', 'taxNumber'])
                ->toArray();
        } else {
            $this->supplierSearchResults = [];
        }
    }

    public function addSupplier(string $supplierId): void
    {
        if (collect($this->cotacaoSuppliers)->firstWhere('supplier_id', $supplierId)) {
            session()->flash('error', 'Fornecedor já adicionado.');
            return;
        }

        $supplier = Supplier::find($supplierId);
        if (!$supplier) return;

        $prices = [];
        foreach ($this->cotacaoItems as $i => $_) {
            $prices[$i] = '0';
        }

        $this->cotacaoSuppliers[] = [
            'supplier_id'   => $supplierId,
            'supplier_name' => $supplier->social_name ?? $supplier->name,
            'delivery_days' => '',
            'notes'         => '',
            'prices'        => $prices,
        ];

        $this->searchSupplierTerm      = '';
        $this->supplierSearchResults   = [];
    }

    public function removeSupplier(int $index): void
    {
        array_splice($this->cotacaoSuppliers, $index, 1);
    }

    /* ─────────────────────────────────────────
       MODAL OPEN / CLOSE
    ───────────────────────────────────────── */
    public function openModal(): void
    {
        $this->resetForm();
        $this->status    = CotacaoStatus::Rascunho->value;
        $this->showModal = true;
        $this->activeTab = 'geral';
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
        unset($this->viewingCotacao);
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->viewingId  = null;
        unset($this->viewingCotacao);
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
        $cotacao = Cotacao::with(['items', 'respostas.supplier'])->findOrFail($id);

        $this->editingId    = $cotacao->id;
        $this->title        = $cotacao->title;
        $this->status       = $cotacao->status->value;
        $this->deadline_date = $cotacao->deadline_date?->format('Y-m-d') ?? '';
        $this->notes        = $cotacao->notes ?? '';

        $this->cotacaoItems = $cotacao->items->map(fn($item) => [
            'product_id'  => $item->product_id,
            'description' => $item->description,
            'sku'         => $item->sku ?? '',
            'unit'        => $item->unit ?? 'UN',
            'quantity'    => (string) $item->quantity,
        ])->toArray();

        // Build supplier rows from respostas
        $supplierData = [];
        foreach ($cotacao->respostas as $resposta) {
            $sid = $resposta->supplier_id;
            if (!isset($supplierData[$sid])) {
                $supplierData[$sid] = [
                    'supplier_id'   => $sid,
                    'supplier_name' => $resposta->supplier?->social_name ?? $resposta->supplier?->name ?? $sid,
                    'delivery_days' => (string) ($resposta->delivery_days ?? ''),
                    'notes'         => $resposta->notes ?? '',
                    'prices'        => [],
                ];
            }
            // Map item DB id to current item index
            $itemIndex = $cotacao->items->search(fn($i) => $i->id === $resposta->cotacao_item_id);
            if ($itemIndex !== false) {
                $supplierData[$sid]['prices'][$itemIndex] = (string) $resposta->unit_price;
            }
        }

        $this->cotacaoSuppliers = array_values($supplierData);

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
            'title'                      => 'required|string|max:255',
            'cotacaoItems'               => 'required|array|min:1',
            'cotacaoItems.*.description' => 'required|string|max:255',
            'cotacaoItems.*.quantity'    => 'required|numeric|min:0.001',
        ], [
            'title.required'          => 'O título da cotação é obrigatório.',
            'cotacaoItems.required'   => 'Adicione pelo menos um item.',
            'cotacaoItems.min'        => 'Adicione pelo menos um item.',
        ]);

        DB::transaction(function () {
            $data = [
                'title'        => $this->title,
                'status'       => $this->status ?: CotacaoStatus::Rascunho->value,
                'deadline_date'=> $this->deadline_date ?: null,
                'notes'        => $this->notes ?: null,
            ];

            if ($this->editingId) {
                $cotacao = Cotacao::findOrFail($this->editingId);
                $cotacao->update($data);
                $cotacao->items()->delete(); // cascades respostas
            } else {
                $cotacao = Cotacao::create($data);
            }

            // Save items
            $savedItems = [];
            foreach ($this->cotacaoItems as $item) {
                $savedItems[] = CotacaoItem::create([
                    'cotacao_id'  => $cotacao->id,
                    'product_id'  => $item['product_id'] ?: null,
                    'description' => $item['description'],
                    'sku'         => $item['sku'] ?? null,
                    'unit'        => $item['unit'] ?? 'UN',
                    'quantity'    => (float) ($item['quantity'] ?? 1),
                ]);
            }

            // Save supplier responses
            foreach ($this->cotacaoSuppliers as $supplierRow) {
                $supplierId = $supplierRow['supplier_id'];
                foreach ($savedItems as $idx => $savedItem) {
                    $unitPrice = (float) ($supplierRow['prices'][$idx] ?? 0);
                    CotacaoResposta::create([
                        'cotacao_id'     => $cotacao->id,
                        'cotacao_item_id'=> $savedItem->id,
                        'supplier_id'    => $supplierId,
                        'unit_price'     => $unitPrice,
                        'delivery_days'  => $supplierRow['delivery_days'] ? (int) $supplierRow['delivery_days'] : null,
                        'notes'          => $supplierRow['notes'] ?: null,
                        'selected'       => false,
                    ]);
                }
            }
        });

        session()->flash('message', $this->editingId ? 'Cotação atualizada com sucesso!' : 'Cotação criada com sucesso!');
        $this->closeModal();
        unset($this->cotacoes, $this->stats);
    }

    /* ─────────────────────────────────────────
       ACTIONS
    ───────────────────────────────────────── */
    public function approveCotacao(int $id): void
    {
        Cotacao::findOrFail($id)->update(['status' => CotacaoStatus::Aprovada->value]);
        session()->flash('message', 'Cotação aprovada!');
        $this->closeDetail();
        unset($this->cotacoes, $this->stats);
    }

    public function cancelCotacao(int $id): void
    {
        Cotacao::findOrFail($id)->update(['status' => CotacaoStatus::Cancelada->value]);
        session()->flash('message', 'Cotação cancelada.');
        $this->closeDetail();
        unset($this->cotacoes, $this->stats);
    }

    public function convertToPurchaseOrder(int $id): void
    {
        $cotacao = Cotacao::with(['items.respostas' => fn($q) => $q->where('selected', true)])->findOrFail($id);

        // Determine the primary supplier from the first selected response
        $primarySupplierId = null;
        foreach ($cotacao->items as $item) {
            if ($item->respostas->isNotEmpty()) {
                $primarySupplierId = $item->respostas->first()->supplier_id;
                break;
            }
        }

        if (!$primarySupplierId) {
            session()->flash('error', 'Selecione ao menos um fornecedor nos itens antes de gerar o pedido.');
            return;
        }

        DB::transaction(function () use ($cotacao, $primarySupplierId) {
            $order = PurchaseOrder::create([
                'supplier_id' => $primarySupplierId,
                'buyer_id'    => auth()->id(),
                'status'      => PurchaseOrderStatus::Rascunho->value,
                'origin'      => PurchaseOrderOrigin::Cotacao->value,
                'order_date'  => now(),
                'notes'       => 'Gerado a partir da cotação ' . $cotacao->number,
            ]);

            foreach ($cotacao->items as $item) {
                $resposta = $item->respostas->first();
                if (!$resposta) continue;

                $qty   = (float) $item->quantity;
                $price = (float) $resposta->unit_price;

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
                ]);
            }

            $order->refresh();
            $order->calculateTotals();
            $order->save();

            $cotacao->update([
                'status'           => CotacaoStatus::Convertida->value,
                'purchase_order_id'=> $order->id,
            ]);
        });

        session()->flash('message', 'Pedido de compra gerado com sucesso!');
        $this->closeDetail();
        unset($this->cotacoes, $this->stats);
    }

    public function selectResposta(int $itemId, string $supplierId): void
    {
        CotacaoResposta::where('cotacao_item_id', $itemId)->update(['selected' => false]);
        CotacaoResposta::where('cotacao_item_id', $itemId)
            ->where('supplier_id', $supplierId)
            ->update(['selected' => true]);

        unset($this->viewingCotacao);
    }

    /* ─────────────────────────────────────────
       HELPERS
    ───────────────────────────────────────── */
    public function clearFilters(): void
    {
        $this->search       = '';
        $this->filterStatus = '';
        $this->resetPage();
        unset($this->cotacoes);
    }

    public function updatingSearch(): void       { $this->resetPage(); unset($this->cotacoes); }
    public function updatingFilterStatus(): void { $this->resetPage(); unset($this->cotacoes); }

    private function resetForm(): void
    {
        $this->editingId             = null;
        $this->title                 = '';
        $this->status                = '';
        $this->deadline_date         = '';
        $this->notes                 = '';
        $this->cotacaoItems          = [];
        $this->cotacaoSuppliers      = [];
        $this->searchProduct         = '';
        $this->searchResults         = [];
        $this->searchSupplierTerm    = '';
        $this->supplierSearchResults = [];
        $this->activeTab             = 'geral';
        $this->resetValidation();
    }

    /* ─────────────────────────────────────────
       RENDER
    ───────────────────────────────────────── */
    public function render(): View
    {
        return view('livewire.compras.cotacoes', [
            'cotacoes'  => $this->cotacoes,
            'stats'     => $this->stats,
            'statuses'  => CotacaoStatus::cases(),
        ]);
    }
}


