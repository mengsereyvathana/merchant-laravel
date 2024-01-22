<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RoleTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdminLoginRequest;
use App\Models\User;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(AdminLoginRequest $request)
    {
        $email = $request->email;

        $user = User::where('email', $email)->first();
        $adminUser = User::where('role', RoleTypeEnum::ADMIN)->first();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect email',
            ], 200);
        }

        if (!$adminUser) {
            return response()->json([
                'success' => false,
                'message' => "This account isn't admin",
            ], 200);
        }

        if (!$user && !$adminUser) {
            return response()->json([
                'success' => false,
                'message' => 'User not found',
            ], 200);
        }

        $isPasswordCorrect = password_verify($request->password, $user->password);

        if (!$isPasswordCorrect) {
            return response()->json([
                'success' => false,
                'message' => 'Incorrect password',
            ], 201);
        }

        $token = $user->createToken('my-app-token')->plainTextToken;

        return response()->json([
            'success' => true,
            'user' => $user,
            'token' => $token,
        ], 200);
    }

    public function logout(Request $request)
    {
        if (auth()->check()) {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'success' => true,
                'message' => 'Logout successfully',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized',
        ], 201);
    }
}
