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
        $baseURI = $this->getBaseURI($input->url);

        $output->products = $crawler
            ->filter('.product-card-list__list > .product-card-list__item .product-card')
            ->each(fn (Crawler $node) => $this->parseProduct($node, $baseURI));

        return $output;
    }

    private function getProductCrawler(string $content): Crawler
    {
        // TODO: move this crawler constructor to a factory or something to be injected _in_
        return new Crawler($content, useHtml5Parser: true);
    }

    private function parseProduct(Crawler $node, string $baseURI): CrawlCategoryProduct
    {
        $titleAnchor = $node->filter('h2.product-card__title .product-card__title-link.track-click');

        return new CrawlCategoryProduct(
            name: $titleAnchor->text(''),
            url: $baseURI . $titleAnchor->attr('href'),
            price: $node->filter('.product-card__price')->text(),
            imageURL: $node->filter('.product-card__image')->attr('src') ?? '',
        );
    }

    private function getBaseURI(string $url): string
    {
        $parsedURL = parse_url($url);
        if ($parsedURL && isset($parsedURL['scheme'], $parsedURL['host'])) {
            ['scheme' => $scheme,
                'host' => $host,] = $parsedURL;
            return $scheme . '://' . $host;
        }

        return '';
    }
}
