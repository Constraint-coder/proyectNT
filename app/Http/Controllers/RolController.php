<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\RolRequest;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolController extends Controller
{
    public function index()
    {
       $roles = Role::with('permissions')->get();
       return response()->json($roles);
    }

    public function store(RolRequest $request)
    {
        $role = Role::create([
            'name' => $request->name,
            'guard_name' => 'api', 
        ]);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return response()->json($role->load('permissions'), 201);
    }

    public function show(Role $role)
    {
        return $role->load('permissions');
    }

    public function update(RolRequest $request, Role $role)
    {
        $role->update([
            'name' => $request->name,
        ]);

        if ($request->permissions) {
            $role->syncPermissions($request->permissions);
        }

        return $role->load('permissions');
    }
public function destroy(Role $role)
{
    $role->delete();

    return response()->json(['message' => 'Rol eliminado']);
}
}