<?php

namespace App\Livewire\Fiscal\TipoOperacao;

use App\Enums\IcmsModalidadeBC;
use App\Enums\IpiModalidade;
use App\Enums\TipoMovimentoFiscal;
use App\Livewire\Forms\TipoOperacaoFiscalForm;
use App\Models\TipoOperacaoFiscal;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?TipoOperacaoFiscal $operacao = null;

    public TipoOperacaoFiscalForm $form;

    public string $activeTab = 'geral';

    public function mount(?TipoOperacaoFiscal $operacao = null): void
    {
        $this->operacao = $operacao && $operacao->exists ? $operacao : null;

        if ($this->operacao) {
            $this->form->codigo             = $this->operacao->codigo;
            $this->form->descricao          = $this->operacao->descricao;
            $this->form->natureza_operacao  = $this->operacao->natureza_operacao;
            $this->form->tipo_movimento     = $this->operacao->tipo_movimento instanceof TipoMovimentoFiscal
                ? $this->operacao->tipo_movimento->value
                : (string) $this->operacao->tipo_movimento;
            $this->form->is_active          = (bool) $this->operacao->is_active;
            $this->form->cfop               = $this->operacao->cfop;
            $this->form->icms_cst           = $this->operacao->icms_cst;
            $this->form->icms_modalidade_bc = $this->operacao->icms_modalidade_bc !== null
                ? (string) $this->operacao->icms_modalidade_bc
                : null;
            $this->form->icms_aliquota      = $this->operacao->icms_aliquota !== null
                ? (string) $this->operacao->icms_aliquota
                : null;
            $this->form->icms_reducao_bc    = $this->operacao->icms_reducao_bc !== null
                ? (string) $this->operacao->icms_reducao_bc
                : null;
            $this->form->ipi_cst            = $this->operacao->ipi_cst;
            $this->form->ipi_modalidade     = $this->operacao->ipi_modalidade;
            $this->form->ipi_aliquota       = $this->operacao->ipi_aliquota !== null
                ? (string) $this->operacao->ipi_aliquota
                : null;
            $this->form->pis_cst            = $this->operacao->pis_cst;
            $this->form->pis_aliquota       = $this->operacao->pis_aliquota !== null
                ? (string) $this->operacao->pis_aliquota
                : null;
            $this->form->cofins_cst         = $this->operacao->cofins_cst;
            $this->form->cofins_aliquota    = $this->operacao->cofins_aliquota !== null
                ? (string) $this->operacao->cofins_aliquota
                : null;
            $this->form->observacoes        = $this->operacao->observacoes;
        }
    }

    public function save(): mixed
    {
        // Validate unique codigo
        $this->form->validateUnique($this->operacao?->id);
        $this->form->validate();

        $payload = [
            'codigo'             => strtoupper(trim($this->form->codigo)),
            'descricao'          => $this->form->descricao,
            'natureza_operacao'  => $this->form->natureza_operacao,
            'tipo_movimento'     => $this->form->tipo_movimento,
            'is_active'          => $this->form->is_active,
            'cfop'               => $this->form->cfop ?: null,
            'icms_cst'           => $this->form->icms_cst ?: null,
            'icms_modalidade_bc' => $this->form->icms_modalidade_bc !== null && $this->form->icms_modalidade_bc !== ''
                ? (int) $this->form->icms_modalidade_bc
                : null,
            'icms_aliquota'      => $this->form->icms_aliquota !== null && $this->form->icms_aliquota !== ''
                ? (float) $this->form->icms_aliquota
                : null,
            'icms_reducao_bc'    => $this->form->icms_reducao_bc !== null && $this->form->icms_reducao_bc !== ''
                ? (float) $this->form->icms_reducao_bc
                : null,
            'ipi_cst'            => $this->form->ipi_cst ?: null,
            'ipi_modalidade'     => $this->form->ipi_modalidade ?: null,
            'ipi_aliquota'       => $this->form->ipi_aliquota !== null && $this->form->ipi_aliquota !== ''
                ? (float) $this->form->ipi_aliquota
                : null,
            'pis_cst'            => $this->form->pis_cst ?: null,
            'pis_aliquota'       => $this->form->pis_aliquota !== null && $this->form->pis_aliquota !== ''
                ? (float) $this->form->pis_aliquota
                : null,
            'cofins_cst'         => $this->form->cofins_cst ?: null,
            'cofins_aliquota'    => $this->form->cofins_aliquota !== null && $this->form->cofins_aliquota !== ''
                ? (float) $this->form->cofins_aliquota
                : null,
            'observacoes'        => $this->form->observacoes ?: null,
        ];

        if ($this->operacao) {
            $this->operacao->update($payload);

            return redirect()->route('fiscal.tipo-operacao.index')
                ->with('success', 'Tipo de operação atualizado com sucesso!');
        }

        TipoOperacaoFiscal::create($payload);

        return redirect()->route('fiscal.tipo-operacao.index')
            ->with('success', 'Tipo de operação criado com sucesso!');
    }

    public function render()
    {
        $title = $this->operacao ? 'Editar Tipo de Operação' : 'Novo Tipo de Operação';

        return view('livewire.fiscal.tipo-operacao.form', [
            'isEditing'       => (bool) $this->operacao,
            'movimentos'      => TipoMovimentoFiscal::cases(),
            'ipiModalidades'  => IpiModalidade::cases(),
            'icmsModalidades' => IcmsModalidadeBC::cases(),
            'cstIcms'         => $this->getCstIcms(),
            'cstIpi'          => $this->getCstIpi(),
            'cstPisCofins'    => $this->getCstPisCofins(),
        ])->title($title);
    }

    private function getCstIcms(): array
    {
        return [
            // Tributação Integral (Regime Normal)
            '00' => '00 – Tributada Integralmente',
            '10' => '10 – Tributada c/ Cobrança do ICMS por Substituição Tributária',
            '20' => '20 – Com Redução da Base de Cálculo',
            '30' => '30 – Isenta ou Não Tributada e com Cobrança do ICMS por ST',
            '40' => '40 – Isenta',
            '41' => '41 – Não Tributada',
            '50' => '50 – Suspensão',
            '51' => '51 – Diferimento',
            '60' => '60 – ICMS Cobrado Anteriormente por Substituição Tributária',
            '70' => '70 – Com Redução da Base de Cálculo e Cobrança do ICMS por ST',
            '90' => '90 – Outras',
            // Simples Nacional (CSOSN)
            '101' => '101 – Tributada pelo Simples Nacional c/ Permissão de Crédito',
            '102' => '102 – Tributada pelo Simples Nacional s/ Permissão de Crédito',
            '103' => '103 – Isenção do ICMS no Simples Nacional p/ Faixa de Receita Bruta',
            '201' => '201 – Tributada pelo SN c/ Permissão de Crédito e com ICMS ST',
            '202' => '202 – Tributada pelo SN s/ Permissão de Crédito e com ICMS ST',
            '203' => '203 – Isenção do ICMS no SN p/ Faixa de Receita Bruta e com ST',
            '300' => '300 – Imune',
            '400' => '400 – Não Tributada pelo Simples Nacional',
            '500' => '500 – ICMS Cobrado Anteriormente por ST ou Antecipação',
            '900' => '900 – Outros (Simples Nacional)',
        ];
    }

    private function getCstIpi(): array
    {
        return [
            // Entradas
            '00' => '00 – Entrada com Recuperação de Crédito',
            '01' => '01 – Entrada Tributada com Alíquota Zero',
            '02' => '02 – Entrada Isenta',
            '03' => '03 – Entrada Não Tributada',
            '04' => '04 – Entrada Imune',
            '05' => '05 – Entrada com Suspensão',
            '49' => '49 – Outras Entradas',
            // Saídas
            '50' => '50 – Saída Tributada',
            '51' => '51 – Saída Tributável com Alíquota Zero',
            '52' => '52 – Saída Isenta',
            '53' => '53 – Saída Não Tributada',
            '54' => '54 – Saída Imune',
            '55' => '55 – Saída com Suspensão',
            '99' => '99 – Outras Saídas',
        ];
    }

    private function getCstPisCofins(): array
    {
        return [
            '01' => '01 – Operação Tributável (base de cálculo = valor da operação alíquota normal)',
            '02' => '02 – Operação Tributável (base de cálculo = valor da operação alíquota diferenciada)',
            '03' => '03 – Operação Tributável (base de cálculo = quantidade vendida × alíquota por unidade de produto)',
            '04' => '04 – Operação Tributável (tributação monofásica – alíquota zero)',
            '05' => '05 – Operação Tributável (Substituição Tributária)',
            '06' => '06 – Operação Tributável (alíquota zero)',
            '07' => '07 – Operação Isenta da Contribuição',
            '08' => '08 – Operação sem Incidência da Contribuição',
            '09' => '09 – Operação com Suspensão da Contribuição',
            '49' => '49 – Outras Operações de Saída',
            '50' => '50 – Operação com Direito a Crédito (vinculada a receita tributada)',
            '51' => '51 – Operação com Direito a Crédito (vinculada a receita não tributada)',
            '52' => '52 – Operação com Direito a Crédito (vinculada a receita de exportação)',
            '53' => '53 – Operação com Direito a Crédito (vinculada a receitas tributadas e não-tributadas)',
            '54' => '54 – Operação com Direito a Crédito (vinculada a receitas tributadas e de exportação)',
            '55' => '55 – Operação com Direito a Crédito (vinculada a receitas não-tributadas e de exportação)',
            '56' => '56 – Operação com Direito a Crédito (vinculada a receitas tributadas, não tributadas e exportação)',
            '60' => '60 – Crédito Presumido (operação de aquisição vinculada a receita tributada)',
            '61' => '61 – Crédito Presumido (operação de aquisição vinculada a receita não tributada)',
            '62' => '62 – Crédito Presumido (operação de aquisição vinculada a receita de exportação)',
            '63' => '63 – Crédito Presumido (operação de aquisição vinculada a receitas tributadas e não-tributadas)',
            '64' => '64 – Crédito Presumido (operação de aquisição vinculada a receitas tributadas e de exportação)',
            '65' => '65 – Crédito Presumido (operação de aquisição vinculada a receitas não-tributadas e de exportação)',
            '66' => '66 – Crédito Presumido (operação de aquisição vinculada a receitas tributadas, não-tributadas e exportação)',
            '67' => '67 – Crédito Presumido (outras operações)',
            '70' => '70 – Operação de Aquisição sem Direito a Crédito',
            '71' => '71 – Operação de Aquisição com Isenção',
            '72' => '72 – Operação de Aquisição com Suspensão',
            '73' => '73 – Operação de Aquisição a Alíquota Zero',
            '74' => '74 – Operação de Aquisição sem Incidência da Contribuição',
            '75' => '75 – Operação de Aquisição por Substituição Tributária',
            '98' => '98 – Outras Operações de Entrada',
            '99' => '99 – Outras Operações',
        ];
    }
}

