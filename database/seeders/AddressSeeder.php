<?php

namespace Database\Seeders;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create addresses for existing users
        $users = User::whereDoesntHave('addresses')->get();
        
        foreach ($users as $user) {
            Address::create([
                'user_id' => $user->id,
                'name' => 'Casa',
                'cep' => '01234-567',
                'street' => 'Rua das Flores',
                'number' => '123',
                'complement' => 'Apto 45',
                'neighborhood' => 'Centro',
                'city' => 'São Paulo',
                'state' => 'SP',
                'is_default' => true,
            ]);
            
            // Create a second address for some users
            if ($user->id % 2 == 0) {
                Address::create([
                    'user_id' => $user->id,
                    'name' => 'Trabalho',
                    'cep' => '04567-890',
                    'street' => 'Av. Paulista',
                    'number' => '1000',
                    'complement' => 'Sala 101',
                    'neighborhood' => 'Bela Vista',
                    'city' => 'São Paulo',
                    'state' => 'SP',
                    'is_default' => false,
                ]);
            }
        }
    }
}
