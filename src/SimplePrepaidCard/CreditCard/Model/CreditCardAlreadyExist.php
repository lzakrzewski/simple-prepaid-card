<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Model;

use Ramsey\Uuid\UuidInterface;

final class CreditCardAlreadyExist extends \DomainException
{
    public static function with(UuidInterface $creditCardId)
    {
        return new self(sprintf('Credit card with id %s already exist.', $creditCardId));
    }
}
