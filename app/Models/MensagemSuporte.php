<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class MensagemSuporte extends Model
{
    protected $table = 'mensagens_suporte';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'ticket_id',
        'user_id',
        'conteudo',
        'is_suporte',
        'is_ia',
        'lida',
    ];

    protected $casts = [
        'is_suporte' => 'boolean',
        'is_ia'      => 'boolean',
        'lida'       => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $mensagem) {
            if (empty($mensagem->id)) {
                $mensagem->id = (string) Str::uuid();
            }
        });
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(TicketSuporte::class, 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}


