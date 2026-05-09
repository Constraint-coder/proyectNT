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
        'email'    => 'required|email',
        'password' => 'required|string',
    ]);

    if (!auth()->attempt($credentials)) {
        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    $user = auth()->user()->load('rol');

    if (!$user->estado) {

        auth()->logout();

        return response()->json([
            'message' => 'Usuario inactivo'
        ], 403);
    }

    $abilities = match($user->rol->nombre) {

        'Administrador' => ['*'],

        'Supervisor' => [
            'productos:ver',
            'productos:crear',
        ],

        'Cajero' => [
            'scan:usar',
            'ventas:ver',
        ],

        default => ['ventas:ver'],
    };

    // NO eliminar tokens anteriores

    $token = $user
        ->createToken('auth_token', $abilities)
        ->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',

        'user' => [
            'id' => $user->id,
            'nombre' => $user->nombre,
            'email' => $user->email,
            'rol' => $user->rol->nombre,
        ],

        'abilities' => $abilities,
    ]);
}
function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Logged out']);
}

}