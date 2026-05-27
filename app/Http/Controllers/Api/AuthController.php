<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        if (!auth()->attempt($credentials)) {

            return response()->json([
                'message' => 'Credenciales incorrectas'
            ], 401);
        }

        $user = auth()->user();
        if (!$user->estado) {

            auth()->logout();

            return response()->json([
                'message' => 'Usuario inactivo'
            ], 403);
        }

      

        $user->load('roles');

        $token = $user
            ->createToken('auth_token')
            ->plainTextToken;

        return response()->json([

            'access_token' => $token,

            'token_type' => 'Bearer',

            'user' => [

                'id' => $user->id,

                'nombre' => $user->nombre,

                'email' => $user->email,

                /*
                |--------------------------------------------------------------------------
                | SPATIE
                |--------------------------------------------------------------------------
                */

                'roles' => $user->getRoleNames(),

                'permissions' => $user->getAllPermissions()
                    ->pluck('name'),

            ],
        ]);
    }

    public function logout(Request $request)
    {
        $request
            ->user()
            ->currentAccessToken()
            ->delete();

        return response()->json([
            'message' => 'Sesión cerrada'
        ]);
    }
}