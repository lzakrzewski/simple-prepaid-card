<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CoffeeShop\Model;

use Ramsey\Uuid\UuidInterface;

final class CustomerDoesNotExist extends \DomainException
{
    public static function with(UuidInterface $customerId): self
    {
        return new self(sprintf('Customer with id "%s" does not exist.', $customerId));
    }
}
