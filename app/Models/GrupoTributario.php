<?php

namespace App\Models;

use App\Enums\RegimeTributario;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class GrupoTributario extends Model
{
    use SoftDeletes;

    protected $table = 'grupo_tributarios';

    protected $fillable = [
        'codigo',
        'nome',
        'descricao',
        'regime_tributario',
        'ncm',
        'tipo_operacao_saida_id',
        'tipo_operacao_entrada_id',
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
        'is_active',
    ];

    protected $casts = [
        'regime_tributario'  => RegimeTributario::class,
        'is_active'          => 'boolean',
        'icms_aliquota'      => 'decimal:2',
        'icms_reducao_bc'    => 'decimal:2',
        'ipi_aliquota'       => 'decimal:2',
        'pis_aliquota'       => 'decimal:2',
        'cofins_aliquota'    => 'decimal:2',
        'icms_modalidade_bc' => 'integer',
    ];

    /* ── Relationships ───────────────────────────────── */

    public function tipoOperacaoSaida(): BelongsTo
    {
        return $this->belongsTo(TipoOperacaoFiscal::class, 'tipo_operacao_saida_id');
    }

    public function tipoOperacaoEntrada(): BelongsTo
    {
        return $this->belongsTo(TipoOperacaoFiscal::class, 'tipo_operacao_entrada_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'grupo_tributario_id');
    }

    /* ── Scopes ──────────────────────────────────────── */

    public function scopeAtivos(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /* ── Helpers ─────────────────────────────────────── */

    public function getDisplayNomeAttribute(): string
    {
        return "[{$this->codigo}] {$this->nome}";
    }

    public function getNcmFormatadoAttribute(): string
    {
        if (!$this->ncm || strlen($this->ncm) < 8) return $this->ncm ?? '—';
        // Format: 0000.00.00
        return substr($this->ncm, 0, 4) . '.' . substr($this->ncm, 4, 2) . '.' . substr($this->ncm, 6, 2);
    }
}

