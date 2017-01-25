<?php

declare(strict_types=1);

namespace tests\specs\SimplePrepaidCard\CoffeeShop\Model;

use PhpSpec\ObjectBehavior;
use Ramsey\Uuid\Uuid;
use SimplePrepaidCard\CoffeeShop\Model\AuthorizationRequestWasDeclined;
use SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider;
use SimplePrepaidCard\CoffeeShop\Model\Customer;
use SimplePrepaidCard\CoffeeShop\Model\Product;

/** @mixin Customer */
class CustomerSpec extends ObjectBehavior
{
    public function it_can_be_created()
    {
        $this->beConstructedThrough('create');

        $this->shouldBeAnInstanceOf(Customer::class);
    }

    public function it_has_customer_id()
    {
        $this->beConstructedThrough('create');

        $this->customerId()->shouldBeLike(Uuid::fromString(Customer::CUSTOMER_ID));
    }

    public function it_can_buy_a_known_product_when_authorization_request_was_accepted(CreditCardProvider $creditCardProvider)
    {
        $this->beConstructedThrough('create');

        $creditCardProvider
            ->authorizationRequest(Customer::create()->customerId(), Product::coffee())
            ->shouldBeCalled();

        $this->buyProduct(Product::coffee(), $creditCardProvider);
    }

    public function it_can_not_buy_a_product_when_authorization_request_was_declined(CreditCardProvider $creditCardProvider)
    {
        $this->beConstructedThrough('create');

        $creditCardProvider
            ->authorizationRequest(Customer::create()->customerId(), Product::coffee())
            ->willThrow(AuthorizationRequestWasDeclined::class);

        $this->shouldThrow(AuthorizationRequestWasDeclined::class)->duringBuyProduct(Product::coffee(), $creditCardProvider);
    }
}
