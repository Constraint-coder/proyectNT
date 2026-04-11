<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::where('estado', 1)->get();
        return response()->json($productos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:191',
        ]);

        $producto = Producto::create([
            'nombre' => $request->nombre,
            'estado' => 1,
        ]);

        return response()->json($producto, 201);
    }

    public function show(Producto $producto)
    {
        return response()->json($producto);
    }

    public function update(Request $request, Producto $producto)
    {
        $request->validate([
            'nombre' => 'required|string|max:191',
        ]);

        $producto->update([
            'nombre' => $request->nombre,
        ]);

        return response()->json($producto);
    }

    public function destroy(Producto $producto)
    {
        // Soft delete — solo cambia estado a 0
        $producto->update(['estado' => 0]);
        return response()->json(['message' => 'Producto desactivado']);
    }
}