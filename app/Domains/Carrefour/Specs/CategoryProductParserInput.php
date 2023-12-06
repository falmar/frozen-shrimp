<?php

namespace App\Domains\Carrefour\Specs;

class CategoryProductParserInput
{
    /** @var string $url source of the content helpful for relative URLs */
    public string $url;

    /** @var string $content raw HTML content to parse */
    public string $content;
}
