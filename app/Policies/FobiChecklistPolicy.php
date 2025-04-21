<?php

namespace App\Policies;

use App\Models\FobiUser;
use App\Models\FobiChecklist;
use Illuminate\Auth\Access\HandlesAuthorization;

class FobiChecklistPolicy
{
    use HandlesAuthorization;

    /**
     * Determine if user can update the checklist
     */
    public function update(FobiUser $user, FobiChecklist $checklist)
    {
        return $user->id === $checklist->fobi_user_id ||
               in_array($user->level, [3, 4]);
    }

    /**
     * Determine if user can delete the checklist
     */
    public function delete(FobiUser $user, FobiChecklist $checklist)
    {
        return $user->id === $checklist->fobi_user_id ||
               in_array($user->level, [3, 4]);
    }
}
