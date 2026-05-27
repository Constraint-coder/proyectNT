<?php

namespace App\Http\Controllers;

use App\Models\CodigoBarra;
use App\Models\Lote;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ScanController extends Controller
{
    public function scan($codigo, Request $request)
    {
        try {
            $codigoBarra = CodigoBarra::with('producto')
                ->where('codigoBarra', $codigo)
                ->firstOrFail();

            $producto = $codigoBarra->producto;
            $cantidad = $request->cantidad ?? 1;

            // ✅ una sola query, trae el primer lote y el stock total
           $lotes = Lote::where('productoId', $producto->id)
    ->where('cantidadDisponible', '>', 0)
    ->orderBy('fechaIngreso', 'asc')
    ->get(['cantidadDisponible', 'precioVenta', 'fechaIngreso']);

if ($lotes->isEmpty() || $lotes->sum('cantidadDisponible') < $cantidad) {
    return response()->json(['message' => 'Stock insuficiente'], 400);
}

$lote = $lotes->first();

return response()->json([
    'productoId'     => $producto->id,
    'nombre'         => $producto->nombre,
    'precioUnitario' => $lote->precioVenta,
    'cantidad'       => $cantidad,
    'subtotal'       => $lote->precioVenta * $cantidad,
]);

        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'Producto no encontrado',
                'error'   => $th->getMessage()
            ], 404);
        }
    }
}