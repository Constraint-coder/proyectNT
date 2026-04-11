<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CodigoBarraController;

Route::apiResource('productos', ProductoController::class);
Route::apiResource('codigos-barra', CodigoBarraController::class);