<?php

namespace App\Http\Controllers;

use App\Models\UserAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthUserAdmin extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['messages' => $validator->messages()], 422);
        }

        $user = UserAdmin::where('email', $request->email)->first();
        $password =  Hash::check($request->password, $user->password);

        if (!$user || !$password) {
            return response()->json(['error' => 'Email atau password salah'], 401);
        }
        $token = $user->createToken('user-admin-token')->plainTextToken;
        return response()->json([
            'message' => 'Login sukses',
            'token' => $token,
            'user' => $user
        ]);
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout sukses'
        ]);
    }


}
