<?php

namespace App\Http\Controllers\Api;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;


class UserController extends Controller
{
    public function index()
    {
       return response()->json(User::with('rol')->get());
    }

    public function store(UserRequest $request)
    {
        $user = User::create($request->validated());
        return response()->json($user, 201);
    }

    public function show(User $user)
    {
        return response()->json($user);
    }

    public function update(UserRequest $request, User $user)
    {
        $user->update($request->validated());
        return response()->json($user);
    }

    public function destroy(User $user)
    {
        $user->delete();
        return response()->json(['message' => 'Usuario eliminado']);
    }
}