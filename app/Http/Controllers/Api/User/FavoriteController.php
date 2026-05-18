<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Http\Resources\FavoriteResource;
use App\Models\Favorite;
use Illuminate\Http\Request;


class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $favorites = Favorite::with('product')
            ->where('user_id', $user->id)->latest()
            ->get();
        if ($favorites->isEmpty()) {
            return response()->json([
                'messages' => 'Favorite  not found'
            ], 404);
        }
        return FavoriteResource::collection($favorites);
    }
    public function toggleOn(Request $request)
    {
        $user = $request->user();
        $validator = $request->validate([
            'product_id' => 'required|exists:product,id'
        ]);



        // Cek apakah sudah ada
        $favorite = Favorite::where('user_id', $user->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['status' => 'unliked']);
        }

        Favorite::create([
            'user_id' => $user->id,
            'product_id' => $validator['product_id'],
        ]);

        return response()->json(['status' => 'liked'], 201);
    }
}
