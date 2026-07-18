<?php

namespace App\Policies;

use App\Models\Cart;
use App\Models\User;

class CartPolicy
{
  

    
    public function view(User $user): bool
    {
        return $user->role === 'user';
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
    public function select(User $user, Cart $cart): bool
    {


        return $user->role === 'user' && $cart->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Cart $cart): bool
    {
        return $user->role === 'user' && $cart->user_id === $user->id;
    }
   
}
