<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model;

use Ramsey\Uuid\UuidInterface;

final class CannotReverseMoreThanAuthorized extends \DomainException
{
    public static function with(UuidInterface $customerId): self
    {
        return new self(
            sprintf(
                'Can not reverse more than authorized amount from customer "%s"',
                $customerId
            )
        );
    }
}
