<?php

namespace App\Livewire\Forms;

use Livewire\Attributes\Validate;
use Livewire\Form;

class GrupoTributarioForm extends Form
{
    #[Validate('required|string|max:20')]
    public string $codigo = '';

    #[Validate('required|string|max:150')]
    public string $nome = '';

    #[Validate('nullable|string')]
    public ?string $descricao = null;

    #[Validate('required|in:simples_nacional,lucro_presumido,lucro_real,todos')]
    public string $regime_tributario = 'todos';

    #[Validate('nullable|string|size:8')]
    public ?string $ncm = null;

    #[Validate('nullable|integer|exists:tipo_operacoes_fiscais,id')]
    public ?string $tipo_operacao_saida_id = null;

    #[Validate('nullable|integer|exists:tipo_operacoes_fiscais,id')]
    public ?string $tipo_operacao_entrada_id = null;

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

    #[Validate('boolean')]
    public bool $is_active = true;

    public function validateUnique(?int $ignoreId = null): void
    {
        $rule = 'unique:grupo_tributarios,codigo';
        if ($ignoreId) {
            $rule .= ',' . $ignoreId;
        }
        $this->validate(['codigo' => ['required', 'string', 'max:20', $rule]]);
    }
}

