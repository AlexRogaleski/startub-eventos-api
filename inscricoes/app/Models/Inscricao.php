<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inscricao extends Model
{
    protected $table = 'inscricoes';
    
    protected $fillable = ['user_id', 'nome', 'email', 'cpf', 'status',
        'asaas_cliente_id', 'asaas_cobranca_id', 'link_pagamento', 'pago_em',                    
    ];

    protected function casts(): array
    {
        return ['pago_em' => 'datetime:Y-m-d H:i:s'];
    }
    
    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }
}
