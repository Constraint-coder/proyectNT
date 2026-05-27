<?php

namespace App\Http\Controllers\Api;

use app\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;

class UserController extends Controller
{
public function index()
{
    return response()->json(
        User::with('roles', 'permissions')->get()

    );
}

public function store(UserRequest $request)
{
    $user = User::create([
        'nombre' => $request->nombre,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'estado' => $request->estado,
    ]);

    if ($request->roles) {
        $user->syncRoles($request->roles);
    }

    return response()->json($user->load('roles', 'permissions'), 201);
}
    public function show(User $user)
    {
        return response()->json(
            $user->load('roles')
        );
    }

    public function update(UserRequest $request, User $user)
    {
        $validated = $request->validated();

        $user->update([
            'nombre' => $validated['nombre'],
            'email' => $validated['email'],
            'estado' => $validated['estado'],
        ]);

        // actualizar password solo si viene
        if (!empty($validated['password'])) {
            $user->update([
                'password' => bcrypt($validated['password'])
            ]);
        }

        // sincronizar roles
        if (isset($validated['role'])) {
            $user->syncRoles([$validated['role']]);
        }

        return response()->json(
            $user->load('roles')
        );
    }

public function destroy(User $user)
{
    $user->update(['estado' => false]);

    return response()->json([
        'message' => 'Usuario desactivado'
    ]);
}
}