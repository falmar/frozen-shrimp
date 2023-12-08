<?php

namespace App\Console\Commands;

use App\Domains\Carrefour\CategoryCrawlerServiceInterface;
use App\Domains\Carrefour\CategoryParserServiceInterface;
use App\Domains\Carrefour\CategoryRepositoryInterface;
use App\Domains\Carrefour\Entities\Category;
use App\Domains\Carrefour\Entities\Product;
use App\Domains\Carrefour\ProductRepositoryInterface;
use App\Domains\Carrefour\Specs\CategoryCrawlInput;
use App\Domains\Carrefour\Specs\CategoryProductParserInput;
use App\Libraries\Context\Context;
use App\Libraries\Hasher\HasherInterface;
use App\Libraries\URLHelperTrait;
use Cocur\Slugify\Slugify;
use Illuminate\Console\Command;

class SaveProductList extends Command
{
    use URLHelperTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:save-product-list {url} {--salepoint}';

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
        CategoryRepositoryInterface $categoryRepository,
        ProductRepositoryInterface $productRepository,
        CategoryParserServiceInterface $categoryParser,
        CategoryCrawlerServiceInterface $categoryCrawler,
        HasherInterface $hasher,
        Slugify $slugify
    ): void {
        try {
            // Hyper Hoya de la Plata
            // '005210|4600013||DRIVE|2'

            // Drive Peatón MK Quevedo
            // '005212|4700003||DRIVE|2'

            // default salepoint
            $salePoint = $this->option('salepoint') ?: '005212|4700003||DRIVE|2';
            $host = parse_url($this->argument('url'), PHP_URL_HOST);

            // crawl the category page
            $crawlerSpec = new CategoryCrawlInput();
            $crawlerSpec->url = $this->argument('url');
            $crawlerSpec->timeout = 5;
            $crawlerSpec->headers = [
                'Cookie' => "salepoint={$salePoint}; Domain={$host}; Path=/; SameSite=None",
            ];

            $crawlerOutput = $categoryCrawler->crawl($context, $crawlerSpec);
            // --

            // parse crawl output
            $parserSpec = new CategoryProductParserInput();
            $parserSpec->baseURI = $this->getBaseURIFromURL($crawlerSpec->url);
            $parserSpec->content = $crawlerOutput->content;

            $parserOutput = $categoryParser->products($context, $parserSpec);
            // --

            // get a hash of the parsed content to "cache" it
            $contentHash = $hasher->hash($parserOutput->products);
            // --

            // fetch the category from db
            $category = $categoryRepository->findByUrl($this->argument('url'));

            // check if the content has changed
            if ($category?->lastCrawlHash === $contentHash) {
                // same content, nothing to save
                return;
            } elseif (!$category) {
                // new category
                // NOTE: the name/slug would come from the "parser" its category by the given url
                $category = new Category();
                $category->name = 'Congelados';
                $category->slug = $slugify->slugify($category->name);
                $category->url = $this->argument('url');
            }

            // update category crawl info
            $category->lastCrawlHash = $contentHash;
            $category->lastCrawlAt = new \DateTimeImmutable();
            $categoryRepository->save($category);
            // --

            // save products to db or update them if they already exist
            foreach ($parserOutput->products as $crawledProduct) {
                $product = $productRepository->findByUrl($crawledProduct->url);
                if (!$product) {
                    $product = new Product();
                }

                $product->categoryId = $category->id;
                $product->name = $crawledProduct->name;
                $product->url = $crawledProduct->url;
                $product->price = $crawledProduct->price;
                $product->imageURL = $crawledProduct->imageURL;

                $productRepository->save($product);
            }
            // --
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return;
        }
    }
}
