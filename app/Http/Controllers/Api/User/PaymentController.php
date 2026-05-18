<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Midtrans\Snap;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class PaymentController extends Controller
{


    public function create(Request $request, $OrderId)
    {
        $user = $request->user();


        return DB::transaction(function () use ($user, $OrderId) {

            $order = Order::where('id', $OrderId)->with('user')->lockForUpdate()->firstOrFail();

            if ($order->user_id !== $user->id) {
                abort(403, 'Unauthorized');
            }

            if ($order->status !== 'Pending') {
                return response()->json(['message' => 'Order tidak bisa dibayar'], 422);
            }
            $existingPayment = $order->payments()
                ->where('transaction_status', 'Pending')
                ->latest()
                ->first();

            if ($existingPayment && $existingPayment->expires_at > now()) {
                return [
                    'payment_id' => $existingPayment->id,
                    'snap_token' => $existingPayment->snap_token,
                ];
            }

            $midtransOrderId = 'PAY-' . $order->id . '-' . time();

            
            $payment = Payment::create([
                'order_id'       => $order->id,
                'midtrans_order_id' => $midtransOrderId,
                'gross_amount'   => $order->total,
                'transaction_status' => 'Pending',
                'snap_token'     => null, 
                'payload'        => null, 
                'expires_at' => now()->addMinutes(15)
            ]);

         
            $params = [
                'transaction_details' => [
                    'order_id' => $midtransOrderId,
                    'gross_amount' => $order->total,
                ],
                'customer_details' => [
                    'fullname' => $order->shipping_name ?? 'Guest',
                    'email' => $user->email ?? 'guest@exmple.com',
                    'phone' => $user->phone ?? '0857241566',
                ],

            ];

            
            $snapToken = Snap::getSnapToken($params);

      
            $payment->update([
                'snap_token' => $snapToken,
                'payload' => $params
            ]);

            return [
                'payment_id' => $payment->id,
                'snap_token' => $snapToken,
            ];
        });
        
    }


    public function callback(Request $request)
    {


        $serverKey = config('Midtrans.server_key');

        $payload = $request->all();

        Log::info('MIDTRANS CALLBACK', $payload);
        $signature = hash(
            'sha512',
            $payload['order_id'] .
                $payload['status_code'] .
                $payload['gross_amount'] .
                $serverKey
        );


        if (!isset($payload['signature_key']) || $signature !== $payload['signature_key']) {
            return response()->json(['message' => 'Invalid signature'], 403);
        }

        $payment = Payment::where('midtrans_order_id', $payload['order_id'])->firstOrFail();

        
        if (in_array($payment->transaction_status, ['settlement', 'capture'])) {
            return response()->json(['message' => 'Already processed']);
        }

        $transactionStatus = $payload['transaction_status'];
        $paymentType = $payload['payment_type'];
        $fraudStatus = $payload['fraud_status'] ?? null;
        $transactionId = $payload['transaction_id'];


        $payment->update([
            'transaction_id' => $transactionId,
            'payment_type' => $paymentType,
            'transaction_status' => $transactionStatus,
            'fraud_status' => $fraudStatus,
            'payload' => $payload
        ]);

        $order = $payment->order;

        if (in_array($transactionStatus, ['capture', 'settlement'])) {
            if ($order->status !== 'Paid') {
                $order->update([
                    'status' => 'Paid',
                    'estimated_delivery_min' => now()->addDays($order->zones_region->estimasi_min_day),
                    'estimated_delivery_max' => now()->addDays($order->zones_region->estimasi_max_day),
                ]);
            }
        }

        if ($transactionStatus == 'expire') {
            $order->update([
                'status' => 'Expired'
            ]);
        }

        if ($transactionStatus == 'cancel') {
            $order->update([
                'status' => 'Canceled'
            ]);
        }

        return response()->json(['message' => 'Transaksi Succes'], 200);
    }
}
