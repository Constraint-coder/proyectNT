<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Crear permisos
        $permisos = [
            'ver usuarios', 'crear usuarios', 'editar usuarios', 'eliminar usuarios',
            'ver roles', 'crear roles', 'editar roles', 'eliminar roles',
            'ver productos', 'crear productos', 'editar productos', 'eliminar productos',
            'ver codigos', 'crear codigos', 'editar codigos', 'eliminar codigos',
            'ver lotes', 'crear lotes', 'editar lotes', 'eliminar lotes',
            'ver ventas', 'crear ventas',
            'ver detalleventa', 'editar detalleventa', 'eliminar detalleventa',
            'usar pos', 'ver reportes',
        ];

        foreach ($permisos as $permiso) {
            Permission::firstOrCreate([
                'name'       => $permiso,
                'guard_name' => 'api',
            ]);
        }

        // Crear rol admin con todos los permisos
        $rolAdmin = Role::firstOrCreate([
            'name'       => 'admin',
            'guard_name' => 'api',
        ]);

        $rolAdmin->syncPermissions($permisos);

        // Crear usuario admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'nombre'   => 'Admin',
                'password' => Hash::make('password'),
                'estado'   => true,
            ]
        );

        $admin->assignRole($rolAdmin);
    }
}