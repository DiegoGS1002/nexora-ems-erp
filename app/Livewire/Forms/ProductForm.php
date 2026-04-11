<?php

namespace App\Livewire\Forms;

use App\Enums\NaturezaProduto;
use App\Enums\TipoProduto;
use Livewire\Attributes\Validate;
use Livewire\Form;

class ProductForm extends Form
{
    // ── Informações Básicas ──────────────────────────
    public string $product_code    = '';

    #[Validate('required|string|min:3|max:120')]
    public string $name            = '';

    #[Validate('nullable|string|size:13')]
    public ?string $ean            = null;

    // ── Tributação ───────────────────────────────────
    #[Validate('nullable|string|size:8')]
    public ?string $ncm            = null;

    #[Validate('nullable|string|size:4')]
    public ?string $cfop_saida     = null;

    #[Validate('nullable|string|size:4')]
    public ?string $cfop_entrada   = null;

    #[Validate('nullable|integer|exists:grupo_tributarios,id')]
    public ?string $grupo_tributario_id = null;

    #[Validate('nullable|string|max:200')]
    public ?string $short_description = null;

    #[Validate('required|string')]
    public string $category        = '';

    #[Validate('nullable|string|max:100')]
    public ?string $brand          = null;

    #[Validate('required|string')]
    public string $unit_of_measure = 'UN';

    #[Validate('required|string')]
    public string $product_type    = 'produto_fisico';

    #[Validate('required|string')]
    public string $nature          = 'mercadoria_revenda';

    #[Validate('nullable|string|max:100')]
    public ?string $product_line   = null;

    // ── Dimensões e Peso ─────────────────────────────
    #[Validate('nullable|numeric|min:0')]
    public ?string $weight_net     = null;

    #[Validate('nullable|numeric|min:0')]
    public ?string $weight_gross   = null;

    #[Validate('nullable|numeric|min:0')]
    public ?string $height         = null;

    #[Validate('nullable|numeric|min:0')]
    public ?string $width          = null;

    #[Validate('nullable|numeric|min:0')]
    public ?string $depth          = null;

    // ── Descrição ────────────────────────────────────
    #[Validate('nullable|string')]
    public ?string $description    = null;

    #[Validate('nullable|string')]
    public ?string $full_description = null;

    // ── Status ───────────────────────────────────────
    #[Validate('boolean')]
    public bool $is_active         = true;

    // ── Destaques / Tags ─────────────────────────────
    /** @var array<int, string> */
    public array $highlights       = [];

    /** @var array<int, string> */
    public array $tags             = [];

    // ── Preços e Custos ──────────────────────────────
    #[Validate('nullable|numeric|min:0')]
    public ?string $sale_price     = null;

    #[Validate('nullable|numeric|min:0')]
    public ?string $cost_price     = null;

    // ── Estoque ──────────────────────────────────────
    #[Validate('nullable|integer|min:0')]
    public ?string $stock          = null;

    #[Validate('nullable|integer|min:0')]
    public ?string $stock_min      = null;

    #[Validate('nullable|date')]
    public ?string $expiration_date = null;
}

