<?php

namespace App\Domains\Carrefour;

use App\Domains\Carrefour\Specs\CategoryProductParserInput;
use App\Domains\Carrefour\Specs\CategoryProductParserOutput;
use App\Libraries\Context\Context;
use Symfony\Component\DomCrawler\Crawler;

class CategoryParserService implements CategoryParserServiceInterface
{
    public function products(Context $context, CategoryProductParserInput $input): CategoryProductParserOutput
    {
        $output = new CategoryProductParserOutput();

        $crawler = $this->getProductCrawler($input->content);

        $baseURI = parse_url($input->url, PHP_URL_HOST);

        $output->products = array_slice(
            $crawler
                ->filter('.product-card-list__list > .product-card-list__item .product-card')
                ->each(fn(Crawler $node) => $this->parseProduct($node, $baseURI)),
            0,
            5
        );

        return $output;
    }


    private function getProductCrawler(string $content): Crawler
    {
        // TODO: move this crawler constructor to a factory or something to be injected _in_
        return new Crawler($content, useHtml5Parser: true);
    }

    private function parseProduct(Crawler $node, $baseURI): array
    {
        $titleAnchor = $node->filter('h2.product-card__title .product-card__title-link.track-click');

        return [
            'name' => $titleAnchor->text(''),
            'url' => $baseURI . $titleAnchor->attr('href'),
            'price' => $node->filter('.product-card__price')->text(),
            'image' => $node->filter('.product-card__image')->attr('src'),
        ];
    }
}
