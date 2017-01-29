<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider;

use Ramsey\Uuid\UuidInterface;
use SimplePrepaidCard\CoffeeShop\Model\Product;

final class AuthorizationRequestWasDeclined extends \DomainException
{
    public static function with(UuidInterface $customerId, Product $product): self
    {
        return new self(
            sprintf(
                'An authorization request for "%d" product "%s" was declined for customer with id "%s"',
                $product->price()->getAmount(),
                $product->name(),
                $customerId
            )
        );
    }
}
