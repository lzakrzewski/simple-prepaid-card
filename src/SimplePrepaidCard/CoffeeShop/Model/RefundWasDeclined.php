<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model;

use Ramsey\Uuid\UuidInterface;

//Todo: add prefix CreditCard
final class RefundWasDeclined extends \DomainException
{
    public static function with(UuidInterface $customerId): self
    {
        return new self(
            sprintf(
                'A refund for customer "%s" was declined',
                $customerId
            )
        );
    }
}
