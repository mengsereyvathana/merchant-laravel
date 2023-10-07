<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Exception;
use App\Http\Requests\admin_loginRequest;

class AdminUserController extends Controller
{
    public function login(admin_loginRequest $req)
    {
        // return 'g';
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

    public function logout(Request $request)
    {
        return 'g';
        try {
            if (auth()->check()) {
                $request->user()->currentAccessToken()->delete();
                return response()->json([
                    'success' => true,
                    'message' => 'Logout successfuly'
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'unAuthorization'
                ], 400);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }

        // return 'logout'; 
    }
}
