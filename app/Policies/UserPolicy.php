<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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
        return false; // hanya admin yang boleh lihat daftar user
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model)
    {
        return false; // hanya admin yang boleh lihat detail user
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return false; // handled by before (admin)
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model)
    {
        return false; // handled by before (admin)
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model)
    {
        return false; // handled by before (admin)
    }
}