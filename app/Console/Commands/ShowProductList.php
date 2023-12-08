<?php

namespace App\Console\Commands;

use App\Domains\Carrefour\CategoryRepositoryInterface;
use App\Domains\Carrefour\Entities\Category;
use App\Domains\Carrefour\Entities\Product;
use App\Domains\Carrefour\ProductRepositoryInterface;
use Illuminate\Console\Command;

class ShowProductList extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'show-product-list {url} {--salepoint}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(
        CategoryRepositoryInterface $categoryRepository,
        ProductRepositoryInterface $productRepository
    ): void {
        try {
            /** @var Category|null $category */
            $category = $categoryRepository->findByUrl($this->argument('url'));

            if (is_null($category)) {
                $this->error('Category not found');
                return;
            }

            $products = $productRepository->listByCategoryId($category->id);
            $products = array_map(
                fn (Product $product) => [
                    'name' => $product->name,
                    'price' => $product->price,
                    'image_url' => $product->imageURL,
                    'url' => $product->url,
                ],
                $products
            );

            $this->output->writeln(
                json_encode($products, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR)
            );
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return;
        }
    }
}
