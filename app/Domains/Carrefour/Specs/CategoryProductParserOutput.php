<?php

namespace App\Domains\Carrefour\Specs;

use App\Domains\Carrefour\ValueObjects\CrawlCategoryProduct;

class CategoryProductParserOutput
{
    /** @var list<CrawlCategoryProduct> $products */
    public array $products = [];
}
