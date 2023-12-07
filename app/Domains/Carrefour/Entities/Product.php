<?php

namespace App\Domains\Carrefour\Entities;

class Product
{
    /** @var string */
    public string $id;

    /** @var string */
    public string $categoryId;

    /** @var string */
    public string $name;

    /** @var string */
    public string $price;

    /** @var string */
    public string $url;

    /** @var string */
    public string $imageURL;

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
        $product = new Product();
        $product->id = $data['id'] ?? null;

        $product->categoryId = $data['category_id'];
        $product->name = $data['name'];
        $product->price = $data['price'];
        $product->url = $data['url'];
        $product->imageURL = $data['image_url'];

        $product->createdAt = new \DateTimeImmutable($data['created_at']);
        $product->updatedAt = new \DateTimeImmutable($data['updated_at']);
        $product->deletedAt = isset($data['deleted_at']) ? new \DateTimeImmutable($data['deleted_at']) : null;

        return $product;
    }
}
