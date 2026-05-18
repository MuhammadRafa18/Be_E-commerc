<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;
use Throwable;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return app('Laravel\Socialite\Contracts\Factory')->driver('google')->stateless()->redirect();
    }
    public function callback()
    {
        try {
            $provider = app('Laravel\Socialite\Contracts\Factory')->driver('google');
            $googleuser = $provider->stateless()->user();
        } catch (Throwable $e) {
            return response()->json(['message' => 'Google authentication failed.']);
        }

        $user = User::where('google_id', $googleuser->id)->first();

        if (!$user) {
            $user = User::where('email', $googleuser->email)->first();
            if ($user) {
                $user->update([
                    'google_id' => $googleuser->id,
                    'email_verified_at' => now(),
                ]);
            } else {
                $user = User::create([
                    'name' => $googleuser->name,
                    'email' => $googleuser->email,
                    'google_id' => $googleuser->id,
                    'password' => bcrypt(Str::random(16)),
                    'email_verified_at' => now(),
                ]);
            }
        }

        $user->tokens()->delete();
        $token = $user->createToken('user-admin-token')->plainTextToken;
        return response()->json([
            'message' => 'Login sukses',
            'token' => $token,
            'user' => $user
        ],200);
    }
}
