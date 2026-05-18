<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PhoneVertivication extends Controller
{
    public function phone(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'regex:/^[0-9]+$/', 'digits_between:9,13']
        ]);

        $user = $request->user();
        $token = Str::random(64);
        $cleanPhone = ltrim($request->phone, '0');
        $fullphone = '62' . $cleanPhone;

        $user->update([
            'phone' => $fullphone,
            'phone_otp_expires_at' => Carbon::now()->addMinute(10),
            'phone_otp' => $token,
            'phone_verified_at' => null
        ]);

        return response()->json(['verification_link' => url("api/phone/verify/$token")]);
    }

    public function verify($token)
    {
        $user = User::where('phone_otp', $token)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Token tidak valid'
            ], 400);
        }

        if (Carbon::now()->greaterThan($user->phone_otp_expires_at)) {
            return response()->json([
                'message' => 'Token expired'
            ], 400);
        }
        $user->update([
            'phone_verified_at' => now(),
            'phone_otp' => null,
            'phone_otp_expires_at' => null
        ]);

        return response()->json([
            'message' => 'Nomor berhasil diverifikasi'
        ],200);
    }
}
