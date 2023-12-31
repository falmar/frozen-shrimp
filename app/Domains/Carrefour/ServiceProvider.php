<?php

namespace App\Domains\Carrefour;

use Illuminate\Contracts\Support\DeferrableProvider;
use Symfony\Component\HttpClient\HttpClient;

/**
 * @codeCoverageIgnore
 */
class ServiceProvider extends \Illuminate\Support\ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->bind(CategoryRepositoryInterface::class, CategoryEloquentRepository::class);
        $this->app->bind(ProductRepositoryInterface::class, ProductEloquentRepository::class);
        $this->app->bind(CategoryParserServiceInterface::class, CategoryParserService::class);
        $this->app->bind(CategoryCrawlerServiceInterface::class, function () {
            return new CategoryCrawlerService(
                HttpClient::create(),
            );
        });

        $this->app->bind(CategoryServiceInterface::class, CategoryService::class);
    }

    /**
     * @return string[]
     */
    public function provides(): array
    {
        return [
            CategoryRepositoryInterface::class,
            ProductRepositoryInterface::class,
            CategoryParserServiceInterface::class,
            CategoryCrawlerServiceInterface::class,
        ];
    }
}
