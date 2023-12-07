<?php

namespace App\Libraries\Hasher;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;

class HasherProvider extends ServiceProvider implements DeferrableProvider
{
    public function register(): void
    {
        $this->app->bind(HasherInterface::class, EmbeddedMagicalHasher::class);
    }

    /**
     * @return class-string[]
     */
    public function provides(): array
    {
        return [
            HasherInterface::class,
        ];
    }
}
