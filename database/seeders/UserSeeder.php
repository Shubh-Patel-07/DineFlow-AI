<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Create superadmin user
        $superEmail = env('SUPERADMIN_EMAIL', 'superadmin@tendorabuy.in');
        $superPass = env('SUPERADMIN_PASSWORD', 'SuperAdmin@Tendorabuy2026!');
        User::updateOrCreate(
            ['email' => $superEmail],
            [
                'username' => 'superadmin',
                'name' => 'Tendorabuy Super Admin',
                'password' => Hash::make($superPass),
                'role' => 'superadmin',
                'restaurant_id' => null,
                'is_active' => true,
            ]
        );

        // Create admin user
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin User',
                'email' => 'admin@dineflow.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'restaurant_id' => '1',
                'is_active' => true,
            ]
        );

        // Create manager user
        User::updateOrCreate(
            ['username' => 'manager'],
            [
                'name' => 'Manager',
                'email' => 'manager@dineflow.com',
                'password' => Hash::make('password'),
                'role' => 'manager',
                'restaurant_id' => '1',
                'is_active' => true,
            ]
        );

        // Create cashier user
        User::updateOrCreate(
            ['username' => 'cashier'],
            [
                'name' => 'Cashier',
                'email' => 'cashier@dineflow.com',
                'password' => Hash::make('password'),
                'role' => 'cashier',
                'restaurant_id' => '1',
                'is_active' => true,
            ]
        );

        // Create kitchen user
        User::updateOrCreate(
            ['username' => 'kitchen'],
            [
                'name' => 'Head Chef',
                'email' => 'chef@dineflow.com',
                'password' => Hash::make('password'),
                'role' => 'kitchen',
                'restaurant_id' => '1',
                'is_active' => true,
            ]
        );

        // Create waiter user
        User::updateOrCreate(
            ['username' => 'waiter'],
            [
                'name' => 'Waiter John',
                'email' => 'waiter@dineflow.com',
                'password' => Hash::make('password'),
                'role' => 'waiter',
                'restaurant_id' => '1',
                'is_active' => true,
            ]
        );
    }
}
