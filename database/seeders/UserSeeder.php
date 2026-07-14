<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run(): void
    {
        // Create admin user
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin User',
                'email' => 'admin@restopos.com',
                'password' => Hash::make('password'),
                'numeric_pin' => '1234',
                'role' => 'admin',
                'restaurant_id' => '1',
            ]
        );

        // Create manager user
        User::updateOrCreate(
            ['username' => 'manager'],
            [
                'name' => 'Manager',
                'email' => 'manager@restopos.com',
                'password' => Hash::make('password'),
                'numeric_pin' => '5678',
                'role' => 'manager',
                'restaurant_id' => '1',
            ]
        );

        // Create cashier user
        User::updateOrCreate(
            ['username' => 'cashier'],
            [
                'name' => 'Cashier',
                'email' => 'cashier@restopos.com',
                'password' => Hash::make('password'),
                'numeric_pin' => '9999',
                'role' => 'cashier',
                'restaurant_id' => '1',
            ]
        );

        // Create kitchen user
        User::updateOrCreate(
            ['username' => 'kitchen'],
            [
                'name' => 'Head Chef',
                'email' => 'chef@restopos.com',
                'password' => Hash::make('password'),
                'numeric_pin' => '2580',
                'role' => 'kitchen',
                'restaurant_id' => '1',
            ]
        );

        // Create waiter user
        User::updateOrCreate(
            ['username' => 'waiter'],
            [
                'name' => 'Waiter John',
                'email' => 'waiter@restopos.com',
                'password' => Hash::make('password'),
                'numeric_pin' => '3690',
                'role' => 'waiter',
                'restaurant_id' => '1',
            ]
        );
    }
}
