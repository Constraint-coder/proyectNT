<?php


namespace App\Http\Controllers\Api;

use App\Models\CodigoBarra;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;



class AuthController extends Controller
{

function login(Request $request)
{
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required|string',
    ]);

    if (auth()->attempt($credentials)) {
        $user = auth()->user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    return response()->json(['message' => 'Invalid credentials'], 401);

}

function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Logged out']);
}

}