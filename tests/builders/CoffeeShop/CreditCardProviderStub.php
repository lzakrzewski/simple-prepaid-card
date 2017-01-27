<?php

declare(strict_types=1);

namespace tests\builders\CoffeeShop;

use Money\Money;
use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider;
use SimplePrepaidCard\CoffeeShop\Model\Product;

class CreditCardProviderStub implements CreditCardProvider
{
    /** {@inheritdoc} */
    public function authorizationRequest(UuidInterface $customerId, Product $product)
    {
    }

    /** {@inheritdoc} */
    public function capture(Money $amount, UuidInterface $customerId)
    {
    }

    /** {@inheritdoc} */
    public function reverse(Money $amount, UuidInterface $customerId)
    {
    }

    /** {@inheritdoc} */
    public function refund(Money $amount, UuidInterface $customerId)
    {
    }
}
