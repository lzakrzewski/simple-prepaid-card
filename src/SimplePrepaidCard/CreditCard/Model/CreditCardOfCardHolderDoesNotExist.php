<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Model;

use Ramsey\Uuid\UuidInterface;

final class CreditCardOfCardHolderDoesNotExist extends \DomainException
{
    public static function with(UuidInterface $holderId): self
    {
        return new self(sprintf('Credit card of card holder with id "%s" does not exists.', $holderId));
    }
}
