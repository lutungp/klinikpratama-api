<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;


class AuthController extends Controller
{

    public function login(Request $request)
    {
        $validator = \Validator::make($request->all(), [ // <---
            'name' => 'required|exists:users,name',
            'password' => 'required'
        ]);
        
        if ($validator->fails()) {
            $errors["name"] = "Username tidak terdaftar";
            return response()->json([
                'success' => false,
                'message' => "Username tidak terdaftar"
            ], 422);
         }

         if ($token = app('auth')->attempt($request->only('name', 'password'))) {
             return response()->json([
                 "token" => $token,
                 "user"  => auth()->user()
             ], 200);
         } else {
            return response()->json([
                'success' => false,
                'message' => "Username tidak terdaftar"
            ], 422);
         }
    }

}