<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCartRequest;
use App\Http\Resources\Cart as ResourcesCart;
use App\Models\Cart as ModelsCart;
use App\Services\Cart\CartService;
use Illuminate\Http\Request;


class Cart extends Controller
{


    public function index(Request $request)
    {
        $user = $request->user();
        $cart = ModelsCart::where('user_id', $user->id)->with(
            'product',
            'product_sku',
            'product_skincare',
            'product_fashion',
        )
            ->latest()
            ->get();
        if ($cart->isEmpty()) {
            return response()->json([
                'messages' => "Cart Not Found"
            ], 404);
        }
        return ResourcesCart::collection($cart);
    }
    public function store(StoreCartRequest $request, CartService $service)
    {
        // \dd($request);
        $user = $request->user();

        $cart = $service->addToCart($request->validated(), $user);

        return response()->json([
            'message' => 'Cart berhasil ditambah',
            'data' => new ResourcesCart($cart),
        ], 200);
    }

    public function destroy(Request $request, $id)
    {
        $user = $request->user();

        $cart = ModelsCart::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $cart->delete();

        return response()->json([
            'message' => 'Cart berhasil dihapus'
        ], 200);
    }
    public function select(Request $request, $id)
    {
        $user = $request->user();

        $cart = ModelsCart::where('id', $id)
            ->where('user_id', $user->id)
            ->firstOrFail();

        $cart->update([
            'is_selected' => !$cart->is_selected
        ]);

        return response()->json([
            'is_selected' => $cart->is_selected
        ], 200);
    }
}
