<?php

namespace App\Policies;

use App\Models\Evento;
use App\Models\User;

class EventoPolicy
{
    public function update(User $user, Evento $evento): bool
    {
        return (int) $evento->user_id === $user->id;
    }

    public function delete(User $user, Evento $evento): bool
    {
        return $this->update($user, $evento);
    }

    public function viewInscricoes(User $user, Evento $evento): bool
    {
        return $this->update($user, $evento);
    }
}
