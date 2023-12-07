<?php

namespace App\Domains\Carrefour\Entities;

class Category
{
    /** @var string */
    public string $id;

    /** @var string */
    public string $name;

    /** @var string */
    public string $slug;

    /** @var string */
    public string $url;

    /** @var string */
    public string $lastCrawlHash;

    /** @var \DateTimeImmutable */
    public \DateTimeImmutable $lastCrawlAt;

    /** @var \DateTimeImmutable */
    public \DateTimeImmutable $createdAt;

    /** @var \DateTimeImmutable */
    public \DateTimeImmutable $updatedAt;

    /** @var \DateTimeImmutable|null */
    public ?\DateTimeImmutable $deletedAt;

    /**
     * @param array<string, mixed> $data
     * @return self
     * @throws \Exception
     */
    public static function fromArray(array $data): self
    {
        $category = new Category();
        $category->id = $data['id'] ?? '';

        $category->name = $data['name'];
        $category->slug = $data['slug'];
        $category->url = $data['url'];
        $category->lastCrawlHash = $data['last_crawl_hash'];
        $category->lastCrawlAt = new \DateTimeImmutable($data['last_crawl_at']);

        $category->createdAt = new \DateTimeImmutable($data['created_at']);
        $category->updatedAt = new \DateTimeImmutable($data['updated_at']);
        $category->deletedAt = isset($data['deleted_at']) ? new \DateTimeImmutable($data['deleted_at']) : null;

        return $category;
    }
}
