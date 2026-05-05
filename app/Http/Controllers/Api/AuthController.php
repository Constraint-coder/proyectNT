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
        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    $user = auth()->user()->load('rol');

    if (!$user->estado) {
    // Cerrar la sesión que attempt() abrió
    auth()->logout();
    return response()->json(['message' => 'Usuario inactivo'], 403);
}

    // ==============================
    // ABILITIES SEGÚN ROL
    // ==============================
    $abilities = match($user->rol->nombre) {
        'Administrador'  => ['*'],
        'supervisor' => [
            'productos:ver',
            'productos:crear',
            'productos:editar',
            'productos:eliminar',
            'ventas:ver',
            'lotes:ver',
            'lotes:crear',
            'lotes:editar',
            'codigosbarra:ver',
            'codigosbarra:crear',
            'codigosbarra:editar',
    
        ],
        'cajero' => [
            'scan:usar',
            'ventas:ver',
            'ventas:cobrar',
            'ventas:cancelar',
            'productos:ver',
            'detallesventa:ver',
            'detallesventa:crear',
            'detallesventa:editar',
            'detallesventa:eliminar',
        ],
        default => ['ventas:ver'],
    };

    // Eliminar tokens anteriores del usuario
    $user->tokens()->delete();

    $token = $user->createToken('auth_token', $abilities)->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'token_type'   => 'Bearer',
        'user'         => [
            'id'    => $user->id,
            'nombre'  => $user->nombre,
            'email' => $user->email,
            'rolId' => $user->rolId,
            'rol'   => $user->rol->nombre,
        ]
    ]);
}
function logout(Request $request)
{
    $request->user()->currentAccessToken()->delete();

    return response()->json(['message' => 'Logged out']);
}

}