<?php

namespace App\Http\Controllers;

use App\Models\CodigoBarra;
use App\Models\Venta;
use App\Models\Lote;
use App\Models\DetalleVenta;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ScanController extends Controller
{
    public function scan($codigo, Request $request)
    {
        $codigoBarra = CodigoBarra::with('producto')
            ->where('codigoBarra', $codigo)
            ->firstOrFail();

        $producto = $codigoBarra->producto;
        $cantidad = $request->cantidad ?? 1;
        $venta    = null;
        $total    = 0;

        try {
            DB::transaction(function () use ($producto, $cantidad, &$venta, &$total) {

                $venta = Venta::firstOrCreate(
                    [
                        'estado' => 'ABIERTA',
                        'userId' => auth()->id()
                    ],
                    [
                        'fecha' => now(),
                        'total' => 0
                    ]
                );

                $lotes = Lote::where('productoId', $producto->id)
                    ->where('cantidadDisponible', '>', 0)
                    ->orderBy('fechaIngreso', 'asc')
                    ->lockForUpdate()
                    ->get();

                if ($lotes->sum('cantidadDisponible') < $cantidad) {
                    throw new \Exception('Stock insuficiente');
                }

                $restante = $cantidad;

                foreach ($lotes as $lote) {

                    if ($restante <= 0) break;

                    $usar   = min($lote->cantidadDisponible, $restante);
                    $precio = $lote->precioVenta;

                    $lote->decrement('cantidadDisponible', $usar);

                    $detalle = DetalleVenta::where('ventaId', $venta->id)
                        ->where('productoId', $producto->id)
                        ->where('loteId', $lote->id)
                        ->first();

                    if ($detalle) {
                        $nuevaCantidad = $detalle->cantidad + $usar;
                        $detalle->update([
                            'cantidad' => $nuevaCantidad,
                            'subtotal' => $nuevaCantidad * $precio,
                        ]);
                    } else {
                        DetalleVenta::create([
                            'ventaId'        => $venta->id,
                            'productoId'     => $producto->id,
                            'loteId'         => $lote->id,
                            'nombreProducto' => $producto->nombre,
                            'precioUnitario' => $precio,
                            'costoUnitario'  => $lote->costoCompra,
                            'cantidad'       => $usar,
                            'subtotal'       => $usar * $precio,
                        ]);
                    }

                    $total    += $usar * $precio;
                    $restante -= $usar;
                }

                $venta->increment('total', $total);
                $venta->refresh();
            });

        } catch (\Exception $e) {
            $status = $e->getMessage() === 'Stock insuficiente' ? 400 : 500;
            return response()->json(['message' => $e->getMessage()], $status);
        }

        return response()->json([
            'venta' => $venta->load('detalles'),
            'total' => $venta->total
        ]);
    }

    public function eliminarProducto($ventaId, $productoId)
    {
        try {
            DB::transaction(function () use ($productoId, $ventaId) {

                $venta = Venta::where('id', $ventaId)
                    ->where('estado', 'ABIERTA')
                    ->where('userId', auth()->id())
                    ->lockForUpdate()
                    ->firstOrFail();

                $detalles = DetalleVenta::where('ventaId', $ventaId)
                    ->where('productoId', $productoId)
                    ->lockForUpdate()
                    ->get();

                if ($detalles->isEmpty()) {
                    throw new \Exception('Producto no encontrado en la venta');
                }

                $totalDevuelto = $detalles->sum(function ($detalle) {
                    Lote::findOrFail($detalle->loteId)
                        ->increment('cantidadDisponible', $detalle->cantidad);
                    return $detalle->cantidad * $detalle->precioUnitario;
                });

                DetalleVenta::where('ventaId', $ventaId)
                    ->where('productoId', $productoId)
                    ->delete();

                $venta->decrement('total', $totalDevuelto);
            });

        } catch (\Exception $e) {
            $status = $e->getMessage() === 'Producto no encontrado en la venta' ? 404 : 500;
            return response()->json(['message' => $e->getMessage()], $status);
        }

        return response()->json(['message' => 'Producto eliminado de la venta']);
    }

    public function cancelarVenta($ventaId)
    {
        try {
            DB::transaction(function () use ($ventaId) {

                $venta = Venta::where('id', $ventaId)
                    ->where('estado', 'ABIERTA')
                    ->where('userId', auth()->id())
                    ->lockForUpdate()
                    ->firstOrFail();

                $detalles = DetalleVenta::where('ventaId', $ventaId)
                    ->lockForUpdate()
                    ->get();

                $detalles->each(function ($detalle) {
                    Lote::findOrFail($detalle->loteId)
                        ->increment('cantidadDisponible', $detalle->cantidad);
                });

                DetalleVenta::where('ventaId', $ventaId)->delete();

                $venta->update([
                    'estado' => 'ANULADA',
                    'total'  => 0
                ]);
            });

        } catch (\Exception $e) {
            $status = $e->getCode() === 404 ? 404 : 500;
            return response()->json(['message' => $e->getMessage()], $status);
        }

        return response()->json(['message' => 'Venta cancelada correctamente']);
    }

    public function cobrarVenta($ventaId)
    {
        try {
            DB::transaction(function () use ($ventaId) {

                $venta = Venta::where('id', $ventaId)
                    ->where('estado', 'ABIERTA')
                    ->where('userId', auth()->id())
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($venta->total <= 0) {
                    throw new \Exception('La venta no tiene productos');
                }

                $venta->update([
                    'estado' => 'PAGADA',
                    'fecha'  => now()
                ]);
            });

        } catch (\Exception $e) {
            $status = $e->getMessage() === 'La venta no tiene productos' ? 400 : 500;
            return response()->json(['message' => $e->getMessage()], $status);
        }

        return response()->json(['message' => 'Venta cobrada correctamente']);
    }
}