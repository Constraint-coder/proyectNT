<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Lote;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with('detalles')->get();
        return response()->json($ventas, 200);
    }

    public function show($id)
    {
        $venta = Venta::with('detalles')->findOrFail($id);
        return response()->json($venta, 200);
    }

    public function cobrarVenta(Request $request)
    {
        $productos = $request->productos;

        if (empty($productos)) {
            return response()->json(['message' => 'No hay productos'], 400);
        }

        $venta = null;

        try {
            DB::transaction(function () use ($productos, &$venta) {

                $venta = Venta::create([
                    'estado' => 'PAGADA',
                    'userId' => auth()->id(),
                    'fecha'  => now(),
                    'total'  => 0,
                ]);

                $total = 0;

                foreach ($productos as $item) {

                    $lotes = Lote::where('productoId', $item['productoId'])
                        ->where('cantidadDisponible', '>', 0)
                        ->orderBy('fechaIngreso', 'asc')
                        ->lockForUpdate()
                        ->get();

                    if ($lotes->sum('cantidadDisponible') < $item['cantidad']) {
                        throw new \Exception("Stock insuficiente para {$item['nombre']}");
                    }

                    $restante = $item['cantidad'];

                    foreach ($lotes as $lote) {

                        if ($restante <= 0) break;

                        $usar   = min($lote->cantidadDisponible, $restante);
                        $precio = $lote->precioVenta;

                        $lote->decrement('cantidadDisponible', $usar);

                        DetalleVenta::create([
                            'ventaId'        => $venta->id,
                            'productoId'     => $item['productoId'],
                            'loteId'         => $lote->id,
                            'nombreProducto' => $item['nombre'],
                            'precioUnitario' => $precio,
                            'cantidad'       => $usar,
                            'subtotal'       => $usar * $precio,
                        ]);

                        $total    += $usar * $precio;
                        $restante -= $usar;
                    }
                }

                $venta->update(['total' => $total]);
            });

        } catch (\Exception $e) {
            $status = str_contains($e->getMessage(), 'Stock insuficiente') ? 400 : 500;
            return response()->json(['message' => $e->getMessage()], $status);
        }

        return response()->json([
            'message' => 'Venta registrada correctamente',
            'venta'   => $venta->load('detalles')
        ]);
    }

    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {

                $venta = Venta::with('detalles')->findOrFail($id);

                // devolver stock
                foreach ($venta->detalles as $detalle) {
                    Lote::findOrFail($detalle->loteId)
                        ->increment('cantidadDisponible', $detalle->cantidad);
                }

                $venta->detalles()->delete();
                $venta->update(['estado' => 'ANULADA']);
            });

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Venta anulada']);
    }
}