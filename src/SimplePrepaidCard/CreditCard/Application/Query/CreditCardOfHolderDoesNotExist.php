<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Application\Query;

use Ramsey\Uuid\UuidInterface;

final class CreditCardOfHolderDoesNotExist extends \DomainException
{
    public static function with(UuidInterface $holderId): self
    {
        return new self(sprintf('Credit card of holder with id "%s" does not exist.', $holderId));
    }
}
