<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Stock;
use Illuminate\Database\Seeder;

class StockSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar produtos para criar estoque
        $camiseta = Product::where('name', 'Camiseta Básica Premium')->first();
        $tenis = Product::where('name', 'Tênis Esportivo Performance')->first();
        $mochila = Product::where('name', 'Mochila Executiva Couro')->first();
        $smartwatch = Product::where('name', 'Smartwatch Fitness Pro')->first();
        $fone = Product::where('name', 'Fone de Ouvido Bluetooth')->first();
        $jaqueta = Product::where('name', 'Jaqueta Jeans Masculina')->first();
        $caderno = Product::where('name', 'Caderno Moleskine Profissional')->first();
        $oculos = Product::where('name', 'Óculos de Sol Aviador')->first();

        // Variations for T-Shirt
        if ($camiseta) {
            Stock::create([
                'product_id' => $camiseta->id,
                'variation_name' => 'Tamanho',
                'variation_value' => 'P',
                'quantity' => 15,
                'price_adjustment' => 0
            ]);

            Stock::create([
                'product_id' => $camiseta->id,
                'variation_name' => 'Tamanho',
                'variation_value' => 'M',
                'quantity' => 25,
                'price_adjustment' => 0
            ]);

            Stock::create([
                'product_id' => $camiseta->id,
                'variation_name' => 'Tamanho',
                'variation_value' => 'G',
                'quantity' => 20,
                'price_adjustment' => 5
            ]);

            Stock::create([
                'product_id' => $camiseta->id,
                'variation_name' => 'Tamanho',
                'variation_value' => 'GG',
                'quantity' => 10,
                'price_adjustment' => 10
            ]);
        }

        // Variations for Sneakers
        if ($tenis) {
            Stock::create([
                'product_id' => $tenis->id,
                'variation_name' => 'Tamanho',
                'variation_value' => '38',
                'quantity' => 8,
                'price_adjustment' => 0
            ]);

            Stock::create([
                'product_id' => $tenis->id,
                'variation_name' => 'Tamanho',
                'variation_value' => '40',
                'quantity' => 12,
                'price_adjustment' => 0
            ]);

            Stock::create([
                'product_id' => $tenis->id,
                'variation_name' => 'Tamanho',
                'variation_value' => '42',
                'quantity' => 10,
                'price_adjustment' => 10
            ]);

            Stock::create([
                'product_id' => $tenis->id,
                'variation_name' => 'Tamanho',
                'variation_value' => '44',
                'quantity' => 5,
                'price_adjustment' => 15
            ]);
        }

        // Product without variations - Backpack
        if ($mochila) {
            Stock::create([
                'product_id' => $mochila->id,
                'quantity' => 30,
                'price_adjustment' => 0
            ]);
        }

        // Variations for Smartwatch
        if ($smartwatch) {
            Stock::create([
                'product_id' => $smartwatch->id,
                'variation_name' => 'Cor',
                'variation_value' => 'Preto',
                'quantity' => 15,
                'price_adjustment' => 0
            ]);

            Stock::create([
                'product_id' => $smartwatch->id,
                'variation_name' => 'Cor',
                'variation_value' => 'Branco',
                'quantity' => 12,
                'price_adjustment' => 20
            ]);

            Stock::create([
                'product_id' => $smartwatch->id,
                'variation_name' => 'Cor',
                'variation_value' => 'Rosa Gold',
                'quantity' => 8,
                'price_adjustment' => 50
            ]);
        }

        // Product without variations - Headphones
        if ($fone) {
            Stock::create([
                'product_id' => $fone->id,
                'quantity' => 45,
                'price_adjustment' => 0
            ]);
        }

        // Variations for Jacket
        if ($jaqueta) {
            Stock::create([
                'product_id' => $jaqueta->id,
                'variation_name' => 'Tamanho',
                'variation_value' => 'M',
                'quantity' => 18,
                'price_adjustment' => 0
            ]);

            Stock::create([
                'product_id' => $jaqueta->id,
                'variation_name' => 'Tamanho',
                'variation_value' => 'G',
                'quantity' => 22,
                'price_adjustment' => 0
            ]);

            Stock::create([
                'product_id' => $jaqueta->id,
                'variation_name' => 'Tamanho',
                'variation_value' => 'GG',
                'quantity' => 15,
                'price_adjustment' => 10
            ]);
        }

        // Variations for Notebook
        if ($caderno) {
            Stock::create([
                'product_id' => $caderno->id,
                'variation_name' => 'Cor',
                'variation_value' => 'Preto',
                'quantity' => 25,
                'price_adjustment' => 0
            ]);

            Stock::create([
                'product_id' => $caderno->id,
                'variation_name' => 'Cor',
                'variation_value' => 'Azul',
                'quantity' => 20,
                'price_adjustment' => 5
            ]);

            Stock::create([
                'product_id' => $caderno->id,
                'variation_name' => 'Cor',
                'variation_value' => 'Vermelho',
                'quantity' => 15,
                'price_adjustment' => 5
            ]);
        }

        // Product without variations - Sunglasses
        if ($oculos) {
            Stock::create([
                'product_id' => $oculos->id,
                'quantity' => 35,
                'price_adjustment' => 0
            ]);
        }
    }
}
