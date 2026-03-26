<?php

namespace App\Http\Controllers\Api\User;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ContactResource;
use App\Models\Contact;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $contact = Contact::orderBy('created_at', 'desc')->get();
        if ($contact->isEmpty()) {
            return response()->json([
                "message" => "Message Not Found"
            ], 404);
        }
        return ContactResource::collection($contact);
    }



    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'fullname' => 'required|string|max:255 ',
            'email' => 'required|email',
            'message' => 'required|string|max:1000'
        ]);
        $contact = Contact::create($data);
        return response()->json([
            'data' => new ContactResource($contact),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contact $contact)
    {
        return response()->json([
            'data' => new ContactResource($contact)
        ], 200);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Contact $contact)
    {
        $contact->delete();
        return response()->json([
            'messages' => 'Message berhasil dihapus',
        ], 200);
    }
}
