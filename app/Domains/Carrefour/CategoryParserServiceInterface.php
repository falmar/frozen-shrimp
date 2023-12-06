<?php

namespace App\Domains\Carrefour;

use App\Domains\Carrefour\Specs\CategoryProductParserInput;
use App\Domains\Carrefour\Specs\CategoryProductParserOutput;
use App\Libraries\Context\Context;

interface CategoryParserServiceInterface
{
    public function products(Context $context, CategoryProductParserInput $input): CategoryProductParserOutput;


    // would be possible to have a methods like this:
    // one to parse the category page and get the category name or other info
    // public function category(Context $context, CategoryProductParserInput $output): CategoryProductParserOutput;
}
