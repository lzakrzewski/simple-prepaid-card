<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model\CreditCardProvider;

use Ramsey\Uuid\UuidInterface;

final class ReverseWasDeclined extends \DomainException
{
    public static function with(UuidInterface $customerId): self
    {
        return new self(
            sprintf(
                'A reverse for customer "%s" was declined',
                $customerId
            )
        );
    }
}
