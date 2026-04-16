<?php

namespace App\Livewire\Estoque;

use App\Models\StockMovement;
use App\Models\Product;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Movimentação de Estoque')]
class Movimentacao extends Component
{
    use WithPagination;

    /* ─────────────────────────────────────
      PROPRIEDADES DE FILTRO
     ─────────────────────────────────────*/
    public string $search = '';
    public string $filterType = '';
    public string $filterProduct = '';
    public string $startDate = '';
    public string $endDate = '';

    /* ─────────────────────────────────────
      PROPRIEDADES DO FORMULÁRIO
     ─────────────────────────────────────*/
    public bool $showModal = false;
    public ?int $editingId = null;
    public string $product_id = '';
    public string $quantity = '';
    public string $type = 'input';
    public string $origin = '';
    public string $unit_cost = '';
    public string $observation = '';

    /* ─────────────────────────────────────
      LIFECYCLE HOOKS
     ─────────────────────────────────────*/
    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
    }

    /* ─────────────────────────────────────
      COMPUTED - MOVIMENTAÇÕES
     ─────────────────────────────────────*/
    #[Computed]
    public function movements()
    {
        $query = StockMovement::with(['product', 'user'])
            ->whereBetween('created_at', [$this->startDate, $this->endDate]);

        if ($this->search) {
            $query->whereHas('product', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }

        if ($this->filterProduct) {
            $query->where('product_id', $this->filterProduct);
        }

        return $query->orderBy('created_at', 'desc')->paginate(20);
    }

    /* ─────────────────────────────────────
      COMPUTED - ESTATÍSTICAS
     ─────────────────────────────────────*/
    #[Computed]
    public function stats()
    {
        $totalMovements = StockMovement::whereBetween('created_at', [$this->startDate, $this->endDate])->count();
        $totalInputs = StockMovement::where('type', 'input')
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->sum('quantity');
        $totalOutputs = StockMovement::where('type', 'output')
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->sum('quantity');
        $totalAdjustments = StockMovement::where('type', 'adjustment')
            ->whereBetween('created_at', [$this->startDate, $this->endDate])
            ->count();

        return [
            'total_movements' => $totalMovements,
            'total_inputs' => $totalInputs,
            'total_outputs' => $totalOutputs,
            'total_adjustments' => $totalAdjustments,
        ];
    }

    /* ─────────────────────────────────────
      COMPUTED - PRODUTOS
     ─────────────────────────────────────*/
    #[Computed]
    public function products()
    {
        return Product::orderBy('name')->get();
    }

    /* ─────────────────────────────────────
      AÇÕES - RESETAR PÁGINA
     ─────────────────────────────────────*/
    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterType(): void
    {
        $this->resetPage();
    }

    public function updatingFilterProduct(): void
    {
        $this->resetPage();
    }

    /* ─────────────────────────────────────
      AÇÕES - FORMULÁRIO
     ─────────────────────────────────────*/
    public function openModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->editingId = null;
        $this->product_id = '';
        $this->quantity = '';
        $this->type = 'input';
        $this->origin = '';
        $this->unit_cost = '';
        $this->observation = '';
    }

    public function save(): void
    {
        $this->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:0.001',
            'type' => 'required|in:input,output,adjustment,transfer',
            'origin' => 'required|string|max:255',
            'unit_cost' => 'nullable|numeric|min:0',
            'observation' => $this->type === 'adjustment' ? 'required|string' : 'nullable|string',
        ]);

        $data = [
            'product_id' => $this->product_id,
            'user_id' => Auth::id(),
            'quantity' => $this->quantity,
            'type' => $this->type,
            'origin' => $this->origin,
            'unit_cost' => $this->unit_cost ?: null,
            'observation' => $this->observation ?: null,
        ];

        if ($this->editingId) {
            $movement = StockMovement::findOrFail($this->editingId);
            $movement->update($data);
            session()->flash('message', 'Movimentação atualizada com sucesso!');
        } else {
            StockMovement::create($data);

            // Atualizar o estoque do produto
            $product = Product::findOrFail($this->product_id);
            $currentStock = $product->stock ?? 0;

            if ($this->type === 'input' || $this->type === 'adjustment') {
                $product->stock = $currentStock + $this->quantity;
            } elseif ($this->type === 'output') {
                $product->stock = $currentStock - $this->quantity;
            }

            $product->save();

            session()->flash('message', 'Movimentação registrada com sucesso!');
        }

        $this->closeModal();
        $this->resetPage();
    }

    public function edit(int $id): void
    {
        $movement = StockMovement::findOrFail($id);
        $this->editingId = $movement->id;
        $this->product_id = $movement->product_id;
        $this->quantity = $movement->quantity;
        $this->type = $movement->type;
        $this->origin = $movement->origin;
        $this->unit_cost = $movement->unit_cost ?? '';
        $this->observation = $movement->observation ?? '';
        $this->showModal = true;
    }

    public function delete(int $id): void
    {
        $movement = StockMovement::findOrFail($id);

        // Reverter estoque
        $product = Product::findOrFail($movement->product_id);
        $currentStock = $product->stock ?? 0;

        if ($movement->type === 'input' || $movement->type === 'adjustment') {
            $product->stock = $currentStock - $movement->quantity;
        } elseif ($movement->type === 'output') {
            $product->stock = $currentStock + $movement->quantity;
        }

        $product->save();
        $movement->delete();

        session()->flash('message', 'Movimentação excluída com sucesso!');
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->filterType = '';
        $this->filterProduct = '';
        $this->startDate = now()->startOfMonth()->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->resetPage();
    }

    /* ─────────────────────────────────────
      RENDER
     ─────────────────────────────────────*/
    public function render(): View
    {
        return view('livewire.estoque.movimentacao', [
            'movements' => $this->movements,
            'stats' => $this->stats,
            'products' => $this->products,
        ]);
    }
}
