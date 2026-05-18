<?php

namespace App\Services\Order;

use App\Handlers\Order\OrderHandlerInterface;
use App\Models\Order;
use App\Models\User;


class OrderService
{
    protected $handler;

    public function __construct(OrderHandlerInterface $handler)
    {
        $this->handler = $handler;
    }

    public function checkout(User $user, array $data): Order
    {
        return $this->handler->checkout($user, $data);
    }

    public function updateStatus(Order $order, array $data): Order
    {
        return $this->handler->updateStatus($order, $data);
    }
}