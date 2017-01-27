<?php

declare(strict_types=1);

namespace tests\specs\SimplePrepaidCard\CoffeeShop\Model;

use Money\Money;
use PhpSpec\ObjectBehavior;
use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CoffeeShop\Model\CannotUseNegativeAmount;
use SimplePrepaidCard\CoffeeShop\Model\Merchant;

/** @mixin Merchant */
class MerchantSpec extends ObjectBehavior
{
    public function it_can_be_created()
    {
        $this->beConstructedThrough('create');

        $this->shouldBeAnInstanceOf(Merchant::class);
    }

    public function it_has_merchant_id()
    {
        $this->beConstructedThrough('create');

        $this->merchantId()->shouldBeLike(Uuid::fromString(Merchant::MERCHANT_ID));
    }

    public function it_can_be_authorized_to()
    {
        $this->beConstructedThrough('create');

        $this->authorize(Money::GBP(100));

        $this->authorizedTo()->shouldBeLike(Money::GBP(100));
    }

    public function it_can_be_authorized_multiple_times()
    {
        $this->beConstructedThrough('create');

        $this->authorize(Money::GBP(100));
        $this->authorize(Money::GBP(100));
        $this->authorize(Money::GBP(100));

        $this->authorizedTo()->shouldBeLike(Money::GBP(300));
    }

    public function it_can_not_be_authorized_with_negative_amount()
    {
        $this->beConstructedThrough('create');

        $this->shouldThrow(CannotUseNegativeAmount::class)->duringAuthorize(Money::GBP(-300));
    }

    public function it_can_not_be_authorized_with_amount_in_different_currency()
    {
        $this->beConstructedThrough('create');

        $this->shouldThrow(\InvalidArgumentException::class)->duringAuthorize(Money::USD(300));
    }
}
