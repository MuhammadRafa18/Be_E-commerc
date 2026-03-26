<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
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
        }
        return ResourcesCart::collection($cart);
    }
    public function store(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'produk_id' => 'required|exists:produks,id'
        ]);

        $cart = ModelsCart::firstOrCreate(
            [
                'user_id' => $user->id,
                'produk_id' => $data['produk_id'],

            ],
            [
                'qty' => $data['qty'] ?? 1,
            ]
        );

        if (!$cart->wasRecentlyCreated) {
            $cart->increment('qty', $data['qty'] ?? 1);
        }

        return response()->json([
            'message' => 'Cart Berhsil ditambah',
            'data' => new ResourcesCart($cart),
        ],200);
    }

    public function destroy(Request $request, ModelsCart $cart)
    {
        $user = $request->user();
        abort_if($cart->user_id !== $user->id, 403);

        $cart->delete();
        return response()->json([
            'message' => 'Cart berhasil dihapus'
        ],200);
    }
    public function select(Request $request, ModelsCart $cart)
    {
        $user = $request->user();

        abort_if($cart->user_id !== $user->id, 403);
        // dd($cart->is_selected);

        $cart->update([
            'is_selected' => !$cart->is_selected
        ]);
        $cart->refresh();
        return response()->json([
            'is_selected' => $cart->is_selected
        ],200);
    }
}
