<?php

namespace App\Domains\Carrefour\ValueObjects;

use App\Libraries\Hasher\HashableInterface;

class CrawlCategoryProduct implements HashableInterface
{
    public function __construct(
        public string $name,
        public string $url,
        public string $price,
        public string $imageURL,
    ) {
    }

    public function hashable(): string
    {
        return (
            $this->name .
            $this->url .
            $this->price .
            $this->imageURL
        );
    }
}
