<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create system users
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@minierp.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
            'is_admin' => true
        ]);

        User::create([
            'name' => 'Cliente Demo',
            'email' => 'cliente@exemplo.com',
            'password' => Hash::make('cliente123'),
            'email_verified_at' => now(),
            'is_admin' => false
        ]);
    }
}
