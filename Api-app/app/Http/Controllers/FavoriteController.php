<?php

namespace App\Http\Controllers;

use App\Http\Resources\FavoriteResource;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavoriteController extends Controller
{
    public function index($user_id)
    {
        $favorites = Favorite::with('produk')
        ->where('user_id', $user_id)
        ->get();

    return response()->json([
        'count' => $favorites->count(),
        'data' => FavoriteResource::collection($favorites)
    ], 200);
    }
    public function toggleOn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'produk_id' => 'required|exists:produks,id'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        // Cek apakah sudah ada
        $favorite = Favorite::where('user_id', $request->user_id)
            ->where('produk_id', $request->produk_id)
            ->first();

        // Jika belum ada, buat
        if (!$favorite) {
            $favorite = Favorite::create([
                'user_id' => $request->user_id,
                'produk_id' => $request->produk_id
            ]);
        }

        return response()->json([
            'message' => 'Product added to favorites',
            'data' => new FavoriteResource($favorite->load('produk'))
        ], 200);
    }
}
