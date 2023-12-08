<?php

namespace App\Domains\Carrefour\Specs;

use App\Domains\Carrefour\Entities\Product;

class ShowProductListOutput
{
    /** @var list<Product> */
    public array $products = [];
}
