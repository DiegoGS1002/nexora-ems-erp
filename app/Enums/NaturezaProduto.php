<?php

namespace App\Enums;

enum NaturezaProduto: string
{
    case MercadoriaRevenda  = 'mercadoria_revenda';
    case UsoConsumo         = 'uso_consumo';
    case MateriaPrima       = 'materia_prima';
    case ProdutoAcabado     = 'produto_acabado';
    case Embalagem          = 'embalagem';
    case AtivoImobilizado   = 'ativo_imobilizado';

    public function label(): string
    {
        return match($this) {
            self::MercadoriaRevenda => 'Mercadoria para Revenda',
            self::UsoConsumo        => 'Uso e Consumo',
            self::MateriaPrima      => 'Matéria-Prima',
            self::ProdutoAcabado    => 'Produto Acabado',
            self::Embalagem         => 'Embalagem',
            self::AtivoImobilizado  => 'Ativo Imobilizado',
        };
    }
}

