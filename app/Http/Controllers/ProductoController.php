<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::all();
        return response()->json($productos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:191',
            'estado'=>'required|numeric'
        ]);

        $producto = Producto::create([
            'nombre' => $request->nombre,
            'estado' => $request->estado,
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
            'estado'=>'required|boolean'
        ]);

        $producto->update([
            'nombre' => $request->nombre,
            'estado' => $request->estado
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