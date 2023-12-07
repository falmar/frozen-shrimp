<?php

namespace App\Domains\Carrefour\Specs;

class CategoryCrawlOutput
{
    /** @var string $content The raw HTML content of the page. */
    public string $content;

    /** @var bool $modified the page has been modified */
    public bool $modified = true;

    /** @var array<string, string> $headers http headers of response */
    public array $headers = [];
}
