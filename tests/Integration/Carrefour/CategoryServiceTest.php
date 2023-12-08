<?php

namespace Tests\Integration\Carrefour;

use App\Domains\Carrefour\CategoryServiceInterface;
use App\Domains\Carrefour\Entities\Category;
use App\Domains\Carrefour\Specs\SaveProductListInput;
use App\Domains\Carrefour\Specs\SaveProductListOutput;
use App\Domains\Carrefour\Specs\ShowProductListInput;
use App\Libraries\Context\AppContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testSaveProductList(): void
    {
        // given
        $context = AppContext::background();
        /** @var CategoryServiceInterface $service */
        $service = $this->app->make(CategoryServiceInterface::class);

        $spec = new SaveProductListInput();
        $spec->url = 'https://www.carrefour.es/supermercado/congelados/cat21449123/c';
        $spec->salePoint = '005212|4700003||DRIVE|2';

        // when
        $output = $service->saveProductList($context, $spec);

        // then
        $this->assertInstanceOf(SaveProductListOutput::class, $output);
        $this->assertInstanceOf(Category::class, $output->category);

        $this->assertDatabaseCount('categories', 1);
        $this->assertDatabaseHas('categories', [
            'url' => $spec->url,
        ]);

        $this->assertDatabaseHas('products', [
            'category_id' => $output->category->id,
        ]);
    }

    public function testSaveProductList_should_save_once(): void
    {
        // given
        $context = AppContext::background();
        /** @var CategoryServiceInterface $service */
        $service = $this->app->make(CategoryServiceInterface::class);

        $spec = new SaveProductListInput();
        $spec->url = 'https://www.carrefour.es/supermercado/congelados/cat21449123/c';
        $spec->salePoint = '005212|4700003||DRIVE|2';

        // when
        $output = $service->saveProductList($context, $spec);

        $lastCrawl = $output->category->lastCrawlAt;

        // then
        $this->assertInstanceOf(SaveProductListOutput::class, $output);
        $this->assertInstanceOf(Category::class, $output->category);

        $this->assertDatabaseCount('categories', 1);
        $this->assertDatabaseHas('categories', [
            'url' => $spec->url,
        ]);

        $this->assertDatabaseHas('products', [
            'category_id' => $output->category->id,
        ]);

        $output2 = $service->saveProductList($context, $spec);
        $this->assertInstanceOf(SaveProductListOutput::class, $output);
        $this->assertInstanceOf(Category::class, $output->category);

        // Had to truncate the milliseconds because the database truncates them
        $this->assertEquals(
            $lastCrawl->format(DATE_RFC3339),
            $output2->category->lastCrawlAt->format(DATE_RFC3339),
        );
    }

    public function testShowProductList(): void
    {
        $this->seed(\Database\Seeders\Tests\Carrefour\DomainSeeder::class);

        // given
        $context = AppContext::background();
        /** @var CategoryServiceInterface $service */
        $service = $this->app->make(CategoryServiceInterface::class);

        $spec = new ShowProductListInput();
        $spec->url = 'https://www.carrefour.es/supermercado/congelados/cat21449123/c';

        // when
        $output = $service->showProductList($context, $spec);

        $this->assertIsArray($output->products);
        $this->assertNotEmpty($output->products);

        $this->assertIsString($output->products[0]->id);
        $this->assertIsString($output->products[0]->name);
        $this->assertIsString($output->products[0]->categoryId);
        $this->assertIsString($output->products[0]->url);
        $this->assertIsString($output->products[0]->price);
        $this->assertIsString($output->products[0]->imageURL);
    }
}
