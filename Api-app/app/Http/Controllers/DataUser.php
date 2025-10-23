<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\DataUserResource;
use Illuminate\Support\Facades\Validator;
use App\Models\DataUser as ModelsDataUser;

class DataUser extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $DataUser = ModelsDataUser::get();
        if($DataUser->count()){
            return DataUserResource::collection($DataUser);
        }else{
            return response()->json([
                'messages' => "Data Not Found"
            ], 401);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
  

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validasi = Validator::make($request->all(),[
             'email' => 'required|string|max:150',
             'fullname' => 'required|string|max:150',
             'password' => 'required|string|max:100',
             'phone' => 'required|string|max:100'
        ]);
        if($validasi->fails()){
            return response()->json([
                'error' => $validasi->messages()
            ], 422);
        }

        $hashedPassword = Hash::make($request->password);
        $DataUser = ModelsDataUser::create([
           'email' => $request->email,
           'fullname' => $request->fullname,
           'password' => $hashedPassword,
           'phone' => $request->phone,
        ]);
        return response()->json([
           'messages' => 'Data Berhasil ditambahkan',
           'data' => new DataUserResource($DataUser)
        ], 200);
    }
    

    /**
     * Display the specified resource.
     */
    public function show(ModelsDataUser $DataUser)
    {
        return new DataUserResource($DataUser);
    }

    /**
     * Show the form for editing the specified resource.
     */
   

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModelsDataUser $DataUser)
    {
         {
        $validasi = Validator::make($request->all(),[
             'email' => 'required|string|max:150',
             'fullname' => 'required|string|max:150',
             'password' => 'required|string|max:100',
             'phone' => 'required|string|max:100'
        ]);
        if($validasi->fails()){
            return response()->json([
                'error' => $validasi->messages()
            ], 422);
        }

        $hashedPassword = Hash::make($request->password);
        $DataUser->update([
           'email' => $request->email,
           'fullname' => $request->fullname,
           'password' => $hashedPassword,
           'phone' => $request->phone,
        ]);
        return response()->json([
           'messages' => 'Data Berhasil diupdate',
           'data' => new DataUserResource($DataUser)
        ], 200);
    }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModelsDataUser $DataUser)
    {
        $DataUser->delete();
        return response()->json([
            'messages' => 'data berhasil dihapus',
        ],200);
    }
}
