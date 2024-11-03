<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class CreateAdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::create([
            'name' => 'Admin',
            'email' => 'admin@correo.com',
            'password' => bcrypt('123'),
            'email_verified_at' => '2024-11-03 18:03:56'
        ]);
        $role = Role::findByName('admin', 'api');
        $user->assignRole($role);
    }
}
