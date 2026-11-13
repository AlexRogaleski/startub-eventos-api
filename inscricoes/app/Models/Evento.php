<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evento extends Model
{
    protected $fillable = ['titulo', 'descricao', 'local', 'data', 'vagas', 'valor'];
    
    protected function casts(): array
    {
        return [
            'data' => 'datetime:Y-m-d H:i',
            'vagas' => 'integer',
            'valor' => 'decimal:2',
        ];
    }
}
