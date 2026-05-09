<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

use App\Http\Controllers\RolController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\DetalleVentaController;
use App\Http\Controllers\CodigoBarraController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\ScanController;
use App\Http\Controllers\PrecioController;
use App\Http\Controllers\ReporteController;

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
*/

Route::post('login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| PROTECTED
|--------------------------------------------------------------------------
*/

Route::middleware('auth:sanctum')->group(function () {

    /*
    |--------------------------------------------------------------------------
    | AUTH
    |--------------------------------------------------------------------------
    */

    Route::post('logout', [AuthController::class, 'logout']);

Route::post('renovar-token', function (Request $request) {

    $user = $request->user();

    $abilities = $request
        ->user()
        ->currentAccessToken()
        ->abilities;

    $token = $user
        ->createToken('auth_token', $abilities)
        ->plainTextToken;

    return response()->json([
        'access_token' => $token
    ]);
});
    /*
    |--------------------------------------------------------------------------
    | USERS
    |--------------------------------------------------------------------------
    */

    Route::middleware('abilities:users:ver')
        ->get('users', [UserController::class, 'index']);

    Route::middleware('abilities:users:ver')
        ->get('users/{user}', [UserController::class, 'show']);

    Route::middleware('abilities:users:crear')
        ->post('users', [UserController::class, 'store']);

    Route::middleware('abilities:users:editar')
        ->put('users/{user}', [UserController::class, 'update']);

    Route::middleware('abilities:users:eliminar')
        ->delete('users/{user}', [UserController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | ROLES
    |--------------------------------------------------------------------------
    */

    Route::middleware('abilities:roles:ver')
        ->get('roles', [RolController::class, 'index']);

    Route::middleware('abilities:roles:ver')
        ->get('roles/{rol}', [RolController::class, 'show']);

    Route::middleware('abilities:roles:crear')
        ->post('roles', [RolController::class, 'store']);

    Route::middleware('abilities:roles:editar')
        ->put('roles/{rol}', [RolController::class, 'update']);

    Route::middleware('abilities:roles:eliminar')
        ->delete('roles/{rol}', [RolController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | PRODUCTOS
    |--------------------------------------------------------------------------
    */

    Route::middleware('abilities:productos:ver')
        ->get('productos', [ProductoController::class, 'index']);

    Route::middleware('abilities:productos:ver')
        ->get('productos/{producto}', [ProductoController::class, 'show']);

    Route::middleware('abilities:productos:crear')
        ->post('productos', [ProductoController::class, 'store']);

    Route::middleware('abilities:productos:editar')
        ->put('productos/{producto}', [ProductoController::class, 'update']);

    Route::middleware('abilities:productos:eliminar')
        ->delete('productos/{producto}', [ProductoController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | CODIGOS DE BARRA
    |--------------------------------------------------------------------------
    */

    Route::middleware('abilities:codigosbarra:ver')
        ->get('codigosbarra', [CodigoBarraController::class, 'index']);

    Route::middleware('abilities:codigosbarra:ver')
        ->get('codigosbarra/{codigobarra}', [CodigoBarraController::class, 'show']);

    Route::middleware('abilities:codigosbarra:crear')
        ->post('codigosbarra', [CodigoBarraController::class, 'store']);

    Route::middleware('abilities:codigosbarra:editar')
        ->put('codigosbarra/{codigobarra}', [CodigoBarraController::class, 'update']);

    Route::middleware('abilities:codigosbarra:eliminar')
        ->delete('codigosbarra/{codigobarra}', [CodigoBarraController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | LOTES
    |--------------------------------------------------------------------------
    */

    Route::middleware('abilities:lotes:ver')
        ->get('lotes', [LoteController::class, 'index']);

    Route::middleware('abilities:lotes:ver')
        ->get('lotes/{lote}', [LoteController::class, 'show']);

    Route::middleware('abilities:lotes:crear')
        ->post('lotes', [LoteController::class, 'store']);

    Route::middleware('abilities:lotes:editar')
        ->put('lotes/{lote}', [LoteController::class, 'update']);

    Route::middleware('abilities:lotes:eliminar')
        ->delete('lotes/{lote}', [LoteController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | VENTAS
    |--------------------------------------------------------------------------
    */

    Route::middleware('abilities:ventas:ver')
        ->get('ventas', [VentaController::class, 'index']);

    Route::middleware('abilities:ventas:ver')
        ->get('ventas/{venta}', [VentaController::class, 'show']);

    /*
    |--------------------------------------------------------------------------
    | DETALLE VENTA
    |--------------------------------------------------------------------------
    */

    Route::middleware('abilities:detallesventa:ver')
        ->get('detalleventa', [DetalleVentaController::class, 'index']);

    Route::middleware('abilities:detallesventa:ver')
        ->get('detalleventa/{id}', [DetalleVentaController::class, 'show']);

    Route::middleware('abilities:detallesventa:editar')
        ->put('detalleventa/{id}', [DetalleVentaController::class, 'update']);

    Route::middleware('abilities:detallesventa:eliminar')
        ->delete('detalleventa/{id}', [DetalleVentaController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | SCAN / POS
    |--------------------------------------------------------------------------
    */

    Route::middleware('abilities:scan:usar')
        ->post('scan/{codigoBarra}', [ScanController::class, 'scan']);

    Route::middleware('abilities:detallesventa:eliminar')
        ->delete(
            'scan/{ventaId}/producto/{productoId}',
            [ScanController::class, 'eliminarProducto']
        );

    Route::middleware('abilities:ventas:cancelar')
        ->patch(
            'scan/{ventaId}/cancelar',
            [ScanController::class, 'cancelarVenta']
        );

    Route::middleware('abilities:ventas:cobrar')
        ->patch(
            'scan/{ventaId}/cobrar',
            [ScanController::class, 'cobrarVenta']
        );

    /*
    |--------------------------------------------------------------------------
    | REPORTES
    |--------------------------------------------------------------------------
    */

    Route::middleware('abilities:reportes:ver')
        ->get(
            'reportes/ventasmes',
            [ReporteController::class, 'ventasPorMes']
        );

    Route::middleware('abilities:reportes:ver')
        ->get(
            'reportes/comprasmes',
            [ReporteController::class, 'comprasPorMes']
        );

    Route::middleware('abilities:reportes:ver')
        ->get(
            'reportes/stock',
            [ReporteController::class, 'stockPorProducto']
        );
});