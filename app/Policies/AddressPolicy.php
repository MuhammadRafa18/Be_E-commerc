<?php

namespace App\Policies;

use App\Models\Addres;
use App\Models\User;

class AddressPolicy
{
       public function viewAny(User $user): bool
    {
         return $user->role === 'user';
    }

    
       public function view(User $user, Addres $addres): bool
    {
         return $user->role === 'user' && $addres->user_id === $user->id;
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
    public function update(User $user,Addres $addres): bool
    {
        return $user->role === 'user' && $addres->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Addres $addres): bool
    {
            return $user->role === 'user' && $addres->user_id === $user->id;
    }
}
