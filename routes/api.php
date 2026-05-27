<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

use App\Http\Controllers\RolController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\DetalleVentaController;
use App\Http\Controllers\CodigoBarraController;
use App\Http\Controllers\LoteController;
use App\Http\Controllers\ScanController;
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

        // eliminar token actual
        $request
            ->user()
            ->currentAccessToken()
            ->delete();

        // crear nuevo token
        $token = $user
            ->createToken('auth_token')
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

    Route::middleware('permission:ver usuarios')->group(function () {

        Route::get(
            'users',
            [UserController::class, 'index']
        );

        Route::get(
            'users/{user}',
            [UserController::class, 'show']
        );
    });

    Route::middleware('permission:crear usuarios')->group(function () {

        Route::post(
            'users',
            [UserController::class, 'store']
        );
    });

    Route::middleware('permission:editar usuarios')->group(function () {

        Route::put(
            'users/{user}',
            [UserController::class, 'update']
        );
    });

    Route::middleware('permission:eliminar usuarios')->group(function () {

        Route::delete(
            'users/{user}',
            [UserController::class, 'destroy']
        );
    });

    /*
    |--------------------------------------------------------------------------
    | ROLES
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:ver roles')->group(function () {

        Route::get(
            'roles',
            [RolController::class, 'index']
        );

        Route::get(
            'roles/{role}',
            [RolController::class, 'show']
        );
    });

    Route::middleware('permission:crear roles')->group(function () {

        Route::post(
            'roles',
            [RolController::class, 'store']
        );
    });

    Route::middleware('permission:editar roles')->group(function () {

        Route::put(
            'roles/{role}',
            [RolController::class, 'update']
        );
    });

    Route::middleware('permission:eliminar roles')->group(function () {

        Route::delete(
            'roles/{role}',
            [RolController::class, 'destroy']
        );
    });

    /*
    |--------------------------------------------------------------------------
    | PRODUCTOS
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:ver productos')->group(function () {

        Route::get(
            'productos',
            [ProductoController::class, 'index']
        );

        Route::get(
            'productos/{producto}',
            [ProductoController::class, 'show']
        );
    });

    Route::middleware('permission:crear productos')->group(function () {

        Route::post(
            'productos',
            [ProductoController::class, 'store']
        );
    });

    Route::middleware('permission:editar productos')->group(function () {

        Route::put(
            'productos/{producto}',
            [ProductoController::class, 'update']
        );
    });

    Route::middleware('permission:eliminar productos')->group(function () {

        Route::delete(
            'productos/{producto}',
            [ProductoController::class, 'destroy']
        );
    });

    /*
    |--------------------------------------------------------------------------
    | CODIGOS DE BARRA
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:ver codigos')->group(function () {

        Route::get(
            'codigosbarra',
            [CodigoBarraController::class, 'index']
        );

        Route::get(
            'codigosbarra/{codigobarra}',
            [CodigoBarraController::class, 'show']
        );
    });

    Route::middleware('permission:crear codigos')->group(function () {

        Route::post(
            'codigosbarra',
            [CodigoBarraController::class, 'store']
        );
    });

    Route::middleware('permission:editar codigos')->group(function () {

        Route::put(
            'codigosbarra/{codigobarra}',
            [CodigoBarraController::class, 'update']
        );
    });

    Route::middleware('permission:eliminar codigos')->group(function () {

        Route::delete(
            'codigosbarra/{codigobarra}',
            [CodigoBarraController::class, 'destroy']
        );
    });

    /*
    |--------------------------------------------------------------------------
    | LOTES
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:ver lotes')->group(function () {

        Route::get(
            'lotes',
            [LoteController::class, 'index']
        );

        Route::get(
            'lotes/{lote}',
            [LoteController::class, 'show']
        );
    });

    Route::middleware('permission:crear lotes')->group(function () {

        Route::post(
            'lotes',
            [LoteController::class, 'store']
        );
    });

    Route::middleware('permission:editar lotes')->group(function () {

        Route::put(
            'lotes/{lote}',
            [LoteController::class, 'update']
        );
    });

    Route::middleware('permission:eliminar lotes')->group(function () {

        Route::delete(
            'lotes/{lote}',
            [LoteController::class, 'destroy']
        );
    });

    /*
    |--------------------------------------------------------------------------
    | VENTAS
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:ver ventas')->group(function () {

        Route::get(
            'ventas',
            [VentaController::class, 'index']
        );

        Route::get(
            'ventas/{venta}',
            [VentaController::class, 'show']
        );
    });

    Route::middleware('permission:crear ventas')->group(function () {

        Route::post(
            'ventas/cobrar',
            [VentaController::class, 'cobrarVenta']
        );
    });

    /*
    |--------------------------------------------------------------------------
    | DETALLE VENTA
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:ver detalleventa')->group(function () {

        Route::get(
            'detalleventa',
            [DetalleVentaController::class, 'index']
        );

        Route::get(
            'detalleventa/{id}',
            [DetalleVentaController::class, 'show']
        );
    });

    Route::middleware('permission:editar detalleventa')->group(function () {

        Route::put(
            'detalleventa/{id}',
            [DetalleVentaController::class, 'update']
        );
    });

    Route::middleware('permission:eliminar detalleventa')->group(function () {

        Route::delete(
            'detalleventa/{id}',
            [DetalleVentaController::class, 'destroy']
        );
    });

    /*
    |--------------------------------------------------------------------------
    | SCAN / POS
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:usar pos')->group(function () {

        Route::post(
            'scan/{codigoBarra}',
            [ScanController::class, 'scan']
        );
    });

    /*
    |--------------------------------------------------------------------------
    | REPORTES
    |--------------------------------------------------------------------------
    */

    Route::middleware('permission:ver reportes')->group(function () {

        Route::get(
            'reportes/ventasmes',
            [ReporteController::class, 'ventasPorMes']
        );

        Route::get(
            'reportes/comprasmes',
            [ReporteController::class, 'comprasPorMes']
        );

        Route::get(
            'reportes/stock',
            [ReporteController::class, 'stockPorProducto']
        );
    });

});