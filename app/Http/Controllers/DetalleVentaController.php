<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Lote;
use App\Models\Venta;
use App\Models\Producto; 

class DetalleVentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            
            $detalles = DetalleVenta::with(['venta', 'producto', 'lote'])->get();

            return response()->json($detalles, 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error al obtener los detalles de venta: ' . $th->getMessage()], 500);
        }
    }


    public function store(Request $request)
    {
        $request->validate([
            'ventaId'   => 'required|exists:ventas,id',
            'productoId'=> 'required|exists:productos,id',
            'loteId'    => 'required|exists:lotes,id',
            'cantidad'  => 'required|integer|min:1',
        ]);

        try {
            $detalle = DB::transaction(function () use ($request) {

                $venta   = Venta::lockForUpdate()->findOrFail($request->ventaId);
                $lote    = Lote::lockForUpdate()->findOrFail($request->loteId);
                $producto = Producto::findOrFail($request->productoId);

                // Validar que el lote tiene stock suficiente
                if ($lote->cantidadDisponible < $request->cantidad) {
                    throw new \Exception('Stock insuficiente');
                }

                // Validar que la venta esté abierta
                if ($venta->estado !== 'ABIERTA') {
                    throw new \Exception('La venta no está abierta');
                }

                $precioUnitario = $lote->precioVenta; // campo real en tabla lotes
                $subtotal       = $request->cantidad * $precioUnitario;

                // Crear el detalle
                $detalle = DetalleVenta::create([
                    'ventaId'        => $venta->id,
                    'productoId'     => $producto->id,
                    'loteId'         => $lote->id,
                    'nombreProducto' => $producto->nombre,
                    'cantidad'       => $request->cantidad,
                    'precioUnitario' => $precioUnitario,
                    'subtotal'       => $subtotal,
                ]);

                // Descontar stock del lote
                $lote->decrement('cantidadDisponible', $request->cantidad);

                // Sumar al total de la venta
                $venta->increment('total', $subtotal);

                return $detalle;
            });

            return response()->json([
                'message' => 'Producto agregado a la venta',
                'detalle' => $detalle->load(['venta', 'producto', 'lote'])
            ], 201);

        } catch (\Exception $e) {
            $status = in_array($e->getMessage(), [
                'Stock insuficiente',
                'La venta no está abierta'
            ]) ? 400 : 500;

            return response()->json(['message' => $e->getMessage()], $status);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $detalleventa = DetalleVenta::with(['venta', 'producto', 'lote'])->findOrFail($id);
            return response()->json($detalleventa, 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error al obtener el detalle de venta: ' . $th->getMessage()], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $nuevaCantidad = $request->cantidad;

        if ($nuevaCantidad <= 0) {
            return response()->json(['message' => 'Cantidad inválida'], 400);
        }

        try {
            DB::transaction(function () use ($id, $nuevaCantidad) {

                $detalle = DetalleVenta::lockForUpdate()->findOrFail($id);
                $lote    = Lote::lockForUpdate()->findOrFail($detalle->loteId);

                $diferencia = $detalle->cantidad - $nuevaCantidad;

                if ($diferencia === 0) {
                    throw new \Exception('No hay cambios');
                }

                // Si aumenta cantidad → validar stock disponible
                if ($diferencia < 0 && $lote->cantidadDisponible < abs($diferencia)) {
                    throw new \Exception('Stock insuficiente');
                }

                // Ajustar stock
                $lote->increment('cantidadDisponible', $diferencia);

                // Ajustar detalle
                $detalle->cantidad = $nuevaCantidad;
                $detalle->subtotal = $nuevaCantidad * $detalle->precioUnitario;
                $detalle->save();

                // Ajustar total de la venta
                $montoAjuste = $diferencia * $detalle->precioUnitario;
                $detalle->venta->increment('total', -$montoAjuste);
            });

        } catch (\Exception $e) {
            $status = in_array($e->getMessage(), [
                'No hay cambios',
                'Stock insuficiente'
            ]) ? 400 : 500;

            return response()->json(['message' => $e->getMessage()], $status);
        }

        return response()->json([
            'message' => 'Detalle actualizado',
            'detalle' => DetalleVenta::with(['venta', 'producto', 'lote'])->find($id)
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {

                $detalle = DetalleVenta::lockForUpdate()->findOrFail($id);

                Lote::lockForUpdate()
                    ->findOrFail($detalle->loteId)
                    ->increment('cantidadDisponible', $detalle->cantidad);

                $detalle->venta->decrement('total', $detalle->subtotal);

                $detalle->delete();
            });

        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Producto eliminado y stock revertido']);
    }
}
