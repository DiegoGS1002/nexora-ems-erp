<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Documento RAG armazenado com embedding vetorial (MySQL 9.x VECTOR nativo).
 *
 * @property int         $id
 * @property string      $titulo
 * @property string      $conteudo
 * @property string      $fonte
 * @property string|null $categoria
 * @property array|null  $metadados
 * @property int         $tokens
 */
class RagDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'conteudo',
        'fonte',
        'categoria',
        'metadados',
        'tokens',
    ];

    protected $casts = [
        'metadados' => 'array',
        'tokens'    => 'integer',
    ];

    // A coluna 'embedding' é VECTOR(1536) — manipulada via raw SQL pelo RagService.
    // Não está em $fillable para evitar problemas com o Eloquent.
}

