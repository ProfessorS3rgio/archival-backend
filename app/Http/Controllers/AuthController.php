<?php

namespace App\Http\Controllers;

use App\Services\LoggingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('API Token')->plainTextToken;

            // Log successful login
            LoggingService::logCustom(
                'login',
                'users',
                $user->id,
                null,
                null,
                "User {$user->username} logged in successfully"
            );

            return response()->json([
                'user' => $user,
                'token' => $token,
            ]);
        }

        // Log failed login attempt
        LoggingService::logCustom(
            'login_failed',
            'users',
            null,
            null,
            null,
            "Failed login attempt for username: {$request->username}"
        );

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        // Log logout before deleting token
        LoggingService::logCustom(
            'logout',
            'users',
            $user->id,
            null,
            null,
            "User {$user->username} logged out"
        );

        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out']);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
