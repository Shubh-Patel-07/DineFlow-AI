<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $email = env('SUPERADMIN_EMAIL', 'superadmin@dineflowai.duckdns.org');
        $password = env('SUPERADMIN_PASSWORD', 'SuperAdmin@DineFlow2026!');
        $name = env('SUPERADMIN_NAME', 'DineFlow Super Admin');
        $username = env('SUPERADMIN_USERNAME', 'superadmin');

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'username' => $username,
                'password' => Hash::make($password),
                'role' => 'superadmin',
                'restaurant_id' => null,
            ]
        );
    }
}
