<?php

namespace Database\Seeders\tests\Carrefour;

use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'id' => '018c463c-2bf4-737d-90a4-4f9d03b56770',
                'category_id' => '018c463c-2bf4-737d-90a4-4f9d03b56760',
                'name' => '4 quesos',
                'url' => 'https://www.carrefour.es/supermercado/queso-rallado-cuatro-quesos-carrefour-200-g/R-521030772/p',
                'price' => '1,95 €',
                'image_url' => 'https://static.carrefour.es/hd_510x_/img_pim_food/712501_00_1.jpg'
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
