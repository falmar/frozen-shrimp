<?php

namespace App\Domains\Carrefour\Specs;

class CategoryProductParserInput
{
    /** @var string $baseURI helpful for relative URLs */
    public string $baseURI;

    /** @var string $content raw HTML content to parse */
    public string $content;
}
