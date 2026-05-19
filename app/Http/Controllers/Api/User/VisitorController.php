<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\Visitor;
use Illuminate\Http\Request;


class VisitorController extends Controller
{
    public function store(Request $request)
    {
        Visitor::create([
            'ip_address' => $request->ip(),
            'visitor' => 1,
        ]);

        return response()->json(['succes' => true], 201);
    }
}
