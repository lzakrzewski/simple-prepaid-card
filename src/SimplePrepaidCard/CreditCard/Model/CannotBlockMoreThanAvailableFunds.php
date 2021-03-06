<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Model;

use Ramsey\Uuid\UuidInterface;

class CannotBlockMoreThanAvailableFunds extends \DomainException
{
    public static function with(UuidInterface $creditCardId): self
    {
        return new self(
            sprintf(
                'Can not block more than available funds on a credit card with id "%s".',
                $creditCardId
            )
        );
    }
}
