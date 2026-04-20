<?php

namespace App\Models;

use App\Enums\StatusTicketSuporte;
use App\Enums\PrioridadeTicketSuporte;
use App\Models\MensagemSuporte;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class TicketSuporte extends Model
{
    protected $table = 'tickets_suporte';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'user_id',
        'assunto',
        'status',
        'prioridade',
        'categoria',
        'fechado_em',
    ];

    protected $casts = [
        'status'     => StatusTicketSuporte::class,
        'prioridade' => PrioridadeTicketSuporte::class,
        'fechado_em' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $ticket) {
            if (empty($ticket->id)) {
                $ticket->id = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function mensagens(): HasMany
    {
        return $this->hasMany(MensagemSuporte::class, 'ticket_id');
    }

    public function ultimaMensagem(): HasOne
    {
        return $this->hasOne(MensagemSuporte::class, 'ticket_id')->latestOfMany();
    }

    public function isAberto(): bool
    {
        return $this->status !== StatusTicketSuporte::Fechado && $this->status !== StatusTicketSuporte::Resolvido;
    }
}



