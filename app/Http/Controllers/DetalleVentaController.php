<?php

namespace App\Http\Controllers;

use App\Models\DetalleVenta;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Lote;

class DetalleVentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       try {
         $detalles = DetalleVenta::with('venta')->get();

        return response()->json($detalles, 200);
       } catch (\Throwable $th) {
        return response()->json(['error' => 'Error al obtener los detalles de venta: ' . $th->getMessage()], 500);
       }
    }


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

            // si aumenta cantidad → validar stock disponible
            if ($diferencia < 0 && $lote->cantidadDisponible < abs($diferencia)) {
                throw new \Exception('Stock insuficiente');
            }

            // ajustar stock
            $lote->increment('cantidadDisponible', $diferencia); // + si reduce, - si aumenta

            // ajustar detalle
            $detalle->cantidad = $nuevaCantidad;
            $detalle->subtotal = $nuevaCantidad * $detalle->precioUnitario;
            $detalle->save();

            // ajustar total de la venta
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
        'detalle' => DetalleVenta::find($id)
    ]);
}

    public function show($id)
    {
        try {
            $detalleventa = DetalleVenta::with('venta')->findOrFail($id);
            return response()->json($detalleventa, 200);
        } catch (\Throwable $th) {
            return response()->json(['error' => 'Error al obtener el detalle de venta: ' . $th->getMessage()], 500);
        }
    }

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
