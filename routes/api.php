<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\RolController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\DetalleVentaController;

Route::apiResource('users', UserController::class);
Route::apiResource('rols', RolController::class);
Route::post('login', [AuthController::class, 'login']);
Route::apiResource('productos', ProductoController::class);
Route::apiResource('codigos-barra', CodigoBarraController::class);