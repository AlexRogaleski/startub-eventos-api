<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notificacao extends Model
{
    protected $table = 'notificacoes';

    protected $fillable = ['asaas_evento_id', 'tipo', 'payload'];

    protected function casts(): array
    {
        return ['payload' => 'array'];
    }
}
