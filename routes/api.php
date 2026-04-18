<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\DetalleVentaController;
use App\Http\Controllers\CodigoBarraController;

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {

    Route::apiResource('users', UserController::class);
    Route::apiResource('roles', RolController::class);
    Route::apiResource('productos', ProductoController::class);
    Route::apiResource('codigosbarra', CodigoBarraController::class);
    Route::apiResource('detalleventa', DetalleVentaController::class);
    Route::apiResource('venta', VentaController::class);
    Route::apiResource('categorias', CategoriaController::class);

});