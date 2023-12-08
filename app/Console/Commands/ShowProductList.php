<?php

namespace App\Console\Commands;

use App\Domains\Carrefour\CategoryServiceInterface;
use App\Domains\Carrefour\Entities\Product;
use App\Domains\Carrefour\Specs\ShowProductListInput;
use App\Libraries\Context\Context;
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
        Context $context,
        CategoryServiceInterface $categoryService,
    ): void {
        try {
            $spec = new ShowProductListInput();
            $spec->url = $this->argument('url');

            // get category products from service
            $output = $categoryService->showProductList($context, $spec);

            // output is responsibility of the command "transport layer"
            $products = array_map(
                fn (Product $product) => [
                    'name' => $product->name,
                    'price' => $product->price,
                    'image_url' => $product->imageURL,
                    'url' => $product->url,
                ],
                $output->products
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
