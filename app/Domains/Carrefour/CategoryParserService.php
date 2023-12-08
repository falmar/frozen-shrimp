<?php

namespace App\Domains\Carrefour;

use App\Domains\Carrefour\Specs\CategoryProductParserInput;
use App\Domains\Carrefour\Specs\CategoryProductParserOutput;
use App\Domains\Carrefour\ValueObjects\CrawlCategoryProduct;
use App\Libraries\Context\Context;
use Symfony\Component\DomCrawler\Crawler;

readonly class CategoryParserService implements CategoryParserServiceInterface
{
    public function products(Context $context, CategoryProductParserInput $input): CategoryProductParserOutput
    {
        $output = new CategoryProductParserOutput();

        $crawler = $this->getProductCrawler($input->content);

        $output->products = array_filter(
            $crawler
                ->filter('.product-card-list__list > .product-card-list__item .product-card')
                ->each(fn (Crawler $node) => $this->parseProduct($node, $input->baseURI)),
            fn (?CrawlCategoryProduct $product) => $product instanceof CrawlCategoryProduct
        );

        return $output;
    }

    private function getProductCrawler(string $content): Crawler
    {
        // TODO: move this crawler constructor to a factory or something to be injected _in_
        return new Crawler($content, useHtml5Parser: true);
    }

    private function parseProduct(Crawler $node, string $baseURI): CrawlCategoryProduct|null
    {
        $anchorEl = $node->filter('h2.product-card__title .product-card__title-link.track-click');
        $priceElement = $node->filter('.product-card__price');
        $imageEl = $node->filter('img.product-card__image');

        if ($anchorEl->count() === 0 || $priceElement->count() === 0 || $imageEl->count() === 0) {
            return null;
        }

        $name = $anchorEl->text('');
        $url = $baseURI . $anchorEl->attr('href');
        $price = $node->filter('.product-card__price')->text();
        $imageURL = $node->filter('img.product-card__image')->attr('src');

        if (empty($name) || empty($url) || empty($price) || empty($imageURL)) {
            return null;
        }

        return new CrawlCategoryProduct(
            name: $name,
            url: $url,
            price: $price,
            imageURL: $imageURL,
        );
    }
}
