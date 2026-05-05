<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
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


Route::post('login', [AuthController::class, 'login']);
// Route::get('users', [UserController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('logout', [AuthController::class, 'logout']);

    // ✅ Renovar token — leer abilities ANTES de eliminar
    Route::post('renovar-token', function (Request $request) {
        $user       = $request->user();
        $abilities  = $request->user()->currentAccessToken()->abilities; // 1. guardar abilities

        $request->user()->currentAccessToken()->delete(); // 2. eliminar token viejo

        $token = $user->createToken('auth_token', $abilities)->plainTextToken; // 3. crear nuevo

        return response()->json(['access_token' => $token]);
    });

    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RolController::class);
    Route::apiResource('productos', ProductoController::class);
    Route::apiResource('codigosbarra', CodigoBarraController::class);
    Route::apiResource('lotes', LoteController::class);
    Route::apiResource('ventas', VentaController::class);

    Route::get('detalleventa',         [DetalleVentaController::class, 'index']);
    Route::get('detalleventa/{id}',    [DetalleVentaController::class, 'show']);
    Route::put('detalleventa/{id}',    [DetalleVentaController::class, 'update']);
    Route::delete('detalleventa/{id}', [DetalleVentaController::class, 'destroy']);

    Route::post('scan/{codigoBarra}',                        [ScanController::class, 'scan']);
    Route::delete('scan/{ventaId}/producto/{productoId}',    [ScanController::class, 'eliminarProducto']);
    Route::patch('scan/{ventaId}/cancelar',                  [ScanController::class, 'cancelarVenta']);
    Route::patch('scan/{ventaId}/cobrar',                    [ScanController::class, 'cobrarVenta']);

    Route::get('reportes/ventasmes',  [ReporteController::class, 'ventasPorMes']);
    Route::get('reportes/comprasmes', [ReporteController::class, 'comprasPorMes']);
    Route::get('reportes/stock',      [ReporteController::class, 'stockPorProducto']);
});