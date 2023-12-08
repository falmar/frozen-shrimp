<?php

namespace App\Domains\Carrefour\Specs;

class CategoryCrawlInput
{
    /**
     * @var string $url the URL to crawl, this could very well be just an ID of the category
     * to let the crawler implementer decide how to build/clean up the URL
     */
    public string $url;

    /** @var float $timeout maximum time in seconds the request will wait for a response */
    public float $timeout = 10;
    /**
     * @var array<string, string> $headers additional headers to send with the request
     */
    public array $headers = [];
}
