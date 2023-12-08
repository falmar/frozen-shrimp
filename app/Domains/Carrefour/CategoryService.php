<?php

namespace App\Domains\Carrefour;

use App\Domains\Carrefour\Entities\Category;
use App\Domains\Carrefour\Entities\Product;
use App\Domains\Carrefour\Specs\CategoryCrawlInput;
use App\Domains\Carrefour\Specs\CategoryCrawlOutput;
use App\Domains\Carrefour\Specs\CategoryProductParserInput;
use App\Domains\Carrefour\Specs\CategoryProductParserOutput;
use App\Domains\Carrefour\Specs\SaveProductListInput;
use App\Domains\Carrefour\Specs\SaveProductListOutput;
use App\Domains\Carrefour\Specs\ShowProductListInput;
use App\Domains\Carrefour\Specs\ShowProductListOutput;
use App\Libraries\Context\Context;
use App\Libraries\Hasher\HasherInterface;
use App\Libraries\URLHelperTrait;
use Cocur\Slugify\Slugify;

class CategoryService implements CategoryServiceInterface
{
    use URLHelperTrait;

    public function __construct(
        readonly private CategoryRepositoryInterface $categoryRepository,
        readonly private ProductRepositoryInterface $productRepository,
        readonly private CategoryParserServiceInterface $categoryParser,
        readonly private CategoryCrawlerServiceInterface $categoryCrawler,
        readonly private HasherInterface $hasher,
        readonly private Slugify $slugify,
    ) {
    }

    public function saveProductList(Context $context, SaveProductListInput $input): SaveProductListOutput
    {
        $response = new SaveProductListOutput();

        // broken apart for readability
        $crawlerOutput = $this->crawlCategoryPage($context, $input);
        $parserOutput = $this->parseCrawlOutput($context, $crawlerOutput, $input);
        $contentHash = $this->hasher->hash($parserOutput->products);
        $category = $this->handleCategory($context, $input, $contentHash);
        $this->handleProducts($context, $parserOutput, $category);

        return $response;
    }

    public function showProductList(Context $context, ShowProductListInput $input): ShowProductListOutput
    {
        $response = new ShowProductListOutput();

        /** @var Category|null $category */
        $category = $this->categoryRepository->findByUrl(
            $input->url
        );

        if (is_null($category)) {
            // todo: throw exception CategoryNotFound to streamline error handling
            return $response;
        }

        $response->products = $this->productRepository->listByCategoryId($category->id);

        return $response;
    }

    private function crawlCategoryPage(Context $context, SaveProductListInput $input): CategoryCrawlOutput
    {
        $crawlerSpec = new CategoryCrawlInput();
        $crawlerSpec->url = $input->url;
        $crawlerSpec->timeout = 5;

        if ($input->salePoint) {
            $host = $this->getHostFromURL($input->url);

            $crawlerSpec->headers = [
                'Cookie' => "salepoint={$input->salePoint}; Domain={$host}; Path=/; SameSite=None",
            ];
        }

        return $this->categoryCrawler->crawl($context, $crawlerSpec);
    }

    private function parseCrawlOutput(
        Context $context,
        CategoryCrawlOutput $crawlerOutput,
        SaveProductListInput $input
    ): CategoryProductParserOutput {
        $parserSpec = new CategoryProductParserInput();
        $parserSpec->baseURI = $this->getBaseURIFromURL($input->url);
        $parserSpec->content = $crawlerOutput->content;

        return $this->categoryParser->products($context, $parserSpec);
    }

    private function handleCategory(Context $_, SaveProductListInput $input, string $contentHash): Category
    {
        $category = $this->categoryRepository->findByUrl($input->url);

        if ($category?->lastCrawlHash === $contentHash) {
            return $category;
        } elseif (!$category) {
            $category = new Category();
            $category->name = 'Congelados';
            $category->slug = $this->slugify->slugify($category->name);
            $category->url = $input->url;
        }

        $category->lastCrawlHash = $contentHash;
        $category->lastCrawlAt = new \DateTimeImmutable();
        $this->categoryRepository->save($category);

        return $category;
    }

    private function handleProducts(Context $_, CategoryProductParserOutput $parserOutput, Category $category): void
    {
        foreach ($parserOutput->products as $crawledProduct) {
            $product = $this->productRepository->findByUrl($crawledProduct->url);
            if (!$product) {
                $product = new Product();
            }

            $product->categoryId = $category->id;
            $product->name = $crawledProduct->name;
            $product->url = $crawledProduct->url;
            $product->price = $crawledProduct->price;
            $product->imageURL = $crawledProduct->imageURL;

            $this->productRepository->save($product);
        }
    }
}
