<?php

namespace App\Livewire\Fiscal\TipoOperacao;

use App\Enums\TipoMovimentoFiscal;
use App\Models\TipoOperacaoFiscal;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Tipos de Operação Fiscal')]
class Index extends Component
{
    use WithPagination;

    #[Url(as: 'search')]
    public string $search = '';

    #[Url(as: 'movimento')]
    public string $filterMovimento = '';

    #[Url(as: 'cfop')]
    public string $filterCfop = '';

    public function updatedSearch(): void    { $this->resetPage(); }
    public function updatedFilterMovimento(): void { $this->resetPage(); }
    public function updatedFilterCfop(): void { $this->resetPage(); }

    public function delete(int $id): void
    {
        $op = TipoOperacaoFiscal::find($id);

        if (!$op) {
            return;
        }

        $op->delete();

        session()->flash('success', 'Tipo de operação excluído com sucesso!');
        $this->resetPage();
    }

    public function render()
    {
        $operacoes = TipoOperacaoFiscal::query()
            ->when($this->search !== '', function ($q) {
                $q->where(function ($q2) {
                    $q2->where('codigo', 'like', "%{$this->search}%")
                       ->orWhere('descricao', 'like', "%{$this->search}%")
                       ->orWhere('natureza_operacao', 'like', "%{$this->search}%")
                       ->orWhere('cfop', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterMovimento !== '', fn($q) => $q->where('tipo_movimento', $this->filterMovimento))
            ->when($this->filterCfop !== '', fn($q) => $q->where('cfop', 'like', "%{$this->filterCfop}%"))
            ->orderBy('codigo')
            ->paginate(15);

        return view('livewire.fiscal.tipo-operacao.index', [
            'operacoes'  => $operacoes,
            'movimentos' => TipoMovimentoFiscal::cases(),
        ]);
    }
}

