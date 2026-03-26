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
        $favorites = Favorite::with('produk')
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
        $validator = $request->validate([
            'produk_id' => 'required|exists:produks,id'
        ]);
        $user = $request->user();


        // Cek apakah sudah ada
        $favorite = Favorite::where('user_id', $user->id)
            ->where('produk_id', $request->produk_id)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['status' => 'unliked']);
        }

        Favorite::create([
            'user_id' => $user->id,
            'produk_id' => $validator['produk_id'],
        ]);

        return response()->json(['status' => 'liked'], 201);
    }
}
