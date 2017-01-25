<?php

declare(strict_types=1);

namespace tests\specs\SimplePrepaidCard\CoffeeShop\Model;

use PhpSpec\ObjectBehavior;
use SimplePrepaidCard\CoffeeShop\Model\Product;
use SimplePrepaidCard\CoffeeShop\Model\ProductIsUnknown;
use SimplePrepaidCard\CoffeeShop\Model\Products;

/** @mixin Products */
class ProductsSpec extends ObjectBehavior
{
    public function it_can_get_product_by_id()
    {
        $this->beAnInstanceOf(Products::class);

        $this->get(Product::COFFEE_PRODUCT_ID)->shouldBeLike(Product::coffee());
    }

    public function it_fails_when_product_with_id_is_unknown()
    {
        $this->beAnInstanceOf(Products::class);

        $this->shouldThrow(ProductIsUnknown::class)->duringGet('unknown');
    }
}
