<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function inscricoes(): HasMany
    {
        return $this->hasMany(Inscricao::class);
    }
}
