<?php

namespace App\Handlers\Order;

use App\Models\Order;
use App\Models\User;

interface OrderHandlerInterface
{
    public function checkout(User $user, array $data): Order;

    public function updateStatus(Order $order, array $data): Order;

}