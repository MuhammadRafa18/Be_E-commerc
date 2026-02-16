<?php

namespace App\Http\Controllers;

use App\Models\User;
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
        ]); 
    }
}
