<?php

namespace Tests\Integration\Carrefour;

use App\Domains\Carrefour\CategoryEloquentRepository;
use App\Domains\Carrefour\Entities\Category;
use Database\Seeders\tests\Carrefour\DomainSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Symfony\Component\Uid\UuidV7;
use Tests\TestCase;

class CategoryEloquentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    public function testFindById_should_return_null(): void
    {
        // given
        $this->seed(DomainSeeder::class);
        $this->expectsDatabaseQueryCount(1);

        /** @var CategoryEloquentRepository $repo */
        $repo = $this->app->make(CategoryEloquentRepository::class);

        // when
        $category = $repo->findById('');

        // then
        $this->assertNull($category, 'Category should be null');
        $this->assertNotInstanceOf(Category::class, $category, 'Category should not be an instance of Category');
    }

    public function testFindById_should_return_a_product(): void
    {
        // given
        $this->seed(DomainSeeder::class);
        $this->expectsDatabaseQueryCount(1);

        /** @var CategoryEloquentRepository $repo */
        $repo = $this->app->make(CategoryEloquentRepository::class);

        // when
        $category = $repo->findById('018c463c-2bf4-737d-90a4-4f9d03b56760');

        // then
        $this->assertInstanceOf(Category::class, $category, 'Category should an instance of Category');

        // magic numbers yuck!
        $this->assertMagicProduct($category);
    }

    public function testFindByUrl_should_return_null(): void
    {
        // given
        $this->seed(DomainSeeder::class);
        $this->expectsDatabaseQueryCount(1);

        /** @var CategoryEloquentRepository $repo */
        $repo = $this->app->make(CategoryEloquentRepository::class);

        // when
        $category = $repo->findByUrl('');

        // then
        $this->assertNull($category, 'Category should be null');
        $this->assertNotInstanceOf(Category::class, $category, 'Category should not be an instance of Category');
    }

    public function testFindByUrl_should_return_a_product(): void
    {
        // given
        $this->seed(DomainSeeder::class);
        $this->expectsDatabaseQueryCount(1);

        /** @var CategoryEloquentRepository $repo */
        $repo = $this->app->make(CategoryEloquentRepository::class);

        // when
        $category = $repo->findByUrl(
            'https://www.carrefour.es/supermercado/congelados/cat21449123/c'
        );

        // then
        $this->assertInstanceOf(Category::class, $category, 'Category should an instance of Category');

        // magic numbers yuck!
        $this->assertMagicProduct($category);
    }

    public function testDelete_should_not_delete_record(): void
    {
        // given
        $this->seed(DomainSeeder::class);
        $this->expectsDatabaseQueryCount(2);

        /** @var CategoryEloquentRepository $repo */
        $repo = $this->app->make(CategoryEloquentRepository::class);

        // when
        $repo->delete('');

        // then
        $this->assertDatabaseHas('categories', [
            'id' => '018c463c-2bf4-737d-90a4-4f9d03b56760',
            'deleted_at' => null
        ]);
    }

    public function testDelete_should_soft_delete_record(): void
    {
        // given
        $this->seed(DomainSeeder::class);
        $this->expectsDatabaseQueryCount(2);

        /** @var CategoryEloquentRepository $repo */
        $repo = $this->app->make(CategoryEloquentRepository::class);

        // when
        $repo->delete('018c463c-2bf4-737d-90a4-4f9d03b56760');

        // then
        $this->assertDatabaseMissing('categories', [
            'id' => '018c463c-2bf4-737d-90a4-4f9d03b56760',
            'deleted_at' => null
        ]);
    }

    public function testSave_should_add_record(): void
    {
        // given
        $this->seed(DomainSeeder::class);
        $this->expectsDatabaseQueryCount(2);

        /** @var CategoryEloquentRepository $repo */

        $repo = $this->app->make(CategoryEloquentRepository::class);

        $category = new Category();

        $category->name = 'Bebidas';
        $category->slug = 'bebidas';
        $category->url = 'https://www.carrefour.es/supermercado/bebidas/cat20003/c';
        $category->lastCrawlAt = new \DateTimeImmutable('2021-01-02 00:00:00');
        $category->lastCrawlHash = sha1('https://www.carrefour.es/supermercado/bebidas/cat20003/c');

        // when
        $repo->save($category);

        // then
        $this->assertIsString($category->id, 'Category id should be a string');
        $this->assertTrue(UuidV7::isValid($category->id), 'Category id should be a valid UUID v7');

        $this->assertDatabaseHas('categories', [
            'name' => 'Bebidas',
            'slug' => 'bebidas',
            'url' => 'https://www.carrefour.es/supermercado/bebidas/cat20003/c',
            'last_crawl_at' => '2021-01-02 00:00:00.000000',
            'last_crawl_hash' => sha1('https://www.carrefour.es/supermercado/bebidas/cat20003/c'),
            'deleted_at' => null
        ]);
    }

    public function testSave_should_update_record(): void
    {
        // given
        $this->seed(DomainSeeder::class);
        $this->expectsDatabaseQueryCount(3);

        /** @var CategoryEloquentRepository $repo */

        $repo = $this->app->make(CategoryEloquentRepository::class);

        $category = new Category();
        $category->id = '018c463c-2bf4-737d-90a4-4f9d03b56760';
        $category->name = 'Congelados mixed with magic numbers';
        $category->slug = 'congelados-mixed';
        $category->url = 'https://www.carrefour.es/supermercado/congelados/cat21449124/c';
        $category->lastCrawlAt = new \DateTimeImmutable('2021-01-03 00:00:00');
        $category->lastCrawlHash = sha1('https://www.carrefour.es/supermercado/congelados/cat21449124/c');

        // when
        $repo->save($category);

        // then
        $this->assertDatabaseHas('categories', [
            'id' => '018c463c-2bf4-737d-90a4-4f9d03b56760',
            'name' => 'Congelados mixed with magic numbers',
            'slug' => 'congelados-mixed',
            'url' => 'https://www.carrefour.es/supermercado/congelados/cat21449124/c',
            'last_crawl_at' => '2021-01-03 00:00:00.000000',
            'last_crawl_hash' => sha1('https://www.carrefour.es/supermercado/congelados/cat21449124/c'),
            'deleted_at' => null
        ]);
    }

    private function assertMagicProduct(Category|null $category): void
    {
        $this->assertNotNull($category, 'Category should not be null');

        $this->assertNotEmpty($category->id, 'Category id should not be empty');
        $this->assertIsString($category->id, 'Category id should be a string');

        $this->assertNotEmpty($category->name, 'Category name should not be empty');
        $this->assertIsString($category->name, 'Category name should be a string');

        $this->assertNotEmpty($category->slug, 'Category slug should not be empty');
        $this->assertIsString($category->slug, 'Category slug should be a string');

        $this->assertNotEmpty($category->url, 'Category url should not be empty');
        $this->assertIsString($category->url, 'Category url should be a string');

        $this->assertNotEmpty($category->lastCrawlAt, 'Category lastCrawlAt should not be empty');
        $this->assertInstanceOf(\DateTimeImmutable::class, $category->lastCrawlAt, 'Category lastCrawlAt should be a DateTimeImmutable');

        $this->assertNotEmpty($category->lastCrawlHash, 'Category lastCrawlHash should not be empty');
        $this->assertIsString($category->lastCrawlHash, 'Category lastCrawlHash should be a string');

        $this->assertEquals('018c463c-2bf4-737d-90a4-4f9d03b56760', $category->id);
        $this->assertEquals('Congelados', $category->name);
        $this->assertEquals('congelados', $category->slug);
        $this->assertEquals(
            'https://www.carrefour.es/supermercado/congelados/cat21449123/c',
            $category->url
        );
        $this->assertEquals(new \DateTimeImmutable('2021-01-01 00:00:00'), $category->lastCrawlAt);
        $this->assertEquals(sha1('https://www.carrefour.es/supermercado/congelados/cat21449123/c'), $category->lastCrawlHash);
    }
}
