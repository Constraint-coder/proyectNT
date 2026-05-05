<?php
namespace App\Http\Controllers;

use App\Models\Precio;
use Illuminate\Http\Request;

class PrecioController extends Controller
{
    public function index()
    {
        return Precio::with('producto')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'productoId' => 'required|exists:productos,id',
            'precioVenta' => 'required|numeric|min:0'
        ]);

        $precio = Precio::create([
            'productoId' => $request->productoId,
            'precioVenta' => $request->precioVenta
        ]);

        return response()->json($precio, 201);
    }

    public function show($id)
    {
        return Precio::findOrFail($id);
    }

    public function update(Request $request, $id)
    {
        $precio = Precio::findOrFail($id);

        $precio->update($request->only('precioVenta'));

        return response()->json($precio);
    }

    public function destroy($id)
    {
        Precio::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Precio eliminado'
        ]);
    }
}