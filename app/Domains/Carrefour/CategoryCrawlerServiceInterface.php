<?php

namespace App\Domains\Carrefour;

use App\Domains\Carrefour\Specs\CategoryCrawlInput;
use App\Domains\Carrefour\Specs\CategoryCrawlOutput;
use App\Libraries\Context\Context;

interface CategoryCrawlerServiceInterface
{
    /**
     * Connect and fetch the content of the given URL.
     *
     * @param Context $context
     * @param CategoryCrawlInput $input
     * @return CategoryCrawlOutput
     */
    public function crawl(Context $context, CategoryCrawlInput $input): CategoryCrawlOutput;
}
