<?php

declare(strict_types=1);

namespace SimplePrepaidCard\CreditCard\Model;

use Ramsey\Uuid\UuidInterface;

final class CannotUseNegativeFunds extends \DomainException
{
    public static function with(UuidInterface $creditCardId): self
    {
        return new self(
            sprintf(
                'Can not use negative funds onto a credit card with id "%s".',
                $creditCardId
            )
        );
    }
}
