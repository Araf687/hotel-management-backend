<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate request input
        $validator = Validator::make($request->all(), [
            'login' => 'required|string', // Can be username or email
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Check if login is an email or username
        $loginField = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Attempt authentication
        if (Auth::attempt([$loginField => $request->login, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken; // Sanctum token (adjust if using Passport)
            return response()->json(['user' => $user, 'token' => $token], 200);
        }

        return response()->json(['error' => 'Invalid Credentials'], 401);
    }
}
