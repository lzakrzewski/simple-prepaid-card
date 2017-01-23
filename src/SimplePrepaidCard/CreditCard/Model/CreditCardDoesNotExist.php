<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Model;

use Ramsey\Uuid\UuidInterface;

final class CreditCardDoesNotExist extends \DomainException
{
    public static function with(UuidInterface $creditCardId): self
    {
        return new self(sprintf('Credit card with id %s does not exist.', $creditCardId));
    }
}
