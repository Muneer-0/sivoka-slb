<?php

namespace App\Policies;

use App\Models\User;
use App\Models\School;
use Illuminate\Auth\Access\HandlesAuthorization;

class SchoolPolicy
{
    use HandlesAuthorization;

    public function before(User $user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    public function viewAny(User $user)
    {
        return $user->isPimpinan() || $user->isAdmin();
    }

    public function view(User $user, School $school)
    {
        return $user->isPimpinan() || 
               ($user->isOperator() && $user->school_id == $school->id);
    }

    public function create(User $user)
    {
        return false; // handled by before (admin)
    }

    public function update(User $user, School $school)
    {
        return false; // handled by before (admin)
    }

    public function delete(User $user, School $school)
    {
        return false; // handled by before (admin)
    }
}