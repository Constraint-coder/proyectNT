<?php

namespace App\Http\Controllers;

use App\Models\CodigoBarra;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CodigoBarraController extends Controller
{
    public function index()
    {
        $codigos = CodigoBarra::with('producto')->where('estado', 1)->get();
        return response()->json($codigos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'codigoBarra' => 'required|string|max:191|unique:codigo_barras,codigoBarra',
            'productoId'  => 'required|exists:productos,id',
        ]);

        $codigo = CodigoBarra::create([
            'codigoBarra' => $request->codigoBarra,
            'productoId'  => $request->productoId,
            'estado'      => 1,
        ]);

        return response()->json($codigo, 201);
    }

    public function show(CodigoBarra $codigoBarra)
    {
        return response()->json($codigoBarra->load('producto'));
    }

    public function destroy(CodigoBarra $codigoBarra)
    {
        $codigoBarra->update(['estado' => 0]);
        return response()->json(['message' => 'Código de barra desactivado']);
    }
}