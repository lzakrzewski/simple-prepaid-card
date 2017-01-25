<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model;

final class Products
{
    /**
     * @param string $productId
     *
     * @throws ProductIsUnknown
     *
     * @return Product
     */
    public function get(string $productId): Product
    {
        if ($productId != Product::COFFEE_PRODUCT_ID) {
            throw ProductIsUnknown::with($productId);
        }

        return Product::coffee();
    }
}
