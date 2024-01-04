<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login (AdminLoginRequest $req) {
        $name = $req->name;

        $user = User::where('name', $name)->first();
        $user_check = User::where('role', 3)->first();

        if (!$user) {
            return response()->json([
                'success'   =>  false,
                'message'   => 'Incorrect name'
            ], 401);
        }
        if (!$user_check) {
            return response()->json([
                'success'   =>  false,
                'message'   => "This accound isn't admin"
            ], 400);
        }
        if (!$user_check & !$user) {
            return response()->json([
                'success'   =>  false,
                'message'   => "User not found"
            ], 400);
        } else {
            // $check = Hash::check($req->password, $user->password);
            $check = User::where('password', $req->password);
            if (!$check) {
                return response()->json([
                    'success'   =>  false,
                    'message'   => 'Incorrect password'
                ], 401);
            }
            $token = $user->createToken('my-app-token')->plainTextToken;

            return response()->json([
                'success' => true,
                'user' => $user,
                'token' => $token
            ], 200);
        }
    }

    public function logout (Request $request) {
        if (auth()->check()) {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'success' => true,
                'message' => 'Logout successfuly'
            ], 200);
        }
        return response()->json([
            'success' => false,
            'message' => 'unAuthorization'
        ], 400);
    }
}
