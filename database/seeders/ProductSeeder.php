<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create sample products with specific real images
        Product::create([
            'name' => 'Camiseta Básica Premium',
            'price' => 29.90,
            'description' => 'Camiseta de algodão 100% orgânico, macia e confortável. Corte moderno e acabamento de qualidade superior.',
            'image' => 'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80',
            'has_variations' => true,
            'active' => true
        ]);

        Product::create([
            'name' => 'Tênis Esportivo Performance',
            'price' => 149.90,
            'description' => 'Tênis para corrida e atividades esportivas. Tecnologia de amortecimento avançada e design moderno.',
            'image' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80',
            'has_variations' => true,
            'active' => true
        ]);

        Product::create([
            'name' => 'Mochila Executiva Couro',
            'price' => 189.90,
            'description' => 'Mochila de couro sintético de alta qualidade. Compartimentos organizadores e design elegante para o trabalho.',
            'image' => 'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80',
            'has_variations' => false,
            'active' => true
        ]);

        Product::create([
            'name' => 'Smartwatch Fitness Pro',
            'price' => 299.90,
            'description' => 'Relógio inteligente com monitor cardíaco, GPS integrado e resistência à água. Bateria de longa duração.',
            'image' => 'https://images.unsplash.com/photo-1523275335684-37898b6baf30?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80',
            'has_variations' => true,
            'active' => true
        ]);

        Product::create([
            'name' => 'Fone de Ouvido Bluetooth',
            'price' => 79.90,
            'description' => 'Fone de ouvido sem fio com cancelamento de ruído ativo. Som de alta qualidade e conforto superior.',
            'image' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80',
            'has_variations' => false,
            'active' => true
        ]);

        Product::create([
            'name' => 'Jaqueta Jeans Masculina',
            'price' => 119.90,
            'description' => 'Jaqueta jeans clássica com corte moderno. Material resistente e design atemporal para todas as ocasiões.',
            'image' => 'https://images.unsplash.com/photo-1551698618-1dfe5d97d256?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80',
            'has_variations' => true,
            'active' => true
        ]);

        Product::create([
            'name' => 'Caderno Moleskine Profissional',
            'price' => 45.90,
            'description' => 'Caderno de anotações premium com capa dura e páginas pautadas. Ideal para profissionais e estudantes.',
            'image' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80',
            'has_variations' => true,
            'active' => true
        ]);

        Product::create([
            'name' => 'Óculos de Sol Aviador',
            'price' => 89.90,
            'description' => 'Óculos de sol estilo aviador com proteção UV 400. Armação resistente e lentes polarizadas.',
            'image' => 'https://images.unsplash.com/photo-1572635196237-14b3f281503f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=500&q=80',
            'has_variations' => false,
            'active' => true
        ]);
    }
}
