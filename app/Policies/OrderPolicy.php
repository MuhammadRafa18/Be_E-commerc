<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
     public function viewAny(User $user): bool
    {
        return in_array($user->role, ['admin', 'super_admin']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Order $order): bool
    {
        return $order->user_id === $user->id;
        
     
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
       return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user,Order $order): bool
    {


        if (in_array($user->role,['admin','super_admin'])) {
            return true;
        }



        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Order $order): bool
    {
            return $user->role === 'user' && $order->user_id === $user->id;
    }
    public function confirm(User $user, Order $order): bool
    {
         return $order->user_id === $user->id && $user->role === 'user';
      
    }
}
