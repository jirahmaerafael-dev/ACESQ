<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        DB::table('roles')->insertOrIgnore([
            [
                'RoleID' => 1,
                'name' => 'Admin',
                'description' => 'Administrator with full system access.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'RoleID' => 2,
                'name' => 'Registrar',
                'description' => 'Registered user with limited access.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'RoleID' => 3,
                'name' => 'Cashier',
                'description' => 'Cashier responsible for handling transactions.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);
    }
}
