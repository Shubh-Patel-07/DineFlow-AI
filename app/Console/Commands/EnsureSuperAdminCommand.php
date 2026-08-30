<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EnsureSuperAdminCommand extends Command
{
    protected $signature = 'superadmin:ensure {--email= : Optional email override} {--password= : Optional password override}';
    protected $description = 'Ensure the platform Super Administrator account exists and is active';

    public function handle(): int
    {
        $email = $this->option('email') ?: env('SUPERADMIN_EMAIL', 'superadmin@tendorabuy.in');
        $password = $this->option('password') ?: env('SUPERADMIN_PASSWORD', 'SuperAdmin@Tendorabuy2026!');
        $name = env('SUPERADMIN_NAME', 'Tendorabuy Super Admin');
        $username = env('SUPERADMIN_USERNAME', 'superadmin');

        $user = User::where('email', $email)->orWhere('username', $username)->first();

        if ($user) {
            $user->name = $name;
            $user->username = $username;
            $user->email = $email;
            $user->role = 'superadmin';
            $user->restaurant_id = null;
            $user->is_active = true;
            if ($this->option('password')) {
                $user->password = Hash::make($password);
            }
            $user->save();

            $this->info("Super Admin account verified and active: {$email} (Role: superadmin)");
        } else {
            User::create([
                'name'          => $name,
                'username'      => $username,
                'email'         => $email,
                'password'      => Hash::make($password),
                'numeric_pin'   => Hash::make('9999'),
                'role'          => 'superadmin',
                'restaurant_id' => null,
                'is_active'     => true,
            ]);

            $this->info("Super Admin account created successfully: {$email} (Role: superadmin)");
        }

        return 0;
    }
}
