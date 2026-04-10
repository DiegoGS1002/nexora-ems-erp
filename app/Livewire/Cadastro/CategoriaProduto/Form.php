<?php

namespace App\Livewire\Cadastro\CategoriaProduto;

use App\Livewire\Forms\ProductCategoryForm;
use App\Models\ProductCategory;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?ProductCategory $category = null;

    public ProductCategoryForm $form;

    public function mount(?ProductCategory $category = null): void
    {
        $this->category = $category && $category->exists ? $category : null;

        if ($this->category) {
            $this->form->name        = $this->category->name;
            $this->form->description = $this->category->description;
            $this->form->color       = $this->category->color;
            $this->form->is_active   = (bool) $this->category->is_active;
        }
    }

    public function save(): mixed
    {
        $this->form->validate();

        $payload = [
            'name'        => $this->form->name,
            'slug'        => Str::slug($this->form->name),
            'description' => $this->form->description,
            'color'       => $this->form->color,
            'is_active'   => $this->form->is_active,
        ];

        if ($this->category) {
            $this->category->update($payload);

            return redirect()->route('product-categories.index')
                ->with('success', 'Categoria atualizada com sucesso!');
        }

        ProductCategory::create($payload);

        return redirect()->route('product-categories.index')
            ->with('success', 'Categoria criada com sucesso!');
    }

    public function render()
    {
        $title = $this->category ? 'Editar Categoria' : 'Nova Categoria';

        return view('livewire.cadastro.categoria-produto.form', [
            'isEditing' => (bool) $this->category,
        ])->title($title);
    }
}

