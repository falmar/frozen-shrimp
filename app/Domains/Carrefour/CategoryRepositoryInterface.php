<?php

namespace App\Domains\Carrefour;

use App\Domains\Carrefour\Entities\Category;

/**
 * Interface CategoryRepositoryInterface
 *
 * @package App\Domains\Carrefour
 */
interface CategoryRepositoryInterface
{
    /**
     * Find a category by its ID.
     *
     * @param string $categoryId The ID of the category.
     * @return Category|null The category, or null if not found.
     */
    public function findById(string $categoryId): ?Category;

    /**
     * Find a category by its URL slug.
     *
     * @param string $url The URL slug of the category.
     * @return Category|null The category, or null if not found.
     */
    public function findByUrl(string $url): ?Category;

    /**
     * Save a category.
     *
     * @param Category $category The category to save.
     * @return bool True if the category was saved successfully, false otherwise.
     */
    public function save(Category $category): bool;

    /**
     * Delete a category by its ID.
     *
     * @param string $categoryId The ID of the category.
     * @return bool True if the category was deleted successfully, false otherwise.
     */
    public function delete(string $categoryId): bool;
}
