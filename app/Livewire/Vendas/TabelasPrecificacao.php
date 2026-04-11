<?php

namespace App\Livewire\Vendas;

use App\Models\PriceTable;
use App\Models\PriceTableItem;
use App\Models\Product;
use App\Services\PricingService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Tabelas de Precificação')]
class TabelasPrecificacao extends Component
{
    use WithPagination;

    /* ─── Filters ─── */
    public string $search = '';
    public string $filterActive = '';

    /* ─── Selected Price Table for Operations ─── */
    public ?int $working_price_table_id = null;

    /* ─── Calculator Modal ─── */
    public bool $showCalculator = false;

    /* ─── Price Table Modal ─── */
    public bool $showTableModal = false;
    public ?int $editingTableId = null;
    public string $table_name = '';
    public string $table_code = '';
    public string $table_description = '';
    public bool $table_is_active = true;
    public bool $table_is_default = false;
    public string $table_valid_from = '';
    public string $table_valid_until = '';

    /* ─── Product Selection ─── */
    public ?string $selected_product_id = null;
    public string $searchProductCalc = '';
    public array $searchProductResults = [];

    /* ─── Calculator Fields ─── */
    public string $custo_materia_prima = '100';
    public string $despesas = '10';
    public string $imposto = '18';
    public string $comissao = '5';
    public string $frete = '3';
    public string $prazo = '2';
    public string $vpc = '1';
    public string $assistencia = '1';
    public string $inadimplencia = '0.5';
    public string $lucro = '20';

    public array $resultado = [];
    public ?array $selectedProductData = null;


    /* ─────────────────────────────────────────
       COMPUTED
    ───────────────────────────────────────── */
    #[Computed]
    public function tables()
    {
        return PriceTable::withCount('items')
            ->when($this->search, fn($q) => $q->where('name', 'like', '%' . $this->search . '%')
                                                ->orWhere('code', 'like', '%' . $this->search . '%'))
            ->when($this->filterActive !== '', fn($q) => $q->where('is_active', (bool) $this->filterActive))
            ->latest()
            ->paginate(10);
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'total' => PriceTable::count(),
            'active' => PriceTable::where('is_active', true)->count(),
            'products_with_price' => PriceTableItem::distinct('product_id')->count('product_id'),
        ];
    }

    #[Computed]
    public function activePriceTables()
    {
        return PriceTable::where('is_active', true)
            ->orderBy('is_default', 'desc')
            ->orderBy('name')
            ->get(['id', 'name', 'code', 'is_default']);
    }

    #[Computed]
    public function workingPriceTable(): ?PriceTable
    {
        if (!$this->working_price_table_id) return null;
        return PriceTable::find($this->working_price_table_id);
    }

    #[Computed]
    public function priceTableItems()
    {
        if (!$this->working_price_table_id) return collect();

        return PriceTableItem::where('price_table_id', $this->working_price_table_id)
            ->with('product')
            ->whereNull('quantity_from') // Apenas preços base
            ->orderBy('updated_at', 'desc')
            ->paginate(15, ['*'], 'itemsPage');
    }

    /* ─────────────────────────────────────────
       CALCULATOR
    ───────────────────────────────────────── */
    public function openCalculator(): void
    {
        $this->showCalculator = true;
        $this->calculate();
    }

    public function closeCalculator(): void
    {
        $this->showCalculator = false;
        $this->resetCalculatorForm();
    }

    /* ─────────────────────────────────────────
       PRODUCT SEARCH IN CALCULATOR
    ───────────────────────────────────────── */
    public function updatedSearchProductCalc(): void
    {
        if (strlen($this->searchProductCalc) >= 2) {
            $this->searchProductResults = Product::where(function ($q) {
                    $q->where('name', 'like', '%' . $this->searchProductCalc . '%')
                      ->orWhere('product_code', 'like', '%' . $this->searchProductCalc . '%')
                      ->orWhere('ean', 'like', '%' . $this->searchProductCalc . '%');
                })
                ->limit(8)
                ->get(['id', 'name', 'product_code', 'ean', 'cost_price', 'sale_price'])
                ->toArray();
        } else {
            $this->searchProductResults = [];
        }
    }

    public function selectProduct(string $productId): void
    {
        $product = Product::find($productId);
        if (!$product) return;

        $this->selected_product_id = $product->id;
        $this->selectedProductData = [
            'id'           => $product->id,
            'name'         => $product->name,
            'product_code' => $product->product_code,
            'cost_price'   => (float) ($product->cost_price ?? 0),
            'sale_price'   => (float) ($product->sale_price ?? 0),
        ];

        // Carregar custo do produto automaticamente
        $this->custo_materia_prima = (string) ($product->cost_price ?? 100);

        $this->searchProductCalc = '';
        $this->searchProductResults = [];
        $this->calculate();
    }

    public function clearSelectedProduct(): void
    {
        $this->selected_product_id = null;
        $this->selectedProductData = null;
        $this->searchProductCalc = '';
        $this->searchProductResults = [];
    }

    public function updateProductPrice(): void
    {
        if (!$this->selected_product_id || empty($this->resultado['preco_final'])) {
            session()->flash('error', 'Selecione um produto e calcule o preço primeiro.');
            return;
        }

        $product = Product::find($this->selected_product_id);
        if (!$product) {
            session()->flash('error', 'Produto não encontrado.');
            return;
        }

        $product->update([
            'sale_price' => $this->resultado['preco_final'],
        ]);

        // Atualizar dados locais
        $this->selectedProductData['sale_price'] = $this->resultado['preco_final'];

        session()->flash('message', 'Preço do produto atualizado com sucesso!');
        unset($this->tables, $this->stats);
    }

    public function saveToPriceTable(): void
    {
        if (!$this->selected_product_id || empty($this->resultado['preco_final'])) {
            session()->flash('error', 'Selecione um produto e calcule o preço primeiro.');
            return;
        }

        if (!$this->working_price_table_id) {
            session()->flash('error', 'Selecione uma tabela de preços na página principal.');
            return;
        }

        $priceTable = PriceTable::find($this->working_price_table_id);
        if (!$priceTable) {
            session()->flash('error', 'Tabela de preços não encontrada.');
            return;
        }

        // Buscar ou criar item na tabela de preços
        $priceTableItem = PriceTableItem::updateOrCreate(
            [
                'price_table_id' => $this->working_price_table_id,
                'product_id'     => $this->selected_product_id,
                'quantity_from'  => null, // Preço base sem quantidade mínima
            ],
            [
                'price'         => $this->resultado['preco_final'],
                'minimum_price' => $this->resultado['preco_minimo'] ?? 0,
            ]
        );

        session()->flash('message', "Preço salvo na tabela '{$priceTable->name}' com sucesso!");
        unset($this->tables, $this->stats, $this->priceTableItems);
    }

    public function calculate(): void
    {
        $pricingService = app(PricingService::class);

        $this->resultado = $pricingService->calculateFinalPrice([
            'custo_materia_prima' => $this->custo_materia_prima,
            'despesas'            => $this->despesas,
            'imposto'             => $this->imposto,
            'comissao'            => $this->comissao,
            'frete'               => $this->frete,
            'prazo'               => $this->prazo,
            'vpc'                 => $this->vpc,
            'assistencia'         => $this->assistencia,
            'inadimplencia'       => $this->inadimplencia,
            'lucro'               => $this->lucro,
        ]);
    }

    public function updatedCustoMateriaPrima(): void { $this->calculate(); }
    public function updatedDespesas(): void { $this->calculate(); }
    public function updatedImposto(): void { $this->calculate(); }
    public function updatedComissao(): void { $this->calculate(); }
    public function updatedFrete(): void { $this->calculate(); }
    public function updatedPrazo(): void { $this->calculate(); }
    public function updatedVpc(): void { $this->calculate(); }
    public function updatedAssistencia(): void { $this->calculate(); }
    public function updatedInadimplencia(): void { $this->calculate(); }
    public function updatedLucro(): void { $this->calculate(); }

    public function resetCalculator(): void
    {
        $this->resetCalculatorForm();
        $this->calculate();
    }

    private function resetCalculatorForm(): void
    {
        $this->selected_product_id = null;
        $this->selectedProductData = null;
        $this->searchProductCalc = '';
        $this->searchProductResults = [];
        $this->custo_materia_prima = '100';
        $this->despesas = '10';
        $this->imposto = '18';
        $this->comissao = '5';
        $this->frete = '3';
        $this->prazo = '2';
        $this->vpc = '1';
        $this->assistencia = '1';
        $this->inadimplencia = '0.5';
        $this->lucro = '20';
    }

    /* ─────────────────────────────────────────
       TOGGLE ACTIVE
    ───────────────────────────────────────── */
    public function toggleActive(int $id): void
    {
        $table = PriceTable::findOrFail($id);
        $table->update(['is_active' => !$table->is_active]);
        unset($this->tables, $this->stats);
        session()->flash('message', 'Status atualizado com sucesso!');
    }

    /* ─────────────────────────────────────────
       PRICE TABLE CRUD
    ───────────────────────────────────────── */
    public function openTableModal(): void
    {
        $this->resetTableForm();
        $this->showTableModal = true;
    }

    public function closeTableModal(): void
    {
        $this->showTableModal = false;
        $this->resetTableForm();
    }

    public function editTable(int $id): void
    {
        $table = PriceTable::findOrFail($id);

        $this->editingTableId = $table->id;
        $this->table_name = $table->name;
        $this->table_code = $table->code;
        $this->table_description = $table->description ?? '';
        $this->table_is_active = $table->is_active;
        $this->table_is_default = $table->is_default;
        $this->table_valid_from = $table->valid_from?->format('Y-m-d') ?? '';
        $this->table_valid_until = $table->valid_until?->format('Y-m-d') ?? '';

        $this->showTableModal = true;
    }

    public function saveTable(): void
    {
        $this->validate([
            'table_name' => 'required|string|max:255',
            'table_code' => 'required|string|max:50|unique:price_tables,code,' . ($this->editingTableId ?? 'NULL'),
        ], [
            'table_name.required' => 'O nome da tabela é obrigatório.',
            'table_code.required' => 'O código da tabela é obrigatório.',
            'table_code.unique' => 'Este código já está em uso.',
        ]);

        $data = [
            'name' => $this->table_name,
            'code' => $this->table_code,
            'description' => $this->table_description ?: null,
            'is_active' => $this->table_is_active,
            'is_default' => $this->table_is_default,
            'valid_from' => $this->table_valid_from ?: null,
            'valid_until' => $this->table_valid_until ?: null,
        ];

        // Se marcar como padrão, desmarcar outras
        if ($this->table_is_default) {
            PriceTable::where('is_default', true)->update(['is_default' => false]);
        }

        if ($this->editingTableId) {
            PriceTable::findOrFail($this->editingTableId)->update($data);
            session()->flash('message', 'Tabela de preços atualizada com sucesso!');
        } else {
            PriceTable::create($data);
            session()->flash('message', 'Tabela de preços criada com sucesso!');
        }

        $this->closeTableModal();
        unset($this->tables, $this->stats, $this->activePriceTables);
    }

    public function deleteTable(int $id): void
    {
        $table = PriceTable::findOrFail($id);

        // Verificar se tem produtos
        $itemCount = $table->items()->count();
        if ($itemCount > 0) {
            session()->flash('error', "Não é possível excluir. Esta tabela possui {$itemCount} produto(s).");
            return;
        }

        $table->delete();
        session()->flash('message', 'Tabela de preços excluída com sucesso!');
        unset($this->tables, $this->stats);
    }

    private function resetTableForm(): void
    {
        $this->editingTableId = null;
        $this->table_name = '';
        $this->table_code = '';
        $this->table_description = '';
        $this->table_is_active = true;
        $this->table_is_default = false;
        $this->table_valid_from = '';
        $this->table_valid_until = '';
        $this->resetValidation();
    }

    /* ─────────────────────────────────────────
       HELPERS
    ───────────────────────────────────────── */
    public function clearFilters(): void
    {
        $this->search = '';
        $this->filterActive = '';
        $this->resetPage();
        unset($this->tables);
    }

    public function updatingSearch(): void { $this->resetPage(); unset($this->tables); }
    public function updatingFilterActive(): void { $this->resetPage(); unset($this->tables); }

    /* ─────────────────────────────────────────
       RENDER
    ───────────────────────────────────────── */
    public function render(): View
    {
        return view('livewire.vendas.tabelas-precificacao', [
            'tables'             => $this->tables,
            'stats'              => $this->stats,
            'workingPriceTable'  => $this->workingPriceTable,
            'priceTableItems'    => $this->priceTableItems,
        ]);
    }
}

