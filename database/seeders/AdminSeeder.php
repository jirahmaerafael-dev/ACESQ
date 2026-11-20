<?php

namespace Database\Seeders;

use App\Models\StaffUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $username = trim((string) config('acesq.admin_username', 'admin'));
        $password = config('acesq.admin_password');

        if ($username === '' || ! is_string($password) || $password === '') {
            return;
        }

        if (mb_strlen($password) < 8) {
            throw new InvalidArgumentException('ACESQ_ADMIN_PASSWORD must contain at least 8 characters.');
        }

        StaffUser::query()->updateOrCreate(
            ['username' => $username],
            [
                'full_name' => 'System Administrator',
                'window_id' => null,
                'role' => 'admin',
                'password_hash' => Hash::make($password),
            ]
        );
    }
}
