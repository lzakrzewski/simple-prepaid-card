<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model;

use Ramsey\Uuid\UuidInterface;

final class CannotRefundMoreThanCaptured extends \DomainException
{
    public static function with(UuidInterface $customerId): self
    {
        return new self(
            sprintf(
                'Can not refund more than captured amount from customer "%s"',
                $customerId
            )
        );
    }
}
