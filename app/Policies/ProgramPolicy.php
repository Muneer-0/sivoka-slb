<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Program;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProgramPolicy
{
    use HandlesAuthorization;

    /**
     * Admin bisa melakukan apa saja
     */
    public function before(User $user, $ability)
    {
        if ($user->isAdmin()) {
            return true;
        }
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return $user->isPimpinan() || $user->isOperator();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Program $program)
    {
        // Pimpinan bisa lihat semua program
        // Operator hanya bisa lihat program sekolahnya sendiri
        return $user->isPimpinan() || 
               ($user->isOperator() && $user->school_id == $program->school_id);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        // Operator bisa create jika punya school_id
        return $user->isOperator() && $user->school_id != null;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Program $program)
    {
        return $user->isOperator() && $user->school_id == $program->school_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Program $program)
    {
        return $user->isOperator() && $user->school_id == $program->school_id;
    }
}