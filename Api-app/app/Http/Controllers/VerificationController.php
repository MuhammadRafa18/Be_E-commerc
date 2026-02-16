<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

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

        return response()->json([
            'message' => 'Email berhasil diverifikasi'
        ]);
    }
}
