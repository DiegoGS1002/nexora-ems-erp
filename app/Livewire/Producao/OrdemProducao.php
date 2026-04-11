<?php

namespace App\Livewire\Producao;

use App\Enums\ProductionOrderStatus;
use App\Models\Product;
use App\Models\ProductionItem;
use App\Models\ProductionOrder;
use App\Models\ProductionOrderProduct;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class OrdemProducao extends Component
{
    /* ─── FILTROS ─────────────────────────────────── */
    public string $search       = '';
    public string $filterStatus = '';

    /* ─── MODAL FORM ──────────────────────────────── */
    public bool  $showModal  = false;
    public bool  $showDetail = false;
    public ?int  $editingId  = null;
    public ?int  $viewingId  = null;

    /* ─── CAMPOS DO FORMULÁRIO ───────────────────── */
    public string $name           = '';
    public string $start_date     = '';
    public string $end_date       = '';
    public string $estimated_cost = '';
    public string $lot_number     = '';
    public string $notes          = '';

    /* ─── PRODUTOS DA OP (multi-produto) ─────────── */
    public array $formProducts = [];

    /* ─── INSUMOS / BOM ───────────────────────────── */
    public array $formItems = [];

    /* ─── MODAL PROGRESSO / PAUSA ─────────────────── */
    public bool  $showProgressModal = false;
    public ?int  $progressOrderId   = null;
    public bool  $pauseAfterSave    = false;
    public array $progressQtys      = [];

    #[Computed]
    public function orders()
    {
        $query = ProductionOrder::with(['orderProducts.product', 'user'])->latest();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('orderProducts.product', fn ($p) => $p->where('name', 'like', '%' . $this->search . '%'))
                  ->orWhere('lot_number', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        return $query->get();
    }

    #[Computed]
    public function stats(): array
    {
        $counts = ProductionOrder::selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        $totalCost = ProductionOrder::whereIn('status', [
            ProductionOrderStatus::Planned->value,
            ProductionOrderStatus::InProgress->value,
            ProductionOrderStatus::Paused->value,
        ])->sum('estimated_cost');

        return [
            'total'       => array_sum($counts),
            'planned'     => $counts[ProductionOrderStatus::Planned->value]    ?? 0,
            'in_progress' => $counts[ProductionOrderStatus::InProgress->value] ?? 0,
            'completed'   => $counts[ProductionOrderStatus::Completed->value]  ?? 0,
            'total_cost'  => $totalCost,
        ];
    }

    #[Computed]
    public function products()
    {
        return Product::orderBy('name')->get();
    }

    #[Computed]
    public function viewingOrder(): ?ProductionOrder
    {
        if (! $this->viewingId) return null;
        return ProductionOrder::with(['user', 'items.component', 'orderProducts.product'])->find($this->viewingId);
    }

    #[Computed]
    public function progressOrder(): ?ProductionOrder
    {
        if (! $this->progressOrderId) return null;
        return ProductionOrder::with(['orderProducts.product'])->find($this->progressOrderId);
    }

    /* ─── MODAL FORM ──────────────────────────────── */
    public function openModal(): void
    {
        $this->resetForm();
        $this->formProducts = [['product_id' => '', 'target_quantity' => '']];
        $this->showModal    = true;
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
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->viewingId  = null;
        unset($this->viewingOrder);
    }

    public function edit(int $id): void
    {
        $order = ProductionOrder::with(['items', 'orderProducts'])->findOrFail($id);

        $this->editingId      = $order->id;
        $this->name           = $order->name ?? '';
        $this->start_date     = $order->start_date?->format('Y-m-d\TH:i') ?? '';
        $this->end_date       = $order->end_date?->format('Y-m-d\TH:i') ?? '';
        $this->estimated_cost = (string) $order->estimated_cost;
        $this->lot_number     = $order->lot_number ?? '';
        $this->notes          = $order->notes ?? '';

        $this->formProducts = $order->orderProducts->map(fn ($op) => [
            'product_id'      => $op->product_id,
            'target_quantity' => (string) $op->target_quantity,
        ])->toArray();

        if (empty($this->formProducts)) {
            $this->formProducts = [[
                'product_id'      => $order->product_id ?? '',
                'target_quantity' => (string) $order->target_quantity,
            ]];
        }

        $this->formItems = $order->items->map(fn ($item) => [
            'component_id' => $item->component_id,
            'required_qty' => (string) $item->required_qty,
        ])->toArray();

        $this->showDetail = false;
        $this->viewingId  = null;
        $this->showModal  = true;
    }

    /* ─── PRODUTOS (FORM) ─────────────────────────── */
    public function addFormProduct(): void
    {
        $this->formProducts[] = ['product_id' => '', 'target_quantity' => ''];
    }

    public function removeFormProduct(int $index): void
    {
        if (count($this->formProducts) > 1) {
            array_splice($this->formProducts, $index, 1);
        }
    }

    /* ─── BOM ITEMS ───────────────────────────────── */
    public function addFormItem(): void
    {
        $this->formItems[] = ['component_id' => '', 'required_qty' => ''];
    }

    public function removeFormItem(int $index): void
    {
        array_splice($this->formItems, $index, 1);
    }

    /* ─── SAVE ────────────────────────────────────── */
    public function save(): void
    {
        $this->validate([
            'formProducts'                     => 'required|array|min:1',
            'formProducts.*.product_id'        => 'required|exists:products,id',
            'formProducts.*.target_quantity'   => 'required|numeric|min:0.001',
            'start_date'                       => 'nullable|date',
            'end_date'                         => 'nullable|date|after_or_equal:start_date',
            'estimated_cost'                   => 'nullable|numeric|min:0',
            'lot_number'                       => 'nullable|string|max:100',
            'formItems.*.component_id'         => 'required_with:formItems|exists:products,id',
            'formItems.*.required_qty'         => 'required_with:formItems|numeric|min:0.001',
        ], [
            'formProducts.required'                   => 'Adicione pelo menos um produto.',
            'formProducts.min'                        => 'Adicione pelo menos um produto.',
            'formProducts.*.product_id.required'      => 'Selecione o produto.',
            'formProducts.*.target_quantity.required' => 'Informe a quantidade planejada.',
            'formProducts.*.target_quantity.min'      => 'A quantidade deve ser maior que zero.',
            'formItems.*.component_id.required_with'  => 'Selecione o componente.',
            'formItems.*.required_qty.required_with'  => 'Informe a quantidade do componente.',
        ]);

        DB::transaction(function () {
            $data = [
                'name'           => $this->name ?: null,
                'start_date'     => $this->start_date ?: null,
                'end_date'       => $this->end_date ?: null,
                'estimated_cost' => $this->estimated_cost ?: 0,
                'lot_number'     => $this->lot_number ?: null,
                'notes'          => $this->notes ?: null,
                'user_id'        => auth()->id(),
            ];

            if ($this->editingId) {
                $order = ProductionOrder::findOrFail($this->editingId);
                $order->update($data);
                $order->orderProducts()->delete();
                $order->items()->delete();
            } else {
                $data['status'] = ProductionOrderStatus::Planned->value;
                $order          = ProductionOrder::create($data);
            }

            foreach ($this->formProducts as $product) {
                if (!empty($product['product_id']) && !empty($product['target_quantity'])) {
                    ProductionOrderProduct::create([
                        'production_order_id' => $order->id,
                        'product_id'          => $product['product_id'],
                        'target_quantity'     => $product['target_quantity'],
                    ]);
                }
            }

            foreach ($this->formItems as $item) {
                if (!empty($item['component_id']) && !empty($item['required_qty'])) {
                    ProductionItem::create([
                        'production_order_id' => $order->id,
                        'component_id'        => $item['component_id'],
                        'required_qty'        => $item['required_qty'],
                    ]);
                }
            }
        });

        session()->flash('message', $this->editingId ? 'Ordem de Produção atualizada!' : 'Ordem de Produção criada!');
        $this->closeModal();
        unset($this->orders, $this->stats);
    }

    /* ─── MODAL PROGRESSO / PAUSA ─────────────────── */
    public function openProgressModal(int $orderId, bool $pause = false): void
    {
        $order = ProductionOrder::with('orderProducts')->findOrFail($orderId);

        $this->progressOrderId = $orderId;
        $this->pauseAfterSave  = $pause;
        $this->progressQtys    = $order->orderProducts
            ->mapWithKeys(fn ($op) => [$op->id => (string) floatval($op->produced_quantity)])
            ->toArray();

        unset($this->progressOrder);
        $this->showProgressModal = true;
    }

    public function closeProgressModal(): void
    {
        $this->showProgressModal = false;
        $this->progressOrderId   = null;
        $this->pauseAfterSave    = false;
        $this->progressQtys      = [];
        unset($this->progressOrder);
    }

    public function saveProgress(): void
    {
        $rules    = [];
        $messages = [];

        foreach ($this->progressQtys as $id => $qty) {
            $rules["progressQtys.{$id}"] = $this->pauseAfterSave
                ? 'required|numeric|min:0'
                : 'nullable|numeric|min:0';
            $messages["progressQtys.{$id}.required"] = 'Informe a quantidade produzida.';
            $messages["progressQtys.{$id}.numeric"]  = 'Valor inválido.';
            $messages["progressQtys.{$id}.min"]      = 'A quantidade não pode ser negativa.';
        }

        if ($this->pauseAfterSave && empty($this->progressQtys)) {
            $this->addError('progressQtys', 'Nenhum produto encontrado nesta OP.');
            return;
        }

        $this->validate($rules, $messages);

        DB::transaction(function () {
            foreach ($this->progressQtys as $productId => $qty) {
                ProductionOrderProduct::where('id', $productId)
                    ->update(['produced_quantity' => (float) str_replace(',', '.', $qty ?: 0)]);
            }

            if ($this->pauseAfterSave) {
                ProductionOrder::findOrFail($this->progressOrderId)
                    ->update(['status' => ProductionOrderStatus::Paused->value]);
            }
        });

        session()->flash('message', $this->pauseAfterSave
            ? 'Produção pausada e progresso salvo!'
            : 'Progresso registrado com sucesso!');

        $this->closeProgressModal();
        unset($this->orders, $this->stats);
    }

    /* ─── CHANGE STATUS ────────────────────────────── */
    public function changeStatus(int $id, string $status): void
    {
        $order     = ProductionOrder::findOrFail($id);
        $newStatus = ProductionOrderStatus::from($status);

        $data = ['status' => $newStatus->value];

        if ($newStatus === ProductionOrderStatus::InProgress && ! $order->start_date) {
            $data['start_date'] = now();
        }

        if ($newStatus === ProductionOrderStatus::Completed) {
            $data['end_date'] = now();
            $order->orderProducts()->each(fn ($op) => $op->update(['produced_quantity' => $op->target_quantity]));
        }

        $order->update($data);
        session()->flash('message', 'Status alterado para: ' . $newStatus->label());
        unset($this->orders, $this->stats);
    }

    /* ─── DELETE ───────────────────────────────────── */
    public function delete(int $id): void
    {
        ProductionOrder::findOrFail($id)->delete();
        session()->flash('message', 'Ordem de Produção excluída.');
        unset($this->orders, $this->stats);
    }

    /* ─── FILTER RESET ─────────────────────────────── */
    public function updatingSearch(): void       { unset($this->orders); }
    public function updatingFilterStatus(): void  { unset($this->orders); }

    public function clearFilters(): void
    {
        $this->search       = '';
        $this->filterStatus = '';
        unset($this->orders);
    }

    private function resetForm(): void
    {
        $this->editingId      = null;
        $this->name           = '';
        $this->start_date     = '';
        $this->end_date       = '';
        $this->estimated_cost = '';
        $this->lot_number     = '';
        $this->notes          = '';
        $this->formProducts   = [];
        $this->formItems      = [];
        $this->resetValidation();
    }

    public function render(): View
    {
        return view('livewire.producao.ordem-producao', [
            'orders'   => $this->orders,
            'stats'    => $this->stats,
            'products' => $this->products,
            'statuses' => ProductionOrderStatus::cases(),
        ]);
    }
}

