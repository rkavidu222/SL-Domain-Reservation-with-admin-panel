<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Admin;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Admin::create([
        'name' => 'Super Admin',
        'email' => 'kavi@gmail.com',
        'password' => Hash::make('Kavi@7878'),
        'role' => 'super_admin',
]);

    }
}
