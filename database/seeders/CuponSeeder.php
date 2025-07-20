<?php

namespace Database\Seeders;

use App\Models\Cupon;
use Illuminate\Database\Seeder;

class CuponSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar cupons de exemplo
        Cupon::create([
            'code' => 'DESCONTO10',
            'name' => 'Desconto de 10%',
            'type' => 'percentage',
            'discount' => 10,
            'minimum_amount' => 50,
            'valid_from' => now(),
            'valid_until' => now()->addMonth(),
            'usage_limit' => 100,
            'active' => true
        ]);

        Cupon::create([
            'code' => 'FRETE15',
            'name' => 'R$ 15 de desconto',
            'type' => 'fixed',
            'discount' => 15,
            'minimum_amount' => 0,
            'valid_from' => now(),
            'valid_until' => now()->addWeek(),
            'usage_limit' => 50,
            'active' => true
        ]);

        Cupon::create([
            'code' => 'BEMVINDO',
            'name' => 'Bem-vindo! 20% de desconto',
            'type' => 'percentage',
            'discount' => 20,
            'minimum_amount' => 100,
            'valid_from' => now(),
            'valid_until' => now()->addDays(30),
            'usage_limit' => 20,
            'active' => true
        ]);
    }
}
