<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use App\Http\Controllers\Controller;
use App\Http\Requests\RolRequest;

class RolController extends Controller
{
    public function index()
    {
       return response()->json(Rol::all());
    }

    public function store(RolRequest $request)
    {
        $rol = Rol::create($request->validated());
        return response()->json($rol, 201);
    }

    public function show(Rol $rol)
    {
        return response()->json($rol);
    }

    public function update(RolRequest $request, Rol $rol)
    {
        $rol->update($request->validated());
        return response()->json($rol);
    }

    public function destroy(Rol $rol)
    {
        $rol->delete();
        return response()->json(['message' => 'Rol eliminado']);
    }
}