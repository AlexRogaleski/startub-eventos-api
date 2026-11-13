<?php

namespace App\Policies;

use App\Models\Inscricao;
use App\Models\User;

class InscricaoPolicy
{
    public function view(User $user, Inscricao $inscricao): bool
    {
        return (int) $inscricao->user_id === $user->id;
    }
}
