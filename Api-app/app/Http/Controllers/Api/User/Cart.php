<?php

namespace App\Http\Controllers;

use App\Http\Resources\Cart as ResourcesCart;
use App\Models\Cart as ModelsCart;
use Illuminate\Http\Request;

class Cart extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $cart = ModelsCart::where('user_id', $user->id)->with('produk')
            ->latest()
            ->get();
        if ($cart->isEmpty()) {
            return response()->json([
                'messages' => "Cart Not Found"
            ], 404);
        } else {
            return ResourcesCart::collection($cart);
        }
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'produk_id' => 'required|exists:produks,id'
        ]);

        $user = $request->user();
        $cart = ModelsCart::firstOrCreate([
            [
                'user_id' => $user->id,
                'produk_id' => $data['produk_id'],
            ],
            [
                'qty' => $data['qty'] ?? 1,
            ]
        ]);

        if (!$cart->wasRecentlyCreated) {
            $cart->increment('qty', $data['qty'] ?? 1);
        }

        response()->json([
            'message' => 'Cart Berhsil ditambah',
            'data' => new ResourcesCart($cart),
        ]);
    }
    public function update(Request $request, ModelsCart $cart)
    {
        $user = $request->user();
        abort_if($cart->user_id !== $user->id, 403);

        $data = $request->validate([
            'qty' => 'required|integer|min:1',
        ]);
        $cart->update($data);
        return response()->json($cart);
    }
    public function destroy(Request $request, ModelsCart $cart)
    {
        $user = $request->user();
        abort_if($cart->user_id !== $user->id, 403);

        $cart->delete();
        return response()->json([
            'message' => 'Cart berhasil dihapus'
        ]);
    }
}
