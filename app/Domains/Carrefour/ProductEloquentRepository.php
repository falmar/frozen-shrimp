<?php

namespace App\Domains\Carrefour;

use App\Domains\Carrefour\Entities\Product;

readonly class ProductEloquentRepository implements ProductRepositoryInterface
{
    public function __construct(private readonly \App\Models\Product $eloquent)
    {
    }

    /**
     * @inheritDoc
     */
    public function findById(string $productId): ?Product
    {
        $elProd = $this->eloquent->find($productId);

        if (!$elProd) {
            return null;
        }

        return Product::fromArray($elProd->toArray());
    }

    /**
     * @inheritDoc
     */
    public function findByUrl(string $url): ?Product
    {
        $elProd = $this->eloquent->where('url', $url)->first();

        if (!$elProd) {
            return null;
        }

        return Product::fromArray($elProd->toArray());
    }

    public function listByCategoryId(string $categoryId): array
    {
        $elProds = $this->eloquent->where('category_id', $categoryId)->get();

        $prods = [];

        foreach ($elProds as $elProd) {
            $prods[] = Product::fromArray($elProd->toArray());
        }

        return $prods;
    }

    /**
     * @inheritDoc
     */
    public function save(Product $product): bool
    {
        if ($product->id ?? null) {
            $elProd = $this->eloquent->find($product->id);
        }

        if (!isset($elProd)) {
            $elProd = new $this->eloquent();
        }

        $elProd->category_id = $product->categoryId;
        $elProd->name = $product->name;
        $elProd->price = $product->price;
        $elProd->url = $product->url;
        $elProd->image_url = $product->imageURL;

        if (!$elProd->save()) {
            return false;
        }

        $product->id = $elProd->id;

        return true;
    }

    /**
     * @inheritDoc
     */
    public function delete(string $productId): bool
    {
        return (new $this->eloquent())->where('id', $productId)->delete();
    }
}
