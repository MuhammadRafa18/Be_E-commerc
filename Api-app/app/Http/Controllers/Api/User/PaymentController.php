<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Midtrans\Snap;
use Midtrans\Transaction;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
   public function create($OrderId)
{
    $order = Order::with(['produk', 'addres'])->findOrFail($OrderId);

    // 1. SIMPAN DULU ke Database dengan data seadanya (tanpa snap_token dulu)
    // Tujuannya agar kita dapat $payment->id
    $payment = Payment::create([
        'order_id'       => $order->id,
        'gross_amount'   => $order->total,
        'transaction_status' => 'Pending',
        'snap_token'     => null, // Nanti diupdate
        'payload'        => null, // Nanti diupdate
    ]);

    // 2. Buat Custom Order ID gabungan: ID_PAYMENT - TIMESTAMP
    // Contoh hasil: 15-173829123 (ID Payment 15)
    $customOrderId = $payment->id . '-' . time();

    $params = [
        'transaction_details' => [
            'order_id' => $customOrderId, // <--- Pakai ID gabungan ini
            'gross_amount' => $order->total,
        ],
        'customer_detail' => [
            'fullname' => $order->user->fullname ?? 'Guest',
            'email' => $order->user->email ?? 'guest@exmple.com',
            'phone' => $order->user->phone ?? '0857241566',
        ],
        // ... item details dll
    ];

    // 3. Request Snap Token
    $snapToken = Snap::getSnapToken($params);

    // 4. UPDATE kembali row payment tadi dengan snap token & payload
    $payment->update([
        'snap_token' => $snapToken,
        'payload'    => json_encode($params)
    ]);

    return [
        'payment_id' => $payment->id,
        'snap_token' => $snapToken,
    ];
}
   public function HandleNotif(Request $request)
{
    try {
        $notif = new \Midtrans\Notification();
        
        $transaction = $notif->transaction_status;
        $type = $notif->payment_type;
        $midtrans_order_id = $notif->order_id; // Isinya misal: "15-173829123"
        $fraud = $notif->fraud_status;

        // --- LOGIKA PEMECAHAN ID (EXPLODE) ---
        // Kita pecah string berdasarkan tanda strip "-"
        // "15-173829123" -> menjadi array ["15", "173829123"]
        $parts = explode('-', $midtrans_order_id);
        
        // Ambil bagian pertama (index 0) yaitu ID Payment asli
        $payment_id_asli = $parts[0]; 

        // Cari berdasarkan Primary Key (ID) tabel Payment
        $payment = Payment::find($payment_id_asli);

        if (!$payment) {
            Log::error("Payment ID $payment_id_asli not found. Full Order ID: $midtrans_order_id");
            return response()->json(['message' => 'Payment not found'], 404);
        }

        // Simpan data update
        $payment->update([
            'transaction_id' => $notif->transaction_id,
            'payment_type' => $type,
            'transaction_status' => $transaction,
            'fraud_status' => $fraud,
            'payload' => json_encode($request->all()), // Update payload dengan response terbaru
        ]);

        // --- LOGIC STATUS ORDER ---
        // Pastikan akses ke relasi order aman
        if ($payment->order) {
            if ($transaction == 'capture') {
                if ($fraud == 'challenge') {
                    $payment->order->update(['status' => 'Pending']);
                } else {
                    $payment->order->update(['status' => 'Paid']);
                }
            } else if ($transaction == 'settlement') {
                $payment->order->update(['status' => 'Paid']);
            } else if ($transaction == 'pending') {
                $payment->order->update(['status' => 'Pending']);
            } else if ($transaction == 'deny' || $transaction == 'expire' || $transaction == 'cancel') {
                $payment->order->update(['status' => 'Failed']);
            }
        }

        return response()->json(['message' => 'OK']);

    } catch (\Exception $e) {
        Log::error('Midtrans Error: ' . $e->getMessage());
        return response()->json(['message' => 'Error'], 500);
    }
}

}