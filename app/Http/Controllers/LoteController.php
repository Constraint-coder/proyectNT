<?php

namespace App\Http\Controllers;

use App\Models\Lote;
use Illuminate\Http\Request;

class LoteController extends Controller
{
    public function index()
    {
        $lotes = Lote::where('estado', 1)
            ->orderBy('fechaIngreso', 'asc') // 🔥 FIFO real
            ->get();

        return response()->json($lotes, 200);
    }

    public function store(Request $request)
    {
       try {
         $request->validate([
            'numeroLote' => 'required|string|max:255',
            'precioCompra' => 'required|numeric|min:0',
            'precioVenta' => 'required|numeric|min:0',
            'cantidadInicial' => 'required|integer|min:1',
            'productoId' => 'required|exists:productos,id',
        ]);

        $lote = Lote::create([
            'fechaIngreso' => now(),
            'numeroLote' => $request->numeroLote,
            'precioCompra' => $request->precioCompra,
            'precioVenta' => $request->precioVenta,
            

            'cantidadDisponible' => $request->cantidadInicial,
            'cantidadInicial' => $request->cantidadInicial,

            'estado' => 1,
            'productoId' => $request->productoId


        ]);

        return response()->json($lote, 201);
       } catch (\Throwable $th) {
        return response()->json(['error' => 'Error al crear el lote: ' . $th->getMessage()], 500);
       }
    }

    public function show($id)
    {
        $lote = Lote::findOrFail($id);
        return response()->json($lote, 200);
    }

    public function update(Request $request, $id)
    {
        $lote = Lote::findOrFail($id);

        $request->validate([
            'precioCompra' => 'nullable|numeric|min:0',
            'precioVenta' => 'nullable|numeric|min:0',
        ]);

        $lote->update($request->only([
            'precioCompra',
            'precioVenta'
        ]));

        return response()->json($lote, 200);
    }

    public function destroy($id)
    {
        $lote = Lote::findOrFail($id);

        $lote->update([
            'estado' => 0
        ]);

        return response()->json(['message' => 'Lote desactivado'], 200);
    }
}