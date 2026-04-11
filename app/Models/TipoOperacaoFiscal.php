<?php

namespace App\Models;

use App\Enums\TipoMovimentoFiscal;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;

class TipoOperacaoFiscal extends Model
{
    use SoftDeletes;

    protected $table = 'tipo_operacoes_fiscais';

    protected $fillable = [
        'codigo',
        'descricao',
        'natureza_operacao',
        'tipo_movimento',
        'is_active',
        'cfop',
        'icms_cst',
        'icms_modalidade_bc',
        'icms_aliquota',
        'icms_reducao_bc',
        'ipi_cst',
        'ipi_modalidade',
        'ipi_aliquota',
        'pis_cst',
        'pis_aliquota',
        'cofins_cst',
        'cofins_aliquota',
        'observacoes',
    ];

    protected $casts = [
        'tipo_movimento'    => TipoMovimentoFiscal::class,
        'is_active'         => 'boolean',
        'icms_aliquota'     => 'decimal:2',
        'icms_reducao_bc'   => 'decimal:2',
        'ipi_aliquota'      => 'decimal:2',
        'pis_aliquota'      => 'decimal:2',
        'cofins_aliquota'   => 'decimal:2',
        'icms_modalidade_bc' => 'integer',
    ];

    /* ── Scopes ──────────────────────────────────────── */

    public function scopeAtivos(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeEntrada(Builder $query): Builder
    {
        return $query->where('tipo_movimento', TipoMovimentoFiscal::Entrada->value);
    }

    public function scopeSaida(Builder $query): Builder
    {
        return $query->where('tipo_movimento', TipoMovimentoFiscal::Saida->value);
    }

    /* ── Helpers ─────────────────────────────────────── */

    public function getCfopDescricaoAttribute(): string
    {
        if (!$this->cfop) return '—';

        $prefix = substr($this->cfop, 0, 1);
        $tipo = match($prefix) {
            '1' => 'Estadual (Entrada)',
            '2' => 'Interestadual (Entrada)',
            '3' => 'Exterior (Entrada)',
            '5' => 'Estadual (Saída)',
            '6' => 'Interestadual (Saída)',
            '7' => 'Exterior (Saída)',
            default => '',
        };

        return $this->cfop . ($tipo ? ' · ' . $tipo : '');
    }

    public function getTipoMovimentoBadgeClassAttribute(): string
    {
        if ($this->tipo_movimento instanceof TipoMovimentoFiscal) {
            return $this->tipo_movimento->badgeClass();
        }
        return 'nx-badge-gray';
    }
}

