<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\DetalleVenta;
use App\Models\Lote;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ReporteController extends Controller
{
    /*
    -------------------------------------------------
    1. VENTAS POR MES
    -------------------------------------------------
    */
    public function ventasPorMes(Request $request)
    {
        $anio = $request->anio ?? now()->year;

        $ventas = Venta::selectRaw("
                EXTRACT(MONTH FROM fecha) as mes,
                COUNT(*) as totalVentas,
                SUM(total) as ingresos
            ")
            ->where('estado', 'PAGADA')
            ->whereYear('fecha', $anio)
            ->groupByRaw('EXTRACT(MONTH FROM fecha)')
            ->orderByRaw('EXTRACT(MONTH FROM fecha)')
            ->get()
            ->map(function ($item) {
                $item->mes = $this->nombreMes($item->mes);
                return $item;
            });

        return response()->json($ventas);
    }

public function comprasPorMes(Request $request)
{
    $anio = $request->anio ?? now()->year;

    $compras = Lote::selectRaw('
            EXTRACT(MONTH FROM "fechaIngreso") as mes,
            COUNT(*) as totalLotes,
            SUM("cantidadInicial") as unidadesCompradas,
            SUM("cantidadInicial" * "precioCompra") as totalInvertido
        ')
        ->whereYear('fechaIngreso', $anio)
        ->groupByRaw('EXTRACT(MONTH FROM "fechaIngreso")')
        ->orderByRaw('EXTRACT(MONTH FROM "fechaIngreso")')
        ->get()
        ->map(function ($item) {
            $item->mes = $this->nombreMes($item->mes);
            return $item;
        });

    return response()->json($compras);
}
    
public function stockPorProducto()
{
    $stock = Lote::selectRaw('
            "productoId",
            SUM("cantidadDisponible") as "stockTotal"
        ')
        ->whereNotNull('productoId')
        ->where('cantidadDisponible', '>', 0)
        ->groupBy('productoId')
        ->orderBy('stockTotal', 'asc')
        ->get()
        ->map(function ($item) {
            $producto = \App\Models\Producto::find($item->productoId);
            return [
                'producto'   => $producto?->nombre ?? 'Sin producto',
                'stockTotal' => $item->stockTotal,
                'stockBajo'  => $item->stockTotal < 10,
            ];
        });

    return response()->json($stock);
}

    /*
    -------------------------------------------------
    HELPER
    -------------------------------------------------
    */
    private function nombreMes($numero)
    {
        $meses = [
            1  => 'Enero',    2  => 'Febrero',   3  => 'Marzo',
            4  => 'Abril',    5  => 'Mayo',       6  => 'Junio',
            7  => 'Julio',    8  => 'Agosto',     9  => 'Septiembre',
            10 => 'Octubre',  11 => 'Noviembre',  12 => 'Diciembre'
        ];

        return $meses[(int)$numero] ?? $numero;
    }
}