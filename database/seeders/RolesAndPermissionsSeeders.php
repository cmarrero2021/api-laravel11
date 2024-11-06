<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeders extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guard = 'api';
        Permission::create(['name' => 'ver usuarios', 'guard_name' => $guard]);
        Permission::create(['name' => 'mostrar usuario', 'guard_name' => $guard]);
        Permission::create(['name' => 'crear usuarios', 'guard_name' => $guard]);
        Permission::create(['name' => 'actualizar usuarios', 'guard_name' => $guard]);
        Permission::create(['name' => 'eliminar usuarios', 'guard_name' => $guard]);
        Permission::create(['name' => 'asignar rol usuarios', 'guard_name' => $guard]);
        Permission::create(['name' => 'remover rol usuarios', 'guard_name' => $guard]);
        Permission::create(['name' => 'ver permisos', 'guard_name' => $guard]);
        Permission::create(['name' => 'mostrar permiso', 'guard_name' => $guard]);
        Permission::create(['name' => 'crear permisos', 'guard_name' => $guard]);
        Permission::create(['name' => 'actualizar permisos', 'guard_name' => $guard]);
        Permission::create(['name' => 'eliminar permisos', 'guard_name' => $guard]);
        Permission::create(['name' => 'ver roles', 'guard_name' => $guard]);
        Permission::create(['name' => 'mostrar rol', 'guard_name' => $guard]);
        Permission::create(['name' => 'crear roles', 'guard_name' => $guard]);
        Permission::create(['name' => 'actualizar roles', 'guard_name' => $guard]);
        Permission::create(['name' => 'eliminar roles', 'guard_name' => $guard]);
        Permission::create(['name' => 'asignar permisos', 'guard_name' => $guard]);
        Permission::create(['name' => 'remover permisos', 'guard_name' => $guard]);

        $role = Role::create(['name' => 'usuario','guard_name' => $guard]);
        $role->givePermissionTo(['ver usuarios']);
        $role = Role::create(['name' => 'supervisor','guard_name' => $guard]);
        $role->givePermissionTo(['ver usuarios','crear usuarios', 'actualizar usuarios']);
        $adminRole = Role::create(['name' => 'admin','guard_name' => $guard]);
        $adminRole->givePermissionTo(Permission::all());

    }
}
