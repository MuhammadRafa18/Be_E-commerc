<?php

namespace App\Policies;

use App\Models\User;

class PaymentPolicy
{
   
    public function create(User $user): bool
    {
        return $user->role === 'user';
    }

   
   
}
