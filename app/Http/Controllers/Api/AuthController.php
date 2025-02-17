<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;

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

    public function register(Request $request)
    {
        // Validate request input
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:users,username|max:255',  // Ensure username is unique
            'username' => 'required|string|unique:users,username|max:255',  // Ensure username is unique
            'email' => 'required|string|email|unique:users,email|max:255',  // Ensure email is unique and valid
            'password' => 'required|string|min:6|confirmed',  // Password confirmation
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Create a new user
        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => bcrypt($request->password),  // Hash the password
        ]);

        // Return user info along with the token
        return response()->json([
            'data' => $user,
            'messgae' => 'User registered successfully',
        ], 201);  // Return 201 for successful creation
    }

}
