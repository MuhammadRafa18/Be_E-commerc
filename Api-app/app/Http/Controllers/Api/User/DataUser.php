<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\DataUserResource;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Validator;
use App\Models\DataUser as ModelsDataUser;
use App\Models\User;

class DataUser extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $DataUser = User::where('role', 'user')->get();
        if ($DataUser->isEmpty()) {
            return response()->json([
                'messages' => "Data Not Found"
            ], 404);
        } else {
            return UserResource::collection($DataUser);
        }
    }

    /**
     * Show the form for creating a new resource.
     */


    /**
     * Store a newly created resource in storage.
     */
    public function register(Request $request)
    {
        $validasi = Validator::make($request->all(),[
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
        ]);
         if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages()
            ], 422);
        }
        $data = $validasi->validate();

        $data['password'] = Hash::make($data['password']);
        $data['role'] = 'user';
        $DataUser = User::create($data);
        $DataUser->sendEmailVerificationNotification();
        return response()->json([
            'message' => 'Register sukses, cek email untuk verifikasi'
        ], 201);
    }


    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $DataUser = $request->user();
        return new DataUserResource($DataUser);
    }

    /**
     * Show the form for editing the specified resource.
     */


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $validasi = Validator::make($request->all(),[
            'email' => 'sometimes|email|unique:users,email,' . $user->id,
            'name' => 'sometimes|string',
            'password' => 'sometimes|min:8',
        ]);
          if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages()
            ], 422);
        }
        $data = $validasi->validate();

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        }
        $user->update($data);
        return response()->json([
            'messages' => 'Data Berhasil diupdate',
            'data' => new DataUserResource($user)
        ], 201);
    }

    public function updatePhone(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'phone' => 'required|string|unique:users,phone,' . $user->id,
        ]);

        $otp = random_int(100000, 999999);

        $user->update([
            'phone' => $request->phone,
            'phone_verified_at' => null,
            'phone_otp' => $otp,
            'phone_otp_expires_at' => now()->addMinutes(5),
        ]);

        // KIRIM OTP (SMS Gateway)
        // SmsService::send($user->phone, "Kode OTP kamu: $otp");

        return response()->json([
            'message' => 'OTP dikirim ke nomor anda'
        ]);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $user = $request->user();
        $user->tokens()->delete();
        $user->delete();
        return response()->json([
            'messages' => 'data berhasil dihapus',
        ], 201);
    }
}
