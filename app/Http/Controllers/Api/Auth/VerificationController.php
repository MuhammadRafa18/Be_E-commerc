<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;


class VerificationController extends Controller
{
    public function verify($id, $hash)
    {
        $user = User::findOrFail($id);

        // VALIDASI HASH EMAIL
        if (! hash_equals(
            (string) $hash,
            sha1($user->getEmailForVerification())
        )) {
            return response()->json([
                'message' => 'Link verifikasi tidak valid'
            ], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Email sudah diverifikasi'
            ]);
        }

        // TANDAI VERIFIED
        $user->markEmailAsVerified();
        $user->tokens()->delete();
        $token = $user->createToken('user-admin-token')->plainTextToken;
        return response()->json([
            'message' => 'Email berhasil diverifikasi',
            'token' =>$token,
        ],200);
    }
}
