<?php

namespace App\Domains\Carrefour;

use App\Domains\Carrefour\Entities\Category;

readonly class CategoryEloquentRepository implements CategoryRepositoryInterface
{
    public function __construct(private \App\Models\Category $eloquent)
    {
    }

    /**
     * @inheritDoc
     */
    public function findById(string $categoryId): ?Category
    {
        $elCat = $this->eloquent->find($categoryId);

        if (!$elCat) {
            return null;
        }

        return Category::fromArray($elCat->toArray());
    }

    /**
     * @inheritDoc
     */
    public function findByUrl(string $url): ?Category
    {
        $elCat = $this->eloquent->where('url', $url)->first();

        if (!$elCat) {
            return null;
        }

        return Category::fromArray($elCat->toArray());
    }

    /**
     * @inheritDoc
     */
    public function save(Category $category): bool
    {
        if ($category->id ?? null) {
            $elCat = $this->eloquent->find($category->id);
        }

        if (!isset($elCat)) {
            $elCat = new $this->eloquent();
        }

        $elCat->name = $category->name;
        $elCat->slug = $category->slug;
        $elCat->url = $category->url;
        $elCat->last_crawl_hash = $category->lastCrawlHash;
        $elCat->last_crawl_at = $category->lastCrawlAt->format('Y-m-d H:i:s');

        if (!$elCat->save()) {
            return false;
        }

        $category->id = $elCat->id;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $categoryId): bool
    {
        return $this->eloquent->destroy($categoryId) > 0;
    }
}
