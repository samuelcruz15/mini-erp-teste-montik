<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserProfile;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserProfileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create profiles for existing users
        $users = User::whereDoesntHave('profile')->get();
        
        foreach ($users as $user) {
            UserProfile::create([
                'user_id' => $user->id,
                'full_name' => $user->name,
                'phone' => '(11) 99999-9999',
                'cpf' => '12345678901',
                'birth_date' => '1990-01-01',
                'gender' => 'M',
            ]);
        }
    }
}
