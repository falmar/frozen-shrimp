<?php

namespace Database\Seeders\Tests\Carrefour;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::create([
            'id' => '018c463c-2bf4-737d-90a4-4f9d03b56760',
            'name' => 'Congelados',
            'slug' => 'congelados',
            'url' => 'https://www.carrefour.es/supermercado/congelados/cat21449123/c',
            'last_crawl_at' => '2021-01-01 00:00:00',
            'last_crawl_hash' => sha1('https://www.carrefour.es/supermercado/congelados/cat21449123/c')
        ]);
    }
}
