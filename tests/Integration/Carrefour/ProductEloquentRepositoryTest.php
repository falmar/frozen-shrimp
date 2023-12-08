<?php

namespace Tests\Integration\Carrefour;

use App\Domains\Carrefour\Entities\Product;
use App\Domains\Carrefour\ProductEloquentRepository;
use Database\Seeders\tests\Carrefour\DomainSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Uid\UuidV7;
use Tests\TestCase;

class ProductEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function testFindById_should_return_null(): void
    {
        // given
        $this->seed(DomainSeeder::class);
        $this->expectsDatabaseQueryCount(1);

        /** @var ProductEloquentRepository $repo */
        $repo = $this->app->make(ProductEloquentRepository::class);

        // when
        $product = $repo->findById('');

        // then
        $this->assertNull($product, 'Product should be null');
        $this->assertNotInstanceOf(Product::class, $product, 'Product should not be an instance of Product');
    }

    public function testFindById_should_return_a_product(): void
    {
        // given
        $this->seed(DomainSeeder::class);
        $this->expectsDatabaseQueryCount(1);

        /** @var ProductEloquentRepository $repo */
        $repo = $this->app->make(ProductEloquentRepository::class);

        // when
        $product = $repo->findById('018c463c-2bf4-737d-90a4-4f9d03b56770');

        // then
        $this->assertInstanceOf(Product::class, $product, 'Product should an instance of Product');

        // magic numbers yuck!
        $this->assertMagicProduct($product);
    }

    public function testFindByUrl_should_return_null(): void
    {
        // given
        $this->seed(DomainSeeder::class);
        $this->expectsDatabaseQueryCount(1);

        /** @var ProductEloquentRepository $repo */
        $repo = $this->app->make(ProductEloquentRepository::class);

        // when
        $product = $repo->findByUrl('');

        // then
        $this->assertNull($product, 'Product should be null');
        $this->assertNotInstanceOf(Product::class, $product, 'Product should not be an instance of Product');
    }

    public function testFindByUrl_should_return_a_product(): void
    {
        // given
        $this->seed(DomainSeeder::class);
        $this->expectsDatabaseQueryCount(1);

        /** @var ProductEloquentRepository $repo */
        $repo = $this->app->make(ProductEloquentRepository::class);

        // when
        $product = $repo->findByUrl(
            'https://www.carrefour.es/supermercado/queso-rallado-cuatro-quesos-carrefour-200-g/R-521030772/p'
        );

        // then
        $this->assertInstanceOf(Product::class, $product, 'Product should an instance of Product');

        // magic numbers yuck!
        $this->assertMagicProduct($product);
    }

    public function testListByCategoryId_should_return_empty_array(): void
    {
        // given
        $this->seed(DomainSeeder::class);
        $this->expectsDatabaseQueryCount(1);

        /** @var ProductEloquentRepository $repo */
        $repo = $this->app->make(ProductEloquentRepository::class);

        // when
        $products = $repo->listByCategoryId('');

        // then
        $this->assertIsArray($products, 'Products should be an array');
        $this->assertEmpty($products, 'Products should be empty');
    }

    public function testListByCategoryId_should_return_products(): void
    {
        // given
        $this->seed(DomainSeeder::class);
        $this->expectsDatabaseQueryCount(1);

        /** @var ProductEloquentRepository $repo */
        $repo = $this->app->make(ProductEloquentRepository::class);

        // when
        $products = $repo->listByCategoryId('018c463c-2bf4-737d-90a4-4f9d03b56760');

        // then
        $this->assertIsArray($products, 'Products should be an array');
        $this->assertNotEmpty($products, 'Products should not be empty');

        foreach ($products as $product) {
            $this->assertInstanceOf(Product::class, $product, 'Product should an instance of Product');

            if ($product->id === '018c463c-2bf4-737d-90a4-4f9d03b56770') {
                $this->assertMagicProduct($product);
            } else {
                $this->fail('Product id should be 018c463c-2bf4-737d-90a4-4f9d03b56770');
            }
        }
    }

    public function testDelete_should_not_delete_record(): void
    {
        // given
        $this->seed(DomainSeeder::class);
        $this->expectsDatabaseQueryCount(2);

        /** @var ProductEloquentRepository $repo */
        $repo = $this->app->make(ProductEloquentRepository::class);

        // when
        $repo->delete('');

        // then
        $this->assertDatabaseHas('products', [
            'id' => '018c463c-2bf4-737d-90a4-4f9d03b56770',
            'deleted_at' => null
        ]);
    }

    public function testDelete_should_soft_delete_record(): void
    {
        // given
        $this->seed(DomainSeeder::class);
        $this->expectsDatabaseQueryCount(2);

        /** @var ProductEloquentRepository $repo */
        $repo = $this->app->make(ProductEloquentRepository::class);

        // when
        $repo->delete('018c463c-2bf4-737d-90a4-4f9d03b56770');

        // then
        $this->assertDatabaseMissing('products', [
            'id' => '018c463c-2bf4-737d-90a4-4f9d03b56770',
            'deleted_at' => null
        ]);
    }

    public function testSave_should_add_record(): void
    {
        // given
        $this->seed(DomainSeeder::class);
        $this->expectsDatabaseQueryCount(2);

        /** @var ProductEloquentRepository $repo */

        $repo = $this->app->make(ProductEloquentRepository::class);

        $product = new Product();

        $product->categoryId = '018c463c-2bf4-737d-90a4-4f9d03b56760';
        $product->name = 'Lasaña de Carne Price 300 g.';
        $product->url = 'https://www.carrefour.es/supermercado/lasana-de-carne-price-300-g/R-VC4AECOMM-203133/p';
        $product->price = '0,89 €';
        $product->imageURL = 'https://static.carrefour.es/hd_510x_/img_pim_food/203133_00_1.jpg';

        // when
        $repo->save($product);

        // then
        $this->assertIsString($product->id, 'Product id should be a string');
        $this->assertTrue(UuidV7::isValid($product->id), 'Product id should be a valid UUID v7');

        $this->assertDatabaseHas('products', [
            'category_id' => '018c463c-2bf4-737d-90a4-4f9d03b56760',
            'name' => 'Lasaña de Carne Price 300 g.',
            'url' => 'https://www.carrefour.es/supermercado/lasana-de-carne-price-300-g/R-VC4AECOMM-203133/p',
            'price' => '0,89 €',
            'image_url' => 'https://static.carrefour.es/hd_510x_/img_pim_food/203133_00_1.jpg',
            'deleted_at' => null
        ]);
    }

    public function testSave_should_update_record(): void
    {
        // given
        $this->seed(DomainSeeder::class);
        $this->expectsDatabaseQueryCount(3);

        /** @var ProductEloquentRepository $repo */

        $repo = $this->app->make(ProductEloquentRepository::class);

        $product = new Product();
        $product->id = '018c463c-2bf4-737d-90a4-4f9d03b56770';
        $product->categoryId = '018c463c-2bf4-737d-90a4-4f9d03b56760';
        $product->name = '4 quesos promotion';
        $product->url = 'https://www.carrefour.es/supermercado/queso-rallado-cuatro-quesos-carrefour-200-g/R-521030772/p';
        $product->imageURL = 'https://static.carrefour.es/hd_510x_/img_pim_food/712501_00_1.jpg';
        $product->price = '0,50 €';

        // when
        $repo->save($product);

        // then
        $this->assertDatabaseHas('products', [
            'id' => '018c463c-2bf4-737d-90a4-4f9d03b56770',
            'category_id' => '018c463c-2bf4-737d-90a4-4f9d03b56760',
            'name' => '4 quesos promotion',
            'url' => 'https://www.carrefour.es/supermercado/queso-rallado-cuatro-quesos-carrefour-200-g/R-521030772/p',
            'price' => '0,50 €',
            'image_url' => 'https://static.carrefour.es/hd_510x_/img_pim_food/712501_00_1.jpg',
            'deleted_at' => null
        ]);
    }

    private function assertMagicProduct(Product|null $product): void
    {
        $this->assertNotNull($product, 'Product should not be null');

        $this->assertNotEmpty($product->id, 'Product id should not be empty');
        $this->assertIsString($product->id, 'Product id should be a string');

        $this->assertNotEmpty($product->categoryId, 'Product category_id should not be empty');
        $this->assertIsString($product->categoryId, 'Product category_id should be a string');

        $this->assertNotEmpty($product->name, 'Product name should not be empty');
        $this->assertIsString($product->name, 'Product name should be a string');

        $this->assertNotEmpty($product->url, 'Product url should not be empty');
        $this->assertIsString($product->url, 'Product url should be a string');

        $this->assertNotEmpty($product->price, 'Product price should not be empty');
        $this->assertIsString($product->price, 'Product price should be a string');

        $this->assertNotEmpty($product->imageURL, 'Product image_url should not be empty');
        $this->assertIsString($product->imageURL, 'Product image_url should be a string');

        $this->assertEquals('018c463c-2bf4-737d-90a4-4f9d03b56770', $product->id);
        $this->assertEquals('018c463c-2bf4-737d-90a4-4f9d03b56760', $product->categoryId);
        $this->assertEquals('4 quesos', $product->name);
        $this->assertEquals(
            'https://www.carrefour.es/supermercado/queso-rallado-cuatro-quesos-carrefour-200-g/R-521030772/p',
            $product->url
        );
        $this->assertEquals('1,95 €', $product->price);
        $this->assertEquals('https://static.carrefour.es/hd_510x_/img_pim_food/712501_00_1.jpg', $product->imageURL);
    }
}
