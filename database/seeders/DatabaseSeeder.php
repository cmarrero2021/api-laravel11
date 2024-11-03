<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// use Database\Seeders\RolesAndPermissionsSeeder;
// use Database\Seeders\CreateAdminUserSeeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RolesAndPermissionsSeeders::class,
            CreateAdminUserSeeder::class,
        ]);
    }
}
