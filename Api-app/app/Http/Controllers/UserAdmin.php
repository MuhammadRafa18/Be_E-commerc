<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserAdminResource;
use App\Models\UserAdmin as ModelsUserAdmin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserAdmin extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $UserAdmin = ModelsUserAdmin::get();
        if($UserAdmin->count()){
            return UserAdminResource::collection($UserAdmin);
        }else{
            return response()->json([
                'messages' => 'Data Not Found'
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
             'password' => 'required|string|max:100',
             'role' => 'required|string|max:100'
        ]);
        if($validasi->fails()){
            return response()->json([
                'error' => $validasi->messages()
            ], 422);
        }

        $hashedPassword = Hash::make($request->password);
        $UserAdmin = ModelsUserAdmin::create([
           'email' => $request->email,
           'password' => $hashedPassword,
           'role' => $request->role,
        ]);
        return response()->json([
           'messages' => 'Data Berhasil ditambahkan',
           'data' => new UserAdminResource($UserAdmin)
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(ModelsUserAdmin $UserAdmin)
    {
        return new UserAdminResource($UserAdmin);
    }

    /**
     * Show the form for editing the specified resource.
     */
  

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ModelsUserAdmin $UserAdmin)
    {
           $validasi = Validator::make($request->all(),[
             'email' => 'required|string|max:150',
             'password' => 'required|string|max:100',
             'role' => 'required|string|max:100'
        ]);
        if($validasi->fails()){
            return response()->json([
                'error' => $validasi->messages()
            ], 422);
        }

        $hashedPassword = Hash::make($request->password);
        $UserAdmin->update([
           'email' => $request->email,
           'password' => $hashedPassword,
           'role' => $request->role,
        ]);
        return response()->json([
           'messages' => 'Data Berhasil ditambahkan',
           'data' => new UserAdminResource($UserAdmin)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ModelsUserAdmin $UserAdmin)
    {
        $UserAdmin->delete();
        return response()->json([
           'messages' => 'Data Berhasil dihapus'
        ],200);
    }
}
