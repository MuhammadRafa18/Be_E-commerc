<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ResendVerificationController extends Controller
{
    public function resend(Request $request)
    {
        
        $request->validate([
            'email' => 'required|email'
        ]);

        $user = User::where('email', $request->email)->first();

        // RESPONSE NETRAL (ANTI ENUMERATION)
        if (!$user || $user->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'Jika email terdaftar dan belum diverifikasi, email verifikasi akan dikirim'
            ]);
        }

        $user->sendEmailVerificationNotification();

        return response()->json([
            'message' => 'Email verifikasi dikirim ulang'
        ],201); 
    }
}
