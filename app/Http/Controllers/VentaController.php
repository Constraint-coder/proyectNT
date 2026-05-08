<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $ventas = Venta::with('detalles')
            ->where('estado', 'ABIERTA')
            ->get();

        return response()->json($ventas, 200);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // No implementation needed for API resources.
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'userId' => 'required|exists:users,id',
        ]);

        $venta = Venta::create([
            'fecha'  => now(),
            'total'  => 0,
            'userId' => $request->userId,
            'estado' => 'ABIERTA',
        ]);

        return response()->json($venta, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $venta = Venta::with('detalles')->findOrFail($id);
        return response()->json($venta, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Venta $venta)
    {
        $request->validate([
            'total' => 'required|numeric',
        ]);

        $venta->update([
            'fecha'   => $request->fecha   ?? $venta->fecha,
            'total'   => $request->total,
            'userId'  => $request->userId  ?? $venta->userId,
            'estado'  => $request->estado  ?? $venta->estado,
        ]);

        return response()->json($venta, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Venta $venta)
    {
        $venta->update(['estado' => 'ANULADA']);
        return response()->json(['message' => 'Venta anulada'], 200);
    }
}