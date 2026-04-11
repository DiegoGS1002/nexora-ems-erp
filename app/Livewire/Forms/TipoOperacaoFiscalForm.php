<?php

namespace App\Livewire\Forms;

use App\Models\TipoOperacaoFiscal;
use Livewire\Attributes\Validate;
use Livewire\Form;

class TipoOperacaoFiscalForm extends Form
{
    // Identificação
    #[Validate('required|string|max:20')]
    public string $codigo = '';

    #[Validate('required|string|max:255')]
    public string $descricao = '';

    #[Validate('nullable|string|max:100')]
    public ?string $natureza_operacao = null;

    #[Validate('required|in:entrada,saida')]
    public string $tipo_movimento = 'saida';

    #[Validate('boolean')]
    public bool $is_active = true;

    // CFOP
    #[Validate('nullable|digits:4')]
    public ?string $cfop = null;

    // ICMS
    #[Validate('nullable|string|max:3')]
    public ?string $icms_cst = null;

    #[Validate('nullable|integer|min:0|max:3')]
    public ?string $icms_modalidade_bc = null;

    #[Validate('nullable|numeric|min:0|max:100')]
    public ?string $icms_aliquota = null;

    #[Validate('nullable|numeric|min:0|max:100')]
    public ?string $icms_reducao_bc = null;

    // IPI
    #[Validate('nullable|string|max:2')]
    public ?string $ipi_cst = null;

    #[Validate('nullable|in:aliquota,pauta,unidade,isento')]
    public ?string $ipi_modalidade = null;

    #[Validate('nullable|numeric|min:0|max:100')]
    public ?string $ipi_aliquota = null;

    // PIS
    #[Validate('nullable|string|max:2')]
    public ?string $pis_cst = null;

    #[Validate('nullable|numeric|min:0|max:100')]
    public ?string $pis_aliquota = null;

    // COFINS
    #[Validate('nullable|string|max:2')]
    public ?string $cofins_cst = null;

    #[Validate('nullable|numeric|min:0|max:100')]
    public ?string $cofins_aliquota = null;

    // Observações
    #[Validate('nullable|string')]
    public ?string $observacoes = null;

    public function validateUnique(?int $ignoreId = null): void
    {
        $rule = 'unique:tipo_operacoes_fiscais,codigo';
        if ($ignoreId) {
            $rule .= ',' . $ignoreId;
        }

        $this->validate([
            'codigo' => ['required', 'string', 'max:20', $rule],
        ]);
    }
}

