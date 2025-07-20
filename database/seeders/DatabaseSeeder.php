<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Chamar seeders individuais na ordem correta
        $this->call([
            UserSeeder::class,
            CuponSeeder::class,
            ProductSeeder::class,
            StockSeeder::class,
        ]);
    }
}
