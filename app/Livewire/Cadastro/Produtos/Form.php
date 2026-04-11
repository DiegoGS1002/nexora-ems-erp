<?php

namespace App\Livewire\Cadastro\Produtos;

use App\Enums\NaturezaProduto;
use App\Enums\TipoProduto;
use App\Livewire\Forms\ProductForm;
use App\Models\GrupoTributario;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Supplier;
use App\Models\UnitOfMeasure;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Livewire\WithFileUploads;

#[Layout('layouts.app')]
class Form extends Component
{
    use WithFileUploads;

    public ?Product $product = null;

    public ProductForm $form;

    public string $activeTab = 'dados-gerais';

    public ?TemporaryUploadedFile $image = null;

    /** @var array<int, string> */
    public array $supplier_ids = [];

    public string $highlightInput = '';
    public string $tagInput       = '';

    // Modal properties for inline creation
    public bool $showCategoryModal = false;
    public string $newCategoryName = '';
    public string $newCategoryDescription = '';
    public string $newCategoryColor = '#3B82F6';

    public bool $showUnitModal = false;
    public string $newUnitName = '';
    public string $newUnitAbbreviation = '';
    public string $newUnitDescription = '';

    public function mount(?Product $product = null): void
    {
        $this->product = $product && $product->exists ? $product->load('suppliers') : null;

        if ($this->product) {
            $this->form->product_code      = $this->product->product_code ?? '';
            $this->form->name              = $this->product->name;
            $this->form->ean               = $this->product->ean;
            $this->form->ncm               = $this->product->ncm;
            $this->form->cfop_saida        = $this->product->cfop_saida;
            $this->form->cfop_entrada      = $this->product->cfop_entrada;
            $this->form->grupo_tributario_id = $this->product->grupo_tributario_id
                ? (string) $this->product->grupo_tributario_id : null;
            $this->form->short_description = $this->product->short_description;
            $this->form->category          = $this->product->category;
            $this->form->brand             = $this->product->brand;
            $this->form->unit_of_measure   = $this->product->unit_of_measure;
            $this->form->product_type      = $this->product->product_type instanceof TipoProduto
                ? $this->product->product_type->value
                : (string) $this->product->product_type;
            $this->form->nature            = $this->product->nature instanceof NaturezaProduto
                ? $this->product->nature->value
                : (string) $this->product->nature;
            $this->form->product_line      = $this->product->product_line;
            $this->form->weight_net        = (string) ($this->product->weight_net ?? '');
            $this->form->weight_gross      = (string) ($this->product->weight_gross ?? '');
            $this->form->height            = (string) ($this->product->height ?? '');
            $this->form->width             = (string) ($this->product->width ?? '');
            $this->form->depth             = (string) ($this->product->depth ?? '');
            $this->form->description       = $this->product->description;
            $this->form->full_description  = $this->product->full_description;
            $this->form->is_active         = (bool) ($this->product->is_active ?? true);
            $this->form->highlights        = $this->product->highlights ?? [];
            $this->form->tags              = $this->product->tags ?? [];
            $this->form->sale_price        = (string) ($this->product->sale_price ?? '');
            $this->form->cost_price        = (string) ($this->product->cost_price ?? '');
            $this->form->stock             = (string) ($this->product->stock ?? '');
            $this->form->stock_min         = (string) ($this->product->stock_min ?? '');
            $this->form->expiration_date   = $this->product->expiration_date?->format('Y-m-d') ?? '';
            $this->supplier_ids            = $this->product->suppliers->pluck('id')->all();
        }
    }

    public function addHighlight(): void
    {
        $value = trim($this->highlightInput);
        if ($value !== '' && ! in_array($value, $this->form->highlights)) {
            $this->form->highlights[] = $value;
        }
        $this->highlightInput = '';
    }

    public function removeHighlight(int $index): void
    {
        array_splice($this->form->highlights, $index, 1);
        $this->form->highlights = array_values($this->form->highlights);
    }

    public function addTag(): void
    {
        $value = strtolower(trim($this->tagInput));
        if ($value !== '' && ! in_array($value, $this->form->tags)) {
            $this->form->tags[] = $value;
        }
        $this->tagInput = '';
    }

    public function removeTag(int $index): void
    {
        array_splice($this->form->tags, $index, 1);
        $this->form->tags = array_values($this->form->tags);
    }

    // Category Modal Methods
    public function openCategoryModal(): void
    {
        $this->showCategoryModal = true;
        $this->newCategoryName = '';
        $this->newCategoryDescription = '';
        $this->newCategoryColor = '#3B82F6';
    }

    public function closeCategoryModal(): void
    {
        $this->showCategoryModal = false;
        $this->resetErrorBag(['newCategoryName']);
    }

    public function saveCategory(): void
    {
        $this->validate([
            'newCategoryName' => 'required|string|max:100|unique:product_categories,name',
        ], [
            'newCategoryName.required' => 'O nome da categoria é obrigatório.',
            'newCategoryName.unique' => 'Esta categoria já existe.',
        ]);

        $category = ProductCategory::create([
            'name' => $this->newCategoryName,
            'slug' => Str::slug($this->newCategoryName),
            'description' => $this->newCategoryDescription,
            'color' => $this->newCategoryColor,
            'is_active' => true,
        ]);

        $this->form->category = (string) $category->id;
        $this->showCategoryModal = false;

        session()->flash('success', 'Categoria criada com sucesso!');
    }

    // Unit Modal Methods
    public function openUnitModal(): void
    {
        $this->showUnitModal = true;
        $this->newUnitName = '';
        $this->newUnitAbbreviation = '';
        $this->newUnitDescription = '';
    }

    public function closeUnitModal(): void
    {
        $this->showUnitModal = false;
        $this->resetErrorBag(['newUnitName', 'newUnitAbbreviation']);
    }

    public function saveUnit(): void
    {
        $this->validate([
            'newUnitName' => 'required|string|max:50|unique:unit_of_measures,name',
            'newUnitAbbreviation' => 'required|string|max:10|unique:unit_of_measures,abbreviation',
        ], [
            'newUnitName.required' => 'O nome da unidade é obrigatório.',
            'newUnitName.unique' => 'Esta unidade já existe.',
            'newUnitAbbreviation.required' => 'A sigla é obrigatória.',
            'newUnitAbbreviation.unique' => 'Esta sigla já está em uso.',
        ]);

        $unit = UnitOfMeasure::create([
            'name' => $this->newUnitName,
            'abbreviation' => strtoupper($this->newUnitAbbreviation),
            'description' => $this->newUnitDescription,
            'is_active' => true,
        ]);

        $this->form->unit_of_measure = (string) $unit->id;
        $this->showUnitModal = false;

        session()->flash('success', 'Unidade de medida criada com sucesso!');
    }

    public function save(): mixed
    {
        $this->form->validate();

        $payload = $this->buildPayload();

        if ($this->product) {
            $this->product->update($payload);

            if ($this->image) {
                if ($this->product->image) {
                    Storage::disk('public')->delete($this->product->image);
                }
                $this->product->update(['image' => $this->image->store('products', 'public')]);
            }

            $this->product->suppliers()->sync($this->supplier_ids);

            return redirect()->route('products.index')
                ->with('success', 'Produto atualizado com sucesso!');
        }

        if ($this->image) {
            $payload['image'] = $this->image->store('products', 'public');
        }

        $product = Product::create($payload);
        $product->suppliers()->sync($this->supplier_ids);

        return redirect()->route('products.index')
            ->with('success', 'Produto cadastrado com sucesso!');
    }

    public function saveAndNew(): void
    {
        $this->form->validate();

        $payload = $this->buildPayload();

        if ($this->image) {
            $payload['image'] = $this->image->store('products', 'public');
        }

        $product = Product::create($payload);
        $product->suppliers()->sync($this->supplier_ids);

        $this->form->reset();
        $this->image        = null;
        $this->supplier_ids = [];
        $this->activeTab    = 'dados-gerais';

        session()->flash('success', 'Produto salvo! Cadastre o próximo.');
    }

    private function buildPayload(): array
    {
        return [
            'name'              => $this->form->name,
            'ean'               => $this->form->ean ?: null,
            'ncm'               => $this->form->ncm ? preg_replace('/\D/', '', $this->form->ncm) : null,
            'cfop_saida'        => $this->form->cfop_saida ?: null,
            'cfop_entrada'      => $this->form->cfop_entrada ?: null,
            'grupo_tributario_id' => $this->form->grupo_tributario_id ?: null,
            'description'       => $this->form->description,
            'short_description' => $this->form->short_description,
            'brand'             => $this->form->brand,
            'product_type'      => $this->form->product_type,
            'nature'            => $this->form->nature,
            'product_line'      => $this->form->product_line,
            'unit_of_measure'   => $this->form->unit_of_measure,
            'category'          => $this->form->category,
            'sale_price'        => $this->form->sale_price ?: null,
            'cost_price'        => $this->form->cost_price ?: null,
            'stock'             => $this->form->stock ?: 0,
            'stock_min'         => $this->form->stock_min ?: 0,
            'expiration_date'   => $this->form->expiration_date ?: null,
            'weight_net'        => $this->form->weight_net ?: null,
            'weight_gross'      => $this->form->weight_gross ?: null,
            'height'            => $this->form->height ?: null,
            'width'             => $this->form->width ?: null,
            'depth'             => $this->form->depth ?: null,
            'full_description'  => $this->form->full_description,
            'is_active'         => $this->form->is_active,
            'highlights'        => $this->form->highlights ?: null,
            'tags'              => $this->form->tags ?: null,
        ];
    }

    public function render()
    {
        $title = $this->product ? 'Editar Produto' : 'Novo Produto';

        return view('livewire.cadastro.produtos.form', [
            'isEditing'        => (bool) $this->product,
            'suppliers'        => Supplier::orderBy('social_name')->get(),
            'categories'       => ProductCategory::where('is_active', true)->orderBy('name')->get(),
            'units'            => UnitOfMeasure::where('is_active', true)->orderBy('name')->get(),
            'gruposTributarios' => GrupoTributario::where('is_active', true)->orderBy('codigo')->get(['id','codigo','nome','ncm']),
            'tipoProdutos'     => TipoProduto::cases(),
            'naturezas'        => NaturezaProduto::cases(),
        ])->title($title);
    }
}


