<?php

namespace App\Domains\Carrefour;

use App\Domains\Carrefour\Entities\Product;

/**
 * Interface ProductRepositoryInterface
 *
 * @package App\Domains\Carrefour
 */
interface ProductRepositoryInterface
{
    /**
     * Find a product by its ID.
     *
     * @param string $productId The ID of the product.
     * @return Product|null The product, or null if not found.
     */
    public function findById(string $productId): ?Product;

    /**
     * Find a product by its URL slug.
     *
     * @param string $url The URL slug of the product.
     * @return Product|null The product, or null if not found.
     */
    public function findByUrl(string $url): ?Product;

    /**
     * List products by its parent category ID.
     *
     * @param string $categoryId
     * @return list<Product>
     */
    public function listByCategoryId(string $categoryId): array;

    /**
     * Save a product.
     *
     * @param Product $product The product to save.
     * @return bool True if the product was saved successfully, false otherwise.
     */
    public function save(Product $product): bool;

    /**
     * Delete a product by its ID.
     *
     * @param string $productId The ID of the product.
     * @return bool True if the product was deleted successfully, false otherwise.
     */
    public function delete(string $productId): bool;
}
