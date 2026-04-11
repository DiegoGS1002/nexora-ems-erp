<?php

namespace App\Livewire\Fiscal\GrupoTributario;

use App\Enums\IcmsModalidadeBC;
use App\Enums\IpiModalidade;
use App\Enums\RegimeTributario;
use App\Livewire\Forms\GrupoTributarioForm;
use App\Models\GrupoTributario;
use App\Models\TipoOperacaoFiscal;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Form extends Component
{
    public ?GrupoTributario $grupo = null;
    public GrupoTributarioForm $form;
    public string $activeTab = 'geral';

    public function mount(?GrupoTributario $grupo = null): void
    {
        $this->grupo = $grupo && $grupo->exists ? $grupo : null;

        if ($this->grupo) {
            $this->form->codigo                   = $this->grupo->codigo;
            $this->form->nome                     = $this->grupo->nome;
            $this->form->descricao                = $this->grupo->descricao;
            $this->form->regime_tributario        = $this->grupo->regime_tributario instanceof RegimeTributario
                ? $this->grupo->regime_tributario->value
                : (string) $this->grupo->regime_tributario;
            $this->form->ncm                      = $this->grupo->ncm;
            $this->form->tipo_operacao_saida_id   = $this->grupo->tipo_operacao_saida_id
                ? (string) $this->grupo->tipo_operacao_saida_id : null;
            $this->form->tipo_operacao_entrada_id = $this->grupo->tipo_operacao_entrada_id
                ? (string) $this->grupo->tipo_operacao_entrada_id : null;
            $this->form->icms_cst           = $this->grupo->icms_cst;
            $this->form->icms_modalidade_bc = $this->grupo->icms_modalidade_bc !== null
                ? (string) $this->grupo->icms_modalidade_bc : null;
            $this->form->icms_aliquota      = $this->grupo->icms_aliquota !== null
                ? (string) $this->grupo->icms_aliquota : null;
            $this->form->icms_reducao_bc    = $this->grupo->icms_reducao_bc !== null
                ? (string) $this->grupo->icms_reducao_bc : null;
            $this->form->ipi_cst            = $this->grupo->ipi_cst;
            $this->form->ipi_modalidade     = $this->grupo->ipi_modalidade;
            $this->form->ipi_aliquota       = $this->grupo->ipi_aliquota !== null
                ? (string) $this->grupo->ipi_aliquota : null;
            $this->form->pis_cst            = $this->grupo->pis_cst;
            $this->form->pis_aliquota       = $this->grupo->pis_aliquota !== null
                ? (string) $this->grupo->pis_aliquota : null;
            $this->form->cofins_cst         = $this->grupo->cofins_cst;
            $this->form->cofins_aliquota    = $this->grupo->cofins_aliquota !== null
                ? (string) $this->grupo->cofins_aliquota : null;
            $this->form->is_active          = (bool) $this->grupo->is_active;
        }
    }

    public function save(): mixed
    {
        $this->form->validateUnique($this->grupo?->id);
        $this->form->validate();

        $payload = [
            'codigo'                   => strtoupper(trim($this->form->codigo)),
            'nome'                     => $this->form->nome,
            'descricao'                => $this->form->descricao ?: null,
            'regime_tributario'        => $this->form->regime_tributario,
            'ncm'                      => $this->form->ncm ? preg_replace('/\D/', '', $this->form->ncm) : null,
            'tipo_operacao_saida_id'   => $this->form->tipo_operacao_saida_id ?: null,
            'tipo_operacao_entrada_id' => $this->form->tipo_operacao_entrada_id ?: null,
            'icms_cst'           => $this->form->icms_cst ?: null,
            'icms_modalidade_bc' => $this->form->icms_modalidade_bc !== null && $this->form->icms_modalidade_bc !== ''
                ? (int) $this->form->icms_modalidade_bc : null,
            'icms_aliquota'      => $this->form->icms_aliquota !== '' && $this->form->icms_aliquota !== null
                ? (float) $this->form->icms_aliquota : null,
            'icms_reducao_bc'    => $this->form->icms_reducao_bc !== '' && $this->form->icms_reducao_bc !== null
                ? (float) $this->form->icms_reducao_bc : null,
            'ipi_cst'            => $this->form->ipi_cst ?: null,
            'ipi_modalidade'     => $this->form->ipi_modalidade ?: null,
            'ipi_aliquota'       => $this->form->ipi_aliquota !== '' && $this->form->ipi_aliquota !== null
                ? (float) $this->form->ipi_aliquota : null,
            'pis_cst'            => $this->form->pis_cst ?: null,
            'pis_aliquota'       => $this->form->pis_aliquota !== '' && $this->form->pis_aliquota !== null
                ? (float) $this->form->pis_aliquota : null,
            'cofins_cst'         => $this->form->cofins_cst ?: null,
            'cofins_aliquota'    => $this->form->cofins_aliquota !== '' && $this->form->cofins_aliquota !== null
                ? (float) $this->form->cofins_aliquota : null,
            'is_active'          => $this->form->is_active,
        ];

        if ($this->grupo) {
            $this->grupo->update($payload);
            return redirect()->route('fiscal.grupo-tributario.index')
                ->with('success', 'Grupo tributário atualizado com sucesso!');
        }

        GrupoTributario::create($payload);
        return redirect()->route('fiscal.grupo-tributario.index')
            ->with('success', 'Grupo tributário criado com sucesso!');
    }

    public function render()
    {
        $title = $this->grupo ? 'Editar Grupo Tributário' : 'Novo Grupo Tributário';

        $tiposOperacaoSaida   = TipoOperacaoFiscal::where('is_active', true)->where('tipo_movimento', 'saida')->orderBy('codigo')->get(['id','codigo','descricao','cfop']);
        $tiposOperacaoEntrada = TipoOperacaoFiscal::where('is_active', true)->where('tipo_movimento', 'entrada')->orderBy('codigo')->get(['id','codigo','descricao','cfop']);

        return view('livewire.fiscal.grupo-tributario.form', [
            'isEditing'            => (bool) $this->grupo,
            'regimes'              => RegimeTributario::cases(),
            'ipiModalidades'       => IpiModalidade::cases(),
            'icmsModalidades'      => IcmsModalidadeBC::cases(),
            'tiposOperacaoSaida'   => $tiposOperacaoSaida,
            'tiposOperacaoEntrada' => $tiposOperacaoEntrada,
            'cstIcms'              => $this->getCstIcms(),
            'cstIpi'               => $this->getCstIpi(),
            'cstPisCofins'         => $this->getCstPisCofins(),
        ])->title($title);
    }

    private function getCstIcms(): array
    {
        return [
            '00' => '00 – Tributada Integralmente',
            '10' => '10 – Tributada c/ ICMS ST',
            '20' => '20 – Com Redução da Base de Cálculo',
            '30' => '30 – Isenta/Não Tributada c/ ST',
            '40' => '40 – Isenta',
            '41' => '41 – Não Tributada',
            '50' => '50 – Suspensão',
            '51' => '51 – Diferimento',
            '60' => '60 – ICMS Cobrado por ST',
            '70' => '70 – Redução BC c/ ST',
            '90' => '90 – Outras',
            // CSOSN Simples Nacional
            '101' => '101 – SN c/ Permissão de Crédito',
            '102' => '102 – SN s/ Permissão de Crédito',
            '103' => '103 – Isenção no SN p/ Faixa RB',
            '201' => '201 – SN c/ Crédito e ST',
            '202' => '202 – SN s/ Crédito e ST',
            '203' => '203 – Isenção SN c/ ST',
            '300' => '300 – Imune',
            '400' => '400 – Não Tributada pelo SN',
            '500' => '500 – ICMS Cobrado por ST (SN)',
            '900' => '900 – Outros (SN)',
        ];
    }

    private function getCstIpi(): array
    {
        return [
            '00' => '00 – Entrada c/ Recuperação de Crédito',
            '49' => '49 – Outras Entradas',
            '50' => '50 – Saída Tributada',
            '52' => '52 – Saída Isenta',
            '53' => '53 – Saída Não Tributada',
            '54' => '54 – Saída Imune',
            '55' => '55 – Saída c/ Suspensão',
            '99' => '99 – Outras Saídas',
        ];
    }

    private function getCstPisCofins(): array
    {
        return [
            '01' => '01 – Tributável (alíquota normal)',
            '02' => '02 – Tributável (alíquota diferenciada)',
            '06' => '06 – Tributável (alíquota zero)',
            '07' => '07 – Isento',
            '08' => '08 – Sem Incidência',
            '09' => '09 – Suspensão',
            '49' => '49 – Outras Saídas',
            '50' => '50 – Aquisição c/ Crédito (receita trib.)',
            '70' => '70 – Aquisição s/ Crédito',
            '99' => '99 – Outras Operações',
        ];
    }
}

