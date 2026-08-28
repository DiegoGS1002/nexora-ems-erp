<?php

namespace App\Policies;

use App\Models\FiscalNote;
use App\Models\User;

class FiscalNotePolicy
{
    public function transmit(User $user, FiscalNote $note): bool
    {
        return $this->authorized($user, $note);
    }

    public function cancel(User $user, FiscalNote $note): bool
    {
        return $this->authorized($user, $note);
    }

    public function correct(User $user, FiscalNote $note): bool
    {
        return $this->authorized($user, $note);
    }

    protected function authorized(User $user, FiscalNote $note): bool
    {
        return ($user->is_admin || in_array('fiscal', $user->modules ?? [], true))
            && ($user->company_id === null || $user->company_id === $note->company_id);
    }
}
