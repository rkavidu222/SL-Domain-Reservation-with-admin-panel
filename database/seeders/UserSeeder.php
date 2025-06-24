<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;  // make sure User model is imported

class UserSeeder extends Seeder
{
    public function run()
    {
        // Example user 1
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('password123'), // secure password
            'remember_token' => Str::random(10),
        ]);

        // Example user 2
        User::create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('secret456'),
            'remember_token' => Str::random(10),
        ]);

        // Example user 3
        User::create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com',
            'email_verified_at' => now(),
            'password' => Hash::make('mypassword'),
            'remember_token' => Str::random(10),
        ]);
    }
}
