<?php

declare(strict_types=1);

namespace tests\specs\SimplePrepaidCard\CoffeeShop\Model;

use Money\Money;
use PhpSpec\ObjectBehavior;
use SimplePrepaidCard\CoffeeShop\Model\Product;

/** @mixin Product */
class ProductSpec extends ObjectBehavior
{
    public function it_can_be_created()
    {
        $this->beConstructedWith('tea', 'tea', Money::GBP(100));

        $this->shouldBeAnInstanceOf(Product::class);
    }

    public function it_can_be_coffee()
    {
        $this->beConstructedThrough('coffee');

        $this->shouldBeAnInstanceOf(Product::class);
        $this->productId()->shouldBe('coffee');
        $this->name()->shouldBe('coffee');
        $this->price()->shouldBeLike(Money::GBP(500));
    }

    public function it_has_price()
    {
        $this->beConstructedWith('tea', 'tea', Money::GBP(100));

        $this->price()->shouldBeLike(Money::GBP(100));
    }

    public function it_has_name()
    {
        $this->beConstructedWith('tea', 'tea', Money::GBP(100));

        $this->name()->shouldBe('tea');
    }

    public function it_has_id()
    {
        $this->beConstructedWith('tea', 'tea', Money::GBP(100));

        $this->productId()->shouldBe('tea');
    }
}
