<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class UserAdmin extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $this->authorize('viewAny', User::class);
        $User = User::whereIn('role', ['admin', 'super_admin'])
            ->latest()
            ->paginate(10);
        if ($User->isEmpty()) {
            return response()->json([
                'messages' => 'Data Not Found'
            ], 404);
        }
        return UserResource::collection($User);
    }

    /**
     * Show the form for creating a new resource.
     */


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $this->authorize('create', User::class);
        $validasi = Validator::make($request->all(), [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8',
            'name' => 'required|string',
            'role' => 'required|in:admin,super_admin',
            'profile_image' => 'nullable|image|max:2048',
        ]);
        if ($validasi->fails()) {
            return response()->json([
                'error' => $validasi->messages()
            ], 422);
        }
        $data = $validasi->validate();


        $data['password'] = Hash::make($data['password']);
        if ($request->hasFile('profile_image')) {
            $data['profile_image'] = $request->file('profile_image')
                ->store('profiles', 'public');
        }
        $User = User::create($data);
        return response()->json([
            'messages' => 'Data Berhasil ditambahkan',
            'data' => new UserResource($User)
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function me(Request $request)
    {
        $this->authorize('view', User::class);
        return $request->user();
    }

  

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $UserAdmin)
    {
        $this->authorize('update', $UserAdmin);
        $validasi = Validator::make($request->all(), [
            'email' => 'sometimes|email|unique:users,email,' . $UserAdmin->id,
            'password' => 'sometimes|min:8',
            'name' => 'sometimes|string',
            'role' => 'sometimes|in:admin,super_admin',
            'profile_image' => 'sometimes|image|max:2048',
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
        if ($request->hasFile('profile_image')) {
            if ($UserAdmin->profile_image && Storage::disk('public')->exists($UserAdmin->profile_image)) {
                Storage::disk('public')->delete($UserAdmin->profile_image);
            }
            $data['profile_image'] = $request->file('profile_image')
                ->store('profiles', 'public');
        }

        $UserAdmin->update($data);
        return response()->json([
            'messages' => 'Data Berhasil diupdate',
            'data' => new UserResource($UserAdmin)
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $UserAdmin)
    {
        $this->authorize('delete', $UserAdmin);
        $UserAdmin->tokens()->delete(); 
        $UserAdmin->delete();
        return response()->json([
            'messages' => 'Data Berhasil dihapus'
        ], 200);
    }
}
