<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model;

use Ramsey\Uuid\UuidInterface;

final class CannotCaptureMoreThanAuthorized extends \DomainException
{
    public static function with(UuidInterface $customerId): self
    {
        return new self(
            sprintf(
                'Can not capture more than authorized amount from customer "%s"',
                $customerId
            )
        );
    }
}
