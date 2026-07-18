<?php

namespace App\Policies;

use App\Models\User;

class ProductPolicy
{
     

  
    public function create(User $user): bool
    {

        if (in_array($user->role, ['admin', 'super_admin'])) {
            return true;
        }



        return false;
    }

   
    public function update(User $user): bool
    {


        if (in_array($user->role, ['admin', 'super_admin'])) {
            return true;
        }



        return false;
    }

   
    public function delete(User $user): bool
    {
        if (in_array($user->role, ['admin', 'super_admin'])) {
            return true;
        }



        return false;
    }
}
