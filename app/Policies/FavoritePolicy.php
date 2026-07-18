<?php

namespace App\Policies;

use App\Models\Favorite;
use App\Models\User;

class FavoritePolicy
{
     public function view(User $user, Favorite $favorite): bool
    {
        return $user->role === 'user' && $favorite->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === 'user';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function select(User $user, Favorite $favorite): bool
    {


        return $user->role === 'user' && $favorite->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Favorite $favorite): bool
    {
        return $user->role === 'user' && $favorite->user_id === $user->id;
    }
}
