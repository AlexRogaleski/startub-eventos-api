<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inscricao extends Model
{
    protected $table = 'inscricoes';
    
    protected $fillable = ['user_id', 'nome', 'email', 'cpf', 'status'];
    
    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class);
    }
}
