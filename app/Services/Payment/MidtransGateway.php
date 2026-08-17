<?php

namespace App\Services\Payment;

use App\Models\Order;
use App\Models\Payment;
use App\Models\User;
use Midtrans\Snap;

class MidtransGateway
{
    public function createSnapToken(
        Payment $payment,
        Order $order,
        User $user
    ): string {

        $params = [

            'transaction_details' => [
                'order_id' => $payment->midtrans_order_id,
                'gross_amount' => $order->total,
            ],
            'customer_details' => [
                'fullname' => $order->shipping_name ?? 'Guest',
                'email' => $user->email ?? 'guest@exmple.com',
                'phone' => $user->phone ?? '0857241566',
            ],
        ];
        try {
            $token = Snap::getSnapToken($params);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Midtrans Error',
                'error' => $e->getMessage(),
                'params' => $params,
            ], 500);
        }



        $payment->update([
            'payload' => $params
        ]);

        return $token;
    }
}
