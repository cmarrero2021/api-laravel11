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
        Permission::create(['name' => 'crear usuarios','guard_name' => $guard]);
        Permission::create(['name' => 'editar usuarios','guard_name' => $guard]);
        Permission::create(['name' => 'eliminar usuarios','guard_name' => $guard]);

        $role = Role::create(['name' => 'usuario','guard_name' => $guard]);
        $role->givePermissionTo(['ver usuarios']);
        $role = Role::create(['name' => 'supervisor','guard_name' => $guard]);
        $role->givePermissionTo(['ver usuarios','crear usuarios', 'editar usuarios']);
        $adminRole = Role::create(['name' => 'admin','guard_name' => $guard]);
        $adminRole->givePermissionTo(Permission::all());

    }
}
