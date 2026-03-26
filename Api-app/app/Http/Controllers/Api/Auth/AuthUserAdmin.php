<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;


class AuthUserAdmin extends Controller
{
    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return response()->json(['error' => 'Email atau password salah'], 401);
        }
        $user->tokens()->delete();
        $token = $user->createToken('user-admin-token')->plainTextToken;
        return response()->json([
            'message' => 'Login sukses',
            'token' => $token,
            'user' => $user
        ],200);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => 'Logout sukses'
        ],200);
    }
}
